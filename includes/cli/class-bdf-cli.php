<?php
/**
 * Bishal Data Fetcher Plugin CLI Class.
 *
 * @package BishalDataFetcher
 */

if ( defined( 'WP_CLI' ) && WP_CLI ) {

	/**
	 * BDF CLI Class.
	 */
	class BDF_CLI extends WP_CLI_Command {
		/**
		 * Fetch data from the API, overriding the 1 request per hour limit.
		 *
		 * ## OPTIONS
		 *
		 * [--force]
		 * : Force refresh the data, bypassing the transient cache.
		 *
		 * ## EXAMPLES
		 *
		 *     wp bdf fetch
		 *     wp bdf fetch --force
		 *
		 * @when after_wp_load
		 *
		 * @param array $args       Positional arguments.
		 * @param array $assoc_args Associative arguments.
		 */
		public function fetch( $args, $assoc_args ) {
			$force = isset( $assoc_args['force'] ) && $assoc_args['force'];

			$transient_key = 'bdf_api_data';

			// If not forcing, check for cached data first.
			if ( ! $force ) {
				$cached_data = get_transient( $transient_key );
				if ( false !== $cached_data ) {
					WP_CLI::success( __( 'Using cached data. Use --force to bypass the cache.', 'bishal-data-fetcher' ) );
					WP_CLI::line( __( 'Data: ' ) . wp_json_encode( $cached_data, JSON_PRETTY_PRINT ) );
					return;
				}
			} else {
				// Delete the transient to force a fresh fetch.
				delete_transient( $transient_key );
				WP_CLI::line( __( 'Transient deleted. Fetching fresh data...', 'bishal-data-fetcher' ) );
			}

			// Fetch data from external API.
			WP_CLI::line( __( 'Fetching data from API...', 'bishal-data-fetcher' ) );
			$response = wp_remote_get( 'https://miusage.com/v1/challenge/1' );

			// Check for errors.
			if ( is_wp_error( $response ) ) {
				WP_CLI::error(
					sprintf(
					/* translators: %s: WP_Error message */
						__( 'Failed to fetch data from API: %s', 'bishal-data-fetcher' ),
						$response->get_error_message()
					)
				);
				return;
			}

			// Check HTTP response code.
			$response_code = wp_remote_retrieve_response_code( $response );
			if ( 200 !== $response_code ) {
				WP_CLI::error(
					sprintf(
					/* translators: %d: HTTP status code */
						__( 'API returned error code: %d', 'bishal-data-fetcher' ),
						$response_code
					)
				);
				return;
			}

			// Retrieve and decode the response body.
			$body = wp_remote_retrieve_body( $response );
			$data = json_decode( $body, true );

			// Check for JSON decoding errors.
			if ( json_last_error() !== JSON_ERROR_NONE ) {
				WP_CLI::error(
					sprintf(
					/* translators: %s: JSON error message */
						__( 'Invalid JSON response from API: %s', 'bishal-data-fetcher' ),
						json_last_error_msg()
					)
				);
				return;
			}

			// Store the data in a transient for 1 hour.
			set_transient( $transient_key, $data, HOUR_IN_SECONDS );

			WP_CLI::success( __( 'Data fetched and cached successfully!', 'bishal-data-fetcher' ) );
			WP_CLI::line( __( 'Data: ', 'bishal-data-fetcher' ) . wp_json_encode( $data, JSON_PRETTY_PRINT ) );
		}
	}

	// Register the command only when WP-CLI is available.
	WP_CLI::add_command( 'bdf', 'BDF_CLI' );
}
