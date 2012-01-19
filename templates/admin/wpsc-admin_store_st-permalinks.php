<h3 class="form_group"><?php _e( 'Permalink Settings', 'wpsc_st' ); ?></h3>
<table class="wpsc_options form-table">
	<tr>
		<th><strong><?php _e( 'Products Page', 'wpsc_st' ); ?></strong></th>
		<td>
			<input type="text" name="wpsc_options[product_list_url]" value="<?php echo get_option( 'product_list_url' ); ?>" size="50" class="text" />
		</td>
	</tr>
	<tr>
		<th><strong><?php _e( 'Checkout', 'wpsc_st' ); ?></strong></th>
		<td>
			<input type="text" name="wpsc_options[checkout_url]" value="<?php echo get_option( 'checkout_url' ); ?>" size="50" class="text" />
		</td>
	</tr>
	<tr>
		<th><strong><?php _e( 'Transaction Results', 'wpsc_st' ); ?></strong></th>
		<td>
			<input type="text" name="wpsc_options[transact_url]" value="<?php echo get_option( 'transact_url' ); ?>" size="50" class="text" />
		</td>
	</tr>
	<tr>
		<th><strong><?php _e( 'My Account', 'wpsc_st' ); ?></strong></th>
		<td>
			<input type="text" name="wpsc_options[user_account_url]" value="<?php echo get_option( 'user_account_url' ); ?>" size="50" class="text" />
		</td>
	</tr>
</table>