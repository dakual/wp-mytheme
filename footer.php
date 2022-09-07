			</div><!--container-->
		</div><!--content-->
				
		<footer class="pt-3">
			<div class="container">
				<?php do_action( 'trendall_footer' ); ?>

				<div class="d-flex justify-content-between py-4">
							
					<?php if(is_active_sidebar('copyright-1')) : ?>
					<div class="copyright-widget">
						<?php dynamic_sidebar( 'copyright-1' ); ?>
					</div>
					<?php endif; ?>
					
					<?php if ( has_nav_menu( 'social' ) ) {	?>
						<nav class="social-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Social Navigation', 'storefront' ); ?>">
							<?php
								wp_nav_menu(
									array(
										'theme_location' => 'social',
										'fallback_cb'    => '',
									)
								);
							?>
						</nav>
					<?php } ?>
					
				</div>
			</div>
		</footer>
	
	</div><!--page-->

	<?php wp_footer(); ?>

</body>
</html>
