<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package SUNY_Optometry
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'optometry' ); ?></a>

	<header id="masthead" class="site-header" role="banner">

		<div class="wrap">
			<div class="site-branding">
				<h1 class="site-title site-logo"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><img alt="SUNY Optometry" src="<?= get_template_directory_uri() . '/assets/images/optologo.png' ?>" /></a></h1>
			</div><!-- .site-branding -->

			<div class="site-actions">
				<div class="site-search-form">
					<?= get_search_form(); ?>
				</div>
				<div class="mobile-button-container">
					<button id="button-trigger-menu" class="menu-toggle" type="button" aria-controls="primary-menu" aria-expanded="false">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
				</div>

			</div>
		</div>

	</header><!-- #masthead -->

	<nav id="site-navigation" class="main-navigation" role="navigation">
		<!--<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><?php esc_html_e( 'Primary Menu', 'optometry' ); ?></button>-->
		<?php wp_nav_menu(
			array(
				'theme_location' => 'menu-1',
				'menu_id' => 'primary-menu',
				'menu_class' => 'js-primary-menu nav-menu',
				'depth' => 2
			)
		); ?>
		<!--<div id="site-main-submenu" class="main-submenu" role="navigation">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'menu-1',
				'menu_id' => 'primary-submenu',
				'menu_class' => 'js-primary-submenu',
				'depth' => 2,
				'walker' => new Main_Nav_Walker()
			));
			?>
		</div>-->
	</nav><!-- #site-navigation -->

	<div id="content" class="site-content">
