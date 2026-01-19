<?php
/**
 * Plugin Name: Bishal Shrestha
 * Description: A WordPress plugin to fetch and display data from an external API.
 * Version: 1.0.0
 * Author: Bishal Shrestha
 * Author URI: https://github.com/N00BFACE
 * License: GPL3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.en.html
 * Text Domain: bishal-data-fetcher
 * Domain Path: /languages
 *
 * @package BishalDataFetcher
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'BDF_VERSION', '1.0.0' );
define( 'BDF_PATH', plugin_dir_path( __FILE__ ) );
define( 'BDF_URL', plugin_dir_url( __FILE__ ) );

// Load Composer autoloader if available.
if ( file_exists( BDF_PATH . 'vendor/autoload.php' ) ) {
	require_once BDF_PATH . 'vendor/autoload.php';
} else {
	// Manual autoloader for when Composer is not available.
	spl_autoload_register(
		function ( $class_name ) {
			// Project-specific namespace prefix.
			$prefix = 'Bishal\\DataFetcher\\';

			// Base directory for the namespace prefix.
			$base_dir = BDF_PATH . 'includes/';

			// Does the class use the namespace prefix?
			$len = strlen( $prefix );
			if ( strncmp( $prefix, $class_name, $len ) !== 0 ) {
				// No, move to the next registered autoloader.
				return;
			}

			// Get the relative class name.
			$relative_class = substr( $class_name, $len );

			// Replace namespace separators with directory separators.
			// Convert CamelCase to lowercase with hyphens for WordPress convention.
			$path_parts = explode( '\\', $relative_class );
			$class_name = array_pop( $path_parts );

			// Convert class name to file name (e.g., Plugin -> class-plugin.php).
			$file_name = 'class-' . strtolower( preg_replace( '/([a-z])([A-Z])/', '$1-$2', $class_name ) ) . '.php';

			// Build the path.
			$path = '';
			if ( ! empty( $path_parts ) ) {
				$path = strtolower( implode( '/', $path_parts ) ) . '/';
			}

			$file = $base_dir . $path . $file_name;

			// If the file exists, require it.
			if ( file_exists( $file ) ) {
				require_once $file;
			}
		}
	);
}

/**
 * Initialize the plugin.
 *
 * @return void
 */
function bishal_data_fetcher_init() {
	\Bishal\DataFetcher\Plugin::get_instance();
}

add_action( 'plugins_loaded', 'bishal_data_fetcher_init' );
