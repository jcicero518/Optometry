<?php

class SideMenus extends FooterMenus {

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
	public static $depth = 1;
	/**
	 * @var string
	 */
	public static $markup = '';
	/**
	 * @var array
	 */
	public static $menu_args = [];

	public static function start() {
		self::$instance = new SideMenus();

		self::$markup = '<div class="nav-menu widget_nav_menu"><ul class="menu">';

		return self::$instance;
	}

	/**
	 * @param $pageid
	 *
	 * @return null
	 * Returns singleton instance of class
	 */
	public static function parentPage($pageid) {
		$parent_page = get_post($pageid, OBJECT);
		if ($parent_page instanceof WP_Post) {
			$ancestors = function($parent_page) {
				$ancestors = $parent_page->ancestors;
				if (!is_array($ancestors)) return NULL;

				if (count($ancestors) > 1) {
					return array_reverse($ancestors)[0];
				}

				return $ancestors[0];
			};

			self::$parent_page_id = $ancestors($parent_page) ?? $pageid;

		} else {
			throw new Exception('Could not find a valid post with an ID of ' . $pageid . '!');
		}

		return self::$instance;
	}

	public static function generateMenu() {
		//self::$markup .= '<ul class="sub-menu">';

		self::$menu_args = [
			'echo' => 0,
			'depth' => self::$depth,
			'title_li' => self::$title_li,
			'sort_column' => self::$sort_column,
			'child_of' => self::$parent_page_id
		];

		self::$markup .= wp_list_pages(self::$menu_args);
		self::$markup .= '</ul></div>';

		return self::$markup;
	}
}