<?php
add_action( 'after_setup_theme', 'mytheme_support' );
function mytheme_support() {
	add_theme_support(
		'custom-background',
		array(
			'default-color' => 'f5efe0',
		)
	);

	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 1200, 9999 );

	// Custom logo.
	$logo_width  = 167;
	$logo_height = 38;

	if ( get_theme_mod( 'retina_logo', false ) ) {
		$logo_width  = floor( $logo_width * 2 );
		$logo_height = floor( $logo_height * 2 );
	}

	add_theme_support(
		'custom-logo',
		array(
			'height'      => $logo_height,
			'width'       => $logo_width,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	add_theme_support( 'title-tag' );
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'script',
			'style',
			'navigation-widgets',
		)
	);
	
	add_theme_support(
		'woocommerce',
		apply_filters(
			'trendall_woocommerce_args',
			array(
				'single_image_width'    => 416,
				'thumbnail_image_width' => 324,
				'product_grid'          => array(
					'default_columns' => 3,
					'default_rows'    => 4,
					'min_columns'     => 1,
					'max_columns'     => 6,
					'min_rows'        => 1,
				),
			)
		)
	);

	add_theme_support( 'wc-product-gallery-zoom' );
	//add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
	
	load_theme_textdomain( 'trendall' );
}



add_action( 'init', 'trendall_menus' );
function trendall_menus() {

	$locations = array(
		'primary'  => __( 'Desktop Menu', 'rendall' ),
		'mobile'   => __( 'Mobile Menu', 'rendall' ),
		'header'   => __( 'Header Menu', 'rendall' ),
		'footer'   => __( 'Footer Menu', 'rendall' ),
		'social'   => __( 'Social Menu', 'rendall' ),
	);

	register_nav_menus( $locations );
	
	remove_action( 'wp_head', 'wc_gallery_noscript' );
}

add_action( 'widgets_init', 'widgets_init' );
function widgets_init() {
	$sidebar_args['sidebar'] = array(
		'name'        => __( 'Sidebar', 'trendall' ),
		'id'          => 'sidebar-1',
		'description' => '',
	);
	
	$sidebar_args['shop'] = array(
		'name'        => __( 'Shop Sidebar', 'trendall' ),
		'id'          => 'sidebar-shop',
		'description' => '',
	);
	
	$sidebar_args['header'] = array(
		'name'        => __( 'Below Header', 'trendall' ),
		'id'          => 'header-1',
		'description' => __( 'Widgets added to this region will appear beneath the header and above the main content.', 'trendall' ),
	);
	
	$sidebar_args['copyright'] = array(
		'name'        => __( 'Footer copyright', 'trendall' ),
		'id'          => 'copyright-1',
		'description' => '',
	);	

	$rows    = intval( apply_filters( 'trendall_footer_widget_rows', 1 ) );
	$regions = intval( apply_filters( 'trendall_footer_widget_columns', 4 ) );

	for ( $row = 1; $row <= $rows; $row++ ) {
		for ( $region = 1; $region <= $regions; $region++ ) {
			$footer_n = $region + $regions * ( $row - 1 ); // Defines footer sidebar ID.
			$footer   = sprintf( 'footer_%d', $footer_n );

			if ( 1 === $rows ) {
				$footer_region_name = sprintf( __( 'Footer Column %1$d', 'trendall' ), $region );
				$footer_region_description = sprintf( __( 'Widgets added here will appear in column %1$d of the footer.', 'trendall' ), $region );
			} else {
				$footer_region_name = sprintf( __( 'Footer Row %1$d - Column %2$d', 'trendall' ), $row, $region );
				$footer_region_description = sprintf( __( 'Widgets added here will appear in column %1$d of footer row %2$d.', 'trendall' ), $region, $row );
			}

			$sidebar_args[ $footer ] = array(
				'name'        => $footer_region_name,
				'id'          => sprintf( 'footer-%d', $footer_n ),
				'description' => $footer_region_description,
			);
		}
	}

	$sidebar_args = apply_filters( 'trendall_sidebar_args', $sidebar_args );

	foreach ( $sidebar_args as $sidebar => $args ) {
		$widget_tags = array(
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<span class="gamma widget-title">',
			'after_title'   => '</span>',
		);

		$filter_hook = sprintf( 'trendall_%s_widget_tags', $sidebar );
		$widget_tags = apply_filters( $filter_hook, $widget_tags );

		if ( is_array( $widget_tags ) ) {
			register_sidebar( $args + $widget_tags );
		}
	}
}


