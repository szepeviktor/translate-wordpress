<?php

namespace WeglotWP\Front;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WeglotWP\Models\Hooks_Interface_Weglot;
use WeglotWP\Models\Mediator_Service_Interface_Weglot;

use Weglot\Client\Client;
use Weglot\Parser\Parser;
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
		$this->option_services = $services['Option_Service_Weglot'];
		return $this;
	}

	/**
	 * @see Hooks_Interface_Weglot
	 *
	 * @since 2.0
	 * @return void
	 */
	public function hooks() {
		add_action( 'init', [ $this, 'weglot_init' ] );
	}

	/**
	 * @see init
	 * @since 2.0
	 *
	 * @return void
	 */
	public function weglot_init() {
		ob_start( [ $this, 'weglot_treat_page' ] );
	}

	/**
	 * @see weglot_init / ob_start
	 *
	 * @param string $content
	 * @return string
	 */
	public function weglot_treat_page( $content ) {
		$config            = new ServerConfigProvider();
		$api_key           = $this->option_services->get_option( 'api_key' );
		$original_language = $this->option_services->get_option( 'original_language' );
		$client            = new Client( $api_key );
		$parser            = new Parser( $client, $config );

		$translated_content = $parser->translate( $content, $original_language, 'fr' ); // phpcs:ignore

		return $translated_content;
	}
}


