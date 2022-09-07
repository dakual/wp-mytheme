<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<div id="page" class="site">
		<header>
			<div class="container header-top">
				<?php do_action( 'trendall_logo' ); ?>
				<div class="order-md-last">
					<?php do_action( 'trendall_header_nav' ); ?>
				</div>	 		
				<div class="search-form col-12 col-lg-auto me-lg-3 mt-3 mt-lg-0 d-none d-lg-block">
					<?php the_widget( 'WC_Widget_Product_Search', 'title=' ); ?>
				</div> 
			</div>	
			<div class="box"></div>
			<nav class="navbar navbar-expand-lg navbar-light bg-light shadow">
				<div class="container d-flex ">
					<?php do_action( 'trendall_menu_navigation' ); ?>
					<div class="search-form flex-grow-1 ms-2 d-lg-none">
						<?php the_widget( 'WC_Widget_Product_Search', 'title=' ); ?>
					</div>	
				</div>
			</nav>
			<?php do_action( 'trendall_page_header' ); ?>
		</header>
		<div id="content" class="site-content">
			<div class="container">