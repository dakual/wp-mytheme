<?php

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'WCPL_Product_Likes_Init' ) ) {

	class WCPL_Product_Likes_Init {

		public function __construct() {

			global $wpdb;
			$table_name = $wpdb->base_prefix.'wcpl_product_likes';
			$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) );

			if ( ! $wpdb->get_var( $query ) == $table_name ) {

				$table_name = $wpdb->prefix . 'wcpl_product_likes';
				$charset_collate = $wpdb->get_charset_collate();

				$sql = "CREATE TABLE IF NOT EXISTS $table_name (
					like_id bigint(20) AUTO_INCREMENT,
					product_id bigint(20) NOT NULL,
					user_id text NOT NULL,
					PRIMARY KEY (like_id)
				) $charset_collate;";

				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				dbDelta( $sql );

				if ( get_option( 'wcpl_product_likes_flush_rewrites' ) === false ) {
					update_option( 'wcpl_product_likes_flush_rewrites', 'yes' );
				}

				if ( get_option( 'wcpl_product_likes_enable' ) === false ) {
					update_option( 'wcpl_product_likes_enable', 'yes' );
				}

				if ( get_option( 'wcpl_product_likes_not_logged_in' ) === false ) {
					update_option( 'wcpl_product_likes_not_logged_in', 'yes' );
				}

				if ( get_option( 'wcpl_product_likes_account' ) === false ) {
					update_option( 'wcpl_product_likes_account', 'yes' );
				}

				if ( get_option( 'wcpl_product_likes_products' ) === false ) {
					update_option( 'wcpl_product_likes_products', 'yes' );
				}

				if ( get_option( 'wcpl_product_likes_archives' ) === false ) {
					update_option( 'wcpl_product_likes_archives', 'yes' );
				}

				if ( get_option( 'wcpl_product_likes_total' ) === false ) {
					update_option( 'wcpl_product_likes_total', 'yes' );
				}

				if ( get_option( 'wcpl_product_likes_icon' ) === false ) {
					update_option( 'wcpl_product_likes_icon', 'heart' );
				}

				if ( get_option( 'wcpl_product_likes_styles' ) === false ) {
					update_option( 'wcpl_product_likes_styles', 'yes' );
				}
			}
		}
	}
}