require get_template_directory() . '/classes/class-trendall-walker-menu.php';
require get_template_directory() . '/includes/trendall-template-functions.php';


add_action( 'trendall_menu_navigation', 'trendall_primary_navigation', 10 ); 
add_action( 'trendall_header_nav', 'trendall_user_nav', 10 ); 
add_action( 'trendall_header_nav', 'trendall_mini_cart', 20 );
add_action( 'trendall_footer', 'trendall_footer_widgets', 10 );
add_action( 'trendall_logo', 'trendall_site_logo', 10 );
add_action( 'trendall_homepage', 'trendall_homepage_content', 10 );
add_action( 'trendall_homepage', function() {
	echo '<p>denemeeeeee</p>';
}, 20 );






add_action( 'trendall_page_header', 'trendall_page_header_callback');
function trendall_page_header_callback() {
	if(trendall_is_woocommerce_activated()) {
		if(is_woocommerce() || is_cart() || is_checkout() || is_account_page()) {
		?>
		<div class="ta-page-header">
			<div class="container">
			  <div class="ta-breadcrumbs">
				<?php if(class_exists('WooCommerce')) { woocommerce_breadcrumb(); } ?>
			  </div>
			  <?php if(is_product_category()) { ?>
			  <div class="ta-page-header-title">
				<h1 class="ta-page-title"><?php echo single_tag_title("", false); ?></h1>
			  </div>
			  <?php } else if(is_search()) { ?>
			  <div class="ta-page-header-title">
				<h1 class="ta-page-title"><?php echo __("Search results", "woocommerce"); ?></h1>
			  </div>			  
			  <?php } else if(is_cart() || is_checkout()) { ?>
			  <div class="ta-page-header-title">
				<h1 class="ta-page-title"><?php echo the_title(); ?></h1>
			  </div>			  
			  <?php } else if(is_account_page()) { ?>
			  <div class="ta-page-header-title">
				<h1 class="ta-page-title">
					<?php 
						if ( ! is_user_logged_in() ) {
							echo __("Login/Register", "woocommerce"); 
						} else {
							echo the_title();
						}
					?>
				</h1>
			  </div>			  
			  <?php } ?>
			  <?php if(is_product_category() || is_shop()) { ?>
			  <?php do_action( 'trendall_page_title_after' ); ?>
			  <?php } ?>
			</div>
		</div>	
		<?php
		}
	}
}



add_action( 'woocommerce_archive_description', 'woocommerce_category_image', 2 );
function woocommerce_category_image() {
    if ( is_product_category() ){
	    global $wp_query;
	    $cat = $wp_query->get_queried_object();
	    $thumbnail_id = get_term_meta( $cat->term_id, 'thumbnail_id', true );
	    $image = wp_get_attachment_url( $thumbnail_id );
	    if ( $image ) {
		    echo '<img src="' . $image . '" alt="' . $cat->name . '" class="category-img" width="847" height="176"/>';
		}
	}
}


add_action( 'trendall_page_title_after', function() {
	echo '<button type="button" class="btn btn-sm btn-outline-secondary woocommerce-filter" data-bs-toggle="offcanvas" data-bs-target="#sidebar-area" aria-controls="sidebar-area"><i class="fas fa-sliders-h"></i> '. __( 'Filters', 'woocommerce' ) .'</button>';
}, 15 );



/*
remove_action( 'woocommerce_account_navigation', 'woocommerce_account_navigation', 10, 1 ); 
add_action( 'woocommerce_account_navigation', function() {
	echo "<div>";
	woocommerce_account_navigation();
	echo "</div>";
});


			
			
add_action( 'woocommerce_before_account_navigation', function() {
	echo '<div class="offcanvas offcanvas-end" tabindex="-1" id="mini-cart-offcanvas">';
	echo '<div class="offcanvas-header">';
	echo '<h5 class="offcanvas-title" id="offcanvasExampleLabel">Nav</h5>';
	echo '<button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>';
	echo '</div>';
	echo '<div class="offcanvas-body">';
});
add_action( 'woocommerce_after_account_navigation', function() {
	echo '</div></div>';
});
*/

add_action( 'woocommerce_before_customer_login_form', function() {
	echo '<nav class="myaccount-login-tab">';
	echo '  <button class="btn btn-outline-secondary active" data-tab="col-1">'.__( 'Login', 'woocommerce' ).'</button>';
	echo '  <button class="btn btn-outline-secondary" data-tab="col-2">'.__( 'Register', 'woocommerce' ).'</button>';
	echo '</nav>';
});



