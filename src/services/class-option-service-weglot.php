<?php

namespace WeglotWP\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Morphism\Morphism;
use WeglotWP\Models\Schema_Option_V3;

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
		'api_key'                    => '',
		'language_from'              => [
			'code' => 'en',
		],
		'language_to'                      => [],
		'autoswitch'                       => false,
		'autoswitch_fallback'              => 'en',
		'exclude_blocks'                   => [],
		'exclude_urls'                     => [],
		'translate_email'                  => false,
		'translate_amp'                    => false,
		'translate_search'                 => false,
		'button_style'                     => [
			'fullname'    => false,
			'withname'    => true,
			'is_dropdown' => true,
			'with_flags'  => true,
			'flag_type'   => 0,
			'custom_css'  => '',
		],
		'has_first_settings'               => true,
		'show_box_first_settings'          => false,
		'rtl_ltr_style'                    => '',
		'allowed'                          => true,
		'active_wc_reload'                 => false,
		'custom_urls'                      => [],
		'flag_css'                         => '',
		'menu_switcher'                    => [],
		'active_search'                    => false,
	];


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
	 * @since 2.0
	 * @version 2.4.0
	 * @return array
	 */
	public function get_options() {
		$file_options = json_decode( file_get_contents( trailingslashit( WEGLOT_DIR ) . 'settings-example.json' ), true );
		// var_dump($file_options);
		// var_dump(get_option( WEGLOT_SLUG ));
		// die;
		// Morphism::setMapper('Schema_Option_V3', Schema_Option_V3::get_schema_switch_option_to_v3());
		// $result = Morphism::map('Schema_Option_V3', $data);
		// die;
		$options = wp_parse_args( $file_options, $this->get_options_default() );

		if ( empty( $options['menu_switcher'] ) ) {
			$menu_options_services     = weglot_get_service( 'Menu_Options_Service_Weglot' );
			$options['menu_switcher']  = $menu_options_services->get_options_default();
		}

		return apply_filters( 'weglot_get_options', $options );
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
		$exclude_urls     = $this->get_option( 'exclude_urls' );
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
