<?php

namespace Amorphous\Optometry\Hooks;

class PostFilters {

	/**
	 * @var PostFilters $instance
	 */
	protected static $instance;

	protected $filters;

	public function __construct() {
		$this->setFilters();
		
	}

	/**
	 * @return PostFilters|static
	 */
	public static function getInstance() {
		if ( NULL === self::$instance ) {
			self::$instance = new static();
		}
		return self::$instance;
	}

	/**
	 * @return $this
	 */
	public function setFilters() {
		$this->filters = add_filter( 'excerpt_more', [$this, 'filterExcerptMore'] );
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getFilters() {
		return $this->filters;
	}

	/**
	 * @return string
	 */
	public function filterExcerptMore() {
		global $post;
		$read_more_link = '';

		$post_title = $post->post_title ?? '';
		$permalink = get_permalink( $post->ID ) ?? NULL;
		if ( $permalink ) {
			$read_more_link .= '<a title="' . $post_title . '" href="' . $permalink . '">READ MORE</a>';
		}
		return ' .....' . $read_more_link;
	}
}