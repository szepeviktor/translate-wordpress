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
		$this->option_services      = weglot_get_service( 'Option_Service_Weglot' );
		$this->request_url_services = weglot_get_service( 'Request_Url_Service_Weglot' );
		$this->language_services    = weglot_get_service( 'Language_Service_Weglot' );
		$this->amp_services         = weglot_get_service( 'Amp_Service_Weglot' );
		$this->custom_url_services  = weglot_get_service( 'Custom_Url_Service_Weglot' );
	}

	/**
	 * @since 2.3.0
	 * @return string
	 */
	public function get_flag_class() {
		$options    = $this->option_services->get_options();
		$type_flags = $options['type_flags'];
		$with_flags = $options['with_flags'];

		$flag_class = $with_flags ? 'weglot-flags ' : '';
		$flag_class .= '0' === $type_flags ? '' : 'flag-' . $type_flags . ' ';

		return apply_filters( 'weglot_get_flag_class', $flag_class );
	}

	/**
	 * @since 2.3.0
	 *
	 * @param LanguageEntry $language_entry
	 * @return string
	 */
	public function get_name_with_language_entry( $language_entry ) {
		$options     = $this->option_services->get_options();
		$with_name   = $options['with_name'];
		$is_fullname = $options['is_fullname'];
		$name        = '';

		if ( $with_name ) {
			$name = ( $is_fullname ) ? $language_entry->getLocalName() : strtoupper( $language_entry->getIso639() );
		}

		return apply_filters( 'weglot_get_name_with_language_entry', $name, $language_entry );
	}

	/**
	 * @since 2.3.0
	 *
	 * @return string
	 */
	public function get_class_dropdown() {
		$options              = $this->option_services->get_options();
		$is_dropdown          = $options['is_dropdown'];
		$class                = $is_dropdown ? 'weglot-dropdown ' : 'weglot-inline ';

		return apply_filters( 'weglot_get_class_dropdown', $class );
	}



	/**
	 * Get html button switcher
	 *
	 * @since 2.0
	 * @version 2.3.0
	 * @return string
	 * @param string $add_class
	 */
	public function get_html( $add_class = '' ) {
		$weglot_url           = $this->request_url_services->get_weglot_url();
		$amp_regex            = $this->amp_services->get_regex( true );
		$destination_language = weglot_get_destination_languages();
		$original_language    = weglot_get_original_language();
		$current_language     = $this->request_url_services->get_current_language( false );

		if ( weglot_get_translate_amp_translation() && preg_match( '#' . $amp_regex . '#', $weglot_url->getUrl() ) === 1 ) {
			$add_class .= ' weglot-invert';
		}

		$flag_class  = $this->get_flag_class();
		$class_aside = $this->get_class_dropdown();

		$button_html = sprintf( '<!--Weglot %s-->', WEGLOT_VERSION );
		$button_html .= sprintf( "<aside data-wg-notranslate class='country-selector %s'>", $class_aside . $add_class );

		if ( ! empty( $original_language ) && ! empty( $destination_language ) ) {
			$current_language_entry = $this->language_services->get_current_language_entry_from_key( $current_language );
			$name                   = $this->get_name_with_language_entry( $current_language_entry );

			$uniq_id = 'wg' . uniqid( strtotime( 'now' ) ) . rand( 1, 1000 );
			$button_html .= sprintf( '<input id="%s" class="weglot_choice" type="checkbox" name="menu"/><label for="%s" class="wgcurrent wg-li %s" data-code-language="%s"><span>%s</span></label>', $uniq_id, $uniq_id, $flag_class . $current_language, $current_language_entry->getIso639(), $name );

			$button_html .= '<ul>';

			array_unshift( $destination_language, $original_language );

			foreach ( $destination_language as $key => $key_code ) {
				if ( $key_code === $current_language ) {
					continue;
				}

				$button_html .= sprintf( '<li class="wg-li %s" data-code-language="%s">', $flag_class . $key_code, $key_code );

				$current_language_entry  = $this->language_services->get_current_language_entry_from_key( $key_code );
				$name                    = $this->get_name_with_language_entry( $current_language_entry );

				$link_button = $this->custom_url_services->get_link_button_with_key_code( $key_code );

				$button_html .= sprintf(
					'<a data-wg-notranslate href="%s">%s</a>',
					$link_button,
					$name
				);

				$button_html .= '</li>';
			}

			$button_html .= '</ul>';
		}

		$button_html .= '</aside>';

		return apply_filters( 'weglot_button_html', $button_html, $add_class );
	}
}
