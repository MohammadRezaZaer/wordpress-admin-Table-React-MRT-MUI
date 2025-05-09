<?php

class RadmanTaskMrz_API {

	public function __construct() {
		// Registering the routes
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	// Register routes for saving and getting the URL
	public function register_routes() {
		// Route for getting the URL
		register_rest_route( 'radmantaskmrz/v1', '/url', [
			'methods' => 'GET',
			'callback' => [ $this, 'get_url' ],
			'permission_callback' => [ $this, 'get_url_permissions_check' ],
		]);

		// Route for saving the URL
		register_rest_route( 'radmantaskmrz/v1', '/url', [
			'methods' => 'POST',
			'callback' => [ $this, 'save_url' ],
			'permission_callback' => [ $this, 'save_url_permissions_check' ],
		]);
	}

	// GET /radmantaskmrz/v1/url - Get the stored URL
	public function get_url( $data ) {
		// Get the stored API URL from the options
		$url = RadmanTaskMrz_Settings::get_url();

		if ( empty( $url ) ) {
			return new WP_REST_Response( 'No URL found', 404 );
		}

		return new WP_REST_Response( [ 'url' => $url ], 200 );
	}

	// POST /radmantaskmrz/v1/url - Save a new URL
	public function save_url( $data ) {
		// Verify nonce for security
		if ( ! isset( $data['nonce'] ) || ! wp_verify_nonce( $data['nonce'], 'radman-task-mrz-nonce' ) ) {
			return new WP_REST_Response( 'Invalid nonce', 403 );
		}

		// Validate and sanitize the URL
		if ( ! isset( $data['url'] ) || ! filter_var( $data['url'], FILTER_VALIDATE_URL ) ) {
			return new WP_REST_Response( 'Invalid URL', 400 );
		}

		// Save the URL in the options table
		RadmanTaskMrz_Settings::save_url( $data['url'] );

		return new WP_REST_Response( 'URL saved successfully', 200 );
	}

	// Permission callback for GET request - Check if the user has permissions
	public function get_url_permissions_check() {
		// Only allow admins to retrieve the URL
		if ( ! current_user_can( 'manage_options' ) ) {
			return new WP_REST_Response( 'Forbidden', 403 );
		}
		return true;
	}

	// Permission callback for POST request - Check if the user has permissions
	public function save_url_permissions_check() {
		// Only allow admins to save the URL
		if ( ! current_user_can( 'manage_options' ) ) {
			return new WP_REST_Response( 'Forbidden', 403 );
		}
		return true;
	}
}
