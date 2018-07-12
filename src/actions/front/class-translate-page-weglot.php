<?php

namespace WeglotWP\Actions\Front;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WeglotWP\Models\Hooks_Interface_Weglot;

use Weglot\Client\Api\Enum\BotType;
use Weglot\Client\Client;
use Weglot\Util\Server;
use Weglot\Client\Api\Exception\ApiError;

/**
 * Translate page
 *
 * @since 2.0
 */
class Translate_Page_Weglot implements Hooks_Interface_Weglot {

	/**
	 * @since 2.0
	 */
	public function __construct() {
		$this->option_services               = weglot_get_service( 'Option_Service_Weglot' );
		$this->button_services               = weglot_get_service( 'Button_Service_Weglot' );
		$this->request_url_services          = weglot_get_service( 'Request_Url_Service_Weglot' );
		$this->redirect_services             = weglot_get_service( 'Redirect_Service_Weglot' );
		$this->replace_url_services          = weglot_get_service( 'Replace_Url_Service_Weglot' );
		$this->replace_link_services         = weglot_get_service( 'Replace_Link_Service_Weglot' );
		$this->language_services             = weglot_get_service( 'Language_Service_Weglot' );
		$this->parser_services               = weglot_get_service( 'Parser_Service_Weglot' );
		$this->wc_active_services            = weglot_get_service( 'WC_Active_Weglot' );
	}


	/**
	 * @see Hooks_Interface_Weglot
	 *
	 * @since 2.0
	 * @return void
	 */
	public function hooks() {
		if ( is_admin() ) {
			return;
		}

		$this->api_key            = $this->option_services->get_option( 'api_key' );

		if ( ! $this->api_key ) {
			return;
		}

		if (
			null === $this->request_url_services->get_current_language() ||
			! $this->request_url_services->is_translatable_url()
		) {
			return;
		}

		add_action( 'init', [ $this, 'weglot_init' ] );
		add_action( 'wp_head', [ $this, 'weglot_href_lang' ] );
	}

	/**
	 * @see init
	 * @since 2.0
	 *
	 * @return void
	 */
	public function weglot_init() {
		do_action( 'weglot_init_start' );

		$this->noredirect         = false;
		$this->original_language  = $this->option_services->get_option( 'original_language' );
		if ( empty( $this->original_language ) ) {
			return;
		}

		$this->current_language   = $this->request_url_services->get_current_language();

		$full_url = $this->request_url_services->get_full_url();
		// URL not eligible
		if ( ! $this->request_url_services->is_eligible_url( $full_url ) ) {
			return;
		}

		$active_translation = apply_filters( 'weglot_active_translation', true );
		// Default : yes
		if ( ! $active_translation ) {
			return;
		}

		$this->redirect_services->verify_no_redirect();
		$this->check_need_to_redirect();
		$this->prepare_request_uri();
		$this->prepare_rtl_language();

		do_action( 'weglot_init_before_translate_page' );

		ob_start( [ $this, 'weglot_treat_page' ] );
	}



	/**
	 * @since 2.0
	 *
	 * @param array $array
	 * @param string $to
	 * @param Parser $parser
	 * @return void
	 */
	public function translate_array( $array ) {
		$array_not_ajax_html = apply_filters( 'weglot_array_not_ajax_html', [ 'redirecturl', 'url' ] );

		foreach ( $array as $key => $val ) {
			if ( is_array( $val ) ) {
				$array[ $key ] = $this->translate_array( $val );
			} else {
				if ( $this->is_ajax_html( $val ) ) {
					$parser                   = $this->parser_services->get_parser();
					$array[$key]              = $parser->translate( $val, $this->original_language, $this->current_language ); //phpcs:ignore
				} elseif ( in_array( $key,  $array_not_ajax_html ) ) { //phpcs:ignore
					$array[$key] = $this->replace_link_services->replace_url( $val ); //phpcs:ignore
				}
			}
		}

		return $array;
	}

	/**
	 * @since 2.0
	 *
	 * @param string $string
	 * @return boolean
	 */
	public function is_ajax_html( $string ) {
		$preg_match_ajax_html = apply_filters( 'weglot_is_ajax_html_regex',  '/<(a|div|span|p|i|aside|input|textarea|select|h1|h2|h3|h4|meta|button|form|li|strong|ul|option)/' );
		$result               = preg_match_all( $preg_match_ajax_html, $string, $m, PREG_PATTERN_ORDER );

		if ( isset( $string[0] ) && '{' !== $string[0] && $result && $result >= 1 ) {
			return true;
		} else {
			return false;
		}
	}


