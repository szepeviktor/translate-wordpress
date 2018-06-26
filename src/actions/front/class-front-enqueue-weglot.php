<?php

namespace WeglotWP\Actions\Front;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WeglotWP\Models\Hooks_Interface_Weglot;

/**
 * Enqueue CSS / JS on front
 *
 * @since 2.0
 */
class Front_Enqueue_Weglot implements Hooks_Interface_Weglot {

	/**
	 * @since 2.0
	 */
	public function __construct() {
		$this->option_services         = weglot_get_service( 'Option_Service_Weglot' );
	}

	/**
	 * @see Hooks_Interface_Weglot
	 *
	 * @since 2.0
	 * @return void
	 */
	public function hooks() {
		add_action( 'wp_enqueue_scripts', [ $this, 'weglot_wp_enqueue_scripts' ] );
		add_action( 'login_enqueue_scripts', [ $this, 'weglot_wp_enqueue_scripts' ] );
	}

	/**
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_inline_css() {
		$css = $this->option_services->get_option( 'override_css' );
		return $css;
	}

	/**
	 * @see wp_enqueue_scripts
	 * @since 2.0
	 *
	 * @return void
	 */
	public function weglot_wp_enqueue_scripts() {
		wp_enqueue_style( 'weglot-css', WEGLOT_URL_DIST . '/css/front-css.css' );
		wp_add_inline_style( 'weglot-css', $this->get_inline_css() );
	}
}