add_filter( 'woocommerce_product_additional_information_heading', '__return_null' );
add_filter( 'woocommerce_product_description_heading', '__return_null' );
add_filter( 'woocommerce_sale_flash', '__return_null' );
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

add_filter('woocommerce_show_page_title', '__return_false');

/*
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
add_action( 'woocommerce_sidebar', function() {
    woocommerce_get_template( 'sidebar-shop.php' );
}, 10 );
*/



add_filter( 'body_class', function( $classes ) {
	if ( is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'right-sidebar';
	}
	
    return $classes;
} );


add_action('woocommerce_before_main_content', 'remove_sidebar' );
function remove_sidebar() {
	// is_shop() || 
	if ( is_product()  ) { 
	 remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10);
   }
}

add_filter('add_to_cart_fragments', 'woocommerce_header_add_to_cart_fragment');
function woocommerce_header_add_to_cart_fragment( $fragments ) {
    global $woocommerce;

    ob_start();
    echo '<span class="cart-count position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">' . $woocommerce->cart->cart_contents_count . '</span>';
    $fragments['span.cart-count'] = ob_get_clean();

    return $fragments;
}

add_filter( 'woocommerce_get_breadcrumb', 'tm_child_remove_product_title', 10, 2 );
function tm_child_remove_product_title( $crumbs, $breadcrumb ) {
    if ( is_product() ) {
        array_pop( $crumbs );
    }
    return $crumbs;
}





remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
add_action( 'woocommerce_before_add_to_cart_form', function() {
    global $product;
    $product_id = $product->is_type('variation') ? $product->get_parent_id() : $product->get_id();
    $product_pa = wc_get_product( $product_id );
    $upsell_ids = $product_pa->get_upsell_ids();
	
	echo '<div class="upsell">';
	foreach ( $upsell_ids as $key => $val ) {
		$uproduct  = wc_get_product( $val );
		$name      = $uproduct->get_name();
		$permalink = $uproduct->get_permalink();
				
		echo '<a href="'.$permalink.'">';
		echo wp_get_attachment_image( $uproduct->get_image_id(), array('53', '83'), "", array( "class" => "img-responsive img-upsell", "alt" => $name ) );
		echo '</a>';
	}
	echo '</div>';
	
	
}, 39 );


/** Change number of related products output */ 
function woo_related_products_limit() {
  global $product;
	$args['posts_per_page'] = 4;
	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'jk_related_products_args', 20 );
  function jk_related_products_args( $args ) {
	$args['posts_per_page'] = 4;
	$args['columns'] = 4;
	return $args;
}




add_filter( 'woocommerce_get_price_html', 'modify_woocommerce_get_price_html', 10, 2 );
function modify_woocommerce_get_price_html( $price, $product ) {
	$html = '';
	if ( $product->price > 0 ) 	{
		if( $product->is_on_sale() ) {
			if( $product->is_type('variable')) {
			  $percentages = array();
			  $prices = $product->get_variation_prices();
			  foreach( $prices['price'] as $key => $val ){
				  if( $prices['regular_price'][$key] !== $val ){
					  $percentages[] = round( 100 - ( floatval($prices['sale_price'][$key]) / floatval($prices['regular_price'][$key]) * 100 ) );
				  }
			  }
			  $percentage = max($percentages) . '%';
			}
			elseif( $product->is_type('grouped') )
			{
			  $percentages = array();
			  $children_ids = $product->get_children();
			  foreach( $children_ids as $child_id ){
				  $child_product = wc_get_product($child_id);
				  $regular_price = (float) $child_product->get_regular_price();
				  $sale_price    = (float) $child_product->get_sale_price();
				  if ( $sale_price != 0 || ! empty($sale_price) ) {
					  $percentages[] = round(100 - ($sale_price / $regular_price * 100));
				  }
			  }
			  $percentage = max($percentages) . '%';
			} else {
			  $regular_price = (float) $product->get_regular_price();
			  $sale_price    = (float) $product->get_sale_price();
			  if ( $sale_price != 0 || ! empty($sale_price) ) {
				  $percentage = round(100 - ($sale_price / $regular_price * 100)) . '%';
			  }
			}
			
			$html .= '<span class="psale">'.$percentage.'<br>'. __( 'Sale', 'woocommerce' ) .'</span>';
			$html .= '<span class="price">'.$price.'</span>';
		} else {
			$html .= '<ins>'.$price.'</ins>';
		}
	}
	
	return $html;
}


