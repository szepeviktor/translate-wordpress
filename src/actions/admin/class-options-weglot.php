<?php

namespace WeglotWP\Actions\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WeglotWP\Helpers\Helper_Tabs_Admin_Weglot;
use WeglotWP\Helpers\Helper_Pages_Weglot;
use WeglotWP\Helpers\Helper_Flag_Type;

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
				$options  = $this->sanitize_options_settings( $options );
				$response = $this->option_services->save_options_to_weglot( $options );
				if ( $response['success'] ) {
					update_option( sprintf( '%s-%s', WEGLOT_SLUG, 'api_key_private' ), $options['api_key_private'] );
					update_option( sprintf( '%s-%s', WEGLOT_SLUG, 'api_key' ), $response['result']['api_key'] );

					$options_bdd = $this->option_services->get_options_bdd();

					if ( $options_bdd['has_first_settings'] ) {
						$options_bdd['has_first_settings']      = false;
						$options_bdd['show_box_first_settings'] = true;
						$this->option_services->set_options( $options_bdd );
					}
				}
				break;
		}

		wp_redirect( $redirect_url ); //phpcs:ignore
	}


	/**
	 * @since 2.0
	 * @version 2.0.6
	 * @param array $options
	 * @return array
	 */
	public function sanitize_options_settings( $options ) {
		$user_info        = $this->user_api_services->get_user_info( $options['api_key_private'] );
		$plans            = $this->user_api_services->get_plans();

		// Limit language
		if (
			$user_info['plan'] <= 0 ||
			in_array( $user_info['plan'], $plans['starter_free']['ids'] ) // phpcs:ignore
		) {
			$options['languages'] = array_splice( $options['languages'], 0, $plans['starter_free']['limit_language'] );
		} elseif (
			in_array( $user_info['plan'], $plans['business']['ids'] ) // phpcs:ignore
		) {
			$options['languages'] = array_splice( $options['languages'], 0, $plans['business']['limit_language'] );
		}

		$options['custom_settings']['button_style']['is_dropdown']  = isset( $options['custom_settings']['button_style']['is_dropdown'] );
		$options['custom_settings']['button_style']['with_flags']   = isset( $options['custom_settings']['button_style']['with_flags'] );
		$options['custom_settings']['button_style']['full_name']    = isset( $options['custom_settings']['button_style']['full_name'] );
		$options['custom_settings']['button_style']['with_name']    = isset( $options['custom_settings']['button_style']['with_name'] );
		$options['custom_settings']['button_style']['custom_css']   = isset( $options['custom_settings']['button_style']['custom_css'] ) ? $options['custom_settings']['button_style']['custom_css'] : '';

		$options['custom_settings']['button_style']['flag_type']    = isset( $options['custom_settings']['button_style']['flag_type'] ) ? $options['custom_settings']['button_style']['flag_type'] : Helper_Flag_Type::RECTANGLE_MAT;

		$options['custom_settings']['translate_email']              = isset( $options['custom_settings']['translate_email'] );
		$options['custom_settings']['translate_search']             = isset( $options['custom_settings']['translate_search'] );
		$options['custom_settings']['translate_amp']                = isset( $options['custom_settings']['translate_amp'] );

		$options['auto_switch']                = isset( $options['auto_switch'] );
		foreach ( $options['languages'] as $key => $language ) {
			if ( 'active' === $key ) {
				continue;
			}
			$options['languages'][ $key ]['enabled'] = ! isset( $options['languages'][ $key ]['enabled'] );
		}

		if ( ! isset( $options['excluded_paths'] ) ) {
			$options['excluded_paths'] = [];
		} else {
			$options['excluded_paths'] = array_values( $options['excluded_paths'] );
		}

		if ( ! isset( $options['excluded_blocks'] ) ) {
			$options['excluded_blocks'] = [];
		}

		return $options;
	}
}
