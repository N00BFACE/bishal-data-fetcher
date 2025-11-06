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

// Include the main plugin class.
require_once BDF_PATH . 'includes/class-bishal-data-fetcher.php';

/**
 * Initialize the plugin.
 */
function bdf_init() {
	Bishal_Data_Fetcher::get_instance();
}

add_action( 'plugins_loaded', 'bdf_init' );
