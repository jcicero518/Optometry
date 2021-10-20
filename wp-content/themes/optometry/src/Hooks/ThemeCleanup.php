<?php

namespace Amorphous\Optometry\Hooks;

class ThemeCleanup {

	/**
	 * @var ThemeCleanup $instance
	 */
	protected static $instance;

	protected $hooks = [];

	public function __construct() {
		$this->collectHooks();
		$this->applyHooks();
		$this->hookFilters();
	}

	public static function getInstance() {
		if ( NULL === self::$instance ) {
			self::$instance = new static();
		}
		return self::$instance;
	}

	protected function collectHooks() {
		$this->hooks = [
			'wp_head' => [
				'feed_links' => 2,
				'feed_links_extra' => 3,
				'print_emoji_detection_script' => 7,
				'rsd_link' => 10,
				'wlwmanifest_link' => 10,
				'adjacent_posts_rel_link_wp_head' => 10,
				'parent_post_rel_link' => 10,
				'start_post_rel_link' => 10,
				'wp_generator' => 10,
				'wp_shortlink_wp_head' => 10
			]
		];
	}

	protected function applyHooks() {
		$tag = 'wp_head';
		foreach( $this->hooks[$tag] as $hook => $priority ) {
			if ( has_action( $tag, $hook ) ) {
				remove_action( $tag, $hook, $priority );
			}
		}
	}

	public function hookFilters() {
		add_filter( 'style_loader_src', [$this, 'cleanAssetVersions'] );
		add_filter( 'script_loader_src', [$this, 'cleanAssetVersions'] );
		add_filter( 'script_loader_tag', [$this, 'modifySomeTags'], 10, 3 );
	}

	public function cleanAssetVersions( $src ) {
		if ( strpos( $src, 'ver=' ) )
			$src = remove_query_arg( 'ver', $src );
		return $src;
	}

	/**
	 * Filter output of script tags before they are "printed" to
	 * the screen.
	 *
	 * @param $tag
	 * @param $handle
	 * @param $src
	 *
	 * @return mixed
	 */
	public function modifySomeTags( $tag, $handle, $src ) {
		if ( $handle === 'wp-embed' || ( strpos( $handle, 'optometry-' ) !== FALSE ) ) {
			$tag = str_replace( "<script type='text/javascript'", "<script type='text/javascript' defer ", $tag );
		}
		return $tag;
	}

	/**
	 * @return mixed
	 */
	public function getHooks() {
		return $this->hooks;
	}

}