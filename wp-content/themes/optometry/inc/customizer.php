<?php
/**
 * SUNY Optometry Theme Customizer
 *
 * @package SUNY_Optometry
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function optometry_customize_register( $wp_customize ) {

	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

	/**
	 * Add sections
	 */
	$wp_customize->add_section( 'footer_info', [
		'title' => __( 'Footer Info', 'optometry' ),
		'description' => __( 'Copy for footer block', 'optometry' ),
		'priority' => 101
	]);

	$wp_customize->add_setting( 'footer_info_copy', [
		'default' => __( '', 'optometry' ),
		'transpost' => 'postMessage',
		'sanitize_callback' => function( $input ) {
			$filtered_output = strip_tags( $input, '<p><br><a><strong><em>');
			return wpautop($filtered_output);
		}
	]);

	$wp_customize->add_control(
		new WP_Customize_Control( $wp_customize, 'footer_info', [
			'label' => esc_html__( 'Add footer copy', 'optometry' ),
			'description' => esc_html__( 'Desc...', 'optometry' ),
			'section' => 'footer_info',
			'settings' => 'footer_info_copy',
			'type' => 'textarea',
			'priority' => 101
		])
	);
}
add_action( 'customize_register', 'optometry_customize_register' );
/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function optometry_customize_preview_js() {
	wp_enqueue_script( 'optometry_customizer', get_template_directory_uri() . '/js/contrib/customizer.js', array( 'customize-preview' ), '20151215', true );
}
add_action( 'customize_preview_init', 'optometry_customize_preview_js' );
