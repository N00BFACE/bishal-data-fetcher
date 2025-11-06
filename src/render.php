<?php
/**
 * Render.php - Frontend display for BDF Block
 *
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 *
 * @package BishalDataFetcher
 */

// Get block attributes with defaults.
$show_id         = isset( $attributes['showId'] ) ? $attributes['showId'] : true;
$show_first_name = isset( $attributes['showFirstName'] ) ? $attributes['showFirstName'] : true;
$show_last_name  = isset( $attributes['showLastName'] ) ? $attributes['showLastName'] : true;
$show_email      = isset( $attributes['showEmail'] ) ? $attributes['showEmail'] : true;
$show_date       = isset( $attributes['showDate'] ) ? $attributes['showDate'] : true;

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
		if ( json_last_error() === JSON_ERROR_NONE ) {
			set_transient( $transient_key, $api_data, HOUR_IN_SECONDS );
		}
	}
}

// Check if we have data to display.
if ( empty( $api_data ) || empty( $api_data['data']['rows'] ) ) {
	?>
	<div <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?>>
		<p><?php esc_html_e( 'No data available.', 'bishal-data-fetcher' ); ?></p>
	</div>
	<?php
	return;
}

$title = isset( $api_data['data']['title'] ) ? $api_data['data']['title'] : '';
$rows  = $api_data['data']['rows'];
?>

<div <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?>>
	<?php if ( ! empty( $title ) ) : ?>
		<h3><?php echo esc_html( $title ); ?></h3>
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
				<tr>
					<?php if ( $show_id ) : ?>
						<td><?php echo esc_html( $row['id'] ); ?></td>
					<?php endif; ?>
					<?php if ( $show_first_name ) : ?>
						<td><?php echo esc_html( $row['fname'] ); ?></td>
					<?php endif; ?>
					<?php if ( $show_last_name ) : ?>
						<td><?php echo esc_html( $row['lname'] ); ?></td>
					<?php endif; ?>
					<?php if ( $show_email ) : ?>
						<td><?php echo esc_html( $row['email'] ); ?></td>
					<?php endif; ?>
					<?php if ( $show_date ) : ?>
						<td><?php echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $row['date'] ) ); ?></td>
					<?php endif; ?>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>