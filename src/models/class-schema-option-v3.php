<?php

namespace WeglotWP\Models;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Schema_Option_V3 {

	/**
	 * @since 2.0
	 * @param string $string
	 * @return string
	 */
	public static function get_schema_switch_option_to_v3( $string ) {
		return $schema = [
			'city' => 'address.city',
			'name' => function($data) {
				return strtoupper($data['name']);
			}
		];
	}
}