	/**
	 * @since 2.0
	 *
	 * @return void
	 */
	public function check_need_to_redirect() {
		if (
			! wp_doing_ajax() && // no ajax
			$this->request_url_services->get_weglot_url()->getBaseUrl() === '/' && // front_page
			! $this->redirect_services->get_no_redirect() && // No force redirect
			! Server::detectBot( $_SERVER ) !== BotType::OTHER && //phpcs:ignore
			$this->option_services->get_option( 'auto_redirect' ) // have option redirect
		) {
			$this->redirect_services->auto_redirect();
		}
	}

	/**
	 * @since 2.0
	 *
	 * @return void
	 */
	public function prepare_request_uri() {
		// Use for good process on URL
		$_SERVER['REQUEST_URI'] = str_replace(
			'/' . $this->current_language . '/',
			'/',
			$_SERVER['REQUEST_URI'] //phpcs:ignore
		);
	}

	/**
	 * @since 2.0
	 *
	 * @return void
	 */
	public function prepare_rtl_language() {
		if ( $this->request_url_services->is_language_rtl( $this->current_language ) ) {
			$GLOBALS['text_direction'] = 'rtl';
		} else {
			$GLOBALS['text_direction'] = 'ltr';
		}
	}

	/**
	 * @see weglot_init / ob_start
	 * @since 2.0
	 * @param string $content
	 * @return string
	 */
	public function weglot_treat_page( $content ) {
		$allowed = $this->option_services->get_option( 'allowed' );

		if ( ! $allowed ) {
			$content = $this->weglot_render_dom( $content );
			return $content . '<!--Not allowed-->';
		}

		// No need to translate but prepare new dom with button
		if ( $this->current_language === $this->original_language ) {
			return $this->weglot_render_dom( $content );
		}

		$parser = $this->parser_services->get_parser();

		// Choose type translate
		$type = ( $this->is_json( $content ) ) ? 'json' : 'html';
		$type = apply_filters( 'weglot_type_treat_page', $type );

		try {
			switch ( $type ) {
				case 'json':
					$json       = json_decode( $content, true );
					$content    = $this->translate_array( $json );
					return wp_json_encode( $content );
					break;
				case 'html':
					$translated_content = $parser->translate( $content, $this->original_language, $this->current_language ); // phpcs:ignore

					if ( $this->wc_active_services->is_active() ) {
						// @TODO : Improve this with multiple service
						$translated_content = weglot_get_service( 'WC_Translate_Weglot' )->translate_words( $translated_content );
					}
					return $this->weglot_render_dom( $translated_content );
					break;
				default:
					$name_filter = sprintf( 'weglot_%s_treat_page', $type );
					return apply_filters( $name_filter, $content, $parser, $this->original_language, $this->current_language );
					break;

			}
		} catch ( ApiError $e ) {
			$content .= '<!--Weglot error API : ' . $this->remove_comments( $e->getMessage() ) . '-->';
			if ( strpos( $e->getMessage(), 'NMC' ) !== false ) {
				$this->option_services->set_option_by_key( 'allowed', false );
			}
			return $content;
		} catch ( \Exception $e ) {
			$content .= '<!--Weglot error : ' . $this->remove_comments( $e->getMessage() ) . '-->';
			return $content;
		}
	}

	/**
	 * @since 2.0
	 *
	 * @param string $html
	 * @return string
	 */
	private function remove_comments( $html ) {
		return preg_replace( '/<!--(.*)-->/Uis', '', $html );
	}

	/**
	 * @since 2.0
	 *
	 * @param string $string
	 * @return boolean
	 */
	public function is_json( $string ) {
		return is_string( $string ) && is_array( json_decode( $string, true ) ) && ( JSON_ERROR_NONE === json_last_error() ) ? true : false;
	}

