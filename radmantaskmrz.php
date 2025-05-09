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