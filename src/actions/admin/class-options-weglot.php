<?php

namespace WeglotWP\Actions\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WeglotWP\Helpers\Helper_Tabs_Admin_Weglot;

use WeglotWP\Models\Hooks_Interface_Weglot;
use WeglotWP\Models\Mediator_Service_Interface_Weglot;

/**
 * Sanitize options after submit form
 *
 * @since 2.0
 */
class Options_Weglot implements Hooks_Interface_Weglot, Mediator_Service_Interface_Weglot {
	/**
	 * @see Mediator_Service_Interface_Weglot
	 *
	 * @param array $services
	 * @return Options_Weglot
	 */
	public function use_services( $services ) {
		$this->option_services = $services['Option_Service_Weglot'];
		return $this;
	}

	/**
	 * @see Hooks_Interface_Weglot
	 *
	 * @since 2.0
	 * @return void
	 */
	public function hooks() {
		add_action( 'admin_init', [ $this, 'weglot_options_init' ] );
	}

	/**
	 * Activate plugin
	 *
	 * @return void
	 */
	public function activate() {
		$version = $this->option_services->get_option( 'version' );
		if ( $version ) {
			return;
		}

		$options            = $this->option_services->get_options();
		$options['version'] = WEGLOT_VERSION;

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
		$tab         = ( isset( $_POST['tab'] ) ) ? $_POST['tab'] : Helper_Tabs_Admin_Weglot::SETTINGS; //phpcs:ignore
		$options_bdd = $this->option_services->get_options();

		$new_options = wp_parse_args( $options, $options_bdd );

		switch ( $tab ) {
			case Helper_Tabs_Admin_Weglot::SETTINGS:
			default:
				$new_options = $this->sanitize_options_settings( $new_options, $options );
				break;
			case Helper_Tabs_Admin_Weglot::APPEARANCE:
				$new_options = $this->sanitize_options_appearance( $new_options, $options );
				break;
			case Helper_Tabs_Admin_Weglot::ADVANCED:
				$new_options = $this->sanitize_options_advanced( $new_options, $options );
				break;
		}

		return $new_options;
	}

	/**
	 * @since 2.0
	 * @param array $new_options
	 * @param array $options
	 * @return array
	 */
	public function sanitize_options_settings( $new_options, $options ) {
		if ( isset( $options['exclude_urls'] ) ) {
			$new_options['exclude_urls'] = array_filter( $options['exclude_urls'], function( $value ) {
				return '' !== $value;
			} );
		} else {
			$new_options['exclude_urls'] = [];
		}

		if ( isset( $options['exclude_blocks'] ) ) {
			$new_options['exclude_blocks'] = array_filter( $options['exclude_blocks'], function( $value ) {
				return '' !== $value;
			} );
		} else {
			$new_options['exclude_blocks'] = [];
		}

		return $new_options;
	}

	/**
	 * @since 2.0
	 * @param array $new_options
	 * @param array $options
	 * @return array
	 */
	public function sanitize_options_advanced( $new_options, $options ) {
		$new_options['auto_redirect']     = isset( $options['auto_redirect'] ) ? 1 : 0;
		$new_options['email_translate']   = isset( $options['email_translate'] ) ? 1 : 0;
		$new_options['translate_amp']     = isset( $options['translate_amp'] ) ? 1 : 0;
		return $new_options;
	}

	/**
	 * @since 2.0
	 * @param array $new_options
	 * @param array $options
	 * @return array
	 */
	public function sanitize_options_appearance( $new_options, $options ) {
		$new_options['is_fullname']       = isset( $options['is_fullname'] ) ? 1 : 0;
		$new_options['with_name']         = isset( $options['with_name'] ) ? 1 : 0;
		$new_options['is_dropdown']       = isset( $options['is_dropdown'] ) ? 1 : 0;
		$new_options['with_flags']        = isset( $options['with_flags'] ) ? 1 : 0;

		$new_options['type_flags']          = isset( $options['type_flags'] ) ? $options['type_flags'] : '0';
		$new_options['override_css']        = isset( $options['override_css'] ) ? $options['override_css'] : '';

		return $new_options;
	}
}