	/**
	 * @since 2.0
	 * @param string $dom
	 * @return string
	 */
	public function weglot_add_button_html( $dom ) {
		$options            = $this->option_services->get_options();

		// Place the button if we see markup
		if ( strpos( $dom, '<div id="weglot_here"></div>' ) !== false ) {
			$button_html  = $this->button_services->get_html( 'weglot-shortcode' );
			$dom          = str_replace( '<div id="weglot_here"></div>', $button_html, $dom );
		}

		if ( strpos( $dom, '[weglot_menu' ) !== false ) {
			$languages_configured = $this->language_services->get_languages_configured();
			$protocol             = 'http://';
			$is_ssl               = is_ssl();
			if ( $is_ssl ) {
				$protocol = 'https://';
			}

			$is_fullname  = $options['is_fullname'];
			$with_name    = $options['with_name'];

			foreach ( $languages_configured as $language ) {
				$shortcode_title        = sprintf( '[weglot_menu_title-%s]', $language->getIso639() );
				$shortcode_title_html   = str_replace( '[', '%5B', $shortcode_title );
				$shortcode_title_html   = str_replace( ']', '%5D', $shortcode_title_html );
				$shortcode_url          = sprintf( '[weglot_menu_current_url-%s]', $language->getIso639() );
				$shortcode_url_html     = str_replace( '[', '%5B', $shortcode_url );
				$shortcode_url_html     = str_replace( ']', '%5D', $shortcode_url_html );

				$url                  = $this->request_url_services->get_weglot_url();

				$name = '';
				if ( $with_name ) {
					$name = ( $is_fullname ) ? $language->getLocalName() : strtoupper( $language->getIso639() );
				}

				$dom                  = str_replace( $shortcode_title, $name, $dom );
				$dom                  = str_replace( $shortcode_title_html, $name, $dom );

				$link_menu = $url->getForLanguage( $language->getIso639() );
				if ( weglot_has_auto_redirect() && strpos( $link_menu, 'no_lredirect' ) === false && ( is_home() || is_front_page() ) ) {
					$link_menu .= '?no_lredirect=true';
				}

				$dom                  = str_replace( $protocol . $shortcode_url, $link_menu, $dom );
				$dom                  = str_replace( $protocol . $shortcode_url_html, $link_menu, $dom );
			}

			$dom .= sprintf( '<!--Weglot %s-->', WEGLOT_VERSION );
		}

		// Place the button if not in the page
		if ( strpos( $dom, sprintf( '<!--Weglot %s-->', WEGLOT_VERSION ) ) === false ) {
			$button_html  = $this->button_services->get_html( 'weglot-default' );
			$dom          = ( strpos( $dom, '</body>' ) !== false) ? str_replace( '</body>', $button_html . ' </body>', $dom ) : str_replace( '</footer>', $button_html . ' </footer>', $dom );
		}

		return $dom;
	}

	/**
	 * @since 2.0
	 * @param string $dom
	 * @return string
	 */
	public function weglot_replace_link( $dom ) {
		$dom = $this->replace_url_services->modify_link( '/<a([^\>]+?)?href=(\"|\')([^\s\>]+?)(\"|\')([^\>]+?)?>/', $dom, 'a' );
		$dom = $this->replace_url_services->modify_link( '/<([^\>]+?)?data-link=(\"|\')([^\s\>]+?)(\"|\')([^\>]+?)?>/', $dom, 'datalink' );
		$dom = $this->replace_url_services->modify_link( '/<([^\>]+?)?data-url=(\"|\')([^\s\>]+?)(\"|\')([^\>]+?)?>/', $dom, 'dataurl' );
		$dom = $this->replace_url_services->modify_link( '/<([^\>]+?)?data-cart-url=(\"|\')([^\s\>]+?)(\"|\')([^\>]+?)?>/', $dom, 'datacart' );
		$dom = $this->replace_url_services->modify_link( '/<form([^\>]+?)?action=(\"|\')([^\s\>]+?)(\"|\')/', $dom, 'form' );
		$dom = $this->replace_url_services->modify_link( '/<link rel="canonical"(.*?)?href=(\"|\')([^\s\>]+?)(\"|\')/', $dom, 'canonical' );
		$dom = $this->replace_url_services->modify_link( '/<link rel="amphtml"(.*?)?href=(\"|\')([^\s\>]+?)(\"|\')/', $dom, 'amp' );
		$dom = $this->replace_url_services->modify_link( '/<meta property="og:url"(.*?)?content=(\"|\')([^\s\>]+?)(\"|\')/', $dom, 'meta' );

		return $dom;
	}

	/**
	 * @since 2.0
	 * @param string $dom
	 * @return string
	 */
	public function weglot_render_dom( $dom ) {
		$dom = $this->weglot_add_button_html( $dom );

		// We only need this on translated page
		if ( $this->current_language !== $this->original_language ) {
			$dom = $this->weglot_replace_link( $dom );

			$dom = preg_replace( '/<html (.*?)?lang=(\"|\')(\S*)(\"|\')/', '<html $1lang=$2' . $this->current_language . '$4', $dom );
			$dom = preg_replace( '/property="og:locale" content=(\"|\')(\S*)(\"|\')/', 'property="og:locale" content=$1' . $this->current_language . '$3', $dom );
		}
		return apply_filters( 'weglot_render_dom', $dom );
	}

	/**
	 * @see wp_head
	 * @since 2.0
	 * @return void
	 */
	public function weglot_href_lang() {
		$href_lang_tags = $this->request_url_services->get_weglot_url()->generateHrefLangsTags(); //phpcs:ignore
		echo apply_filters( 'weglot_href_lang', $href_lang_tags ); //phpcs:ignore
	}
}


