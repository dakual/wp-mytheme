<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package storefront
 */

if ( ! is_active_sidebar( 'sidebar-shop' ) ) {
	return;
}
?>

<div id="secondary" class="widget-area" role="complementary">
	<div class="offcanvas offcanvas-start" tabindex="-1" id="sidebar-area">
		<div class="offcanvas-header">
			<h5 class="offcanvas-title"><?php _e('Filters','woothemes'); ?></h5>
			<button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
		</div>
		<div class="offcanvas-body">
			<?php dynamic_sidebar( 'sidebar-shop' ); ?>
		</div>
	</div>
</div>	