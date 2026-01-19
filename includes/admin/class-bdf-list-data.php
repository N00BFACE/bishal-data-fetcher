<?php
/**
 * Bishal Data Fetcher Plugin List Data Class.
 *
 * @package BishalDataFetcher
 */

namespace Bishal\DataFetcher\Admin;

/**
 * List Table Class for displaying API data.
 */
class ListTable extends \WP_List_Table {
	/**
	 * Define the columns for the list table.
	 *
	 * @return array The columns for the list table.
	 */
	public function get_columns() {
		$columns = array(
			'id'         => __( 'ID', 'bishal-data-fetcher' ),
			'first_name' => __( 'First Name', 'bishal-data-fetcher' ),
			'last_name'  => __( 'Last Name', 'bishal-data-fetcher' ),
			'email'      => __( 'Email', 'bishal-data-fetcher' ),
			'date'       => __( 'Date', 'bishal-data-fetcher' ),
		);
		return $columns;
	}

	/**
	 * Default column rendering.
	 *
	 * @param array  $item The current item.
	 * @param string $column_name The column name.
	 * @return mixed The column value.
	 */
	public function column_default( $item, $column_name ) {
		return isset( $item[ $column_name ] ) ? $item[ $column_name ] : '';
	}

	/**
	 * Prepare the items for the list table.
	 */
	public function prepare_items() {
		// Get the data from the transient.
		$raw = get_transient( 'bdf_api_data' );
		if ( false === $raw || empty( $raw ) ) {
			$raw = array();
		}

		// Extract row data.
		$api_rows = array();
		if ( ! empty( $raw['data'] ) && ! empty( $raw['data']['rows'] ) && is_array( $raw['data']['rows'] ) ) {
			$api_rows = $raw['data']['rows'];
		}

		// Map API rows to table items.
		$data = array();
		foreach ( $api_rows as $row ) {
			// Convert date to readable format if exists.
			if ( isset( $row['date'] ) ) {
				$row['date'] = date_i18n( 'F j, Y \a\t g:i A', $row['date'] );
			}
			$data[] = array(
				'id'         => isset( $row['id'] ) ? intval( $row['id'] ) : '',
				'first_name' => isset( $row['fname'] ) ? sanitize_text_field( $row['fname'] ) : '',
				'last_name'  => isset( $row['lname'] ) ? sanitize_text_field( $row['lname'] ) : '',
				'email'      => isset( $row['email'] ) ? sanitize_email( $row['email'] ) : '',
				'date'       => isset( $row['date'] ) ? sanitize_text_field( $row['date'] ) : '',
			);
		}

		// Define columns.
		$columns = $this->get_columns();

		// No hidden or sortable columns for now.
		$hidden                = array();
		$sortable              = array();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		// Pagination.
		$per_page     = 10;
		$current_page = $this->get_pagenum();
		$total_items  = count( $data );
		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
			)
		);

		$this->items = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );
	}
}
