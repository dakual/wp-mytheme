<?php

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'WCPL_Product_Likes_Button' ) ) {

	class WCPL_Product_Likes_Button {

		public function __construct() {
			add_action( 'init', array( $this, 'cookie' ) );
			add_action( 'wp_footer', array( $this, 'scripts_styles' ) );
			add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'product_page_display' ) );
			add_action( 'woocommerce_after_shop_loop_item', array( $this, 'archive_page_display' ), 20 );
			add_action( 'wp_ajax_wcpl_product_likes_update', array( $this, 'update_likes' ) );
			add_action( 'wp_ajax_nopriv_wcpl_product_likes_update', array( $this, 'update_likes' ) );
		}

		public function cookie() {
			if ( !isset( $_COOKIE['wcpl_product_likes'] ) ) {
				setcookie( 'wcpl_product_likes', wp_generate_uuid4(), time() + ( 86400 * 30 ), '/' );
			}
		}

		public function scripts_styles() {
			$user_id = $this->user_id();

			if ( $this->user_logged_in() == true || ( $this->user_logged_in() == false && get_option( 'wcpl_product_likes_not_logged_in' ) == 'yes' ) ) { ?>

				<script>
				function productLikesInitialize() {
					if ( window.jQuery ) {
						jQuery( document ).ready( function($) {
							$( 'body' ).on( 'click', '.wcpl-product-likes-button', function(e) {
								e.preventDefault();
								var data = {
									'action': 'wcpl_product_likes_update',
									'product_id': $(this).parent().attr('data-product-id'),
									'type': $(this).attr('data-type'),
									'nonce': '<?php echo esc_html( wp_create_nonce( 'wcpl_product_likes_update' ) ); ?>',
								};

								jQuery.post( '<?php echo esc_html( admin_url( 'admin-ajax.php' ) ); ?>', data, function( response ) {
									response = $.trim( response );
									response = response.split('_');
									likedTotal = parseInt( $('.wcpl-product-likes-product[data-product-id="' + response[1] + '"] .wcpl-product-likes-liked-total').text() );

									if ( response[0] == 'liked' ) {
										likedTotalNew = likedTotal + 1;
										$('.wcpl-product-likes-product[data-product-id="' + response[1] + '"] .wcpl-product-likes-button').attr( 'data-type', 'unlike' ).html( '<i class="fas fa-heart"></i>' ); 
										$('.wcpl-product-likes-product[data-product-id="' + response[1] + '"] .wcpl-product-likes-liked-total').text( likedTotalNew );

										if ( likedTotalNew > 0 ) {
											$('.wcpl-product-likes-product[data-product-id="' + response[1] + '"] .wcpl-product-likes-liked').show();
										}
									} else {
										likedTotalNew = likedTotal - 1;
										$('.wcpl-product-likes-product[data-product-id="' + response[1] + '"] .wcpl-product-likes-button').attr( 'data-type', 'like' ).html( '<i class="far fa-heart"></i>' );
										$('.wcpl-product-likes-product[data-product-id="' + response[1] + '"] .wcpl-product-likes-liked-total').text( likedTotalNew );

										if ( likedTotalNew == 0 ) {
											$('.wcpl-product-likes-product[data-product-id="' + response[1] + '"] .wcpl-product-likes-liked').hide();
										}
									}
								});
							});
						});
					} else {
						window.setTimeout( 'productLikesInitialize();' , 100 );
					}
				}
				productLikesInitialize();
				</script>
				<?php
			}

		}

		public function product_page_display() {
			if ( get_option( 'wcpl_product_likes_products' ) == 'yes' ) {
				$this->like_button();
			}
		}

		public function archive_page_display() {
			if ( get_option( 'wcpl_product_likes_archives' ) == 'yes' ) {
				$this->like_button();
			}
		}

		public function like_button() {
			global $post, $wpdb;
			$user_id = $this->user_id();

			if ( $this->user_logged_in() == true || ( $this->user_logged_in() == false && get_option( 'wcpl_product_likes_not_logged_in' ) == 'yes' ) ) {

				$product_id = $post->ID;
				$product_likes = get_post_meta( $product_id, '_wcpl_product_likes_likes', true );
				$product_likes = (int) ( !empty( $product_likes ) ? $product_likes : '0' );
				$product_liked = $wpdb->get_results( $wpdb->prepare( "SELECT COUNT(*) AS liked FROM {$wpdb->prefix}wcpl_product_likes WHERE product_id = %d AND user_id = %s", array( $product_id, $user_id ) ) );
				$product_liked = (int) ( isset( $product_liked[0] ) ? $product_liked[0]->liked : '0' );

				echo '<div class="wcpl-product-likes-product" data-product-id="' . esc_html( $product_id ) . '">';

				if ( 1 == $product_liked ) {
					echo '<span class="wcpl-product-likes-button" data-type="unlike"><i class="fas fa-heart"></i></span>';
				} else {
					echo '<span class="wcpl-product-likes-button" data-type="like"><i class="far fa-heart"></i></span>';
				}

				if ( 'yes' == get_option( 'wcpl_product_likes_total' ) ) {
					echo '<span class="wcpl-product-likes-liked"' . ( 0 == $product_likes ? ' style="display: none;"' : '' ) . '>' . esc_html( apply_filters( 'wcpl_product_likes_total_before_text', __( 'Liked by', 'wcpl-product-likes' ) ) ) . ' <span class="wcpl-product-likes-liked-total">' . esc_html( $product_likes ) . '</span> ' . esc_html( apply_filters( 'wcpl_product_likes_total_after_text', __( 'users', 'wcpl-product-likes' ) ) ) . '</span>';
				}

				echo '</div>';

			}

		}

		public function update_likes() {

			$response = '';

			if ( isset( $_POST['nonce'] ) ) {

				if ( wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'wcpl_product_likes_update' ) ) {

					global $wpdb;
					$user_id = $this->user_id();
					$product_id = ( isset( $_POST['product_id'] ) ? sanitize_text_field( $_POST['product_id'] ) : '' );
					$type = ( isset( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : '' );

					if ( !empty( $user_id ) ) {

						$existing_like = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wcpl_product_likes WHERE product_id = %d AND user_id = %s", array( $product_id, $user_id ) ) );

						if ( 'like' == $type ) {

							// Check for existing like incase someone tries changing the type to like on the link to try to inflate likes

							if ( empty( $existing_like ) ) {

								$like = $wpdb->query(
									$wpdb->prepare(
										"INSERT INTO {$wpdb->prefix}wcpl_product_likes VALUES ('', %d, %s);",
										array(
											$product_id,
											$user_id
										)
									)
								);

								if ( $like > 0 ) {

									$this->update_likes_meta( $product_id, 'increase' );
									$response = 'liked_' . $product_id;

								}

							}

						} elseif ( 'unlike' == $type ) {

							// Check for existing like incase someone tries changing the type to unlike on the link to try to deflate likes

							if ( !empty( $existing_like ) ) {

								$unlike = $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}wcpl_product_likes WHERE product_id = %d AND user_id = %s;", array( $product_id, $user_id ) ) );

								if ( $unlike > 0 ) {

									$this->update_likes_meta( $product_id, 'decrease' );
									$response = 'unliked_' . $product_id;

								}

							}

						}

					}

				}

			}

			echo esc_html( $response );
			exit;

		}

		public function user_id() {

			$user_id = get_current_user_id();

			if ( 0 == $user_id ) { // Not logged in

				if ( isset( $_COOKIE['wcpl_product_likes'] ) ) {

					$user_id = sanitize_text_field( $_COOKIE['wcpl_product_likes'] );

				}

			}

			return $user_id;

		}

		public function user_logged_in() {

			$user_id = $this->user_id();
			$user_logged_in = true;

			if ( !empty( $user_id ) ) {

				if ( substr_count( $user_id, '-' ) > 0 ) {

					$user_logged_in = false;

				}

			}

			return $user_logged_in;

		}

		public function update_likes_meta( $product_id, $type ) {

			if ( !empty( $product_id ) ) {

				if ( 'increase' == $type || 'decrease' == $type ) {

					$likes = get_post_meta( $product_id, '_wcpl_product_likes_likes', true );
					$likes = (int) ( !empty( $likes ) ? $likes : '0' );

					if ( 'increase' == $type ) {

						$likes = ++$likes;

					} elseif ( 'decrease' == $type ) {

						$likes = --$likes;

					}

					update_post_meta( $product_id, '_wcpl_product_likes_likes', $likes );

				}				

			}

		}

	}

}
