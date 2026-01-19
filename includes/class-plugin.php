<?php
/**
 * Bishal Data Fetcher Plugin Main File.
 *
 * @package BishalDataFetcher
 */

namespace Bishal\DataFetcher;

/**
 * Main plugin class.
 */
class Plugin {
	/**
	 * The single instance of the class.
	 *
	 * @var Plugin
	 */
	private static $instance = null;

	/**
	 * Get the plugin instance.
	 *
	 * @return Plugin The single instance of the class.
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
		// Load plugin textdomain for translations.
		add_action( 'init', array( $this, 'load_textdomain' ) );

		// Load the admin and public classes.
		if ( is_admin() ) {
			Admin\Admin::get_instance();
		} else {
			Frontend\Frontend::get_instance();
		}

		// Handle AJAX requests.
		add_action( 'wp_ajax_bdf_fetch_data', array( $this, 'handle_ajax_request' ) );
		add_action( 'wp_ajax_nopriv_bdf_fetch_data', array( $this, 'handle_ajax_request' ) );

		// Load the WP_CLI class only when WP_CLI is available.
		if ( defined( 'WP_CLI' ) && \WP_CLI ) {
			CLI\CLI::register();
		}

		// Add block registration.
		Blocks\Blocks::get_instance();
	}

	/**
	 * Load the plugin textdomain for translations.
	 */
	public function load_textdomain() {
		// Load translations from the plugin root languages directory.
		load_plugin_textdomain( 'bishal-data-fetcher', false, dirname( plugin_basename( BDF_PATH . 'bishal-data-fetcher.php' ) ) . '/languages/' );
	}

	/**
	 * Handle AJAX request to fetch data from external API.
	 */
	public function handle_ajax_request() {
		// Check nonce for security.
		check_ajax_referer( 'bdf_nonce', 'security' );

		// Try to get cached data from transient.
		$transient_key = 'bdf_api_data';
		$cached_data   = get_transient( $transient_key );
		if ( false !== $cached_data ) {
			wp_send_json_success( $cached_data );
		}

		// Use GET HTTP method to fetch data from external API.
		$response = wp_remote_get( 'https://miusage.com/v1/challenge/1' );

		// Check for errors.
		if ( is_wp_error( $response ) ) {
			wp_send_json_error( array( 'message' => __( 'Failed to fetch data from API.', 'bishal-data-fetcher' ) ) );
		}

		// Retrieve and decode the response body.
		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		// Check for JSON decoding errors.
		if ( json_last_error() !== JSON_ERROR_NONE ) {
			wp_send_json_error( array( 'message' => __( 'Invalid JSON response from API.', 'bishal-data-fetcher' ) ) );
		}

		// Store the data in a transient for 1 hour.
		$transient_key = 'bdf_api_data';
		set_transient( $transient_key, $data, HOUR_IN_SECONDS );

		// Send the data back to the AJAX request.
		wp_send_json_success( $data );
	}
}
