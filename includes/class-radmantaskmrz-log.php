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

	public static function get_logs($filters = [], $page = 1, $per_page = 10) {
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

		// Count total
		$count_query = "SELECT COUNT(*) FROM $table_name $where_sql";
		$total = $params ? $wpdb->get_var($wpdb->prepare($count_query, ...$params)) : $wpdb->get_var($count_query);

		$page = max(1, intval($page));
		$per_page = max(1, intval($per_page));
		$offset = ($page - 1) * $per_page;

		// Fetch data
		$data_query = "SELECT * FROM $table_name $where_sql ORDER BY request_date DESC LIMIT %d OFFSET %d";
		$params_with_limit = array_merge($params, [$per_page, $offset]);
		$prepared_query = $wpdb->prepare($data_query, ...$params_with_limit);
		$results = $wpdb->get_results($prepared_query, ARRAY_A);

		$last_page = ceil($total / $per_page);
		$from = $total > 0 ? $offset + 1 : 0;
		$to = $from + count($results) - 1;

		return [
			"current_page" => $page,
			"data"         => $results,
			"from"         => $from,
			"last_page"    => $last_page,
			"per_page"     => $per_page,
			"to"           => $to,
			"total"        => intval($total),
		];
	}



}
