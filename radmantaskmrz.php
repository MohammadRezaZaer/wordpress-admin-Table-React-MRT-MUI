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





define( 'RADMANTASKMRZ_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );


require_once RADMANTASKMRZ_PLUGIN_DIR . 'includes/class-radmantaskmrz.php';


function radmantaskmrz_init() {
	$plugin = new RadmanTaskMrz();
	$plugin->run();
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

add_action( 'admin_enqueue_scripts', 'enqueue_scripts' );
function enqueue_scripts() {
	global $plugin_page;

	// Check if $plugin_page is set and not empty, indicating a plugin page
	if ( ! empty( $plugin_page ) && $plugin_page == "mrz_product_price_list" ) {


		wp_register_style( 'mrz-product-price-list-styles', plugin_dir_url( __FILE__ ) . 'dist/style.css' );
		wp_enqueue_style( 'mrz-product-price-list-styles' );

		wp_register_script( 'mrz-product-price-list', plugin_dir_url( __FILE__ ) . 'dist/index.js', array(
			'wp-i18n',
			'wp-element',
			'wp-components',
			'wp-api'
		), '1.0.0', true );
		wp_enqueue_script( 'mrz-product-price-list' );

		wp_set_script_translations( 'mrz-product-price-list', 'mrz-product-price-list', plugin_dir_path( __FILE__ ) . 'languages' );


		// Generate a nonce for the REST API request
		$nonce = wp_create_nonce( 'mrz-price-list-nonce' );

		// Pass the API endpoint URL and nonce to the script
		$api_endpoint_url = rest_url( 'mrz-product-price-list/v1/' );
		wp_localize_script( 'mrz-product-price-list', 'mrzProductPriceListData', [
			'apiEndpointUrl' => $api_endpoint_url,
			'nonce'          => $nonce,
		] );
	}
}