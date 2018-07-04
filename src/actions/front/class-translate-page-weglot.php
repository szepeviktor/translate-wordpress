<?php

namespace WeglotWP\Actions\Front;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WeglotWP\Models\Hooks_Interface_Weglot;


use Weglot\Client\Api\Enum\BotType;
use Weglot\Client\Client;
use Weglot\Parser\Parser;
use Weglot\Util\Url;
use Weglot\Util\Server;
use Weglot\Parser\ConfigProvider\ServerConfigProvider;

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
		$this->option_services            = weglot_get_service( 'Option_Service_Weglot' );
		$this->button_services            = weglot_get_service( 'Button_Service_Weglot' );
		$this->request_url_services       = weglot_get_service( 'Request_Url_Service_Weglot' );
		$this->redirect_services          = weglot_get_service( 'Redirect_Service_Weglot' );
		$this->replace_url_services       = weglot_get_service( 'Replace_Url_Service_Weglot' );
		$this->replace_link_services      = weglot_get_service( 'Replace_Link_Service_Weglot' );
		$this->language_services          = weglot_get_service( 'Language_Service_Weglot' );
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

		$this->config    = new ServerConfigProvider();
		$this->client    = new Client( $this->api_key );

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
					$parser                   = new Parser( $this->client, $this->config );
					$array[$key]              = $parser->translate( $val, $this->original_language, $this->current_language ); //phpcs:ignore
				} elseif ( in_array( $key,  $array_not_ajax_html ) ) {
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
		// No need to translate but prepare new dom with button
		if ( $this->current_language === $this->original_language ) {
			return $this->weglot_render_dom( $content );
		}

		$exclude_blocks = $this->option_services->get_exclude_blocks();

		$parser             = new Parser( $this->client, $this->config, $exclude_blocks );

		$full_url = $this->request_url_services->get_full_url();

		$type = ( $this->is_json( $content ) ) ? 'json' : 'html';
		$type = apply_filters( 'weglot_type_treat_page', $type );

		switch ( $type ) {
			case 'json':
				$json       = json_decode( $content, true );
				$content    = $this->translate_array( $json );
				return wp_json_encode( $content );
				break;
			case 'html':
				$translated_content = $parser->translate( $content, $this->original_language, $this->current_language ); // phpcs:ignore
				return $this->weglot_render_dom( $translated_content );
				break;
			default:
				$name_filter = sprintf( 'weglot_%s_treat_page', $type );
				return apply_filters( $name_filter, $content, $parser, $this->original_language, $this->current_language );
				break;

		}
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
				$shortcode_title      = sprintf( '[weglot_menu_title-%s]', $language->getIso639() );
				$shortcode_url        = sprintf( '[weglot_menu_current_url-%s]', $language->getIso639() );

				$url                  = $this->request_url_services->get_weglot_url();

				$name = '';
				if ( $with_name ) {
					$name = ( $is_fullname ) ? $language->getEnglishName() : strtoupper( $language->getIso639() );
				}

				$dom                  = str_replace( $shortcode_title, $name, $dom );
				$dom                  = str_replace( $protocol . $shortcode_url, $url->getForLanguage( $language->getIso639() ), $dom );
			}
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
		$dom = $this->replace_url_services->modify_link( '/<option (.*?)?(\"|\')([^\s\>]+?)(\"|\')(.*?)?>/', $dom, 'option' );
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
		$dom = $this->weglot_replace_link( $dom );

		$dom = preg_replace( '/<html (.*?)?lang=(\"|\')(\S*)(\"|\')/', '<html $1lang=$2' . $this->current_language . '$4', $dom );
		$dom = preg_replace( '/property="og:locale" content=(\"|\')(\S*)(\"|\')/', 'property="og:locale" content=$1' . $this->current_language . '$3', $dom );

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


