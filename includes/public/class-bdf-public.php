<?php
/**
 * Bishal Data Fetcher Plugin Public Class.
 *
 * @package BishalDataFetcher
 */

/**
 * Admin class to manage frontend functionalities.
 */
class BDF_Public {
	/**
	 * The single instance of the class.
	 *
	 * @var BDF_Public
	 */
	private static $instance = null;

	/**
	 * Get the plugin instance.
	 *
	 * @return BDF_Public The single instance of the class.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}
