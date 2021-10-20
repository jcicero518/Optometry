<?php

namespace Amorphous\Optometry\API;

class RestCache {

	/**
	 * @var RestCache $instance
	 */
	private static $instance;

	/**
	 * @return RestCache|static
	 */
	public static function getInstance() {
		if ( self::$instance === NULL ) {
			self::$instance = new static();
		}
		return self::$instance;
	}

	public static function setHeaders( $headers ) {
		//$headers['Pragma'] = 'cache';
		//$headers['Expires'] = 3 * DAY_IN_SECONDS;
		//$headers['Cache-Control'] = 'public, max-age=3600';

		return $headers;
	}
}