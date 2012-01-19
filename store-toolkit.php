<?php
/*
Plugin Name: WP e-Commerce - Store Toolkit
Plugin URI: http://www.visser.com.au/wp-ecommerce/plugins/store-toolkit/
Description: Permanently remove all store-generated details of your WP e-Commerce store.
Version: 1.7.7
Author: Visser Labs
Author URI: http://www.visser.com.au/about/
License: GPL2
*/

load_plugin_textdomain( 'wpsc_st', null, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

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

$wpsc_st = array(
	'filename' => basename( __FILE__ ),
	'dirname' => basename( dirname( __FILE__ ) ),
	'abspath' => dirname( __FILE__ ),
	'relpath' => basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ )
);

$wpsc_st['prefix'] = 'wpsc_st';
$wpsc_st['name'] = __( 'Store Toolkit for WP e-Commerce', 'wpsc_st' );
$wpsc_st['menu'] = __( 'Store Toolkit', 'wpsc_st' );

if( is_admin() ) {

	function wpsc_st_init() {

		$action = wpsc_get_action();
		switch( $action ) {

			case 'relink-pages':
				$productpage_sql = "SELECT `ID` FROM `" . $wpdb->posts . "` WHERE `post_content` = '[productspage]' AND `post_status` = 'publish' AND `post_type` = 'page' LIMIT 1";
				$productpage = $wpdb->get_var( $productpage_sql );
				$checkout_sql = "SELECT `ID` FROM `" . $wpdb->posts . "` WHERE `post_content` = '[shoppingcart]' AND `post_status` = 'publish' AND `post_type` = 'page' LIMIT 1";
				$checkout = $wpdb->get_var( $checkout_sql );
				$transactionresults_sql = "SELECT `ID` FROM `" . $wpdb->posts . "` WHERE `post_content` = '[transactionresults]' AND `post_status` = 'publish' AND `post_type` = 'page' LIMIT 1";
				$transactionresults = $wpdb->get_var( $transactionresults_sql );
				$account_sql = "SELECT `ID` FROM `" . $wpdb->posts . "` WHERE `post_content` = '[userlog]' AND `post_status` = 'publish' AND `post_type` = 'page' LIMIT 1";
				$account = $wpdb->get_var ( $account_sql );
				if( $productpage )
					update_option( 'product_list_url', get_bloginfo( 'url' ) . "/?page_id=" . $productpage );
				if( $checkout )
					update_option( 'shopping_cart_url', get_bloginfo( 'url' ) . "/?page_id=" . $checkout );
				if( $checkout )
					update_option( 'checkout_url', get_bloginfo( 'url' ) . "/?page_id=" . $checkout );
				if( $transactionresults )
					update_option( 'transact_url', get_bloginfo( 'url' ) . "/?page_id=" . $transactionresults );
				if( $account )
					update_option( 'user_account_url', get_bloginfo( 'url' ) . "/?page_id=" . $account );
				break;

			case 'relink-existing-preregistered-sales':
				$sales_sql = "SELECT `id` as ID FROM `" . $wpdb->prefix . "wpsc_purchase_logs` WHERE `user_ID` = 0";
				$sales = $wpdb->get_results( $sales_sql );
				if( $sales ) {
					$adjusted_sales = 0;
					foreach( $sales as $sale ) {
						$sale_email_sql = "SELECT wpsc_submited_form_data.`value` FROM `" . $wpdb->prefix . "wpsc_checkout_forms` as wpsc_checkout_forms, `" . $wpdb->prefix . "wpsc_submited_form_data` as wpsc_submited_form_data WHERE wpsc_checkout_forms.`id` = wpsc_submited_form_data.`form_id` AND wpsc_checkout_forms.`type` = 'email' AND wpsc_submited_form_data.`log_id` = " . $sale->ID . " LIMIT 1";
						$sale_email = $wpdb->get_var( $sale_email_sql );
						if( $sale_email ) {
							$sale_user_sql = "SELECT `ID` FROM `" . $wpdb->users . "` WHERE `user_email` = '" . $sale_email . "' LIMIT 1";
							$sale_user = $wpdb->get_var( $sale_user_sql );
							$wpdb->update( $wpdb->prefix . 'wpsc_purchase_logs', array(
								'user_ID' => $sale_user
							), array( 'id' => $sale->ID ) );
							$adjusted_sales++;
						}
					}
				}
				if( $adjusted_sales > 0 )
					$message = "<strong>" . $adjusted_sales . "</strong>" . __( ' existing Sale(s) from pre-registered Users have been re-linked.', 'wpsc_st' );
				else
					$message = __( 'No existing Sales from pre-registered Users have been re-linked.', 'wpsc_st' );
				$output = '<div class="updated settings-error"><p>' . $message . '</p></div>';
				echo $output;
				break;

			case 'nuke':

				if( !ini_get( 'safe_mode' ) )
					set_time_limit( 0 );

				if( isset( $_POST['wpsc_st_products'] ) )
					wpsc_st_clear_dataset( 'products' );
				if( isset( $_POST['wpsc_st_product_variations'] ) )
					wpsc_st_clear_dataset( 'variations' );
				if( isset( $_POST['wpsc_st_product_tags'] ) )
					wpsc_st_clear_dataset( 'tags' );
				if( isset( $_POST['wpsc_st_product_categories'] ) )
					wpsc_st_clear_dataset( 'categories' );
				if( isset( $_POST['wpsc_st_product_images'] ) )
					wpsc_st_clear_dataset( 'images' );
				if( isset( $_POST['wpsc_st_product_files'] ) )
					wpsc_st_clear_dataset( 'files' );
				if( isset( $_POST['wpsc_st_sales_orders'] ) )
					wpsc_st_clear_dataset( 'orders' );
				if( isset( $_POST['wpsc_st_wishlist'] ) )
					wpsc_st_clear_dataset( 'wishlist' );
				if( isset( $_POST['wpsc_st_enquiries'] ) )
					wpsc_st_clear_dataset( 'enquiries' );
				if( isset( $_POST['wpsc_st_creditcards'] ) )
					wpsc_st_clear_dataset( 'credit-cards' );
				if( isset( $_POST['wpsc_st_customfields'] ) )
					wpsc_st_clear_dataset( 'custom-fields' );
				break;

		}

	}
	add_action( 'admin_init', 'wpsc_st_init' );

	function wpsc_st_store_admin_menu() {

		global $wpsc_st;

		$base_page = $wpsc_st['prefix'];
		//add_submenu_page( $base_page, __( 'Store Toolkit', 'wpsc_st' ), __( 'Store Toolkit', 'wpsc_st' ), 'manage_options', 'wpsc_st', 'wpsc_st_html_page' );
		remove_filter( 'wpsc_additional_pages', 'wpsc_st_add_modules_manage_pages', 10 );
		remove_action( 'admin_menu', 'wpsc_st_admin_menu', 10 );

	}
	add_action( 'wpsc_sm_store_admin_subpages', 'wpsc_st_store_admin_menu' );

	function wpsc_st_default_html_page() {

		global $wpdb, $wpsc_st;

		$products = wpsc_st_return_count( 'products' );
		$variations = wpsc_st_return_count( 'variations' );
		$images = wpsc_st_return_count( 'images' );
		$files = wpsc_st_return_count( 'files' );
		$tags = wpsc_st_return_count( 'tags' );
		$categories = wpsc_st_return_count( 'categories' );
		$orders = wpsc_st_return_count( 'orders' );
		$wishlist = wpsc_st_return_count( 'wishlist' );
		$enquiries = wpsc_st_return_count( 'enquiries' );
		$credit_cards = wpsc_st_return_count( 'credit-cards' );
		$custom_fields = wpsc_st_return_count( 'custom-fields' );
		if( $products || $variations || $images || $files || $tags || $categories || $orders || $wishlist || $enquiries || $credit_cards || $custom_fields )
			$show_table = true;
		else
			$show_table = false;

		include_once( 'templates/admin/wpsc-admin_st-toolkit.php' );

	}

	function wpsc_st_html_page() {

		global $wpdb;

		wpsc_st_template_header();
		$action = wpsc_get_action();
		switch( $action ) {

			case 'nuke':
				$message = __( 'Chosen WP e-Commerce details have been permanently erased from your WP e-Commerce store.', 'wpsc_st' );
				$output = '<div class="updated settings-error"><p><strong>' . $message . '</strong></p></div>';
				echo $output;

				wpsc_st_default_html_page();
				break;

			case 'relink-existing-preregistered-sales':
				wpsc_st_default_html_page();
				break;

			case 'relink-pages':
				$message = __( 'Default WP e-Commerce Pages have been restored.', 'wpsc_st' );
				$output = '<div class="updated settings-error"><p>' . $message . '</p></div>';
				echo $output;

				wpsc_st_default_html_page();
				break;

			case 'fix-wpsc_version':

				if( ( wpsc_get_major_version() == '3.8' ) && ( WPSC_VERSION == '3.7' ) ) {

					update_option( 'wpsc_version', '3.7' );
					$message = __( 'WordPress option \'wpsc_version\' has been repaired.', 'wpsc_st' );
					$output = '<div class="updated settings-error"><p>' . $message . '</p></div>';

				} else {

					$message = __( 'WordPress option \'wpsc_version\' did not require attention.', 'wpsc_st' );
					$output = '<div class="error settings-error"><p>' . $message . '</p></div>';

				}
				echo $output;

				wpsc_st_default_html_page();
				break;

			case 'clear-claimed_stock':

				$wpdb->query( "TRUNCATE TABLE `" . $wpdb->prefix . "wpsc_claimed_stock`" );
				$message = __( 'The \'claimed stock\' table has been emptied.', 'wpsc_st' );
				$output = '<div class="updated settings-error"><p>' . $message . '</p></div>';
				echo $output;

				wpsc_st_default_html_page();
				break;

			default:
				wpsc_st_default_html_page();
				break;

		}
		wpsc_st_template_footer();

	}

}
?>