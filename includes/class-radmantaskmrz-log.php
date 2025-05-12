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
}
