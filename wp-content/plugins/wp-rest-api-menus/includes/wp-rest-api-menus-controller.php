<?php

namespace Amorphous;

use WP_REST_Controller;

/**
 * Class WP_REST_API_Menus_Controller
 *
 * Extends abstract WP_REST_Controller class as a guide. We're actually only
 * overriding the register_routes method.
 *
 * @package Amorphous
 */
class WP_REST_API_Menus_Controller extends WP_REST_Controller {

	/**
	 * @var string $namespace
	 */
	protected $namespace = 'wp-menus/v1';
	/**
	 * @var array $menus
	 */
	protected $menus = [];
	/**
	 * @var WP_REST_API_Menus_Controller
	 */
	protected static $instance;

	/**
	 * Get instance of class in the singleton pattern
	 *
	 * @return WP_REST_API_Menus_Controller
	 */
	public static function getInstance() {
		if ( self::$instance === NULL ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Register the routes. Leaving permissions callback and args parameters to their
	 * defaults for now.
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/registered_menus', array(
			'methods' => \WP_REST_Server::READABLE,
			'callback' => [$this, 'get_all_registered_menus'],
			'args' => [
				'context' => [
					'default' => 'view'
				]
			]
		));
		register_rest_route( $this->namespace, '/menus', array(
			'methods' => \WP_REST_Server::READABLE,
			'callback' => [$this, 'get_all_menus']
		));
		register_rest_route( $this->namespace, '/menus/(?P<id>[a-zA-Z(-]+)', array(
			'methods' => \WP_REST_Server::READABLE,
			'callback' => [$this, 'get_menu_data'],
		) );
	}

	function rest_field_plaintitle( $object ) {
		return strip_tags( html_entity_decode( $object->title ) );
	}

	/**
	 * Get a flat map of all available menus. Includes menus
	 * not actually attached to a menu location
	 *
	 * @return mixed|\WP_REST_Response
	 */
	public function get_all_menus() {
		$menus = wp_get_nav_menus();
		return rest_ensure_response( $menus );
	}

	/**
	 * Get all registered menus
	 *
	 * @return array List of menus with slug and description
	 */
	public function get_all_registered_menus() {
		$menus = [];
		foreach ( get_registered_nav_menus() as $slug => $description ) {
			$obj = new \stdClass;
			$obj->slug = $slug;
			$obj->description = $description;
			$menus[] = $obj;
		}

		return $menus;
	}

	/**
	 * @param \WP_REST_Request $request
	 * - $request has <id> parameter as found in $wpdb_prefix_terms table as term_id column
	 * - slug column is queried
	 *
	 * @return mixed|\WP_REST_Response
	 */
	public function get_menu_data( \WP_REST_Request $request ) {
		$menu_data = [];
		$menuId = $request->get_param( 'id' );

		/**
		 * returns WP_Term object of matching menu slug
		 */
		$menu_term = get_term_by( 'slug', $menuId, 'nav_menu' );

		if ( NULL === $menu_term->term_id ) {
			return new \WP_Error( 404, 'No menus found for ' . $menuId );
		}

		$menu_data = wp_get_nav_menu_items( $menu_term->term_id );

		foreach ( $menu_data as $item ) {
			$this->prepare_item_for_response( $item, $request );
		}

		if ( function_exists( '_wp_menu_item_classes_by_context' ) ) {
			_wp_menu_item_classes_by_context( $menu_data );
		}

		return rest_ensure_response( $menu_data );
	}

	/**
	 * Prepares the item for the REST response.
	 *
	 * @since 4.7.0
	 *
	 * @param mixed           $item    WordPress representation of the item.
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_Error|\WP_REST_Response Response object on success, or WP_Error object on failure.
	 */
	public function prepare_item_for_response( $item, $request ) {
		$data = [];

		// Add classes usually added by wp_nav_menu
		$classes[] = 'menu-item';
		$classes[] = 'menu-item-type-' . $item->type;
		$classes[] = 'menu-item-object-' . $item->object;

		$item->classes = $classes;

		$item->title = $this->rest_field_plaintitle( $item );
		$data[] = $item;

		return new \WP_REST_Response( $data, 200 );
	}
}