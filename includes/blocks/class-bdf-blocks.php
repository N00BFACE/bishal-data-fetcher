<?php
/**
 * Blocks class file.
 *
 * @package BishalDataFetcher
 */

/**
 * Class to handle block registration and rendering.
 */
class BDF_Blocks {
	/**
	 * The single instance of the class.
	 *
	 * @var BDF_Blocks
	 */
	private static $instance = null;

	/**
	 * Get the plugin instance.
	 *
	 * @return BDF_Blocks The single instance of the class.
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
		add_action( 'init', array( $this, 'register_blocks' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
	}

	/**
	 * Enqueue block editor assets.
	 */
	public function enqueue_block_editor_assets() {
		wp_enqueue_script( 'bdf-block-editor-script', BDF_URL . 'build/index.js', array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-api-fetch' ), BDF_VERSION, true );
		wp_localize_script(
			'bdf-block-editor-script',
			'bdfBlockData',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'bdf_nonce' ),
			)
		);

		// Load JavaScript translations.
		wp_set_script_translations( 'bdf-block-editor-script', 'bishal-data-fetcher', BDF_PATH . 'languages' );
	}

	/**
	 * Enqueue frontend assets.
	 */
	public function enqueue_frontend_assets() {
		if ( has_block( 'bdf/bdf-block' ) ) {
			wp_enqueue_style( 'bdf-block-style', BDF_URL . 'src/style.css', array(), BDF_VERSION );
		}
	}

	/**
	 * Register blocks.
	 */
	public function register_blocks() {
		register_block_type( BDF_PATH . '/build' );
	}
}
