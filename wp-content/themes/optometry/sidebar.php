<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package SUNY_Optometry
 */

/*if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}*/
?>

<nav id="secondary" class="widget-area" role="navigation" aria-expanded="false">
	<?php dynamic_sidebar( 'sidebar-1' ); ?>
	<?php
	global $post;
	$sm = new SideMenus();
	echo $sm->start()->parentPage($post->ID)->generateMenu();
	?>
</nav><!-- #secondary -->
