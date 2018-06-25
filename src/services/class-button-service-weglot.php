<?php

namespace WeglotWP\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Button services
 *
 * @since 2.0
 */
class Button_Service_Weglot {

	/**
	 * @since 2.0
	 */
	public function __construct() {
		$this->option_services           = weglot_get_service( 'Option_Service_Weglot' );
		$this->request_url_services      = weglot_get_service( 'Request_Url_Service_Weglot' );
		$this->language_services         = weglot_get_service( 'Language_Service_Weglot' );
	}


	/**
	 * Use for wp_ses
	 * @since 2.0
	 * @return array
	 */
	public function get_allowed_tags() {
		return [
			'a' => [
				'href'                => [],
				'title'               => [],
				'onclick'             => [],
				'target'              => [],
				'data-wg-notranslate' => [],
				'class'               => [],
			],
			'div' => [
				'class'               => [],
				'data-wg-notranslate' => [],
			],
			'aside' => [
				'onclick'             => [],
				'class'               => [],
				'data-wg-notranslate' => [],
			],
			'ul'    => [
				'class'               => [],
				'data-wg-notranslate' => [],
			],
			'li'    => [
				'class'               => [],
				'data-wg-notranslate' => [],
			],
		];
	}

	/**
	 * Get html button switcher
	 *
	 * @since 2.0
	 * @return string
	 * @param string $add_class
	 */
	public function get_html( $add_class = '' ) {
		$options                          = $this->option_services->get_options();
		$is_fullname                      = $options['is_fullname'];
		$with_name                        = $options['with_name'];
		$is_dropdown                      = $options['is_dropdown'];
		$with_flags                       = $options['with_flags'];
		$type_flags                       = $options['type_flags'];
		$weglot_url                       = $this->request_url_services->get_weglot_url();

		$destination_language             = $options['destination_language'];
		$original_language                = $options['original_language'];
		$current_language                 = $this->request_url_services->get_current_language();

		$flag_class                       = $with_flags ? 'weglot-flags ' : '';
		$flag_class .= '0' === $type_flags ? '' : 'flag-' . $type_flags . ' ';

		$tag                              = $is_dropdown ? 'div' : 'li';
		$class_aside                      = $is_dropdown ? 'weglot-dropdown ' : 'weglot-inline ';

		$languages = $this->language_services->get_languages_available();

		$button_html = sprintf( '<!--Weglot %s-->', WEGLOT_VERSION );
		$button_html .= sprintf( "<aside class='weglot-selector %s'>", $class_aside . $add_class );

		$name = '';
		if ( $with_name ) {
			$name = ( $is_fullname ) ? $languages[ $current_language ]->getEnglishName() : strtoupper( $languages[ $current_language ]->getIso639() );
		}

		$button_html .= sprintf( '<input id="weglot_choice" type="checkbox" name="menu"/><label for="weglot_choice" class="%s">%s</label>', $flag_class . $current_language, $name );

		$button_html .= '<ul>';

		array_unshift( $destination_language, $original_language );

		foreach ( $destination_language as $key => $key_code ) {
			if ( $key_code === $current_language ) {
				continue;
			}

			$name = '';
			if ( $with_name ) {
				$name = ( $is_fullname ) ? $languages[ $key_code ]->getEnglishName() : strtoupper( $languages[ $key_code ]->getIso639() );
			}

			$button_html .= sprintf( '<li class="%s">', $flag_class . $key_code );

			$button_html .= sprintf(
				'<a data-wg-notranslate href="%s">%s</a>',
				$weglot_url->getForLanguage( $key_code ),
				$name
			);

			$button_html .= '</li>';
		}

		$button_html .= '</ul>';
		$button_html .= '</aside>';

		return apply_filters( 'weglot_button_html', $button_html );
	}
}
