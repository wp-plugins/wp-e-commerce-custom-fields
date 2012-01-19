<?php
/*
Plugin Name: WP e-Commerce - Custom Fields
Plugin URI: http://www.visser.com.au/wp-ecommerce/plugins/custom-fields/
Description: Add and manage custom Product meta details within WP e-Commerce.
Version: 1.4.2
Author: Visser Labs
Author URI: http://www.visser.com.au/about/
Contributor: Ryan Waggoner
Contributor URI: http://ryanwaggoner.com/
Contributor: Kleber Lopes da Silva
Contributor URI: http://gameplaceholder.blogspot.com/
License: GPL2
*/

load_plugin_textdomain( 'wpsc_cf', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

include_once( 'includes/functions.php' );

include_once( 'includes/common.php' );

switch( wpsc_get_major_version() ) {

	case '3.7':
		include_once( 'includes/release-3_7.php' );
		break;

	case '3.8':
		include_once( 'includes/release-3_8.php' );
		break;

}

$wpsc_cf = array(
	'filename' => basename( __FILE__ ),
	'dirname' => basename( dirname( __FILE__ ) ),
	'abspath' => dirname( __FILE__ ),
	'relpath' => basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ )
);

$wpsc_cf['prefix'] = 'wpsc_cf';
$wpsc_cf['name'] = __( 'Custom Fields for WP e-Commerce', 'wpsc_cf' );
$wpsc_cf['menu'] = __( 'Custom Fields', 'wpsc_cf' );

