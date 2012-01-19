<?php
if( is_admin() ) {

	/* Start of: WordPress Administration */

	function wpsc_cf_template_header() {

		global $wpsc_cf; ?>
<div class="wrap">
	<div id="icon-tools" class="icon32"><br /></div>
	<h2><?php echo $wpsc_cf['menu']; ?>
		<a href="admin.php?page=wpsc_cf&action=new" class="button add-new-h2"><?php _e( 'Add New', 'wpsc_cf' ); ?></a>
	</h2>
<?php
	}

	function wpsc_cf_template_footer() { ?>
</div>
<?php
	}

	function wpsc_cf_check_options_exist() {

		$sample = get_option( 'vl_wpsccf_data' );
		if( $sample )
			return true;

	}

	function wpsc_cf_is_serialized( $str ) {

		return( $str == serialize( false ) || @unserialize( $str ) !== false );

	}

	function wpsc_cf_return_type_label( $type ) {

		$options = array();
		$options[] = array( 'name' => 'input', 'label' => 'Input' );
		$options[] = array( 'name' => 'textarea', 'label' => 'Textarea' );
		$options[] = array( 'name' => 'dropdown', 'label' => 'Dropdown' );
		$options[] = array( 'name' => 'wysiwyg', 'label' => 'Textarea (with Editor)' );

		foreach( $options as $option ) {
			if( $option['name'] == $type ) {
				$label = $option['label'];
				break;
			}
		}
		return $label;

	}

	function wpsc_cf_custom_field_types() {

		$options = array();
		$options[] = array( 'name' => 'input', 'label' => 'Input' );
		$options[] = array( 'name' => 'textarea', 'label' => 'Textarea' );
		$options[] = array( 'name' => 'dropdown', 'label' => 'Dropdown' );
		$options[] = array( 'name' => 'wysiwyg', 'label' => 'Textarea (with Editor)' );

		return $options;

	}

	/* End of: WordPress Administration */

} else {

	/* Start of: Storefront */

	function wpsc_cf_show_name( $show_name = null ) {

		if( $show_name )
			return $show_name;
		else
			return true;

	}

	/* End of: Storefront */

}

function wpsc_cf_custom_field_sort( $array, $key ) {

	$sort = array();

	$ret = array();
	reset( $array );
	foreach( $array as $ii => $va )
		$sort[$ii] = $va[$key];
	asort( $sort );
	foreach( $sort as $ii => $va )
		$ret[$ii] = $array[$ii];
	$array = $ret;

	return $array;

}
?>