<?php

namespace Amorphous\Optometry\API;

class API {

	/**
	 * @var API $instance
	 */
	private static $instance;
	/**
	 * @var RestController|static $restController
	 */
	protected $restController;
	/**
	 * @var RestCache|static $restCache
	 */
	protected $restCache;

	public function __construct() {
		$this->restController = new RestController();
		$this->restCache = new RestCache();
	}

	public static function getInstance() {

		if ( self::$instance = NULL ) {
			self::$instance = new static();
		}
		return self::$instance;
	}

	/**
	 * @return RestController|static
	 */
	public function getRestController() {
		return $this->restController;
	}
}