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
			'permission_callback' => [ $this, 'post_permissions_check' ],
		]);

		// Route for fetching from the URL
		register_rest_route( 'radmantaskmrz/v1', '/run-fetch-from-url', [
			'methods' => 'POST',
			'callback' => [ $this, 'run_fetch_from_url' ],
			'permission_callback' => [ $this, 'post_permissions_check' ],
		]);

		register_rest_route('radmantaskmrz/v1', '/logs', [
			'methods'  => 'GET',
			'callback' => function(WP_REST_Request $request) {
				// Optionally validate user permission
//				if (!current_user_can('manage_options')) {
//					return new WP_REST_Response(['error' => 'Unauthorized'], 403);
//				}

				$filters = [
					'request_date'     => sanitize_text_field($request->get_param('request_date')),
					'response_content' => sanitize_text_field($request->get_param('response_content')),
					'request_method'   => sanitize_text_field($request->get_param('request_method')),
				];

				$page     = absint($request->get_param('page')) ?: 1;
				$per_page = absint($request->get_param('per_page')) ?: 10;

				return RadmanTaskMrz_Log::get_logs($filters, $page, $per_page);

			},
			'permission_callback' => [ $this, 'post_permissions_check' ],
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



	public function run_fetch_from_url( $data ) {
		$url = RadmanTaskMrz_Settings::get_url();

		if (empty($url)) {
			return new WP_REST_Response(['message' => 'No URL found.'], 404);
		}

		// Send POST request to the external URL
		$response = wp_remote_post($url, [
			'timeout' => 15,
			'headers' => [
				'Content-Type' => 'application/json',
			],
			'body' => json_encode(['trigger' => true]), // Optional data
		]);

		if (is_wp_error($response)) {
			return new WP_REST_Response(['message' => 'Failed to connect to URL.', 'error' => $response->get_error_message()], 500);
		}

		$body = wp_remote_retrieve_body($response);

		// Save to DB

		$response = RadmanTaskMrz_Log::log_request([
			'response_content' => $body,
			'request_method'   => 'POST',
		]);


		return new WP_REST_Response($response, $response['success'] ? 200 : 500);



	}

	// POST /radmantaskmrz/v1/url - Save a new URL
	public function save_url( $data ) {
		// Verify nonce for security
//		if ( ! isset( $data['nonce'] ) || ! wp_verify_nonce( $data['nonce'], 'radman-task-mrz-nonce' ) ) {
//			return new WP_REST_Response( 'Invalid nonce', 403 );
//		}

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
	public function post_permissions_check() {
		// Only allow admins to save the URL
//		if ( ! current_user_can( 'manage_options' ) ) {
//			return new WP_REST_Response( 'Forbidden', 403 );
//		}
		return true;
	}
}
