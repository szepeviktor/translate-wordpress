<?php

namespace Weglot\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Option services
 *
 * @since 2.0
 */
class Option_Service_Weglot {

	/**
	 * @var array
	 */
	protected $options_default = [
		'api_key'              => 'en',
		'original_language'    => '',
		'destination_language' => '',
		'exclude_amp'          => true,
		'version'              => null,
	];

	public function get_options_default() {
		return $this->options_default;
	}

	/**
	 * @since 2.0
	 * @return array
	 */
	public function get_options() {
		return wp_parse_args( get_option( WEGLOT_SLUG ), $this->get_options_default() );
	}

	/**
	 * @since 2.0
	 * @param string $name
	 * @return array
	 */
	public function get_option( $name ) {
		$options = $this->get_options();
		if ( ! array_key_exists( $name, $options ) ) {
			return null; // @TODO : throw exception
		}

		return $options[ $name ];
	}


	/**
	 * @since 2.0
	 * @param array $options
	 */
	public function set_options( $options ) {
		update_option( WEGLOT_SLUG, $options );
	}
}
