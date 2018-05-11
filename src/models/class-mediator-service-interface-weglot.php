<?php

namespace WeglotWP\Models;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Mediator_Service_Interface_Weglot interface
 *
 * @since 2.0
 */
interface Mediator_Service_Interface_Weglot {
	/**
	 * Send services available
	 *
	 * @param array $services
	 * @return Mediator_Service_Interface_Weglot
	 */
	public function use_services( $services );
}
