<?php

namespace WeglotWP\Actions\Front;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WeglotWP\Models\Hooks_Interface_Weglot;
use WeglotWP\Models\Mediator_Service_Interface_Weglot;

/**
 *
 * @since 2.0
 */
class Shortcode_Weglot implements Mediator_Service_Interface_Weglot {

	/**
	 * @since 2.0
	 */
	public function __construct() {
		add_shortcode( 'weglot_switcher', [ $this, 'weglot_switcher_callback' ] );
	}

	/**
	 * @see Mediator_Service_Interface_Weglot
	 *
	 * @param array $services
	 * @return void
	 */
	public function use_services( $services ) {
		$this->button_services = $services['Button_Service_Weglot'];
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
