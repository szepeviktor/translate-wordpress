<?php

namespace WeglotWP\Third\Woocommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WeglotWP\Models\Hooks_Interface_Weglot;
use WeglotWP\Models\Mediator_Service_Interface_Weglot;
use WeglotWP\Helpers\Helper_Filter_Url_Weglot;

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

		add_filter( 'woocommerce_get_cart_url', [ '\WeglotWP\Helpers\Helper_Filter_Url_Weglot', 'filter_url_without_ajax' ] );
		add_filter( 'woocommerce_get_checkout_url', [ '\WeglotWP\Helpers\Helper_Filter_Url_Weglot', 'filter_url_without_ajax' ] );
		add_filter( 'woocommerce_payment_successful_result', [ $this, 'woocommerce_filter_url_array' ] );
		add_filter( 'woocommerce_get_checkout_order_received_url',  [ '\WeglotWP\Helpers\Helper_Filter_Url_Weglot', 'filter_url_with_ajax' ] );
		add_action( 'woocommerce_reset_password_notification', [ $this, 'woocommerce_filter_reset_password' ] );

		add_filter( 'woocommerce_login_redirect', [ '\WeglotWP\Helpers\Helper_Filter_Url_Weglot', 'filter_url_log_redirect' ] );
		add_filter( 'woocommerce_registration_redirect', [ '\WeglotWP\Helpers\Helper_Filter_Url_Weglot', 'filter_url_log_redirect' ] );
	}

	/**
	 * Filter array woocommerce filter with optional Ajax
	 *
	 * @since 2.0
	 * @param array $result
	 * @return string
	 */
	public function woocommerce_filter_url_array( $result ) {
		$current_and_original_language = weglot_get_current_and_original_language();
		$choose_current_language       = $current_and_original_language['current'];
		if ( $current_and_original_language['current'] !== $current_and_original_language['original'] ) { // Not ajax
			$url = $this->request_url_services->create_url_object( $result['redirect'] );
		} else {
			if ( isset( $_SERVER['HTTP_REFERER'] ) ) { //phpcs:ignore
				// Ajax
				$url                     = $this->request_url_services->create_url_object( $_SERVER['HTTP_REFERER'] ); //phpcs:ignore
				$choose_current_language = $url->detectCurrentLanguage();
				$url                     = $this->request_url_services->create_url_object( $result['redirect'] );
			}
		}
		$result['redirect'] = $url->getForLanguage( $choose_current_language );
		return $result;
	}


	/**
	 * Redirect URL Lost password for WooCommerce
	 * @param mixed $url
	 */
	public function woocommerce_filter_reset_password( $url ) {
		$current_and_original_language = weglot_get_current_and_original_language();

		if ( $current_and_original_language['current'] === $current_and_original_language['original'] ) {
			return $url;
		}

		$url_redirect = add_query_arg( 'reset-link-sent', 'true', wc_get_account_endpoint_url( 'lost-password' ) );
		$url_redirect = $this->request_url_services->create_url_object( $url_redirect );

		wp_redirect( $url_redirect->getForLanguage( $current_and_original_language['current'] ) ); //phpcs:ignore
		exit;
	}
}
