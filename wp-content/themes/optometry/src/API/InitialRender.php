<?php

namespace Amorphous\Optometry\API;

class InitialRender {

	public function __construct() {

	}

	public function dump_query() {

	}

	public function get_post_data( $posts = NULL ) {
		if ( $posts === NULL && !is_404() ) {
			$posts = $GLOBALS['wp_query']->posts;
		}
		//print '<pre>';var_dump($posts);print '</pre>';
		global $wp_rest_server;

		if ( empty( $wp_rest_server ) ) {
			$wp_rest_server_class = apply_filters( 'wp_rest_server_class', 'WP_REST_Server' );
			$wp_rest_server = new $wp_rest_server_class;
			do_action( 'rest_api_init' );
		}

		$data = array();
		$request = new \WP_REST_Request();
		$request['context'] = 'view';

		foreach ( (array) $posts as $post ) {
			$controller = new \WP_REST_Posts_Controller( $post->post_type );
			$data[] = $wp_rest_server->response_to_data( $controller->prepare_item_for_response( $post, $request ), true );
		}

		return $data;
	}
}