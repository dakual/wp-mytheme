<?php

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'WCPL_Product_Likes_Admin' ) ) {

	class WCPL_Product_Likes_Admin {

		public function __construct() {

			add_filter( 'woocommerce_get_settings_products', array( $this, 'settings' ), 10, 2 );
			add_action( 'manage_edit-product_columns', array( $this, 'product_columns' ) );
			add_action( 'manage_product_posts_custom_column', array( $this, 'product_columns_values' ) );
			add_action( 'manage_edit-product_sortable_columns', array( $this, 'product_columns_sortable' ) );
			add_action( 'pre_get_posts', array( $this, 'product_columns_orderby' ) );
			add_action( 'admin_head', array( $this, 'meta_debug' ) );

		}

		public function settings( $settings, $current_section ) {

			// Products > General

			if ( '' == $current_section ) {

				// Section Start

				$product_likes_settings[] = array(
					'name' => esc_html__( 'Likes', 'wcpl-product-likes' ),
					'type' => 'title',
					'id' => 'wcpl-product-likes'
				);

				// Updates to any of the ids (option names) below should be accounted for in activation class default settings

				// General

				$product_likes_settings[] = array(
					'name'     => esc_html__( 'General', 'wcpl-product-likes' ),
					'id'       => 'wcpl_product_likes_enable',
					'type'     => 'checkbox',
					'css'      => 'min-width:300px;',
					'desc'     => esc_html__( 'Enable', 'wcpl-product-likes' ),
					'desc_tip' => esc_html__( 'Enables product likes across your store, ensure you also enable at least one of the display options below to see the buttons.', 'wcpl-product-likes' ),
					'checkboxgroup' => 'start',
				);

				$product_likes_settings[] = array(
					'id'       => 'wcpl_product_likes_not_logged_in',
					'type'     => 'checkbox',
					'css'      => 'min-width:300px;',
					'desc'     => esc_html__( 'Enable If Not Logged In', 'wcpl-product-likes' ),
					'desc_tip' => esc_html__( 'Allows users without an account to like products, user will see their likes on products for 30 days or until cookies cleared. After this period likes by a not logged in user will still count towards the total number of product likes.', 'wcpl-product-likes' ),
					'checkboxgroup' => '',
				);

				$product_likes_settings[] = array(
					'id'       => 'wcpl_product_likes_account',
					'type'     => 'checkbox',
					'css'      => 'min-width:300px;',
					'desc'     => esc_html__( 'Enable In Account', 'wcpl-product-likes' ),
					'desc_tip' => esc_html__( 'Adds a section within the user\'s account listing the products the customer has previously liked.', 'wcpl-product-likes' ),
					'checkboxgroup' => '',
				);

				// Display

				$product_likes_settings[] = array(
					'title'		=> esc_html__( 'Display', 'wcpl-product-likes' ),
					'id'       => 'wcpl_product_likes_products',
					'type'     => 'checkbox',
					'css'      => 'min-width:300px;',
					'desc'     => esc_html__( 'Show On Product Pages', 'wcpl-product-likes' ),
					'desc_tip' => esc_html__( 'Shows a product like button on individual product pages.', 'wcpl-product-likes' ),
					'checkboxgroup' => 'start',
				);

				$product_likes_settings[] = array(
					'id'       => 'wcpl_product_likes_archives',
					'type'     => 'checkbox',
					'css'      => 'min-width:300px;',
					'desc'     => esc_html__( 'Show On Product Archive Pages', 'wcpl-product-likes' ),
					'desc_tip' => esc_html__( 'Shows a product like button on each product in an archive page e.g. shop page, product categories, etc.', 'wcpl-product-likes' ),
					'checkboxgroup' => '',
				);

				$product_likes_settings[] = array(
					'id'       => 'wcpl_product_likes_total',
					'type'     => 'checkbox',
					'css'      => 'min-width:300px;',
					'desc'     => esc_html__( 'Show Total Likes', 'wcpl-product-likes' ),
					'desc_tip' => esc_html__( 'Shows the total number of likes if a product has been liked.', 'wcpl-product-likes' ),
					'checkboxgroup' => 'end',
				);

				// Section End
				
				$product_likes_settings[] = array(
					'type'	=> 'sectionend',
					'id'	=> 'wcpl-product-likes'
				);

				return array_merge( $settings, $product_likes_settings );

			} else {

				return $settings;

			}

		}

		public function product_columns( $columns ) {

			$date_label = $columns['date'];
			unset( $columns['date'] );

			$featured_label = $columns['featured'];
			unset( $columns['featured'] );

			$columns['wcpl_product_likes_likes'] = __( 'Likes', 'wcpl-product-likes' );
			$columns['featured'] = $featured_label;
			$columns['date'] = $date_label;

			return $columns;

		}

		public function product_columns_values( $name ) {

			global $post;

			switch ( $name ) {

				case 'wcpl_product_likes_likes':
					$likes = get_post_meta( $post->ID, '_wcpl_product_likes_likes', true );

					if ( !empty( $likes ) ) {

						echo esc_html( $likes );

					} else {

						echo esc_html( '0' );

					}

					break;

				default:
					break;
			}

		}

		public function product_columns_sortable( $columns ) {

			$columns['wcpl_product_likes_likes'] = 'wcpl_product_likes_likes';
			return $columns;

		}

		public function product_columns_orderby( $query ) {

			if ( is_admin() && 'product' == $query->get( 'post_type' ) ) {

				if ( 'wcpl_product_likes_likes' == $query->get( 'orderby' ) ) {

					$query->set( 'meta_query',
						array(
							'relation' => 'OR',
							array(
								'key' => '_wcpl_product_likes_likes', 
								'compare' => 'NOT EXISTS' // This must happen before the EXISTS array or it won't sort correctly https://wordpress.stackexchange.com/questions/102447/sort-on-meta-value-but-include-posts-that-dont-have-one
							),
							array(
								'key' => '_wcpl_product_likes_likes', 
								'compare' => 'EXISTS'
							),
						)
					);

					$query->set( 'orderby', 'meta_value_num name' );

				}

			}

		}

		public function meta_debug() {

			if ( current_user_can( 'administrator' ) ) {

				if ( isset( $_GET['wcpl_product_likes_meta_debug'] ) ) {

					if ( '1' == sanitize_text_field( $_GET['wcpl_product_likes_meta_debug'] ) ) {

						global $wpdb;

						$wpdb->get_results(
							$wpdb->prepare(
								"DELETE FROM `{$wpdb->prefix}postmeta` WHERE `meta_key` = '_wcpl_product_likes_likes';"
							)
						);

						$products_with_likes = $wpdb->get_results(
							$wpdb->prepare(
								"SELECT product_id, count( product_id ) AS likes FROM `{$wpdb->prefix}wcpl_product_likes` GROUP BY product_id;"
							)
						);

						if ( !empty( $products_with_likes ) ) {

							foreach ( $products_with_likes as $product_with_likes ) {

								update_post_meta( $product_with_likes->product_id, '_wcpl_product_likes_likes', $product_with_likes->likes );

							}

						}

					}

				}

			}

		}

	}

}
