<?php
if( is_admin() ) {

	/* Start of: WordPress Administration */

	function wpsc_st_template_header() {

		global $wpsc_st; ?>
<div class="wrap">
	<div id="icon-tools" class="icon32"><br /></div>
	<h2><?php echo $wpsc_st['menu']; ?></h2>
<?php
	}

	function wpsc_st_template_footer() { ?>
</div>
<?php
	}


	function wpsc_st_empty_dir( $dir ) {

		if( strpos( php_uname(), 'Windows' ) !== FALSE )
			$dir = str_replace( '/', '\\', $dir );	
		
		$handle = opendir( $dir );
		if( $handle ) {
			while( ( $file = readdir( $handle ) ) !== false ) {
				if( $file <> '.htaccess' )
					@unlink( $dir . '/' . $file );
			}
		}
		closedir( $handle );

	}

	if( !function_exists( 'remove_filename_extension' ) ) {

		function remove_filename_extension( $filename ) {

			$extension = strrchr( $filename, '.' );
			$filename = substr( $filename, 0, -strlen( $extension ) );

			return $filename;

		}

	}

	/* End of: WordPress Administration */

}
?>