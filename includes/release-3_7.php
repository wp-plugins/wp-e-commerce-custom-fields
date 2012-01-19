<?php
if( is_admin() ) {

	/* Start of: WordPress Administration */

	/* WordPress Administration menu */
	function wpsc_st_add_modules_admin_pages( $page_hooks, $base_page ) {

		$page_hooks[] = add_submenu_page( $base_page, __( 'Store Toolkit', 'wpsc_st' ), __( 'Store Toolkit', 'wpsc_st' ), 7, 'wpsc_st', 'wpsc_st_html_page' );
		return $page_hooks;

	}
	add_filter( 'wpsc_additional_pages', 'wpsc_st_add_modules_admin_pages', 10, 2 );

	function wpsc_st_return_count( $dataset ) {

		global $wpdb;

		$count_sql = null;
		switch( $dataset ) {

			case 'products':
				$count_sql = "SELECT COUNT(`id`) FROM `" . $wpdb->prefix . "wpsc_product_list`";
				break;

			case 'variations':
				$count_sql = "SELECT COUNT(`id`) FROM `" . $wpdb->prefix . "wpsc_product_variations`";
				break;

			case 'images':
				$count_sql = "SELECT COUNT(`id`) FROM `" . $wpdb->prefix . "wpsc_product_images`";
				break;

			case 'files':
				$count_sql = "SELECT COUNT(`id`) FROM `" . $wpdb->prefix . "wpsc_product_files`";
				break;

			case 'tags':
				$term_taxonomy = 'product_tag';
				$count_sql = "SELECT COUNT(`term_taxonomy_id`) FROM `" . $wpdb->term_taxonomy . "` WHERE `taxonomy` = '" . $term_taxonomy . "'";
				break;

			case 'categories':
				$count_sql = "SELECT COUNT(`id`) FROM `" . $wpdb->prefix . "wpsc_product_categories`";
				break;

			case 'orders':
				$count_sql = "SELECT COUNT(`id`) FROM `" . $wpdb->prefix . "wpsc_purchase_logs`";
				break;

			case 'wishlist':
				$count_sql = "SELECT COUNT(`id`) FROM `" . $wpdb->prefix . "wpsc_wishlist`";
				break;

			case 'enquiries':
				$post_type = 'wpsc-enquiry';
				$count = wp_count_posts( $post_type );
				break;

			case 'credit-cards':
				$count_sql = "SELECT COUNT(`id`) FROM `" . $wpdb->prefix . "wpsc_creditcard`";
				break;

			case 'custom-fields':
				$custom_fields = count( get_option( 'wpsc_cf_data' ) );
				if( $custom_fields )
					$count = count( maybe_unserialize( $custom_fields ) );
				else
					$count = 0;
				break;

		}
		if( isset( $count ) || $count_sql ) {
			if( isset( $count ) ) {
				if( is_object( $count ) ) {
					$count_object = $count;
					$count = 0;
					foreach( $count_object as $key => $item )
						$count = $item + $count;
				}
				return $count;
			} else {
				$count = $wpdb->get_var( $count_sql );
			}
			return $count;
		} else {
			return false;
		}

	}

	function wpsc_st_clear_dataset( $dataset ) {

		global $wpdb;

		$post_statuses = array(
			'publish',
			'pending',
			'draft',
			'auto-draft',
			'future',
			'private',
			'inherit',
			'trash'
		);

		switch( $dataset ) {

			case 'products':
				$wpdb->query( "TRUNCATE TABLE `" . $wpdb->prefix . "wpsc_product_list`" );
				$wpdb->query( "TRUNCATE TABLE `" . $wpdb->prefix . "wpsc_productmeta`" );
				$wpdb->query( "TRUNCATE TABLE `" . $wpdb->prefix . "wpsc_product_order`" );
				$wpdb->query( "TRUNCATE TABLE `" . $wpdb->prefix . "wpsc_product_rating`" );
				$wpdb->query( "TRUNCATE TABLE `" . $wpdb->prefix . "wpsc_item_category_assoc`" );
				break;

			case 'variations':
				$wpdb->query( "TRUNCATE TABLE `" . $wpdb->prefix . "wpsc_product_variations`" );
				$wpdb->query( "TRUNCATE TABLE `" . $wpdb->prefix . "wpsc_variation_assoc`" );
				$wpdb->query( "TRUNCATE TABLE `" . $wpdb->prefix . "wpsc_variation_combinations`" );
				$wpdb->query( "TRUNCATE TABLE `" . $wpdb->prefix . "wpsc_variation_properties`" );
				$wpdb->query( "TRUNCATE TABLE `" . $wpdb->prefix . "wpsc_variation_values`" );
				$wpdb->query( "TRUNCATE TABLE `" . $wpdb->prefix . "wpsc_variation_values_assoc`" );
				break;

			case 'tags':
				$tags_sql = "SELECT `term_id` FROM `" . $wpdb->term_taxonomy . "` WHERE `taxonomy` = 'product_tag'";
				$tags = $wpdb->get_results( $tags_sql );
				if( $tags ) {
					foreach( $tags as $tag ) {
						wp_delete_term( $tag->term_id, 'product_tag' );
						$wpdb->query( "DELETE FROM `" . $wpdb->terms . "` WHERE `term_id` = " . $tag->term_id );
					}
				}
				break;

			case 'categories':
				$wpdb->query( "TRUNCATE TABLE `" . $wpdb->prefix . "wpsc_product_categories`" );
				break;

			case 'images':
				$upload_dir = wp_upload_dir();
				wpsc_st_empty_dir( $upload_dir['basedir'] . '/wpsc/product_images' );
				wpsc_st_empty_dir( $upload_dir['basedir'] . '/wpsc/product_images/thumbnails' ); 
				$wpdb->query( "TRUNCATE TABLE `" . $wpdb->prefix . "wpsc_product_images`" );
				break;

			case 'files':
				wpsc_st_empty_dir( $upload_dir['basedir'] . '/wpsc/downloadables' );
				$wpdb->query( "TRUNCATE TABLE `" . $wpdb->prefix . "wpsc_product_files`" );
				break;

			case 'orders':
				$wpdb->query( "TRUNCATE TABLE `" . $wpdb->prefix . "wpsc_purchase_logs`" );
				$wpdb->query( "TRUNCATE TABLE `" . $wpdb->prefix . "wpsc_cart_contents`" );
				$wpdb->query( "TRUNCATE TABLE `" . $wpdb->prefix . "wpsc_submited_form_data`" );
				$wpdb->query( "TRUNCATE TABLE `" . $wpdb->prefix . "wpsc_download_status`" );
				break;

			case 'wishlist':
				$wpdb->query( "TRUNCATE TABLE `" . $wpdb->prefix . "wpsc_wishlist`");
				break;

			case 'enquiries':
				$post_type = 'wpsc-enquiry';
				$enquiries_sql = "SELECT `ID` FROM `" . $wpdb->posts . "` WHERE `post_type` = '" . $post_type . "'";
				$enquiries = $wpdb->get_results( $enquiries_sql );
				if( $enquiries ) {
					foreach( $enquiries as $enquiry ) {
						if( $enquiry->ID )
							wp_delete_post( $enquiry->ID );
					}
				}
				break;

			case 'credit-cards':
				$wpdb->query( "TRUNCATE TABLE `" . $wpdb->prefix . "wpsc_creditcard`");
				break;

			case 'custom-fields':
				delete_option( 'wpsc_cf_data' );
				break;

		}

	}

	/* End of: WordPress Administration */

}
?>