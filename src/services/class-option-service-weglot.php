<?php

namespace WeglotWP\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Morphism\Morphism;
use WeglotWP\Models\Schema_Option_V3;
use WeglotWP\Helpers\Helper_Flag_Type;

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
		'has_first_settings'      => true,
		'show_box_first_settings' => false,
		'language_from'           => [
			'code' => 'en',
		],
		'languages'                        => [],
		'auto_switch'                      => false,
		'auto_switch_fallback'             => 'en',
		'excluded_blocks'                  => [],
		'excluded_paths'                   => [],
		'custom_settings'                  => [
			'translate_email'                   => false,
			'translate_amp'                     => false,
			'translate_search'                  => false,
			'button_style'                      => [
				'full_name'     => false,
				'with_name'     => true,
				'is_dropdown'   => true,
				'with_flags'    => true,
				'flag_type'     => Helper_Flag_Type::RECTANGLE_MAT,
				'custom_css'    => '',
			],
			'has_first_settings'               => true,
			'show_box_first_settings'          => false,
			'rtl_ltr_style'                    => '',
			'active_wc_reload'                 => false,
			'flag_css'                         => '',
			'menu_switcher'                    => [],
			'custom_urls'                      => [],
		],
		'allowed' => true,
	];


	// 'original_language' => string 'en' (length=2)
	// 'destination_language' =>
	// 	array (size=2)
	// 	0 => string 'fr' (length=2)
	// 	1 => string 'es' (length=2)
	// 'translate_amp' => int 0
	// 'exclude_blocks' =>
	// 	array (size=0)
	// 	empty
	// 'exclude_urls' =>
	// 	array (size=0)
	// 	empty
	// 'auto_redirect' => int 1
	// 'email_translate' => int 1
	// 'is_fullname' => int 0
	// 'with_name' => int 1
	// 'is_dropdown' => int 1
	// 'type_flags' => string '1' (length=1)
	// 'with_flags' => int 1
	// 'override_css' => string '' (length=0)
	// 'has_first_settings' => boolean false
	// 'show_box_first_settings' => boolean false
	// 'rtl_ltr_style' => string '' (length=0)
	// 'allowed' => boolean true
	// 'active_wc_reload' => boolean false
	// 'custom_urls' =>
	// 	array (size=0)
	// 	empty
	// 'flag_css' => string '' (length=0)
	// 'menu_switcher' =>
	// 	array (size=2)
	// 	'hide_current' => int 0
	// 	'dropdown' => int 0
	// 'active_search' => int 0
	// 'private_mode' =>
	// 	array (size=4)
	// 	'active' => int 1
	// 	'es' => int 1
	// 	'en' => int 0
	// 	'fr' => int 0
	// 'is_menu' => int 0

	/**
	 * @since 3.0.0
	 */
	public function __construct() {
		Morphism::setMapper( 'WeglotWP\Models\Schema_Option_V3', Schema_Option_V3::get_schema_switch_option_to_v3() );
	}


	/**
	 * Get options default
	 *
	 * @since 2.0
	 * @return array
	 */
	public function get_options_default() {
		return $this->options_default;
	}

	/**
	 * @since 3.0.0
	 * @param string $api_key
	 * @return array
	 */
	public function get_options_from_cdn_with_api_key( $api_key ) {
		$key      = str_replace( 'wg_', '', $api_key );
		$url      = sprintf( '%s%s.json', 'https://cdn.weglot.com/projects-settings/', $key );

		$response = wp_remote_get( $url, [
			'timeout'     => 15,
		] );

		if ( is_wp_error( $response ) ) {
			return [
				'success' => false,
				'result'  => [],
			];
		}

		try {
			$body = json_decode( $response['body'], true );
			return [
				'success' => true,
				'result'  => $body,
			];
		} catch ( \Exception $th ) {
			return [
				'success' => false,
			];
		}
	}


	/**
	 * @since 2.0
	 * @version 3.0.0
	 * @return array
	 */
	public function get_options() {
		$api_key = $this->get_api_key();

		if ( ! $api_key ) {
			return (array) Morphism::map( 'WeglotWP\Models\Schema_Option_V3', $this->get_options_default() );
		}

		$response = $this->get_options_from_cdn_with_api_key(
			$api_key
		);

		if ( $response['success'] ) {
			$options = $response['result'];
		}

		$options['api_key_private'] = $this->get_api_key_private();

		if ( empty( $options['custom_settings']['menu_switcher'] ) ) {
			$menu_options_services                        = weglot_get_service( 'Menu_Options_Service_Weglot' );
			$options['custom_settings']['menu_switcher']  = $menu_options_services->get_options_default();
		}

		$options = apply_filters( 'weglot_get_options', array_merge( $this->get_options_bdd(), $options ) );

		return (array) Morphism::map( 'WeglotWP\Models\Schema_Option_V3', $options );
	}

	/**
	 * @since 3.0.0
	 * @return string
	 */
	public function get_api_key_private() {
		return get_option( sprintf( '%s-%s', WEGLOT_SLUG, 'api_key_private' ) );
	}


	/**
	 * @since 3.0.0
	 * @return string
	 */
	public function get_api_key() {
		return get_option( sprintf( '%s-%s', WEGLOT_SLUG, 'api_key' ) );
	}

	/**
	 * @since 3.0.0
	 * @return array
	 */
	public function get_options_bdd() {
		return wp_parse_args( get_option( WEGLOT_SLUG ), $this->get_options_default() );
	}


	/**
	 * @since 3.0.0
	 * @param array $options
	 * @return array
	 */
	public function save_options_to_weglot( $options ) {
		$response    = wp_remote_post( 'https://api-staging.weglot.com/projects/settings?api_key=' . $options['api_key_private'],  [
			'body'        => json_encode( $options ), //phpcs:ignore
			'headers'     => [
				'technology'   => 'wordpress',
				'Content-Type' => 'application/json; charset=utf-8',
			],
		] );

		if ( is_wp_error( $response ) ) {
			return [
				'success' => false,
			];
		}

		return [
			'success' => true,
			'result'  => json_decode( $response['body'], true ),
		];
	}

	/**
	 * @since 2.0
	 * @param string $key
	 * @return array
	 */
	public function get_option( $key ) {
		$options = $this->get_options();

		if ( ! array_key_exists( $key, $options ) ) {
			return null;
		}

		return $options[ $key ];
	}

	/**
	 * @since 3.0.0
	 * @param string $key
	 * @return string|boolean|int
	 */
	public function get_option_button( $key ) {
		$options = $this->get_options();

		if ( array_key_exists( $key, $options['custom_settings']['button_style'] ) ) {
			return $options['custom_settings']['button_style'][ $key ];
		}

		switch ( $key ) {
			case 'flag_type':
				$key = 'type_flags';
				break;
			case 'fullname':
				$key = 'is_fullname';
				break;
		}

		// Retrocompatibility v2
		$options = wp_parse_args( get_option( WEGLOT_SLUG ), $this->get_options_default() );
		if ( ! array_key_exists( $key, $options ) ) {
			return null;
		}

		return $options[ $key ];
	}

	/**
	 * @since 2.0
	 * @return array
	 */
	public function get_exclude_blocks() {
		$exclude_blocks     = $this->get_option( 'exclude_blocks' );
		$exclude_blocks[]   = '#wpadminbar';
		$exclude_blocks[]   = '#query-monitor';
		$exclude_blocks[]   = '.menu-item-weglot';
		$exclude_blocks[]   = '.menu-item-weglot a';

		return apply_filters( 'weglot_exclude_blocks', $exclude_blocks );
	}

	/**
	 * @since 2.0.4
	 * @return array
	 */
	public function get_destination_languages() {
		$destination_languages     = $this->get_option( 'destination_language' );

		return apply_filters( 'weglot_destination_languages', $destination_languages );
	}

	/**
	 * @since 2.0
	 * @return array
	 */
	public function get_exclude_urls() {
		$exclude_urls     = $this->get_option( 'excluded_paths' );
		$exclude_urls[]   = '/wp-login.php';
		$exclude_urls[]   = '/sitemaps_xsl.xsl';
		$exclude_urls[]   = '/sitemaps.xml';
		$exclude_urls[]   = 'wp-comments-post.php';
		$exclude_urls[]   = '/ct_template'; // Compatibility Oxygen

		return apply_filters( 'weglot_exclude_urls', $exclude_urls );
	}

	/**
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_css_custom_inline() {
		return apply_filters( 'weglot_css_custom_inline', $this->get_option( 'override_css' ) );
	}

	/**
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_flag_css() {
		return apply_filters( 'weglot_flag_css', $this->get_option( 'flag_css' ) );
	}


	/**
	 * @since 2.0
	 * @param array $options
	 * @return Option_Service_Weglot
	 */
	public function set_options( $options ) {
		update_option( WEGLOT_SLUG, $options );
		return $this;
	}

	/**
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return Option_Service_Weglot
	 */
	public function set_option_by_key( $key, $value ) {
		$options         = $this->get_options();
		$options[ $key ] = $value;
		$this->set_options( $options );
		return $this;
	}
}
