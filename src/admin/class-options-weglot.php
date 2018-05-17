<?php

namespace WeglotWP\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
		$options['exclude_urls'] = array_filter( $options['exclude_urls'], function( $value ) {
			return '' !== $value;
		} );
		$options['exclude_blocks'] = array_filter( $options['exclude_blocks'], function( $value ) {
			return '' !== $value;
		} );

		return $options;
	}
}
