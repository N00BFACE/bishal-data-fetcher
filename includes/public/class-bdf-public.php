<?php
/**
 * Bishal Data Fetcher Plugin Frontend Class.
 *
 * @package BishalDataFetcher
 */

namespace Bishal\DataFetcher\Frontend;

/**
 * Frontend class to manage public-facing functionalities.
 */
class Frontend {
	/**
	 * The single instance of the class.
	 *
	 * @var Frontend
	 */
	private static $instance = null;

	/**
	 * Get the plugin instance.
	 *
	 * @return Frontend The single instance of the class.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		// Frontend initialization hooks can be added here.
	}
}
