<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * Template Name: Community Backup
 *
 * @package SUNY_Optometry
 */

get_header();
/**
 * Featured image for top hero?
 */
global $post;
$featured_image_src = null;
if (has_post_thumbnail($post->ID)):
	$featured_image_id = get_post_thumbnail_id($post->ID);
	$featured_image_src_attrs = wp_get_attachment_image_src($featured_image_id, 'full');
	$featured_image_w = $featured_image_src_attrs[1];
	$featured_image_h = $featured_image_src_attrs[2];
	$featured_image_src = $featured_image_src_attrs[0];

endif;
?>
	<?php if ($featured_image_src): ?>
		<section class="site-top-hero section-row" style="background-image:url(<?= $featured_image_src; ?>);"></section>
	<?php else: ?>
		<section class="site-top-hero section-row"></section>
	<?php endif; ?>
	<div id="primary" class="content-area">
		<div class="wrap">
			<main id="main" class="site-main" role="main">

			<?php
			while ( have_posts() ) : the_post();

				get_template_part( 'template-parts/content', 'page' );

				// If comments are open or we have at least one comment, load up the comment template.
				if ( comments_open() || get_comments_number() ) :
					comments_template();
				endif;

			endwhile; // End of the loop.
			?>

			</main><!-- #main -->
			<?php get_sidebar('community'); ?>

		</div>
	</div><!-- #primary -->

<?php
get_footer();
