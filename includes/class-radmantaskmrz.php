<?php

class RadmanTaskMrz {

	// Constructor: Initializes hooks and actions
	public function __construct() {


		// Enqueue scripts and styles
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );


	}



	// Function to enqueue styles and scripts for the plugin's admin page
	public function enqueue_scripts( $hook ) {
		// Check if the current page is the plugin's settings page
		if ( isset( $hook ) && 'toplevel_page_radman-task-mrz' === $hook ) {

			// Register and enqueue the styles
			wp_register_style( 'radman-task-mrz-styles', plugin_dir_url( __FILE__ ) . '../dist/style.css' );
			wp_enqueue_style( 'radman-task-mrz-styles' );

			// Register and enqueue the script
			wp_register_script( 'radman-task-mrz-scripts', plugin_dir_url( __FILE__ ) . '../dist/index.js', [
				'wp-i18n',
				'wp-element',
				'wp-components',
				'wp-api'
			], '1.0.0', true );
			wp_enqueue_script( 'radman-task-mrz-scripts' );

			// Localize script to pass data to the front-end
			wp_set_script_translations( 'radman-task-mrz-scripts', 'radman-task-mrz', plugin_dir_path( __FILE__ ) . '../languages' );

			// Pass the API endpoint URL and nonce to the JavaScript
			$nonce = wp_create_nonce( 'radman-task-mrz-nonce' );
			$api_endpoint_url = rest_url( 'radmantaskmrz/v1/' );
			wp_localize_script( 'radman-task-mrz-scripts', 'radmanTaskMrzData', [
				'apiEndpointUrl' => $api_endpoint_url,
				'nonce'          => $nonce,
			] );
		}
	}


}
