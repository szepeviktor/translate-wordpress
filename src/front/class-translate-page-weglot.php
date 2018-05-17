<?php

namespace WeglotWP\Front;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WeglotWP\Models\Hooks_Interface_Weglot;
use WeglotWP\Models\Mediator_Service_Interface_Weglot;

use Weglot\Client\Client;
use Weglot\Parser\Parser;
use Weglot\Util\Url;
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
		$this->option_services      = $services['Option_Service_Weglot'];
		$this->button_services      = $services['Button_Service_Weglot'];
		$this->request_url_services = $services['Request_Url_Service_Weglot'];
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

		// Use for good process on URL
		$_SERVER['REQUEST_URI'] = str_replace(
			'/' . $this->current_language . '/',
			'/',
			$_SERVER['REQUEST_URI'] //phpcs:ignore
		);

		if ( $this->request_url_services->is_language_rtl( $this->current_language ) ) {
			$GLOBALS['text_direction'] = 'rtl';
		} else {
			$GLOBALS['text_direction'] = 'ltr';
		}

		ob_start( [ $this, 'weglot_treat_page' ] );
	}

	/**
	 * @see weglot_init / ob_start
	 * @since 2.0
	 * @param string $content
	 * @return string
	 */
	public function weglot_treat_page( $content ) {
		$original_language  = $this->option_services->get_option( 'original_language' );
		$button_html        = $this->button_services->get_html();

		if ( $this->current_language === $original_language ) {
			return str_replace( '</body>', $button_html . '</body>', $content );
		}

		$config             = new ServerConfigProvider();
		$client             = new Client( $this->api_key );
		$parser             = new Parser( $client, $config );

		$translated_content = $parser->translate( $content, $original_language, $this->current_language ); // phpcs:ignore

		return str_replace( '</body>', $button_html . '</body>', $translated_content );
	}

	/**
	 * @see wp_head
	 * @since 2.0
	 * @return void
	 */
	public function weglot_href_lang() {
		echo $this->request_url_services->get_weglot_url()->generateHrefLangsTags(); //phpcs:ignore
	}
}


