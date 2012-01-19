<?php
/*

Filename: common.php
Description: common.php loads commonly accessed functions across the Visser Labs suite.

- wpsc_get_action
- wpsc_get_major_version

*/

if( is_admin() ) {

	/* Start of: WordPress Administration */

	include_once( 'common-dashboard_widgets.php' );

	/* End of: WordPress Administration */

}

if( !function_exists( 'wpsc_get_action' ) ) {

	function wpsc_get_action( $switch = false ) {

		if( $switch ) {

			if( isset( $_GET['action'] ) )
				$action = $_GET['action'];
			else if( !isset( $action ) && isset( $_POST['action'] ) )
				$action = $_POST['action'];
			else
				$action = false;

		} else {

			if( isset( $_POST['action'] ) )
				$action = $_POST['action'];
			else if( !isset( $action ) && isset( $_GET['action'] ) )
				$action = $_GET['action'];
			else
				$action = false;

		}
		return $action;

	}

}

if( !function_exists( 'wpsc_get_major_version' ) ) {

	function wpsc_get_major_version() {

		$version = get_option( 'wpsc_version' );
		return substr( $version, 0, 3 );

	}

}
?>