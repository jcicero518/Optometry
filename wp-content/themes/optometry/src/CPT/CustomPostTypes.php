<?php

namespace Amorphous\Optometry\CPT;

class CustomPostTypes {

	protected static $instance;

	public function __construct() {
		
	}

	protected function register() {
		add_action( 'init', [ __CLASS__, 'custom_post_type_testimonials'] );
	}

	/**
	 * @return mixed
	 */
	public static function getInstance() {
		if ( NULL === self::$instance ) {
			self::$instance = new static();
		}
		return self::$instance;
	}

	public static function custom_post_type_testimonials() {

		$labels = array(
			'name'                  => _x( 'Testimonials', 'Post Type General Name', 'optometry' ),
			'singular_name'         => _x( 'Testimonial', 'Post Type Singular Name', 'optometry' ),
			'menu_name'             => __( 'Testimonials', 'optometry' ),
			'name_admin_bar'        => __( 'Testimonials', 'optometry' ),
			'archives'              => __( 'Testimonial Archives', 'optometry' ),
			'attributes'            => __( 'Testimonial Attributes', 'optometry' ),
			'parent_item_colon'     => __( 'Parent Item:', 'optometry' ),
			'all_items'             => __( 'All Testimonials', 'optometry' ),
			'add_new_item'          => __( 'Add New Testimonial', 'optometry' ),
			'add_new'               => __( 'Add New Testimonial', 'optometry' ),
			'new_item'              => __( 'New Testimonial', 'optometry' ),
			'edit_item'             => __( 'Edit Testimonial', 'optometry' ),
			'update_item'           => __( 'Update Testimonial', 'optometry' ),
			'view_item'             => __( 'View Testimonial', 'optometry' ),
			'view_items'            => __( 'View Testimonials', 'optometry' ),
			'search_items'          => __( 'Search Testimonials', 'optometry' ),
			'not_found'             => __( 'Not found', 'optometry' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'optometry' ),
			'featured_image'        => __( 'Featured Image', 'optometry' ),
			'set_featured_image'    => __( 'Set featured image', 'optometry' ),
			'remove_featured_image' => __( 'Remove featured image', 'optometry' ),
			'use_featured_image'    => __( 'Use as featured image', 'optometry' ),
			'insert_into_item'      => __( 'Insert into Testimonial', 'optometry' ),
			'uploaded_to_this_item' => __( 'Uploaded to this Testimonial', 'optometry' ),
			'items_list'            => __( 'Testimonials list', 'optometry' ),
			'items_list_navigation' => __( 'Testimonials list navigation', 'optometry' ),
			'filter_items_list'     => __( 'Filter Testimonials list', 'optometry' ),
		);
		$capabilities = array(
			'edit_post'             => 'edit_post',
			'read_post'             => 'read_post',
			'delete_post'           => 'delete_post',
			'edit_posts'            => 'edit_posts',
			'edit_others_posts'     => 'edit_others_posts',
			'publish_posts'         => 'publish_posts',
			'read_private_posts'    => 'read_private_posts',
		);
		$args = array(
			'label'                 => __( 'Testimonial', 'optometry' ),
			'description'           => __( 'Testimonials', 'optometry' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'editor', 'comments' ),
			'taxonomies'            => array( 'post_tag' ),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 5,
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => false,
			'can_export'            => true,
			'has_archive'           => true,
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'capabilities'          => $capabilities,
			'show_in_rest'          => true,
			//'rest_controller_class' => 'WP_REST_Testimonials_Controller',
		);

		register_post_type( 'testimonial', $args );
	}
}