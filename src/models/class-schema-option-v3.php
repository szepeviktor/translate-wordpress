<?php

namespace WeglotWP\Models;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class Schema_Option_V3 {

	/**
	 * @since 3.0.0
	 * @return array
	 */
	public static function get_schema_switch_option_to_v3() {
		$schema = [
			'api_key'                      => 'api_key',
			'api_key_private'              => 'api_key_private',
			'allowed'                      => 'allowed',
			'original_language'            => 'language_from',
			'destination_language'         => (object) [
				'path' => 'languages',
				'fn'   => function( $language_to ) {
					$languages = [];
					foreach ( $language_to as $item ) {
						$languages[] = $item['language_to'];
					}
					return $languages;
				},
			],
			'private_languages'         => (object) [
				'path' => 'languages',
				'fn'   => function( $language_to ) {
					$languages = [];
					foreach ( $language_to as $item ) {
						if ( $item['enabled'] ) {
							continue;
						}
						$languages[] = $item['language_to'];
					}
					return $languages;
				},
			],
			'autoswitch'                  => 'auto_switch',
			'autoswitch_fallback'         => 'auto_switch_fallback',
			'excluded_paths'              => (object) [
				'path' => 'excluded_paths',
				'fn'   => function( $excluded_paths ) {
					$excluded = [];
					foreach ( $excluded_paths as $item ) {
						$excluded[] = $item['value'];
					}
					return $excluded;
				},
			],
			'excluded_blocks'              => (object) [
				'path' => 'excluded_blocks',
				'fn'   => function( $excluded_blocks ) {
					$excluded = [];
					foreach ( $excluded_blocks as $item ) {
						$excluded[] = $item['value'];
					}
					return $excluded;
				},
			],
			'custom_settings' => 'custom_settings',
			'is_dropdown'     => 'custom_settings.button_style.is_dropdown',
			'fullname'        => 'custom_settings.button_style.full_name',
			'with_flags'      => 'custom_settings.button_style.with_flags',
			'flag_type'       => 'custom_settings.button_style.flag_type',
		];

		return $schema;
	}
}
