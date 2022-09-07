<?php

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'WCPL_Product_Likes_Account' ) ) {

	class WCPL_Product_Likes_Account {

		public function __construct() {

			add_filter( 'woocommerce_account_menu_items', array( $this, 'item' ), 10, 1 );
			add_action( 'woocommerce_account_likes_endpoint', array( $this, 'content' ) );
			add_action( 'init', array( $this, 'endpoint' ) );
			add_filter( 'query_vars', array( $this, 'vars' ), 0 );

		}

		public function item( $items ) {

			unset( $items['customer-logout'] );
			$items['likes'] =  esc_html( apply_filters( 'wcpl_product_likes_likes_text', __( 'Likes', 'wcpl-product-likes' ) ) );
			$items['customer-logout'] = esc_html__( 'Logout', 'woocommerce' ); // Puts the log out menu item below likes
			return $items;

		}

		public function content() {

			global $wpdb;
			$user_id = get_current_user_id(); // Not using get user id function from buttons class as already logged in and don't want the not logged in user ids from that function
			$products_liked = $wpdb->get_results( $wpdb->prepare( "SELECT product_id FROM {$wpdb->prefix}wcpl_product_likes WHERE user_id = %s", $user_id ) );
			$products_liked_ids = array();
			$none_text = false;

			if ( !empty( $products_liked ) ) {

				foreach ( $products_liked as $product_liked ) {

					$product = wc_get_product( $product_liked->product_id );

					if ( !empty( $product ) ) {

						if ( true == $product->is_visible() ) {

							$products_liked_ids[] = $product_liked->product_id;

						}

					}				

				} ?>

				<ul class="products">
					<?php
					$args = array(
						'post_type'			=> 'product',
						'posts_per_page'	=> -1,
						'post__in'			=> $products_liked_ids,
						'post_status'		=> 'publish',
					);
					$loop = new WP_Query( $args );

					if ( $loop->have_posts() ) {
						while ( $loop->have_posts() ) {
							$loop->the_post();
							wc_get_template_part( 'content', 'product' );
						}
					} else {
						$none_text = true;
					}
					wp_reset_postdata();
					?>
				</ul>

				<?php

			} else {

				$none_text = true;

			}

			if ( true == $none_text ) {

				echo esc_html( apply_filters( 'wcpl_product_likes_likes_none_text', __( 'No products liked yet.', 'wcpl-product-likes' ) ) );

			}

		}

		public function endpoint() {

			add_rewrite_endpoint( 'likes', EP_PAGES );

			// Only flush rewrites once

			if ( get_option( 'wcpl_product_likes_flush_rewrites' ) == 'yes' ) {
				
				flush_rewrite_rules();
				update_option( 'wcpl_product_likes_flush_rewrites', 'no' );
			
			}

		}

		public function vars( $vars ) {

			$vars[] = 'likes';
			return $vars;

		}

	}

}
