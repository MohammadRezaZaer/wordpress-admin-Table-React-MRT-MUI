<?php
/**
 * Plugin Name: RadmanTaskMrz
 * Plugin URI: https://mohammad-res.vercel.app/
 * Description: A custom plugin for RadmanTaskMrz.
 * Version: 1.0
 * Author: Your Name
 * Author URI: https://mohammad-res.vercel.app/
 * License: GPL2
 */

// Avoid direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


global $radmantaskmrz_log_table;
$radmantaskmrz_log_table = $wpdb->prefix . 'radmantaskmrz_logs';




define( 'RADMANTASKMRZ_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );


require_once RADMANTASKMRZ_PLUGIN_DIR . 'includes/class-radmantaskmrz.php';
require_once RADMANTASKMRZ_PLUGIN_DIR . 'includes/class-radmantaskmrz-api.php';
require_once RADMANTASKMRZ_PLUGIN_DIR . 'includes/class-radmantaskmrz-log.php';
require_once RADMANTASKMRZ_PLUGIN_DIR . 'includes/class-radmantaskmrz-settings.php';
require_once RADMANTASKMRZ_PLUGIN_DIR . 'includes/class-radmantaskmrz-db.php';



// Register the plugin activation hook to create the database table
register_activation_hook( __FILE__, [ 'RadmanTaskMrz_DB', 'create_db_table' ] );



function radmantaskmrz_init() {
	$plugin = new RadmanTaskMrz();
//	$plugin->run();
}
add_action( 'plugins_loaded', 'radmantaskmrz_init' );


// Add a simple menu item in the WordPress admin
function radmantaskmrz_admin_menu() {
    add_menu_page(
        'Radman Task Mrz',             // Page title
        'RadmanTaskMrz',               // Menu title
        'manage_options',              // Capability
        'radman-task-mrz',             // Menu slug
        'radmantaskmrz_settings_page'  // Function to display the settings page
    );
}
add_action( 'admin_menu', 'radmantaskmrz_admin_menu' );

// Display settings page
function radmantaskmrz_settings_page() {
    echo '<div class="wrap">';
    echo '<h1>' . esc_html( get_admin_page_title() ) . '</h1>';
    echo '<p>Welcome to RadmanTaskMrz plugin settings page!</p>';
	echo '<div id="root"></div>';
    echo '</div>';
}


// Function to create the database table when the plugin is activated
 function radmantaskmrz_create_db_table() {
	global $wpdb;

	// Define the global variable for table name
	$table_name = $wpdb->prefix . 'radmantaskmrz_logs'; // Table name with WordPress prefix

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