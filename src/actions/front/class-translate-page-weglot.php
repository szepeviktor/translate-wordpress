<?php

namespace WeglotWP\Actions\Front;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WeglotWP\Models\Hooks_Interface_Weglot;
use WeglotWP\Models\Mediator_Service_Interface_Weglot;


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
class Translate_Page_Weglot implements Hooks_Interface_Weglot, Mediator_Service_Interface_Weglot {
	/**
	 * @see Mediator_Service_Interface_Weglot
	 *
	 * @param array $services
	 * @return Options_Weglot
	 */
	public function use_services( $services ) {
		$this->option_services         = $services['Option_Service_Weglot'];
		$this->button_services         = $services['Button_Service_Weglot'];
		$this->request_url_services    = $services['Request_Url_Service_Weglot'];
		$this->redirect_services       = $services['Redirect_Service_Weglot'];
		$this->replace_url_services    = $services['Replace_Url_Service_Weglot'];
		return $this;
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

		$this->current_language   = $this->request_url_services->get_current_language();

		if (
			null === $this->current_language ||
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
		$this->noredirect         = false;
		$this->original_language  = $this->option_services->get_option( 'original_language' );

		$this->redirect_services->verify_no_redirect();
		$this->check_need_to_redirect();
		$this->prepare_request_uri();
		$this->prepare_rtl_language();

		ob_start( [ $this, 'weglot_treat_page' ] );
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
		if ( $this->current_language === $this->original_language ) {
			return $this->weglot_render_dom( $content );
		}

		$exclude_blocks = $this->option_services->get_option( 'exclude_blocks' );

		$config             = new ServerConfigProvider();
		$client             = new Client( $this->api_key );
		$parser             = new Parser( $client, $config, $exclude_blocks );

		$translated_content = $parser->translate( $content, $this->original_language, $this->current_language ); // phpcs:ignore

		return $this->weglot_render_dom( $translated_content );
	}

	/**
	 * @since 2.0
	 * @param string $dom
	 * @return string
	 */
	public function weglot_add_button_html( $dom ) {
		$button_html        = $this->button_services->get_html();

		// Place the button if we see short code
		if ( strpos( $dom, '<div id="weglot_here"></div>' ) !== false ) {
			$dom  = str_replace( '<div id="weglot_here"></div>', $button_html, $dom );
		}

		// Place the button if not in the page
		if ( strpos( $dom, 'class="weglot-current' ) === false ) {
			$button_html = str_replace( '<aside data-wg-notranslate class="', '<aside data-wg-notranslate class="wg-default ', $button_html );
			$dom         = ( strpos( $dom, '</body>' ) !== false) ? str_replace( '</body>', $button_html . ' </body>', $dom ) : str_replace( '</footer>', $button_html . ' </footer>', $dom );
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
		return $dom;
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


