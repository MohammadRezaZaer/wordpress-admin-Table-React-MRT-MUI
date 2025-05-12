<?php
class RadmanTaskMrz_Log {

	public static function log_request($data) {
		global $wpdb;
		global $radmantaskmrz_log_table;
		$table_name =  $radmantaskmrz_log_table;

		$inserted = $wpdb->insert(
			$table_name,
			[
				'response_content' => $data['response_content'],
				'request_method'   => $data['request_method'],
			],
			['%s', '%s']
		);

		if ( $inserted ) {
			$insert_id = $wpdb->insert_id;
			return [
				'success'   => true,
				'insert_id' => $insert_id,
			];
		} else {
			return [
				'success' => false,
				'error'   => $wpdb->last_error,
			];
		}
	}

	//  method to fetch logs with optional filters
	public static function get_logs($filters = []) {
		global $wpdb;
		global $radmantaskmrz_log_table;
		$table_name = $radmantaskmrz_log_table;

		$where = [];
		$params = [];

		if (!empty($filters['request_date'])) {
			$where[] = 'DATE(request_date) = %s';
			$params[] = $filters['request_date'];
		}

		if (!empty($filters['response_content'])) {
			$where[] = 'response_content LIKE %s';
			$params[] = '%' . $wpdb->esc_like($filters['response_content']) . '%';
		}

		if (!empty($filters['request_method'])) {
			$where[] = 'request_method = %s';
			$params[] = $filters['request_method'];
		}

		$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
		$query     = "SELECT * FROM $table_name $where_sql ORDER BY request_date DESC LIMIT 100";

		// Secure prepared query
		$prepared = $params ? $wpdb->prepare($query, ...$params) : $query;

		$results = $wpdb->get_results($prepared, ARRAY_A);

		return [
			'success' => true,
			'logs'    => $results,
		];
	}


}
