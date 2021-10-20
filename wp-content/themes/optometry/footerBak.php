<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package SUNY_Optometry
 */
$fm = new FooterMenus();
?>

	<footer id="colophon" class="site-footer" role="contentinfo">
		<div class="wrap">
			<div class="footer-cols">
				<div class="footer-item">
					<div class="footer-navigation">
						<?= $fm->start()->parentPage(9)->generateMenu(); ?>
						<?= $fm->start()->parentPage(11)->generateMenu(); ?>
					</div>
				</div>
				<div class="footer-item">
					<div class="footer-navigation">
						<?= $fm->start()->parentPage(13)->generateMenu(); ?>
						<?= $fm->start()->parentPage(15)->generateMenu(); ?>
						<?= $fm->start()->parentPage(17)->generateMenu(); ?>
					</div>
				</div>
				<div class="footer-item last">
					<div class="address-info">
						<?php if ( is_active_sidebar( 'footer-address') ): ?>
							<?php dynamic_sidebar( 'footer-address' ); ?>
						<?php endif; ?>
					</div>
					<div class="site-info">
						<?= get_theme_mod( 'footer_info_copy', '' ); ?>
					</div><!-- .site-info -->
				</div>
				<!--
				<a href="<?php echo esc_url( __( 'https://wordpress.org/', 'optometry' ) ); ?>"><?php printf( esc_html__( 'Proudly powered by %s', 'optometry' ), 'WordPress' ); ?></a>
				<span class="sep"> | </span>
				<?php printf( esc_html__( 'Theme: %1$s by %2$s.', 'optometry' ), 'optometry', '<a href="https://automattic.com/" rel="designer">Jeff Cicero</a>' ); ?>
				-->
			</div>
		</div>
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
