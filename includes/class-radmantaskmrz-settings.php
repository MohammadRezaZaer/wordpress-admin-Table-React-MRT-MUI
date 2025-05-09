<?php

class RadmanTaskMrz_Settings {

	// Save the API URL in the database
	public static function save_url( $url ) {
		update_option( 'radmantaskmrz_api_url', sanitize_text_field( $url ) );
	}

	// Retrieve the API URL from the database
	public static function get_url() {
		return get_option( 'radmantaskmrz_api_url' );
	}
}
