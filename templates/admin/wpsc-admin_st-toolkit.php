<script type="text/javascript">
	function showProgress() {
		window.scrollTo(0,0);
		document.getElementById('progress').style.display = 'block';
		document.getElementById('content').style.display = 'none';
	}
</script>
<div id="content">
	<h3><?php _e( 'Nuke WP e-Commerce', 'wpsc_st' ); ?></h3>
	<p><?php _e( 'Select the tables you wish to empty then click Remove to permanently clear those details from your WP e-Commerce store.', 'wpsc_st' ); ?></p>
	<form method="post" onsubmit="showProgress()">
		<div id="poststuff">
			<div class="postbox">
				<h3 class="hndle"><?php _e( 'Empty WP e-Commerce Tables', 'wpsc_st' ); ?></h3>
				<div class="inside">
					<table class="form-table">

						<tr>
							<th>
									<label for="wpsc_st_products"><?php _e( 'Products', 'wpsc_st' ); ?></label>
							</th>
							<td>
								<input type="checkbox" name="wpsc_st_products"<?php if( $products == 0 ) { ?> disabled="disabled"<?php } ?> /> (<?php echo $products; ?>)
							</td>
						</tr>

						<tr>
							<th>
								<label for="wpsc_st_product_variations"><?php _e( 'Product Variations', 'wpsc_st' ); ?></label>
							</th>
							<td>
								<input type="checkbox" name="wpsc_st_product_variations"<?php if( $variations == 0 ) { ?> disabled="disabled"<?php } ?> /> (<?php echo $variations; ?>)
							</td>
						</tr>

						<tr>
							<th>
								<label for="wpsc_st_product_image"><?php _e( 'Product Images', 'wpsc_st' ); ?></label>
							</th>
							<td>
								<input type="checkbox" name="wpsc_st_product_images"<?php if( $images == 0 ) { ?> disabled="disabled"<?php } ?> /> (<?php echo $images; ?>)
							</td>
						</tr>

						<tr>
							<th>
								<label for="wpsc_st_product_files"><?php _e( 'Product Files', 'wpsc_st' ); ?></label>
							</th>
							<td>
								<input type="checkbox" name="wpsc_st_product_files"<?php if( $files == 0 ) { ?> disabled="disabled"<?php } ?> /> (<?php echo $files; ?>)
							</td>
						</tr>

						<tr>
							<th>
								<label for="wpsc_st_product_tags"><?php _e( 'Product Tags', 'wpsc_st' ); ?></label>
							</th>
							<td>
								<input type="checkbox" name="wpsc_st_product_tags"<?php if( $tags == 0 ) { ?> disabled="disabled"<?php } ?> /> (<?php echo $tags; ?>)
							</td>
						</tr>

						<tr>
							<th>
								<label for="wpsc_st_product_categories"><?php _e( 'Product Categories', 'wpsc_st' ); ?></label>
							</th>
							<td>
								<input type="checkbox" name="wpsc_st_product_categories"<?php if( $categories == 0 ) { ?> disabled="disabled"<?php } ?> /> (<?php echo $categories; ?>)
							</td>
						</tr>

						<tr>
							<th>
								<label for="wpsc_st_sales_orders"><?php _e( 'Sales', 'wpsc_st' ); ?></label>
							</th>
							<td>
								<input type="checkbox" name="wpsc_st_sales_orders"<?php if( $orders == 0 ) { ?> disabled="disabled"<?php } ?> /> (<?php echo $orders; ?>)
							</td>
						</tr>

<?php if( $wishlist ) { ?>
						<tr>
							<th>
								<label for="wpsc_st_wishlist"><?php _e( 'Wishlist', 'wpsc_st' ); ?></label>
							</th>
							<td>
								<input type="checkbox" name="wpsc_st_wishlist"<?php if( $wishlist == 0 ) { ?> disabled="disabled"<?php } ?> /> (<?php echo $wishlist; ?>)
							</td>
						</tr>
<?php } ?>

<?php if( $enquiries ) { ?>
						<tr>
							<th>
								<label for="wpsc_st_enquiries"><?php _e( 'Enquiries', 'wpsc_st' ); ?></label>
							</th>
							<td>
								<input type="checkbox" name="wpsc_st_enquiries"<?php if( $enquiries == 0 ) { ?> disabled="disabled"<?php } ?> /> (<?php echo $enquiries; ?>)
							</td>
						</tr>
<?php } ?>

<?php if( $credit_cards ) { ?>
						<tr>
							<th>
								<label for="wpsc_st_creditcards"><?php _e( 'Credit Cards', 'wpsc_st' ); ?></label>
							</th>
							<td>
								<input type="checkbox" name="wpsc_st_creditcards"<?php if( $credit_cards == 0 ) { ?> disabled="disabled"<?php } ?> /> (<?php echo $credit_cards; ?>)
							</td>
						</tr>
<?php } ?>

<?php if( $custom_fields ) { ?>
						<tr>
							<th>
								<label for="wpsc_st_customfields"><?php _e( 'Custom Fields', 'wpsc_st' ); ?></label>
							</th>
							<td>
								<input type="checkbox" name="wpsc_st_customfields"<?php if( $custom_fields == 0 ) { ?> disabled="disabled"<?php } ?> /> (<?php echo $custom_fields; ?>)
							</td>
						</tr>
<?php } ?>

					</table>
				</div>
			</div>
		</div>
		<p class="submit">
			<input type="submit" value="<?php _e( 'Remove', 'wpsc_st' ); ?>" class="button-primary" />
		</p>
		<input type="hidden" name="action" value="nuke" />
	</form>
	<h3><?php _e( 'WP e-Commerce Tools', 'wpsc_st' ); ?></h3>
	<div id="poststuff">
		<div class="postbox">
			<h3 class="hndle"><?php _e( 'Tools', 'wpsc_st' ); ?></h3>
			<div class="inside">
				<table class="form-table">
					<tr>
						<td>
							<a href="admin.php?page=wpsc_st&action=relink-pages"><?php _e( 'Re-link WP e-Commerce Pages', 'wpsc_st' ); ?></a>
						</td>
					</tr>
					<tr>
						<td>
							<a href="admin.php?page=wpsc_st&action=relink-existing-preregistered-sales"><?php _e( 'Re-link existing Sales from pre-registered Users', 'wpsc_st' ); ?></a>
						</td>
					</tr>
					<tr>
						<td>
							<a href="admin.php?page=wpsc_st&action=fix-wpsc_version"><?php _e( 'Repair WordPress option \'wpsc_version\'', 'wpsc_st' ); ?></a>
						</td>
					</tr>
					<tr>
						<td>
							<a href="admin.php?page=wpsc_st&action=clear-claimed_stock"><?php _e( 'Empty the \'claimed_stock\' table', 'wpsc_st' ); ?></a>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>
<div id="progress" style="display:none;">
	<p><?php _e( 'Chosen WP e-Commerce details are being nuked, this process can take awhile. Time for a beer?', 'wpsc_st' ); ?></p>
	<img src="<?php echo plugins_url( '/templates/admin/images/progress.gif', $wpsc_st['relpath'] ); ?>" alt="" />
</div>