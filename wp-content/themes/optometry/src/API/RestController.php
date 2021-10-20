<?php

namespace Amorphous\Optometry\API;

use WP_REST_Controller;
use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

class RestController extends WP_REST_Controller {

	/**
	 * @var string $namespace
	 */
	protected $namespace = 'optometry/v1';
	/**
	 * @var RestController $instance
	 */
	private static $instance;

	public function __construct() {
		add_action( 'rest_api_init', function() {
			$this->register_routes();
			$this->register_fields();
		});
	}

	/**
	 * Get instance of RestController
	 *
	 * @return RestController|static
	 */
	public static function getInstance() {
		if ( self::$instance === NULL ) {
			self::$instance = new static();
		}
		return self::$instance;
	}

	/**
	 * Registers the routes for the objects of the controller.
	 *
	 * @since 4.7.0
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, 'search/(?P<term>[a-zA-Z0-9-]+)', [
			'methods' => WP_REST_Server::READABLE,
			'callback' => [$this, 'theme_rest_pagination'],
			'args' => []
		]);
		register_rest_route( $this->namespace, 'ancestors', [
			'methods' => WP_REST_Server::READABLE,
			'callback' => [$this, 'get_items'],
			'args' => [],
			'permission_callback' => [$this, 'get_items_permissions_check']
		]);

		register_rest_route( $this->namespace, 'children', [
			'methods' => WP_REST_Server::READABLE,
			'callback' => [$this, 'get_children'],
			'args' => [],
			'permission_callback' => [$this, 'get_items_permissions_check']
		]);

		register_rest_route( $this->namespace, 'byPath/(?P<url>[a-zA-Z0-9-]+)', [
			'methods' => WP_REST_Server::READABLE,
			'callback' => [$this, 'get_item'],
			'args' => [],
			'permission_callback' => [$this, 'get_items_permissions_check']
		]);
	}

	public function register_fields() {
		global $post;
		$postObjects = [ 'page', 'nav-menu-item' ];

		register_rest_field( $postObjects, 'plaintitle', [
			'get_callback' => [$this, 'rest_field_plaintitle']
		]);

	}

	function rest_field_plaintitle( $object, $fieldname, $request ) {
		return strip_tags( html_entity_decode( $object['title']['rendered'] ) );
	}
	/**
	 * Checks if a given request has access to get items.
	 *
	 * @since 4.7.0
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|bool True if the request has read access, WP_Error object otherwise.
	 */
	public function get_items_permissions_check( $request ) {
		return TRUE;
	}

	public function theme_rest_pagination( WP_REST_Request $request ) {
		$params = $request->get_params();
		$queryParams = $request->get_query_params();
		$pageNum = absint( $params['pageId'] );
		$sourcePageId = absint( $params['sourcePageId'] );
		$term = $params['term'];
		$perPage = 5;
		$bignum = 999999999;

		$sourcePage = get_post( $sourcePageId );
		$sourcePageType = get_post_type( $sourcePage );
		$sourcePageBase = str_replace( $bignum, '%#%', esc_url( trailingslashit( get_permalink( $sourcePage->ID ) . $sourcePageType . '/' . $bignum ) ) );

		$args = [
			'post_type' => 'page',
			'posts_per_page' => $perPage,
			's' => $term
			//'paged' => $pageNum
		];

		$query = new \WP_Query( $args );

		$data = paginate_links( array(
			'base'         => '',
			'format'       => '',
			'current'      => max( 1, $pageNum ),
			'total'        => $query->max_num_pages,
			'prev_text'    => '&larr;',
			'next_text'    => '&rarr;',
			'type'         => 'list',
			'end_size'     => 3,
			'mid_size'     => 3
		) );

		wp_reset_query();

		return rest_ensure_response( $data );
	}

	/**
	 * Retrieves a collection of items.
	 *
	 * @since 4.7.0
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|WP_REST_Response Response object on success, or WP_Error object on failure.
	 */
	public function get_items( $request ) {
		$params = $request->get_query_params();
		$pId = (int)$params['pageId'];

		$ancestors = get_ancestors( $pId, 'page' );
		//$sm = new \SideMenus();
		//$sm::start()::parentPage( $pId );
		//$items = $sm::generateMenu();
		//$sideMenu = $sm->start()->parentPage($post->ID)->generateMenu();
		return rest_ensure_response( $ancestors );
	}

	public function get_children( WP_REST_Request $request ) {
		$params = $request->get_query_params();
		$pId = (int)$params['pageId'];

		$data = [];

		$args = [
			'post_type'      => 'page',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'post_parent'    => $pId
		];

		$children = get_posts( $args );
		if ( \count( $children ) > 1 ) {
			foreach( $children as $child ) {
				$child->permalink = get_permalink( $child->ID );
				$data[] = $child;
			}
		} else if ( \count( $children ) === 1 ) {
			$child = current( $children );
			$child->permalink = get_permalink( $child->ID );
			$data[] = $child;
		}
		return rest_ensure_response( $data );
	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_item( $request ) {
		$params = $request->get_params();
		$url = $params['url'];

		$data = get_page_by_path( $url );

		return rest_ensure_response( $data );
	}

	/**
	 * Prepares the item for the REST response.
	 *
	 * @since 4.7.0
	 *
	 * @param mixed $item              WordPress representation of the item.
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_Error|WP_REST_Response Response object on success, or WP_Error object on failure.
	 */
	public function prepare_item_for_response( $item, $request ) {

	}


}