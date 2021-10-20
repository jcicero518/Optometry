<?php

/**
 * Class FooterMenus
 */
class FooterMenus {
	/**
	 * @var FooterMenus instance
	 */
	private static $instance = NULL;
	/**
	 * @var int
	 */
	public static $post_id = 0;
	/**
	 * @var int
	 */
	public static $parent_page_id = 0;
	/**
	 * @var string
	 */
	public static $menu_classes = 'menu-item menu-item-object-page';
	/**
	 * @var string
	 */
	public static $title_li = '';
	/**
	 * @var string
	 */
	public static $sort_column = 'menu_order, post_title';
	/**
	 * @var int
	 */
	public static $depth = 2;
	/**
	 * @var string
	 */
	public static $markup = '';
	/**
	 * @var array
	 */
	public static $menu_args = [];

	/**
	 * @return FooterMenus|null
	 * Returns singleton instance of class
	 */
	public static function start() {
		self::$instance = new FooterMenus();

		global $post;

		self::$post_id = $post->ID;

		self::$markup = '<div class="nav-menu">'
			. '<ul class="menu">'
			. '<li class="' . self::$menu_classes . '">';

		return self::$instance;
	}

	/**
	 * @param $pageid
	 *
	 * @return null
	 * Returns singleton instance of class
	 */
	public static function parentPage($pageid) {
		if ($pageid instanceof WP_Post) {
			self::$parent_page_id = get_post($pageid, ARRAY_A)['ID'];
		} else {
			self::$parent_page_id = $pageid ?? self::$parent_page_id;
		}

		self::$markup .= '<a href="' . get_permalink(self::$parent_page_id) . '" title="' . get_the_title(self::$parent_page_id) . '">' . get_the_title(self::$parent_page_id) . '</a>';

		return self::$instance;
	}

	/**
	 * @param $title
	 *
	 * @return null
	 * Returns singleton instance of class
	 */
	public static function titleLi($title) {
		self::$title_li = $title ?? self::$title_li;
		return self::$instance;
	}

	/**
	 * @param $column
	 *
	 * @return null
	 * Returns singleton instance of class
	 */
	public static function sortColumn($column) {
		self::$sort_column = $column ?? self::$sort_column;
		return self::$instance;
	}

	/**
	 * @param $depth
	 *
	 * @return null
	 * Returns singleton instance of class
	 */
	public static function menuDepth($depth) {
		self::$depth = $depth ?? self::$depth;
		return self::$instance;
	}

	/**
	 * @return string
	 */
	public static function generateMenu() {
		self::$markup .= '<ul class="sub-menu">';

		self::$menu_args = [
			'echo' => 0,
			'depth' => self::$depth,
			'title_li' => self::$title_li,
			'sort_column' => self::$sort_column,
			'child_of' => self::$parent_page_id
		];

		self::$markup .= wp_list_pages(self::$menu_args);
		self::$markup .= '</ul></li></ul></div>';

		return self::$markup;
	}
}