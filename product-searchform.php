<form role="search" method="get" class="woocommerce-product-search input-group" action="<?php echo esc_url( home_url( '/'  ) ); ?>">
	<input type="hidden" name="post_type" value="product" />
	<label class="screen-reader-text" for="s"><?php _e( 'Search for:', 'woocommerce' ); ?></label>
	<input type="search" class="search-field form-control" placeholder="<?php echo esc_attr_x( 'Search Products&hellip;', 'placeholder', 'woocommerce' ); ?>" value="<?php echo get_search_query(); ?>" name="s" title="<?php echo esc_attr_x( 'Search for:', 'label', 'woocommerce' ); ?>" />
	<button class="btn btn-outline-secondary btn-search" type="submit" value="<?php echo esc_attr_x( 'Search', 'submit button', 'woocommerce' ); ?>">
		<i class="fa-solid fa-magnifying-glass"></i>
	</button>
</form>