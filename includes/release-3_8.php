<?php
if( is_admin() ) {

	/* Start of: WordPress Administration */

	/* WordPress Adminstration Menu */
	function wpsc_cf_admin_menu() {

		add_options_page( __( 'Custom Fields for WP e-Commerce', 'wpsc_cf' ), __( 'Custom Fields', 'wpsc_cf' ), 'manage_options', 'wpsc_cf', 'wpsc_cf_html_page' );

	}
	add_action( 'admin_menu', 'wpsc_cf_admin_menu' );

	function wpsc_cf_init_meta_box() {

		$pagename = 'wpsc-product';
		add_meta_box( 'wpsc_cf_meta_box', __( 'Custom Fields', 'wpsc_cf' ), 'wpsc_cf_meta_box', $pagename, 'normal', 'high' );

	}
	add_action( 'admin_menu', 'wpsc_cf_init_meta_box' );

	function wpsc_cf_add_to_product_form( $order ) {

		if( array_search( 'wpsc_cf_meta_box', (array)$order ) === false )
			$order[] = 'wpsc_cf_meta_box';
		return $order;

	}
	add_filter( 'wpsc_products_page_forms', 'wpsc_cf_add_to_product_form' );

	function wpsc_cf_meta_box() {

		global $post, $wpdb, $closed_postboxes;

		$product_data = get_post_custom( $post->ID );
		$product_data['meta'] = maybe_unserialize( $product_data );
		foreach( $product_data['meta'] as $meta_key => $meta_value )
			$product_data['meta'][$meta_key] = $meta_value[0];
		$product_meta = maybe_unserialize( $product_data['_wpsc_product_metadata'][0] );

		$wpsc_cf_data = unserialize( get_option( 'wpsc_cf_data' ) );
		if( $wpsc_cf_data ) {
			$wpsc_cf_data = wpsc_cf_custom_field_sort( $wpsc_cf_data, 'order' );
			$i = 0;
			foreach( $wpsc_cf_data as $wpsc_cf_field ) { ?>
			<label for="wpsc_cf_product_<?php echo $i; ?>"><?php echo $wpsc_cf_field['name']; ?>:</label><br />
<?php
				switch( $wpsc_cf_field['type'] ) {

					case 'input':
						$output = '
<input type="text" id="wpsc_cf_product_' . $i . '" name="meta[' . WPSC_META_PREFIX . $wpsc_cf_field['slug'] . ']" value="' . get_product_meta( $post->ID, $wpsc_cf_field['slug'], true ) . '" size="32" />
<span class="howto">' . $wpsc_cf_field['description'] . '</span>';
						break;

					case 'textarea':
						$output = '
<textarea id="wpsc_cf_product_' . $i . '" name="meta[' . WPSC_META_PREFIX . $wpsc_cf_field['slug'] . ']" rows="3" cols="30">' . get_product_meta( $post->ID, $wpsc_cf_field['slug'], true ) . '</textarea>
<span class="howto">' . $wpsc_cf_field['description'] . '</span>';
						break;

					case 'dropdown':
						$output = '
<select id="wpsc_cf_product_' . $i . '" name="meta[' . WPSC_META_PREFIX . $wpsc_cf_field['slug'] . ']">
	<option></option>';
						if( $wpsc_cf_field['options'] ) {
							$options = explode( '|', $wpsc_cf_field['options'] );
							foreach( $options as $option )
								$output .= '
	<option value="' . $option . '"' . selected( $option, get_product_meta( $post->ID, $wpsc_cf_field['slug'], true ), false ) . '>' . $option . '&nbsp;</option>' . "\n";
						}
						$output .= '
</select>
<span class="howto">' . $wpsc_cf_field['description'] . '</span>';
						break;

					case 'wysiwyg':
						$output = '
<script type="text/javascript">
	jQuery(document).ready( function () {
	jQuery("#wpsc_cf_product_' . $i . '").addClass("mceEditor");
	if ( typeof( tinyMCE ) == "object" && typeof( tinyMCE.execCommand ) == "function" ) {
	jQuery("#wpsc_cf_product_' . $i . '").wrap( "" );
	tinyMCE.execCommand("mceAddControl", false, "wpsc_cf_product_' . $i . '");
	}
	});
</script>
<div style="background-color:#fff;">
	<textarea id="wpsc_cf_product_' . $i . '" name="meta[' . WPSC_META_PREFIX . $wpsc_cf_field['slug'] . ']" rows="3" cols="30">' . wpautop( get_product_meta( $post->ID, $wpsc_cf_field['slug'], true ) ) . '</textarea>
</div>
<span class="howto">' . $wpsc_cf_field['description'] . '</span>';
					break;

				}
				echo $output; ?>
			<br />
<?php
				$i++;
			}
		}
	}

	/* End of: WordPress Administration */

} else {

	/* Start of: Storefront */

	function wpsc_cf_init() {

		global $wp_query;

		$position = get_option( 'wpsc_cf_position' );

		if( $wp_query->is_single ) {
			if( $position <> 'manual' )
				wpsc_cf_html_product();
		}

	}

	function wpsc_cf_html_product( $args = null ) {

		global $wpsc_cf;

		$data = unserialize( get_option( 'wpsc_cf_data' ) );
		if( $data ) {
			$data = wpsc_cf_custom_field_sort( $data, 'order' );
			$custom_fields = $data;
			$layout = get_option( 'wpsc_cf_layout' );
			if( $layout ) {
				if( file_exists( STYLESHEETPATH . '/wpsc-single_product_customfields_' . $layout ) )
					include( STYLESHEETPATH . '/wpsc-single_product_customfields_' . $layout );
				else
					include( $wpsc_cf['abspath'] . '/templates/store/wpsc-single_product_customfields_' . $layout );
			} else {
				include( $wpsc_cf['abspath'] . '/templates/store/wpsc-single_product_customfields_table.php' );
			}

		}

	}

	function wpsc_cf_has_value( $custom_field ) {

		global $post;

		$check = get_post_meta( $post->ID, '_wpsc_' . $custom_field['slug'], true );
		if( $check )
			return true;

	}

	function wpsc_cf_value( $custom_field ) {

		global $post;

		$output = '';
		switch( $custom_field['type'] ) {

			case 'input':
			case 'dropdown':
				$output = stripcslashes( $custom_field['prefix'] ) . get_post_meta( $post->ID, '_wpsc_' . $custom_field['slug'], true ) . stripslashes( $custom_field['suffix'] );
				break;

			case 'textarea':
			case 'wysiwyg':
				$output = stripcslashes( $custom_field['prefix'] ) . get_post_meta( $post->ID, '_wpsc_' . $custom_field['slug'], true ) . stripslashes( $custom_field['suffix'] );
				$output = str_replace( "\n", '<br />', $output );
				break;

		}
		echo $output;

	}

	/* End of: Storefront */

}
?>