if( is_admin() ) {

	/* Start of: WordPress Administration */

	include_once( 'includes/install.php' );
	register_activation_hook( __FILE__, 'wpsc_cf_install' );

	include_once( dirname( __FILE__ ) . '/includes/update.php' );

	function wpsc_cf_html_page() {

		global $wpdb, $wpsc_cf;

		wpsc_cf_template_header();
		$action = wpsc_get_action();
		switch( $action ) {

			case 'update':
				$position = $_POST['position'];
				$layout = $_POST['layout'];
				$display_title = $_POST['display_title'];
				$title_text = $_POST['title_text'];

				update_option( 'wpsc_cf_position', $position );
				update_option( 'wpsc_cf_layout', $layout );
				update_option( 'wpsc_cf_display_title', $display_title );
				update_option( 'wpsc_cf_title_text', $title_text );

				$message = __( 'Settings updated', 'wpsc_cf' );
				$output = '<div class="updated settings-error"><p><strong>' . $message . '.</strong></p></div>';
				echo $output;

				wpsc_cf_manage_form();
				break;

			case 'delete':
				$id = $_GET['id'];
				$data = unserialize( get_option( 'wpsc_cf_data' ) );
				unset( $data[$id] );
				$data = serialize( $data );

				update_option( 'wpsc_cf_data', $data );
				unset( $data );

				$message = __( 'Custom field deleted', 'wpsc_cf' );
				$output = '<div class="updated settings-error"><p><strong>' . $message . '.</strong></p></div>';
				echo $output;

				wpsc_cf_manage_form();
				break;

			case 'edit-confirm':
				$id = $_POST['custom-field-id'];
				$name = $_POST['custom-field-name'];
				$slug = $_POST['custom-field-slug'];
				$type = $_POST['custom-field-type'];
				$order = $_POST['custom-field-order'];
				$prefix = $_POST['custom-field-prefix'];
				$suffix = $_POST['custom-field-suffix'];
				$show_name = $_POST['custom-field-show-name'];
				if( isset( $id ) && $name && $slug && $type ) {
					$options = $_POST['custom-field-options'];
					$description = $_POST['custom-field-description'];
					$field = array();
					$field[] = array(
						'name' => $name, 
						'slug' => $slug, 
						'type' => $type, 
						'options' => $options, 
						'order' => $order, 
						'description' => $description,
						'prefix' => $prefix,
						'suffix' => $suffix,
						'show_name' => $show_name
					);
					$data = unserialize( get_option( 'wpsc_cf_data' ) );
					$data[$id]['name'] = $name;
					$data[$id]['slug'] = $slug;
					$data[$id]['type'] = $type;
					$data[$id]['order'] = $order;
					$data[$id]['prefix'] = $prefix;
					$data[$id]['suffix'] = $suffix;
					$data[$id]['show_name'] = $show_name;
					if( $type == 'dropdown' )
						$data[$id]['options'] = $options;
					$data[$id]['description'] = $description;
					$data = serialize( $data );

					update_option( 'wpsc_cf_data', $data );
					unset( $data );

					$message = __( 'Custom field updated', 'wpsc_cf' );
					$output = '<div class="updated settings-error"><p><strong>' . $message . '.</strong></p></div>';
				} else {
					$message = '<strong>' . __( 'ERROR', 'wpsc_cf' ) . '</strong>: ' . __( 'A required field was not filled. Please ensure required fields are filled.', 'wpsc_cf' );
					$output = '<div class="error settings-error"><p>' . $message . '</strong></p></div>';
				}
				echo $output;

				wpsc_cf_manage_form();
				break;

			case 'new-confirm':
				$wpsc_cf_name = $_POST['custom-field-name'];
				$wpsc_cf_slug = $_POST['custom-field-slug'];
				$wpsc_cf_type = $_POST['custom-field-type'];
				$wpsc_cf_order = $_POST['custom-field-order'];
				$wpsc_cf_prefix = $_POST['custom-field-prefix'];
				$wpsc_cf_suffix = $_POST['custom-field-suffix'];
				$wpsc_cf_show_name = $_POST['custom-field-show-name'];
				if( $wpsc_cf_name && $wpsc_cf_type ) {
					if( !$wpsc_cf_slug ) {
						$slug_filters = array( '(', ')' );
						$wpsc_cf_slug = str_replace( $slug_filters, '', $wpsc_cf_name );
						$wpsc_cf_slug = strtolower( str_replace( ' ', '-', $wpsc_cf_slug ) );
					}
					$wpsc_cf_description = $_POST['custom-field-description'];
					if( get_option( 'wpsc_cf_data' ) ) {
						$wpsc_cf_data = unserialize( get_option( 'wpsc_cf_data' ) );
						$wpsc_cf_field = array(
							'name' => $wpsc_cf_name, 
							'slug' => $wpsc_cf_slug, 
							'type' => $wpsc_cf_type, 
							'order' => $wpsc_cf_order, 
							'description' => $wpsc_cf_description,
							'prefix' => $wpsc_cf_prefix,
							'suffix' => $wpsc_cf_suffix,
							'show_name' => $wpsc_cf_show_name
						);
						$wpsc_cf_data[] = $wpsc_cf_field;
						$wpsc_cf_data = serialize( $wpsc_cf_data );
						update_option( 'wpsc_cf_data', $wpsc_cf_data );
					} else {
						$wpsc_cf_data = array();
						$wpsc_cf_data[] = array(
							'name' => $wpsc_cf_name, 
							'slug' => $wpsc_cf_slug, 
							'type' => $wpsc_cf_type, 
							'order' => $wpsc_cf_order, 
							'description' => $wpsc_cf_description,
							'prefix' => $wpsc_cf_prefix,
							'suffix' => $wpsc_cf_suffix,
							'show_name' => $wpsc_cf_show_name
						);
						$wpsc_cf_data = serialize( $wpsc_cf_data );
						update_option( 'wpsc_cf_data', $wpsc_cf_data );
					}
					unset( $wpsc_cf_data );

					if( $wpsc_cf_type == 'dropdown' ) {
						$message = __( 'Custom field saved, you\'ll now need to define the Options for this field', 'wpsc_cf' );
						$output = '<div class="updated settings-error"><p><strong>' . $message . '.</strong></p></div>';
					} else {
						$message = __( 'Custom field saved', 'wpsc_cf' );
						$output = '<div class="updated settings-error"><p><strong>' . $message . '.</strong></p></div>';
					}
				} else {
					$message = '<strong>' . __( 'ERROR', 'wpsc_cf' ) . '</strong>: ' . __( 'A required field was not filled. Please ensure required fields are filled.', 'wpsc_cf' ) . '.</strong>';
					$output = '<div class="error settings-error"><p>' . $message . '</p></div>';
				}
				echo $output;
				wpsc_cf_manage_form();
				break;

			case 'edit':
			case 'new':
				if( $action == 'edit' ) {
					$wpsc_cf_id = $_GET['id'];
					$wpsc_cf_data = unserialize( get_option( 'wpsc_cf_data' ) );
					$wpsc_cf_field = $wpsc_cf_data[$wpsc_cf_id];
				}

				if( $action == 'edit' )
					$title = __( 'Edit Custom Field', 'wpsc_cf' );
				else
					$title = __( 'Add New Custom Field', 'wpsc_cf' );
				$options = wpsc_cf_custom_field_types();

				include( 'templates/admin/wpsc-admin_cf_settings_detail.php' );

				break;

			default:
				wpsc_cf_manage_form();
				break;

		}
		wpsc_cf_template_footer();

	}

	function wpsc_cf_manage_form() {

		$positions = wpsc_productpage_positions();

		$layouts = array();
		$layouts[] = array( 'table.php', __( 'Table', 'wpsc_cf' ) );
		$layouts[] = array( 'list-ordered.php', __( 'List - Ordered', 'wpsc_cf' ) );
		$layouts[] = array( 'list-unordered.php', __( 'List - Unordered', 'wpsc_cf' ) );

		$wpsc_cf_data = get_option( 'wpsc_cf_data' );
		if( $wpsc_cf_data ) {
			if( wpsc_cf_is_serialized( $wpsc_cf_data ) )
				$wpsc_cf_data = unserialize( $wpsc_cf_data );
			$wpsc_cf_data = wpsc_cf_custom_field_sort( $wpsc_cf_data, 'order' );
		}

		include( 'templates/admin/wpsc-admin_cf_settings.php' );

	}

	/* End of: WordPress Administration */

} else {

	/* Start of: Storefront */

	include_once( 'includes/template.php' );
	include_once( 'includes/legacy.php' );

	$position = get_option( 'wpsc_cf_position' );

	if( $position )
		add_action( $position, 'wpsc_cf_init' );
	else
		add_action( 'wpsc_product_addon_after_descr', 'wpsc_cf_init' );

	/* End of: Storefront */

}
?>