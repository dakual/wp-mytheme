<?php
/**
 * The template for displaying the homepage.
 *
 * Template name: Homepage
 *
 * @package trendall
 */

get_header(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">

		<?php do_action( 'trendall_homepage' ); ?>

	</main>
</div>
	
<?php get_footer(); ?>
