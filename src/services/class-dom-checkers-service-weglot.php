<?php

namespace WeglotWP\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Dom Checkers
 *
 * @since 2.0
 */
class Dom_Checkers_Service_Weglot {
	protected $languages = null;

	/**
	 * @since 2.0
	 */
	public function __construct() {
		$this->option_services = weglot_get_service( 'Option_Service_Weglot' );
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

	/**
	 * @since 2.0
	 * @return array
	 */
	public function get_languages_configured() {
		$languages[]      = weglot_get_original_language();
		$languages        = array_merge( $languages, weglot_get_destination_language() );
		$languages_object = [];

		foreach ( $languages as $language ) {
			$languages_object[] = $this->get_language( $language );
		}

		return $languages_object;
	}
}
