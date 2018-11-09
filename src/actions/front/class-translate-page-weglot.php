<?php

namespace WeglotWP\Actions\Front;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WeglotWP\Models\Hooks_Interface_Weglot;
use WeglotWP\Helpers\Helper_Post_Meta_Weglot;

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
		$this->option_services                = weglot_get_service( 'Option_Service_Weglot' );
		$this->button_services                = weglot_get_service( 'Button_Service_Weglot' );
		$this->request_url_services           = weglot_get_service( 'Request_Url_Service_Weglot' );
		$this->redirect_services              = weglot_get_service( 'Redirect_Service_Weglot' );
		$this->replace_url_services           = weglot_get_service( 'Replace_Url_Service_Weglot' );
		$this->replace_link_services          = weglot_get_service( 'Replace_Link_Service_Weglot' );
		$this->language_services              = weglot_get_service( 'Language_Service_Weglot' );
		$this->parser_services                = weglot_get_service( 'Parser_Service_Weglot' );
		$this->wc_active_services             = weglot_get_service( 'WC_Active_Weglot' );
		$this->other_translate_services       = weglot_get_service( 'Other_Translate_Service_Weglot' );
		$this->generate_switcher_service      = weglot_get_service( 'Generate_Switcher_Service_Weglot' );
	}

	/**
	 * @see Hooks_Interface_Weglot
	 *
	 * @since 2.0
	 * @return void
	 */
	public function hooks() {
		if ( is_admin() && ( ! wp_doing_ajax() || $this->no_translate_action_ajax() ) ) {
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

		$this->current_language   = $this->request_url_services->get_current_language();

		$role_private_mode = 'administrator'; // apply_filters it not possible
		$private_mode      = $this->option_services->get_option( 'private_mode' );
		if ( $private_mode && ! current_user_can( $role_private_mode ) ) {
			return;
		}

		$this->prepare_request_uri();
		$this->prepare_rtl_language();

		add_action( 'init', [ $this, 'weglot_init' ], 11 );
		add_action( 'wp_head', [ $this, 'weglot_href_lang' ] );
	}

	/**
	 * @since 2.1.1
	 *
	 * @return boolean
	 */
	protected function no_translate_action_ajax() {
		$action_ajax_no_translate = apply_filters( 'weglot_ajax_no_translate', [
			'add-menu-item', // WP Core
			'query-attachments', // WP Core
			'avia_ajax_switch_menu_walker', // Enfold theme
			'query-themes', // WP Core
			'wpestate_ajax_check_booking_valability_internal', // WP Estate theme
		] );

		if ( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['action'] ) && in_array( $_POST['action'], $action_ajax_no_translate ) ) { //phpcs:ignore
			return true;
		}

		return false;
	}

	/**
	 * @since 2.2.2
	 * @param string $original_language
	 */
	public function set_original_language( $original_language ) {
		$this->original_language = $original_language;
		return $this;
	}

	/**
	 * @see init
	 * @since 2.0
	 * @version 2.0.4
	 * @return void
	 */
	public function weglot_init() {
		do_action( 'weglot_init_start' );

		if ( $this->no_translate_action_ajax() ) {
			return;
		}

		$this->noredirect         = false;
		$this->original_language  = $this->option_services->get_option( 'original_language' );
		if ( empty( $this->original_language ) ) {
			return;
		}

		$full_url_no_language = $this->request_url_services->get_full_url_no_language();

		// URL not eligible
		if ( ! $this->request_url_services->is_eligible_url( $full_url_no_language ) ) {
			return;
		}

		$active_translation = apply_filters( 'weglot_active_translation_before_process', true );
		// Default : yes
		if ( ! $active_translation ) {
			return;
		}

		$this->redirect_services->verify_no_redirect();
		$this->check_need_to_redirect();

		do_action( 'weglot_init_before_translate_page' );

		if ( ! function_exists( 'curl_version' ) ) {
			return;
		}

		$active_translation = apply_filters( 'weglot_active_translation_before_treat_page', true );
		// Default : yes
		if ( ! $active_translation ) {
			return;
		}

		ob_start( [ $this, 'weglot_treat_page' ] );
	}



	/**
	 * @since 2.0
	 * @version 2.0.4
	 *
	 * @param array $array
	 * @return array
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
	 * Replace links for JSON translate
	 *
	 * @since 2.1.0
	 *
	 * @param array $array
	 * @return array
	 */
	public function replace_link_array( $array ) {
		$array_not_ajax_html = apply_filters( 'weglot_array_not_ajax_html', [ 'redirecturl', 'url' ] );

		foreach ( $array as $key => $val ) {
			if ( is_array( $val ) ) {
				$array[ $key ] = $this->replace_link_array( $val );
			} else {
				if ( $this->is_ajax_html( $val ) ) {
					$array[ $key ] = $this->weglot_replace_link( $val );
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
	 * @since 2.1.0
	 * @return void
	 */
	protected function request_uri_default() {
		$_SERVER['REQUEST_URI'] = str_replace(
			'/' . $this->request_url_services->get_current_language( false ) . '/',
			'/',
			$_SERVER['REQUEST_URI'] //phpcs:ignore
		);
		return;
	}

	/**
	 * @since 2.0
	 * @version 2.1.0
	 * @return void
	 */
	public function prepare_request_uri() {
		$original_language = weglot_get_original_language();
		$current_language  = $this->request_url_services->get_current_language( false );

		if ( $original_language === $current_language ) {
			return;
		}

		$request_without_language = array_values(array_filter( explode( '/', str_replace(
			'/' . $current_language . '/',
			'/',
			$_SERVER['REQUEST_URI'] //phpcs:ignore
		) ), 'strlen' ));

		$index_entries = count( $request_without_language ) - 1;
		if ( isset( $request_without_language[ $index_entries ] ) ) {
			$slug_in_work  = $request_without_language[ $index_entries ];
		}

		// Like is_home
		if ( empty( $request_without_language ) || ! isset( $slug_in_work ) ) {
			$this->request_uri_default();
			return;
		}

		$custom_urls = $this->option_services->get_option( 'custom_urls' );

		// No language configured
		if ( ! isset( $custom_urls[ $current_language ] ) ) {
			$this->request_uri_default();
			return;
		}

		$key_slug = array_search( $slug_in_work, $custom_urls[ $current_language ] ); //phpcs:ignore

		// No custom URL for this language with this slug
		if ( ! isset( $custom_urls[ $current_language ][ $slug_in_work ] ) && false === $key_slug ) {
			$this->request_uri_default();
			return;
		}

		// Custom URL exist but not good slug
		if ( ! isset( $custom_urls[ $current_language ][ $slug_in_work ] ) ) {
			return;
		}

		$_SERVER['REQUEST_URI'] = str_replace(
			'/' . $current_language . '/',
			'/',
			str_replace( $slug_in_work, $custom_urls[ $current_language ][ $slug_in_work ], $_SERVER['REQUEST_URI'] ) //phpcs:ignore
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
	 * @since 2.0.2
	 *
	 * Check if there are Weglot menu links and make sure there is the data-wg-notranslate
	 * @param string $content
	 * @return string
	 */
	public function fix_menu_link( $content ) {
		$content = preg_replace( '#<a([^\>]+?)?href="(http|https):\/\/\[weglot_#', '<a$1 data-wg-notranslate="true" href="$2://[weglot_', $content );

		return $content;
	}

	/**
	 * @see weglot_init / ob_start
	 * @since 2.0
	 * @version 2.0.4
	 * @param string $content
	 * @return string
	 */
	public function weglot_treat_page( $content ) {
		$this->current_language   = $this->request_url_services->get_current_language(); // Need to reset

		$allowed                  = $this->option_services->get_option( 'allowed' );
		// Choose type translate
		$type     = ( $this->is_json( $content ) ) ? 'json' : 'html';
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
					$json       = \json_decode( $content, true );
					$content    = $this->translate_array( $json );
					$content    = $this->replace_link_array( $content );
					$content    = apply_filters( 'weglot_json_treat_page', $content );

					return wp_json_encode( $content );
					break;
				case 'html':
					$content            = $this->fix_menu_link( $content );
					$translated_content = $parser->translate( $content, $this->original_language, $this->current_language ); // phpcs:ignore

					if ( $this->wc_active_services->is_active() ) {
						// @TODO : Improve this with multiple service
						$translated_content = weglot_get_service( 'WC_Translate_Weglot' )->translate_words( $translated_content );
					}

					$translated_content = $this->other_translate_services->translate_words( $translated_content );

					$translated_content = apply_filters( 'weglot_html_treat_page', $translated_content );

					return $this->weglot_render_dom( $translated_content );
					break;
				default:
					$name_filter = sprintf( 'weglot_%s_treat_page', $type );
					return apply_filters( $name_filter, $content, $parser, $this->original_language, $this->current_language );
					break;

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
		return is_string( $string ) && is_array( \json_decode( $string, true ) ) && ( JSON_ERROR_NONE === \json_last_error() ) ? true : false;
	}


	/**
	 * @since 2.0
	 * @version 2.0.4
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


