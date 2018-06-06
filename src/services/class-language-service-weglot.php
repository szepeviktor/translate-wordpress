<?php

namespace WeglotWP\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WeglotWP\Models\Mediator_Service_Interface_Weglot;

use Weglot\Client\Endpoint\Languages;
use Weglot\Client\Client;

/**
 * Language service
 *
 * @since 2.0
 */
class Language_Service_Weglot implements Mediator_Service_Interface_Weglot {
	protected $languages = null;

	/**
	 * @see Mediator_Service_Interface_Weglot
	 * @since 2.0
	 * @param array $services
	 * @return void
	 */
	public function use_services( $services ) {
		$this->option_services = $services['Option_Service_Weglot'];
	}

	/**
	 * Get languages available from API
	 * @since 2.0
	 *
	 * @return array
	 */
	public function get_languages_available() {
		if ( null === $this->languages ) {
			$client           = new Client( $this->option_services->get_option( 'api_key' ) );
			$languages        = new Languages( $client );
			$this->languages  = $languages->handle();
		}

		return $this->languages;
	}

	/**
	 * Get language entry
	 * @since 2.0
	 * @param string $key_code
	 * @return array
	 */
	public function get_language( $key_code ) {
		return $this->get_languages_available()[ $key_code ];
	}
}
