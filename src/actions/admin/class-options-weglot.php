<?php

namespace WeglotWP\Actions\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WeglotWP\Helpers\Helper_Tabs_Admin_Weglot;
use WeglotWP\Helpers\Helper_Pages_Weglot;

use WeglotWP\Models\Hooks_Interface_Weglot;

/**
 * Sanitize options after submit form
 *
 * @since 2.0
 */
class Options_Weglot implements Hooks_Interface_Weglot {

	/**
	 * @since 2.0
	 */
	public function __construct() {
		$this->option_services   = weglot_get_service( 'Option_Service_Weglot' );
		$this->user_api_services = weglot_get_service( 'User_Api_Service_Weglot' );
	}

	/**
	 * @see Hooks_Interface_Weglot
	 *
	 * @since 2.0
	 * @return void
	 */
	public function hooks() {
		add_action( 'admin_post_weglot_save_settings', [ $this, 'weglot_save_settings' ] );
		// $api_key = $this->option_services->get_option( 'api_key' );
		// if ( empty( $api_key ) && ( ! isset( $_GET['page'] ) || strpos( $_GET['page'], 'weglot-settings' ) === false) ) { // phpcs:ignore
		// 	//We don't show the notice if we are on Weglot configuration
		// 	add_action( 'admin_notices', [ '\WeglotWP\Notices\No_Configuration_Weglot', 'admin_notice' ] );
		// }
	}

	/**
	 * Activate plugin
	 *
	 * @return void
	 */
	public function activate() {
		update_option( 'weglot_version', WEGLOT_VERSION );
		$options            = $this->option_services->get_options();

		$this->option_services->set_options( $options );
	}


	/**
	 * @since 3.0.0
	 * @return void
	 */
	public function weglot_save_settings() {
		$redirect_url = admin_url( 'admin.php?page=' . Helper_Pages_Weglot::SETTINGS );
		if ( ! isset( $_GET['tab'] ) || ! isset( $_GET['_wpnonce'] ) ) { //phpcs:ignore
			wp_redirect( $redirect_url );
			return;
		}

		if ( ! wp_verify_nonce( $_GET[ '_wpnonce' ], 'weglot_save_settings' ) ) { //phpcs:ignore
			wp_redirect( $redirect_url );
			return;
		}

		$tab = $_GET[ 'tab' ]; //phpcs:ignore
		switch ( $tab ) {
			case Helper_Tabs_Admin_Weglot::SETTINGS:
				$options  = $_POST[ WEGLOT_SLUG ]; //phpcs:ignore
				$response = $this->option_services->save_options_to_weglot( $options );
				if ( $response['success'] ) {
					update_option( sprintf( '%s-%s', WEGLOT_SLUG, 'api_key_private' ), $options['api_key_private'] );
					update_option( sprintf( '%s-%s', WEGLOT_SLUG, 'api_key' ), $response['result']['api_key'] );
				}
				break;
		}
		// $options = $this->sanitize_options_settings( $options );

		// 	if ( $options['has_first_settings'] ) {
		// 		$options['has_first_settings']      = false;
		// 		$options['show_box_first_settings'] = true;
		// 	}

		wp_redirect( $redirect_url );
	}


	/**
	 * @since 2.0
	 * @version 2.0.6
	 * @param array $options
	 * @return array
	 */
	public function sanitize_options_settings( $options ) {
		// $user_info        = $this->user_api_services->get_user_info( $options['api_key'] );
		// $plans            = $this->user_api_services->get_plans();

		// $old_destination_languages = array_diff( $options_bdd['destination_language'], $options['destination_language'] );

		// // Limit language
		// if (
		// 	$user_info['plan'] <= 0 ||
		// 	in_array( $user_info['plan'], $plans['starter_free']['ids'] ) // phpcs:ignore
		// ) {
		// 	$new_options['destination_language'] = array_splice( $options['destination_language'], 0, $plans['starter_free']['limit_language'] );
		// } elseif (
		// 	in_array( $user_info['plan'], $plans['business']['ids'] ) // phpcs:ignore
		// ) {
		// 	$new_options['destination_language'] = array_splice( $options['destination_language'], 0, $plans['business']['limit_language'] );
		// }

		return $options;
	}
}
