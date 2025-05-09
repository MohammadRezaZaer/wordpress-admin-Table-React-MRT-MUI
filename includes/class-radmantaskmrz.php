<?php

class RadmanTaskMrz {

	public function __construct() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	// تابع برای بارگذاری اسکریپت‌ها و استایل‌ها
	public function enqueue_scripts( $hook ) {
		// بررسی اینکه آیا صفحه‌ی موردنظر صفحه‌ی پلاگین است یا خیر
		if ( isset( $hook ) && 'toplevel_page_radman-task-mrz' === $hook ) {

			// بارگذاری استایل
			wp_register_style( 'radman-task-mrz-styles', plugin_dir_url( __FILE__ ) . '../assets/dist/style.css' );
			wp_enqueue_style( 'radman-task-mrz-styles' );

			// بارگذاری اسکریپت React
			wp_register_script( 'radman-task-mrz-scripts', plugin_dir_url( __FILE__ ) . '../assets/dist/index.js', array(
				'wp-i18n',
				'wp-element',
				'wp-components',
				'wp-api'
			), '1.0.0', true );
			wp_enqueue_script( 'radman-task-mrz-scripts' );

			// ترجمه اسکریپت
			wp_set_script_translations( 'radman-task-mrz-scripts', 'radman-task-mrz', plugin_dir_path( __FILE__ ) . '../languages' );

			// ارسال nonce و endpoint API به اسکریپت
			$nonce = wp_create_nonce( 'radman-task-mrz-nonce' );
			$api_endpoint_url = rest_url( 'radman-task-mrz/v1/' );
			wp_localize_script( 'radman-task-mrz-scripts', 'radmanTaskMrzData', [
				'apiEndpointUrl' => $api_endpoint_url,
				'nonce'          => $nonce,
			] );
		}
	}


}
