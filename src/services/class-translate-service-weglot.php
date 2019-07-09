<?php

namespace WeglotWP\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Weglot\Client\Api\Exception\ApiError;
use WeglotWP\Helpers\Helper_Json_Inline_Weglot;
use WeglotWP\Helpers\Helper_Keys_Json_Weglot;


/**
 * @since 2.3.0
 */
class Translate_Service_Weglot {


	/**
	 * @since 2.3.0
	 */
	public function __construct() {
		$this->option_services                  = weglot_get_service( 'Option_Service_Weglot' );
		$this->request_url_services             = weglot_get_service( 'Request_Url_Service_Weglot' );
		$this->replace_url_services             = weglot_get_service( 'Replace_Url_Service_Weglot' );
		$this->replace_link_services            = weglot_get_service( 'Replace_Link_Service_Weglot' );
		$this->parser_services                  = weglot_get_service( 'Parser_Service_Weglot' );
		//$this->wc_active_services               = weglot_get_service( 'Wc_Active' );
		//$this->ninja_active_services            = weglot_get_service( 'Ninja_Active_Weglot' );
		//$this->caldera_active_services          = weglot_get_service( 'Caldera_Active' );
		//$this->wpforms_active_services          = weglot_get_service( 'WPForms_Active' );
		$this->other_translate_services         = weglot_get_service( 'Other_Translate_Service_Weglot' );
		//$this->translate_json_service           = weglot_get_service( 'Translate_Json_Service' );
		$this->generate_switcher_service        = weglot_get_service( 'Generate_Switcher_Service_Weglot' );
		//$this->translate_json_ld_services       = weglot_get_service( 'Translate_Json_Ld' );
	}


	/**
	 * @since 2.3.0
	 * @return void
	 */
	public function weglot_translate() {
		$this->set_original_language( weglot_get_original_language() );
		$this->set_current_language( $this->request_url_services->get_current_language() );

		ob_start( [ $this, 'weglot_treat_page' ] );
	}

	/**
	 * @since 2.3.0
	 * @param string $current_language
	 */
	public function set_current_language( $current_language ) {
		$this->current_language = $current_language;
		return $this;
	}

	/**
	 * @since 2.3.0
	 * @param string $original_language
	 */
	public function set_original_language( $original_language ) {
		$this->original_language = $original_language;
		return $this;
	}

	/**
	 * @see weglot_init / ob_start
	 * @since 2.3.0
	 * @param string $content
	 * @return string
	 */
	public function weglot_treat_page( $content ) {
		$this->set_current_language( $this->request_url_services->get_current_language() ); // Need to reset

		$allowed                  = $this->option_services->get_option( 'allowed' );
		// Choose type translate
		$type     = ( Helper_Json_Inline_Weglot::is_json( $content ) ) ? 'json' : 'html';
		$type     = apply_filters( 'weglot_type_treat_page', $type );

		if ( ! $allowed ) {
			$content = $this->weglot_render_dom( $content );
			if ( 'json' === $type || wp_doing_ajax() ) {
				return $content;
			}

			return $content . '<!--Not allowed-->';
		}

		$active_translation = apply_filters( 'weglot_active_translation', true );

		// No need to translate but prepare new dom with button
		if ( $this->current_language === $this->original_language || ! $active_translation ) {
			return $this->weglot_render_dom( $content );
		}

		$parser = $this->parser_services->get_parser();

		try {
			switch ( $type ) {
				case 'json':
                    $translated_content = $parser->translate( $content, $this->original_language, $this->current_language );
                    $translated_content    = apply_filters( 'weglot_json_treat_page', $translated_content );
                    return $translated_content;
				case 'html':
					$translated_content = $parser->translate( $content, $this->original_language, $this->current_language ); // phpcs:ignore
					$translated_content = apply_filters( 'weglot_html_treat_page', $translated_content );
					return $this->weglot_render_dom( $translated_content );
				default:
					$name_filter = sprintf( 'weglot_%s_treat_page', $type );
					return apply_filters( $name_filter, $content, $parser, $this->original_language, $this->current_language );

			}
		} catch ( ApiError $e ) {
			if ( 'json' !== $type ) {
				$content .= '<!--Weglot error API : ' . $this->remove_comments( $e->getMessage() ) . '-->';
			}
			if ( strpos( $e->getMessage(), 'NMC' ) !== false ) {
				$this->option_services->set_option_by_key( 'allowed', false );
			}
			return $content;
		} catch ( \Exception $e ) {
			if ( 'json' !== $type ) {
				$content .= '<!--Weglot error : ' . $this->remove_comments( $e->getMessage() ) . '-->';
			}
			return $content;
		}
	}


	/**
	 * @since 2.3.0
	 *
	 * @param string $html
	 * @return string
	 */
	private function remove_comments( $html ) {
		return preg_replace( '/<!--(.*)-->/Uis', '', $html );
	}


	/**
	 * @since 2.3.0
	 * @param string $dom
	 * @return string
	 */
	public function weglot_render_dom( $dom ) {
		$dom = $this->generate_switcher_service->generate_switcher_from_dom( $dom );

		// We only need this on translated page
		if ( $this->current_language !== $this->original_language ) {
			$dom = $this->replace_url_services->replace_link_in_dom( $dom );
		}

		return apply_filters( 'weglot_render_dom', $dom );
	}
}



