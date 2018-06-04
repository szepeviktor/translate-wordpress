<?php

namespace WeglotWP\Third\Woocommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WeglotWP\Models\Hooks_Interface_Weglot;
use WeglotWP\Models\Mediator_Service_Interface_Weglot;

/**
 * WC_Filter_Urls_Weglot
 *
 * @since 2.0
 */
class WC_Filter_Urls_Weglot implements Hooks_Interface_Weglot, Mediator_Service_Interface_Weglot {
	/**
	 * @since 2.0
	 * @see Mediator_Service_Interface_Weglot
	 * @param array $services
	 * @return void
	 */
	public function use_services( $services ) {
		$this->request_url_services = $services['Request_Url_Service_Weglot'];
		$this->option_services      = $services['Option_Service_Weglot'];
	}

	/**
	 * @since 2.0
	 * @see Hooks_Interface_Weglot
	 *
	 * @return void
	 */
	public function hooks() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			return;
		}

		add_filter( 'woocommerce_get_cart_url', [ $this, 'woocommerce_filter_url_without_ajax' ] );
		add_filter( 'woocommerce_get_checkout_url', [ $this, 'woocommerce_filter_url_without_ajax' ] );
		add_filter( 'woocommerce_payment_successful_result', [ $this, 'woocommerce_filter_url_array' ] );
		add_filter( 'woocommerce_get_checkout_order_received_url', [ $this, 'woocommerce_filter_with_ajax' ] );
		// add_action( 'woocommerce_reset_password_notification', [ $this, 'redirectUrlLostPassword']);

		// add_filter( 'woocommerce_login_redirect', [ $this, 'wg_log_redirect' ] );
		// add_filter( 'woocommerce_registration_redirect', [ $this, 'wg_log_redirect' ] );
	}

	protected function get_current_and_original_language() {
		$current_language    = $this->request_url_services->get_current_language();
		$original_language   = $this->option_services->get_option( 'original_language' );

		return [
			'current'  => $current_language,
			'original' => $original_language,
		];
	}

	/**
	 * Filter url woocommerce without Ajax
	 *
	 * @since 2.0
	 * @param string $woo_url
	 * @return string
	 */
	public function woocommerce_filter_url_without_ajax( $woo_url ) {
		$current_and_original_language = $this->get_current_and_original_language();

		if ( $current_and_original_language['current'] === $current_and_original_language['original'] ) {
			return $woo_url;
		}

		$url = $this->request_url_services->create_url_object( $woo_url );
		return $url->getForLanguage( $current_and_original_language['current'] );
	}

	/**
	 * Filter array woocommerce filter with optional Ajax
	 *
	 * @since 2.0
	 * @param array $result
	 * @return string
	 */
	public function woocommerce_filter_url_array( $result ) {
		$current_and_original_language = $this->get_current_and_original_language();
		$choose_current_language       = $current_and_original_language['current'];
		if ( $current_and_original_language['current'] !== $current_and_original_language['original'] ) { // Not ajax
			$url = $this->request_url_services->create_url_object( $result['redirect'] );
		} else {
			if ( isset( $_SERVER['HTTP_REFERER'] ) ) { //phpcs:ignore Ajax
				$url                     = $this->request_url_services->create_url_object( $_SERVER['HTTP_REFERER'] ); //phpcs:ignore
				$choose_current_language = $url->detectCurrentLanguage();
				$url                     = $this->request_url_services->create_url_object( $result['redirect'] );
			}
		}
		$result['redirect'] = $url->getForLanguage( $choose_current_language );
		return $result;
	}

	/**
	 * Filter url woocommerce with optional Ajax
	 *
	 * @param string $woo_url
	 * @return string
	 */
	public function woocommerce_filter_with_ajax( $woo_url ) {
		$current_and_original_language = $this->get_current_and_original_language();
		$choose_current_language       = $current_and_original_language['current'];
		if ( $current_and_original_language['current'] !== $current_and_original_language['original'] ) { // Not ajax
			$url = $this->request_url_services->create_url_object( $woo_url );
		} else {
			if ( isset( $_SERVER['HTTP_REFERER'] ) ) { //phpcs:ignore Ajax
				$url                     = $this->request_url_services->create_url_object( $_SERVER['HTTP_REFERER'] ); //phpcs:ignore
				$choose_current_language = $url->detectCurrentLanguage();
				$url                     = $this->request_url_services->create_url_object( $woo_url );
			}
		}

		return $url->getForLanguage( $choose_current_language );
	}
}
