<?php
/**
 * Render.php - Frontend display for BDF Block
 *
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 *
 * @package BishalDataFetcher
 */

// Get block attributes with defaults and explicit boolean sanitization.
$show_id         = isset( $attributes['showId'] ) ? rest_sanitize_boolean( $attributes['showId'] ) : true;
$show_first_name = isset( $attributes['showFirstName'] ) ? rest_sanitize_boolean( $attributes['showFirstName'] ) : true;
$show_last_name  = isset( $attributes['showLastName'] ) ? rest_sanitize_boolean( $attributes['showLastName'] ) : true;
$show_email      = isset( $attributes['showEmail'] ) ? rest_sanitize_boolean( $attributes['showEmail'] ) : true;
$show_date       = isset( $attributes['showDate'] ) ? rest_sanitize_boolean( $attributes['showDate'] ) : true;

// Try to get cached data from transient.
$transient_key = 'bdf_api_data';
$api_data      = get_transient( $transient_key );

// If no cached data, fetch from API.
if ( false === $api_data ) {
	$response = wp_remote_get( 'https://miusage.com/v1/challenge/1' );

	if ( ! is_wp_error( $response ) ) {
		$body     = wp_remote_retrieve_body( $response );
		$api_data = json_decode( $body, true );

		// Cache for 1 hour.
		if ( json_last_error() === JSON_ERROR_NONE && is_array( $api_data ) ) {
			set_transient( $transient_key, $api_data, HOUR_IN_SECONDS );
		}
	}
}

// Validate api_data is an array.
if ( ! is_array( $api_data ) ) {
	$api_data = array();
}

// Check if we have data to display with proper array validation.
$has_rows = isset( $api_data['data'] )
	&& is_array( $api_data['data'] )
	&& isset( $api_data['data']['rows'] )
	&& is_array( $api_data['data']['rows'] )
	&& ! empty( $api_data['data']['rows'] );

if ( ! $has_rows ) {
	?>
	<div <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?>>
		<p><?php esc_html_e( 'No data available.', 'bishal-data-fetcher' ); ?></p>
	</div>
	<?php
	return;
}

$table_title = isset( $api_data['data']['title'] ) ? sanitize_text_field( $api_data['data']['title'] ) : '';
$rows        = $api_data['data']['rows'];
?>

<div <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?>>
	<?php if ( ! empty( $table_title ) ) : ?>
		<h3><?php echo esc_html( $table_title ); ?></h3>
	<?php endif; ?>

	<table class="wp-block-bdf-table">
		<thead>
			<tr>
				<?php if ( $show_id ) : ?>
					<th><?php esc_html_e( 'ID', 'bishal-data-fetcher' ); ?></th>
				<?php endif; ?>
				<?php if ( $show_first_name ) : ?>
					<th><?php esc_html_e( 'First Name', 'bishal-data-fetcher' ); ?></th>
				<?php endif; ?>
				<?php if ( $show_last_name ) : ?>
					<th><?php esc_html_e( 'Last Name', 'bishal-data-fetcher' ); ?></th>
				<?php endif; ?>
				<?php if ( $show_email ) : ?>
					<th><?php esc_html_e( 'Email', 'bishal-data-fetcher' ); ?></th>
				<?php endif; ?>
				<?php if ( $show_date ) : ?>
					<th><?php esc_html_e( 'Date', 'bishal-data-fetcher' ); ?></th>
				<?php endif; ?>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $rows as $row ) : ?>
				<?php
				// Validate row is an array.
				if ( ! is_array( $row ) ) {
					continue;
				}
				?>
				<tr>
					<?php if ( $show_id ) : ?>
						<td><?php echo isset( $row['id'] ) ? esc_html( intval( $row['id'] ) ) : ''; ?></td>
					<?php endif; ?>
					<?php if ( $show_first_name ) : ?>
						<td><?php echo isset( $row['fname'] ) ? esc_html( sanitize_text_field( $row['fname'] ) ) : ''; ?></td>
					<?php endif; ?>
					<?php if ( $show_last_name ) : ?>
						<td><?php echo isset( $row['lname'] ) ? esc_html( sanitize_text_field( $row['lname'] ) ) : ''; ?></td>
					<?php endif; ?>
					<?php if ( $show_email ) : ?>
						<td><?php echo isset( $row['email'] ) ? esc_html( sanitize_email( $row['email'] ) ) : ''; ?></td>
					<?php endif; ?>
					<?php if ( $show_date ) : ?>
						<td><?php echo isset( $row['date'] ) ? esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), intval( $row['date'] ) ) ) : ''; ?></td>
					<?php endif; ?>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
