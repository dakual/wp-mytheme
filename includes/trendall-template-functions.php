<?php
if ( ! function_exists( 'trendall_footer_widgets' ) ) {

	function trendall_footer_widgets() {
		$rows    = intval( apply_filters( 'trendall_footer_widget_rows', 1 ) );
		$regions = intval( apply_filters( 'trendall_footer_widget_columns', 4 ) );

		for ( $row = 1; $row <= $rows; $row++ ) :

			for ( $region = $regions; 0 < $region; $region-- ) {
				if ( is_active_sidebar( 'footer-' . esc_attr( $region + $regions * ( $row - 1 ) ) ) ) {
					$columns = $region;
					break;
				}
			}

			if ( isset( $columns ) ) :
				?>
				<div class=<?php echo '"footer-widgets row-' . esc_attr( $row ) . ' col-' . esc_attr( $columns ) . ' fix"'; ?>>
				<?php
				for ( $column = 1; $column <= $columns; $column++ ) :
					$footer_n = $column + $regions * ( $row - 1 );

					if ( is_active_sidebar( 'footer-' . esc_attr( $footer_n ) ) ) :
						?>
					<div class="block footer-widget-<?php echo esc_attr( $column ); ?>">
						<?php dynamic_sidebar( 'footer-' . esc_attr( $footer_n ) ); ?>
					</div>
						<?php
					endif;
				endfor;
				?>
				</div>
				<?php
				unset( $columns );
			endif;
		endfor;
	}
}

if ( ! function_exists( 'trendall_site_logo' ) ) {
	
	function trendall_site_logo( $args = array(), $echo = true ) {
		$logo       = get_custom_logo();
		$site_title = get_bloginfo( 'name' );
		$contents   = '';
		$classname  = '';

		$defaults = array(
			'logo'        => '%1$s',
			'logo_class'  => 'site-logo',
			'title'       => '<a href="%1$s">%2$s</a>',
			'title_class' => 'site-title',
			'single_wrap' => '<div class="%1$s">%2$s</div>',
		);

		$args = wp_parse_args( $args, $defaults );
		$args = apply_filters( 'trendall_site_logo_args', $args, $defaults );

		if ( has_custom_logo() ) {
			$contents  = sprintf( $args['logo'], $logo, esc_html( $site_title ) );
			$classname = $args['logo_class'];
		} else {
			$contents  = sprintf( $args['title'], esc_url( get_home_url( null, '/' ) ), esc_html( $site_title ) );
			$classname = $args['title_class'];
		}

		$wrap = $args['condition'] ? 'home_wrap' : 'single_wrap';

		$html = sprintf( $args[ $wrap ], $classname, $contents );
		$html = apply_filters( 'trendall_site_logo', $html, $args, $classname, $contents );

		if ( ! $echo ) {
			return $html;
		}

		echo $html; 
	}
}

if ( ! function_exists( 'trendall_homepage_content' ) ) {
	function trendall_homepage_content() {
		while ( have_posts() ) {
			the_post();

			the_content();

		} 
	}
}

if ( ! function_exists( 'trendall_is_woocommerce_activated' ) ) {
	function trendall_is_woocommerce_activated() {
		return class_exists( 'WooCommerce' ) ? true : false;
	}
}

if ( !class_exists( 'WCPL_Product_Likes' ) ) {

	class WCPL_Product_Likes {

		public function __construct() {

			require_once( __DIR__ . '/class-wcpl-product-likes-init.php' );
			
			new WCPL_Product_Likes_Init();

			include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); 
			if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
				require_once( __DIR__ . '/class-wcpl-product-likes-account.php' );
				require_once( __DIR__ . '/class-wcpl-product-likes-admin.php' );
				require_once( __DIR__ . '/class-wcpl-product-likes-button.php' );

				new WCPL_Product_Likes_Admin();

				if ( get_option('wcpl_product_likes_enable') == 'yes' ) {

					if ( get_option('wcpl_product_likes_account') == 'yes' ) {

						new WCPL_Product_Likes_Account();

					}

					new WCPL_Product_Likes_Button();
				}
			}
		}
	}

	new WCPL_Product_Likes();
}

if ( ! function_exists( 'trendall_mini_cart' ) ) {
	function trendall_mini_cart() {
		if ( trendall_is_woocommerce_activated() ) {
			$target = is_cart() || is_checkout() ? '' : 'data-bs-toggle="offcanvas" data-bs-target="#mini-cart-offcanvas" aria-controls="mini-cart-offcanvas"';
			?>
			<button class="btn btn-outline-secondary position-relative" type="button" <?php echo $target; ?>>
				<i class="fa-solid fa-cart-shopping"></i>
				<span class="cart-count position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
					<?php echo WC()->cart->get_cart_contents_count(); ?>
				</span>					
			</button>			
			<div class="offcanvas offcanvas-end" tabindex="-1" id="mini-cart-offcanvas">
			  <div class="offcanvas-header">
				<h5 class="offcanvas-title" id="offcanvasExampleLabel"><?php _e( 'Cart', 'woocommerce' ); ?></h5>
				<button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
			  </div>
			  <div class="offcanvas-body">
				<?php the_widget( 'WC_Widget_Cart', 'title=' ); ?>
			  </div>
			</div>
			<?php
		}
	}
}

if ( ! function_exists( 'trendall_user_nav' ) ) {

	function trendall_user_nav() {
		if ( trendall_is_woocommerce_activated() ) {
		?>
		<div class="dropdown d-inline">
			<?php if ( ! is_user_logged_in() ) { ?>
			<a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" title="<?php _e('Login / Register','woothemes'); ?>" class="btn btn-outline-secondary me-2"><i class="fa-solid fa-user"></i></a>
			<?php } else { ?>
			<button class="btn btn-outline-secondary me-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
				<i class="fa-solid fa-user"></i>
			</button>
			<ul class="dropdown-menu">
			<?php foreach(wc_get_account_menu_items() as $endpoint => $label ) : ?>
				<li class="<?php echo wc_get_account_menu_item_classes( $endpoint ); ?>">
					<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>" class="dropdown-item"><?php echo esc_html( $label ); ?></a>
				</li>
			<?php endforeach; ?>
			</ul>
			<?php } ?>
		</div>
		<?php
		}
	}
}

if ( ! function_exists( 'trendall_primary_navigation' ) ) {

	function trendall_primary_navigation() {
		?>
		<nav id="site-navigation" class="main-navigation" role="navigation">
			<button class="navbar-toggler" data-bs-toggle="offcanvas" data-bs-target="#primaryNav" aria-controls="primaryNav">
				<span class="navbar-toggler-icon"></span>
			</button>

			<div class="primary-navigation offcanvas offcanvas-start" tabindex="-1" id="primaryNav">
				<div class="offcanvas-header">
					<h5 class="offcanvas-title"><?php _e('Categories','woothemes'); ?></h5>
					<button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
				</div>
				<div class="offcanvas-body">
				<?php
				wp_nav_menu(
					array(
						//'container' => 'div',
						'menu_class' => 'navbar-nav me-auto mb-2 mb-lg-0',
						//'items_wrap' => '%3$s',
						'theme_location'  => 'primary',
						//'container_class' => 'primary-navigation',
						'walker'   => new WP_Bootstrap_Navwalker()
					)
				);
				?>
				</div>
			</div>
		</nav>
		<?php
	}
}
?>