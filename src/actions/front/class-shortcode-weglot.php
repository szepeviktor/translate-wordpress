<?php

namespace WeglotWP\Actions\Front;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WeglotWP\Models\Hooks_Interface_Weglot;

/**
 *
 * @since 2.0
 */
class Shortcode_Weglot {

	/**
	 * @since 2.0
	 */
	public function __construct() {
		$this->button_services = weglot_get_service( 'Button_Service_Weglot' );

		add_shortcode( 'weglot_switcher', [ $this, 'weglot_switcher_callback' ] );
	}

	/**
	 * @see weglot_switcher
	 * @since 2.0
	 *
	 * @return string
	 */
	public function weglot_switcher_callback() {
		return $this->button_services->get_html( 'weglot-shortcode' ); //phpcs:ignore
	}
}
