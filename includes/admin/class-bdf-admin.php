<?php
/**
 * Bishal Data Fetcher Plugin Admin Class.
 *
 * @package BishalDataFetcher
 */

namespace Bishal\DataFetcher\Admin;

/**
 * Admin class to manage backend functionalities.
 */
class Admin {
	/**
	 * The single instance of the class.
	 *
	 * @var Admin
	 */
	private static $instance = null;

	/**
	 * Get the plugin instance.
	 *
	 * @return Admin The single instance of the class.
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
		// Ensure WP_List_Table is available.
		if ( ! class_exists( 'WP_List_Table' ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
		}

		// Add admin menu.
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );

		// Register settings.
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Add admin menu for the plugin.
	 */
	public function add_admin_menu() {
		add_menu_page(
			__( 'Bishal Data Fetcher', 'bishal-data-fetcher' ),
			__( 'Data Fetcher', 'bishal-data-fetcher' ),
			'manage_options',
			'bdf_admin_page',
			array( $this, 'render_admin_page' ),
			'dashicons-download',
			20
		);
	}

	/**
	 * Render admin page.
	 */
	public function render_admin_page() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Bishal Data Fetcher Settings', 'bishal-data-fetcher' ); ?></h1>
			<form method="post">
				<?php
				settings_fields( 'bdf_settings_group' );
				do_settings_sections( 'bdf_admin_page' );
				$list_table = new ListTable();
				$list_table->prepare_items();
				$list_table->display();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Register plugin settings.
	 */
	public function register_settings() {
		register_setting( 'bdf_settings_group', 'bdf_api_endpoint' );
		add_settings_section(
			'bdf_settings_section',
			__( 'API Settings', 'bishal-data-fetcher' ),
			null,
			'bdf_admin_page'
		);
		// Add a button to fetch data.
		add_settings_field(
			'bdf_fetch_data',
			__( 'Fetch Data', 'bishal-data-fetcher' ),
			array( $this, 'render_fetch_button' ),
			'bdf_admin_page',
			'bdf_settings_section'
		);
	}

	/**
	 * Render fetch data button.
	 */
	public function render_fetch_button() {
		?>
		<button type="button" class="button button-primary" id="bdf-fetch-data-button">
			<?php esc_html_e( 'Fetch Data from API', 'bishal-data-fetcher' ); ?>
		</button>
		<span id="bdf-fetch-status"></span>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				$('#bdf-fetch-data-button').on('click', function() {
					$('#bdf-fetch-status').text('<?php echo esc_js( __( 'Fetching data...', 'bishal-data-fetcher' ) ); ?>');
					$.ajax({
						url: ajaxurl,
						method: 'POST',
						data: {
							action: 'bdf_fetch_data',
							security: '<?php echo esc_js( wp_create_nonce( 'bdf_nonce' ) ); ?>'
						},
						success: function(response) {
							if (response.success) {
								$('#bdf-fetch-status').text('<?php echo esc_js( __( 'Data fetched successfully!', 'bishal-data-fetcher' ) ); ?>');
								location.reload();
							} else {
								$('#bdf-fetch-status').text('<?php echo esc_js( __( 'Failed to fetch data.', 'bishal-data-fetcher' ) ); ?>');
							}
						},
						error: function() {
							$('#bdf-fetch-status').text('<?php echo esc_js( __( 'Error occurred.', 'bishal-data-fetcher' ) ); ?>');
						}
					});
				});
			});
		</script>
		<?php
	}
}
