=== Bishal Shrestha ===
Contributors: rainynewt
Tags: api, data fetcher, external api, gutenberg block
Requires at least: 6.0
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

A WordPress plugin to fetch and display data from an external API.

== Description ==

Bishal Shrestha is a WordPress plugin that fetches data from an external API and displays it in a customizable format. It includes:

* **Admin Dashboard** - View fetched data in a sortable table within the WordPress admin
* **Gutenberg Block** - Display API data on the frontend using a customizable block
* **WP-CLI Support** - Fetch and manage data via command line
* **Caching** - Data is cached for 1 hour to reduce API calls

= Features =

* Fetches data from external REST API
* Displays data in WordPress admin with WP_List_Table
* Custom Gutenberg block for frontend display
* WP-CLI commands for data management
* Transient-based caching (1 hour)
* Internationalization ready

== Installation ==

1. Upload the `bishal-shrestha` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to 'Bishal Shrestha' in the admin menu to view fetched data
4. Use the Gutenberg block to display data on the frontend

== Frequently Asked Questions ==

= How often is the data refreshed? =

Data is cached for 1 hour using WordPress transients. After the cache expires, fresh data is fetched from the API.

= Can I force refresh the data? =

Yes, you can use WP-CLI to force refresh:

`wp bdf fetch --force`

= What API does this plugin use? =

The plugin fetches data from `https://miusage.com/v1/challenge/1`.

== Screenshots ==

1. Admin dashboard showing fetched data
2. Gutenberg block in the editor
3. Frontend display of the data

== Changelog ==

= 1.0.0 =
* Initial release
* Admin dashboard with data table
* Gutenberg block for frontend display
* WP-CLI integration
* Transient caching

== Upgrade Notice ==

= 1.0.0 =
Initial release of the plugin.
