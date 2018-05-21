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

		// add_filter( 'woocommerce_get_cart_url', [ $this, 'woocommerce_filter_url' ] );
		// add_filter( 'woocommerce_get_checkout_url', [ $this, 'woocommerce_filter_url' ] );
		// add_filter( 'woocommerce_payment_successful_result', [ $this, 'filter_woocommerce_payment_successful_result' ] );
		// add_filter( 'woocommerce_get_checkout_order_received_url', [ $this, 'filter_woocommerce_get_checkout_order_received_url' ] );
		// add_action( 'woocommerce_reset_password_notification', [ $this, 'redirectUrlLostPassword']);

		// add_filter( 'woocommerce_login_redirect', [ $this, 'wg_log_redirect' ] );
		// add_filter( 'woocommerce_registration_redirect', [ $this, 'wg_log_redirect' ] );
	}

	/**
	 * @since 2.0
	 * @param string $wc_get_page_permalink
	 * @return string
	 */
	public function woocommerce_filter_url( $wc_get_page_permalink ) {
		$current_language  = $this->request_url_services->get_current_language();
		$original_language = $this->option_services->get_option( 'original_language' );

		if ( $current_language === $original_language ) {
			return $wc_get_page_permalink;
		}

		$url = $this->request_url_services->create_url_object( $wc_get_page_permalink );
		return $url->getForLanguage( $current_language );
	}
}
