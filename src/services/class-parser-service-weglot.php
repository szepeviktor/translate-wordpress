<?php

namespace WeglotWP\Services;

use WeglotWP\Models\Hooks_Interface_Weglot;


use Weglot\Client\Client;
use Weglot\Parser\Parser;
use Weglot\Util\Url;
use Weglot\Util\Server;
use Weglot\Parser\ConfigProvider\ServerConfigProvider;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Parser abstraction
 *
 * @since 2.0
 */
class Parser_Service_Weglot {

	/**
	 * @since 2.0
	 */
	public function __construct() {
		$this->option_services               = weglot_get_service( 'Option_Service_Weglot' );
		$this->dom_listeners_services        = weglot_get_service( 'Dom_Listeners_Service_Weglot' );
	}

	/**
	 * @since 2.0
	 * @return array
	 */
	public function get_parser() {
		$exclude_blocks = $this->option_services->get_exclude_blocks();
		$api_key        = $this->option_services->get_option( 'api_key' );

		$config    = new ServerConfigProvider();
		$client    = new Client( $api_key );
		$listeners = $this->dom_listeners_services->get_dom_listeners();
		$parser    = new Parser( $client, $config, $exclude_blocks, $listeners );

		return $parser;
	}
}
