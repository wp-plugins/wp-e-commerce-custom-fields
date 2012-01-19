<?php
if( is_admin() ) {

	/* Start of: WordPress Administration */

	/* WordPress Administration menu */
	function wpsc_st_admin_menu() {

		add_management_page( __( 'Store Toolkit', 'wpsc_st' ), __( 'Store Toolkit', 'wpsc_st' ), 'manage_options', 'wpsc_st', 'wpsc_st_html_page' );

	}
	add_action( 'admin_menu', 'wpsc_st_admin_menu' );

	function wpsc_st_aioseop_init_meta_box() {

		$pagename = 'wpsc-product';
		add_meta_box( 'wpsc_st_aioseop_meta_box', __( 'All in One SEO Pack', 'wpsc_st' ), 'wpsc_st_aioseop_meta_box', $pagename, 'normal', 'high' );

	}
	if( function_exists( 'aioseop_get_version' ) ) {
		add_action( 'admin_menu', 'wpsc_st_aioseop_init_meta_box' );
		add_filter( 'wpsc_products_page_forms', 'wpsc_st_aioseop_add_to_product_form' );
	}

	function wpsc_st_aioseop_add_to_product_form( $order ) {

		if( array_search( 'wpsc_st_aioseop_meta_box', (array)$order['side'] ) === false )
			$order['side'][] = 'wpsc_st_aioseop_meta_box';

		return $order;

	}

	function wpsc_st_aioseop_meta_box() {

		global $post, $wpdb, $closed_postboxes, $wpsc_st;

		$title = get_post_meta( $post->ID, '_aioseop_title', true );
		$description = get_post_meta( $post->ID, '_aioseop_description', true );
		$keywords = get_post_meta( $post->ID, '_aioseop_keywords', true );
		$title_atr = get_post_meta( $post->ID, '_aioseop_titleatr', true );
		$menu_label = get_post_meta( $post->ID, '_aioseop_menulabel', true );

		include_once( $wpsc_st['abspath'] . '/templates/admin/wpsc-admin_st_aioseop_38.php' );

	}

	/* WordPress Administration menu */
	function wpsc_st_wpsc_admin_html_page() {

		global $wpsc_st;

		include_once( $wpsc_st['abspath'] . '/templates/admin/wpsc-admin_store_st-permalinks.php' );

	}
	add_action( 'wpsc_admin_settings_page', 'wpsc_st_wpsc_admin_html_page' );

	function wpsc_st_tool_box() {

		$title = __( 'Store Toolkit', 'wpsc_st' );
		$message = 'Permanently remove all store-generated details of your WP e-Commerce store, aka \'Nuke\' with <a href="?page=wpsc_st">' . __( 'Store Toolkit', 'wpsc_st' ) . '</a>.';
		echo '<div class="tool-box"><h3 class="title">' . $title . '</h3><p>' . $message . '</p></div>';

	}
	add_action( 'tool_box', 'wpsc_st_tool_box' );

	function wpsc_st_return_count( $dataset ) {

		global $wpdb;

		$count_sql = null;
		switch( $dataset ) {

			case 'products':
				$post_type = 'wpsc-product';
				$count = wp_count_posts( $post_type );
				break;

			case 'variations':
				$term_taxonomy = 'wpsc-variation';
				$count_sql = "SELECT COUNT(`term_id`) FROM `" . $wpdb->term_taxonomy . "` WHERE `taxonomy` = '" . $term_taxonomy . "'";
				break;

			case 'images':
				$count_sql = "SELECT COUNT(`post_id`) FROM `" . $wpdb->postmeta . "` WHERE `meta_key` = '_wpsc_selected_image_size'";
				break;

			case 'files':
				$post_type = 'wpsc-product-file';
				$count = wp_count_posts( $post_type );
				break;

			case 'tags':
				$term_taxonomy = 'product_tag';
				$count_sql = "SELECT COUNT(`term_taxonomy_id`) FROM `" . $wpdb->term_taxonomy . "` WHERE `taxonomy` = '" . $term_taxonomy . "'";
				break;

			case 'categories':
				$term_taxonomy = 'wpsc_product_category';
				$count_sql = "SELECT COUNT(terms.`term_id`) FROM `" . $wpdb->terms . "` as terms, `" . $wpdb->term_taxonomy . "` as term_taxonomy WHERE terms.`term_id` = term_taxonomy.`term_id` AND term_taxonomy.`taxonomy` = '" . $term_taxonomy . "'";
				break;

			case 'orders':
				$count_sql = "SELECT COUNT(`id`) FROM `" . $wpdb->prefix . "wpsc_purchase_logs`";
				break;

			case 'wishlist':
				$post_type = 'wpsc-wishlist';
				$count = wp_count_posts( $post_type );
				break;

			case 'enquiries':
				$post_type = 'wpsc-enquiry';
				$count = wp_count_posts( $post_type );
				break;

			case 'credit-cards':
				$count_sql = "SELECT COUNT(`id`) FROM `" . $wpdb->prefix . "wpsc_creditcard`";
				break;

			case 'custom-fields':
				$custom_fields = get_option( 'wpsc_cf_data' );
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
				$post_type = 'wpsc-product';
				$products = (array)get_posts( array(
					'post_type' => $post_type,
					'post_status' => $post_statuses,
					'numberposts' => -1
				) );
				if( $products ) {
					foreach( $products as $product )
						wp_delete_post( $product->ID, true );
				}
				break;

			case 'variations':
				$term_taxonomy = 'wpsc-variation';
				$variations_sql = "SELECT `term_id` FROM `" . $wpdb->term_taxonomy . "` WHERE `taxonomy` = '" . $term_taxonomy . "'";
				$variations = $wpdb->get_results( $variations_sql );
				if( $variations ) {
					foreach( $variations as $variation )
						wp_delete_term( $variation->term_id, $term_taxonomy );
				}
				break;

			case 'categories':
				$term_taxonomy = 'wpsc_product_category';
				$categories_sql = "SELECT `term_id`, `term_taxonomy_id` FROM `" . $wpdb->term_taxonomy . "` WHERE `taxonomy` = '" . $term_taxonomy . "'";
				$categories = $wpdb->get_results( $categories_sql );
				if( $categories ) {
					foreach( $categories as $category ) {
						wp_delete_term( $category->term_id, $term_taxonomy );
						$wpdb->query( "DELETE FROM `" . $wpdb->terms . "` WHERE `term_id` = " . $category->term_id );
						$wpdb->query( "DELETE FROM `" . $wpdb->term_relationships . "` WHERE `term_taxonomy_id` = " . $category->term_taxonomy_id );
					}
				}
				$wpdb->query( "DELETE FROM `" . $wpdb->prefix . "wpsc_meta` WHERE `object_type` = 'wpsc_category'" );
				$wpdb->query( "DELETE FROM `" . $wpdb->term_taxonomy . "` WHERE `taxonomy` = '" . $term_taxonomy . "'" );
				break;

			case 'tags':
				$term_taxonomy = 'product_tag';
				$tags_sql = "SELECT `term_id` FROM `" . $wpdb->term_taxonomy . "` WHERE `taxonomy` = '" . $term_taxonomy . "'";
				$tags = $wpdb->get_results( $tags_sql );
				if( $tags ) {
					foreach( $tags as $tag ) {
						wp_delete_term( $tag->term_id, $term_taxonomy );
						$wpdb->query( "DELETE FROM `" . $wpdb->terms . "` WHERE `term_id` = " . $tag->term_id );
					}
				}
				break;

			case 'images':
				$images_sql = "SELECT `post_id` FROM `" . $wpdb->postmeta . "` WHERE `meta_key` = '_wpsc_selected_image_size'";
				$images = $wpdb->get_results( $images_sql );
				if( $images ) {
					$upload_dir = wp_upload_dir();
					$intermediate_sizes = wpsc_intermediate_image_sizes_advanced( $intermediate_sizes );
					foreach( $images as $image ) {
						$image->filepath = dirname( $upload_dir['basedir'] . '/' . get_post_meta( $image->post_id, '_wp_attached_file', true ) );
						chdir( $image->filepath );
						$image->filename = basename( get_post_meta( $image->post_id, '_wp_attached_file', true ) );
						$image->extension = strrchr( $image->filename, '.' );
						$image->filebase = remove_filename_extension( $image->filename );
						foreach( $intermediate_sizes as $intermediate_name => $intermediate_size ) {
							if( file_exists( $image->filebase . '-' . $intermediate_size['width'] . 'x' . $intermediate_size['height'] . $image->extension ) )
								@unlink( $image->filebase . '-' . $intermediate_size['width'] . 'x' . $intermediate_size['height'] . $image->extension );
						}
						if( file_exists( $image->filename ) )
							@unlink( basename( $image->filename ) );
						wp_delete_post( $image->post_id );
					}
				}
				break;

			case 'files':
				$post_type = 'wpsc-product-file';
				$files_sql = "DELETE FROM `" . $wpdb->posts . "` WHERE `post_type` = '" . $post_type . "'";
				$wpdb->query( $files_sql );
				break;

			case 'orders':
				$wpdb->query( "TRUNCATE TABLE `" . $wpdb->prefix . "wpsc_purchase_logs`" );
				$wpdb->query( "TRUNCATE TABLE `" . $wpdb->prefix . "wpsc_cart_contents`" );
				$wpdb->query( "TRUNCATE TABLE `" . $wpdb->prefix . "wpsc_submited_form_data`" );
				$wpdb->query( "TRUNCATE TABLE `" . $wpdb->prefix . "wpsc_download_status`" );
				$wpdb->query( "DELETE FROM `" . $wpdb->prefix . "wpsc_meta` WHERE `object_type` = 'wpsc_cart_item'" );
				break;

			case 'wishlist':
				$post_type = 'wpsc-wishlist';
				$wishlists = (array)get_posts( array( 
					'post_type' => $post_type,
					'post_status' => $post_statuses,
					'numberposts' => -1
				) );
				if( $wishlists ) {
					foreach( $wishlists as $wishlist )
						wp_delete_post( $wishlist->ID, true );
				}
				break;

			case 'enquiries':
				$post_type = 'wpsc-enquiry';
				$enquiries = (array)get_posts( array( 
					'post_type' => $post_type,
					'post_status' => $post_statuses,
					'numberposts' => -1
				) );
				if( $enquiries ) {
					foreach( $enquiries as $enquiry )
						wp_delete_post( $enquiry->ID, true );
				}
				if( $enquiries ) {
					foreach( $enquiries as $enquiry ) {
						if( $enquiry->ID )
							wp_delete_post( $enquiry->ID, true );
					}
				}
				break;

			case 'credit-cards':
				$wpdb->query( "TRUNCATE TABLE `" . $wpdb->prefix . "wpsc_creditcard`" );
				break;

			case 'custom-fields':
				delete_option( 'wpsc_cf_data' );
				break;

		}

	}

	/* End of: WordPress Administration */

}
?>