//add_action( 'woocommerce_after_shop_loop_item', array( $this, 'archive_page_display' ), 20 );
//remove_action( 'woocommerce_after_shop_loop_item', 'archive_page_display', 20 );

//add_action('wp', function(){ echo '<pre>';print_r($GLOBALS['wp_filter']); echo '</pre>';exit; } );


remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
add_action( 'woocommerce_after_shop_loop_item_title', 'replace_product_rating', 5 );
function replace_product_rating() {
    global $product;

    $rating  = $product->get_rating_count();
    $review  = $product->get_review_count();
    $average = $product->get_average_rating();
	
    if ( 0 <= $rating ) {
		$count_html   = '';
		if($review > 0) {		
			$count_html = sprintf(_n( '%s review', '%s reviews', $review, 'woocommerce' ), esc_html($review));
			$count_html = '<span class="count-rating"> (' . $count_html .')</span>';
		}

        $html  = '<div class="container-rating">';
		$html .= '<div class="star-rating">';
        $html .= wc_get_star_rating_html( $average, $rating );
        $html .= '</div>' . $count_html . '</div>';
    }
    echo $html;	
}

/*
add_filter('woocommerce_get_star_rating_html', 'replace_star_ratings', 10, 2);
function replace_star_ratings($html, $rating) {
    $html = "";
    for($i = 0; $i < 5; $i++) {
        $html .= $i < $rating ? '<i class="fa fa-star"></i>' : '<i class="fa fa-star-o"></i>';
    }
    return $html;
}
*/



add_action( 'woocommerce_before_quantity_input_field', 'ts_quantity_plus_sign' );
function ts_quantity_plus_sign() {
	echo '<button type="button" class="minus alt" >-</button>';
}
 
add_action( 'woocommerce_after_quantity_input_field', 'ts_quantity_minus_sign' );
function ts_quantity_minus_sign() {
	echo '<button type="button" class="plus alt" >+</button>';
}



































add_filter('woocommerce_enqueue_styles', '__return_empty_array');
add_action( 'wp_print_styles', function(){
	wp_style_add_data( 'woocommerce-inline', 'after', '' );
});

add_action( 'wp_enqueue_scripts', 'mytheme_assets' );
function mytheme_assets() {
	$theme = wp_get_theme();
	
	wp_enqueue_style( 'fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css' );
	
	wp_enqueue_style( 'woocommerce-layout', get_template_directory_uri() . '/assets/css/woocommerce-layout.css' );
	wp_enqueue_style( 'woocommerce-smallscreen', get_template_directory_uri() . '/assets/css/woocommerce-smallscreen.css', [], true, 'only screen and (max-width: 768px)' );	
	wp_enqueue_style( 'woocommerce-general', get_template_directory_uri() . '/assets/css/woocommerce.css' );	
	
	wp_enqueue_script( 'jquery' );
	wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/assets/vendors/bootstrap/css/bootstrap.min.css' );	
	//wp_enqueue_style( 'stylesheet', get_stylesheet_uri() );
    wp_enqueue_style( 'stylesheet', get_stylesheet_uri(), array(), $theme->Version);	
	wp_enqueue_script( 'bootstrap', get_template_directory_uri() . '/assets/vendors/bootstrap/js/bootstrap.bundle.min.js' );
	//wp_enqueue_script( 'fontawesome', 'https://use.fontawesome.com/4b376f4c0c.js');
	wp_enqueue_script( 'scripts', get_template_directory_uri() . '/assets/js/scripts.js' );
	
}
 




// REMOVE WP EMOJI
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );

//Remove Gutenberg Block Library CSS from loading on the frontend
add_action( 'wp_enqueue_scripts', 'smartwp_remove_wp_block_library_css', 100 );
function smartwp_remove_wp_block_library_css(){
    wp_dequeue_style( 'wp-block-library' );
    wp_dequeue_style( 'wp-block-library-theme' );
    wp_dequeue_style( 'wc-blocks-style' ); // Remove WooCommerce block CSS
	wp_dequeue_style( 'global-styles' );
} 


add_action('init', 'remheadlink');
function remheadlink() {
	remove_action('wp_head', 'rsd_link');
	remove_action('wp_head', 'wlwmanifest_link');
}

// Hide WP REST API links in page headers
remove_action( 'wp_head', 'rest_output_link_wp_head', 10);
remove_action( 'template_redirect', 'rest_output_link_header', 11);
?>