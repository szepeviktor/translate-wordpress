<?php

namespace WeglotWP\Actions\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WeglotWP\Helpers\Helper_Tabs_Admin_Weglot;

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
		add_action( 'admin_init', [ $this, 'weglot_options_init' ] );
		$api_key = $this->option_services->get_option( 'api_key' );
		if ( empty( $api_key ) && ( ! isset( $_GET['page'] ) || strpos( $_GET['page'], 'weglot-settings' ) === false) ) { // phpcs:ignore
			//We don't show the notice if we are on Weglot configuration
			add_action( 'admin_notices', [ '\WeglotWP\Notices\No_Configuration_Weglot', 'admin_notice' ] );
		}
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
	 * Register setting options
	 *
	 * @see admin_init
	 * @since 2.0
	 *
	 * @return void
	 */
	public function weglot_options_init() {
		register_setting( WEGLOT_OPTION_GROUP, WEGLOT_SLUG, [ $this, 'sanitize_options' ] );
	}

	/**
	 * Callback register_setting for sanitize options
	 *
	 * @since 2.0
	 *
	 * @param array $options
	 * @return array
	 */
	public function sanitize_options( $options ) {
		if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
			// $options = $this->sanitize_options_settings( $options );

			if ( $options['has_first_settings'] ) {
				$options['has_first_settings']      = false;
				$options['show_box_first_settings'] = true;
			}
			$options = $this->option_services->save_options_to_weglot( $options );
		}

		return $options;
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
