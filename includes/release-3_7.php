<?php
if( is_admin() ) {

	/* Start of: WordPress Administration */

	/* WordPress Adminstration Menu */
	function wpsc_cf_add_modules_admin_pages( $page_hooks, $base_page ) {

		$page_hooks[] = add_submenu_page( $base_page, __( 'Custom Fields for WP e-Commerce', 'wpsc_cf' ), __( 'Custom Fields', 'wpsc_cf' ), 7, 'wpsc_cf', 'wpsc_cf_html_page' );
		return $page_hooks;

	}
	add_filter( 'wpsc_additional_pages', 'wpsc_cf_add_modules_admin_pages', 10, 2 );

	function wpsc_cf_init_meta_box() {

		$pagename = 'store_page_wpsc-edit-products';
		add_meta_box( 'wpsc_cf_meta_box', __( 'Custom Fields', 'wpsc_cf' ), 'wpsc_cf_meta_box', $pagename, 'normal', 'high' );

	}
	add_action( 'admin_menu', 'wpsc_cf_init_meta_box' );

	function wpsc_cf_add_to_product_form( $order ) {

		if( array_search( 'wpsc_cf_meta_box', (array)$order ) === false )
			$order[] = 'wpsc_cf_meta_box';
		return $order;

	}
	add_filter( 'wpsc_products_page_forms', 'wpsc_cf_add_to_product_form' );

	function wpsc_cf_meta_box( $product_data = array() ) {

		global $wpdb, $closed_postboxes;

		$wpsc_cf_data = unserialize( get_option( 'wpsc_cf_data' ) ); ?>
<div id="wpsc_product_custom_fields" class="postbox <?php echo ( ( array_search('wpsc_cf_meta_box', (array)$product_data['closed_postboxes']) !== false) ? 'closed"' : '' ); ?>" <?php echo ( ( array_search( 'wpsc_cf_meta_box', (array)$product_data['hidden_postboxes'] ) !== false ) ? ' style="display: none;"' : '' ); ?>>
	<h3 class="hndle"><?php _e( 'Custom Fields', 'wpsc_cf' ); ?></h3>
	<div class="inside">
		<div>
			<p><span class="howto"><?php _e( 'Custom Fields', 'wpsc_cf' ); ?></span></p>
<?php
		if( $wpsc_cf_data ) {
			$wpsc_cf_data = wpsc_cf_custom_field_sort( $wpsc_cf_data, 'order' );
			$i = 0;
			foreach( $wpsc_cf_data as $wpsc_cf_field ) { ?>
			<label><?php echo $wpsc_cf_field['name']; ?>:</label><br />
<?php
				switch( $wpsc_cf_field['type'] ) {

					case 'input':
						$output = '
						<input type="text" id="wpsc_cf_product_' . $i . '" name="productmeta_values[' . $wpsc_cf_field['slug'] . ']" value="' . get_product_meta( $product_data['id'], $wpsc_cf_field['slug'], true ) . '" size="32" />
						<span class="howto">' . $wpsc_cf_field['description'] . '</span>';
						break;

					case 'textarea':
						$output = '
<textarea id="wpsc_cf_product_' . $i . '" name="productmeta_values[' . $wpsc_cf_field['slug'] . ']" rows="3" cols="30">' . get_product_meta( $product_data['id'], $wpsc_cf_field['slug'], true ) . '</textarea>
<span class="howto">' . $wpsc_cf_field['description'] . '</span>';
						break;

					case 'dropdown':
						$output = '
<select id="wpsc_cf_product_' . $i . '" name="productmeta_values[' . $wpsc_cf_field['slug'] . ']">
	<option></option>';
						if( $wpsc_cf_field['options'] ) {
							$options = explode( '|', $wpsc_cf_field['options'] );
							foreach( $options as $option )
								$output .= '
	<option value="' . $option . '"' . selected( $option, get_product_meta( $product_data['id'], $wpsc_cf_field['slug'], true ), false ) . '>' . $option . '&nbsp;</option>' . "\n";
						}
							$output .= '
</select>
<span class="howto">' . $wpsc_cf_field['description'] . '</span>';
						break;

				}
				echo $output; ?>
			<br />
<?php
				$i++;
			}
		} ?>
		</div>
	</div>
</div>
<?php
	}

	/* End of: WordPress Administration */

} else {

	/* Start of: Storefront */

	function wpsc_cf_init() {

		global $wpsc_query;

		$position = get_option( 'wpsc_cf_position' );

		if( $wpsc_query->is_single ) {
			if( $position <> 'manual' )
				wpsc_cf_html_product();
		}

	}

	function wpsc_cf_html_product( $args = null ) {

		global $wpsc_cf, $wpsc_query;

		$wpsc_cf_data = unserialize( get_option( 'wpsc_cf_data' ) );
		if( $wpsc_cf_data ) {
			$wpsc_cf_data = wpsc_cf_custom_field_sort( $wpsc_cf_data, 'order' );

			if( $args ) {
				$args_data = explode( '&', $args );
				$args_filter_data = array();
				for( $i = 0; $i <= ( count( $args_data ) - 1 ); $i++ ) {
					$args_filter_data[$i] = explode( '=', $args_data[$i] );
					if( in_array( 'slug', $args_filter_data[$i] ) ) {
						$args_filter_value = $args_filter_data[$i][1];
						foreach( $wpsc_cf_data as $wpsc_cf_field_id => $wpsc_cf_field ) {
							if( $args_filter_value == $wpsc_cf_field['slug'] ) {
								$wpsc_cf_data = array();
								$wpsc_cf_data[] = $wpsc_cf_field;
							}
						}
					}
				}
			}

			$layout = get_option( 'wpsc_cf_layout' );
			$custom_fields = $wpsc_cf_data;

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

		$check = get_product_meta( wpsc_the_product_id(), $custom_field['slug'], true );
		if( $check )
			return true;

	}

	function wpsc_cf_value( $custom_field ) {

		$output = '';
		switch( $custom_field['type'] ) {

			case 'input':
			case 'dropdown':
				$output = stripcslashes( $custom_field['prefix'] ) . get_product_meta( wpsc_the_product_id(), $custom_field['slug'], true ) . stripslashes( $custom_field['suffix'] );
				break;

			case 'textarea':
				$output = stripcslashes( $custom_field['prefix'] ) . get_product_meta( wpsc_the_product_id(), $custom_field['slug'], true ) . stripslashes( $custom_field['suffix'] );
				$output = str_replace( "\n", '<br />', $output );
				break;

		}
		echo $output;

	}

	/* End of: Storefront */

}
?>
