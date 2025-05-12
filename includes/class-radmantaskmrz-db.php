<?php


class RadmanTaskMrz_DB {

	// Function to create the database table when the plugin is activated
	public static function create_db_table() {
		global $wpdb;
		global $radmantaskmrz_log_table;
		// Define the global variable for table name
		$table_name = $wpdb->prefix . $radmantaskmrz_log_table; // Table name with WordPress prefix

		// SQL query to create the logs table
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
                id BIGINT(20) NOT NULL AUTO_INCREMENT,
                request_date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
                response_content TEXT NOT NULL,
                request_method VARCHAR(10) NOT NULL,
                PRIMARY KEY  (id)
            ) $charset_collate;";

		// Include the WordPress database upgrade function
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		// Create the table or update it if necessary
		dbDelta( $sql );
	}
}
