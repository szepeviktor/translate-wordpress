<?php

use WeglotWP\Services\Translate_Service_Weglot;
use WeglotWP\Services\Href_Lang_Service_Weglot;
use Weglot\Client\Api\Enum\BotType;
use Weglot\Parser\ConfigProvider\ManualConfigProvider;

class HreflangPageTest extends WP_UnitTestCase {
	public function test_hreflang_page() {
		add_filter( 'weglot_get_options', function( $options ) {
			$options['api_key'] = getenv( 'API_KEY' );
			$options['orignal_language'] = 'en';
			$options['destination_language'] = [ 'fr', 'es' ];
			$options['allowed'] = true;
			return $options;
		});

		add_filter( 'weglot_translate_current_language', function() {
			return 'fr';
		});

		add_filter( 'weglot_parser_config_provider', function() {
			$config = new ManualConfigProvider( 'http://weglot-plugin.local', BotType::HUMAN, 'Homepage' );
			return $config;
		});

		add_filter( 'weglot_check_link_server_host', function() {
			return 'weglot-plugin.local';
		});

		$href_lang_service = new Href_Lang_Service_Weglot();
		$out               = $href_lang_service->generate_href_lang_tags(); //phpcs:ignore

		$this->assertNotFalse( strpos( $out, 'hreflang="fr"' ) );
		$this->assertNotFalse( strpos( $out, 'hreflang="es"' ) );
	}

	public function test_hreflang_private_page() {
		add_filter( 'weglot_get_options', function( $options ) {
			$options['api_key'] = getenv( 'API_KEY' );
			$options['orignal_language'] = 'en';
			$options['destination_language'] = [ 'fr', 'es' ];
			$options['private_mode'] = [
				'active' => true,
				'fr'     => true,
			];
			$options['allowed'] = true;
			return $options;
		});

		add_filter( 'weglot_translate_current_language', function() {
			return 'fr';
		});

		add_filter( 'weglot_parser_config_provider', function() {
			$config = new ManualConfigProvider( 'http://weglot-plugin.local', BotType::HUMAN, 'Homepage' );
			return $config;
		});

		add_filter( 'weglot_check_link_server_host', function() {
			return 'weglot-plugin.local';
		});

		$href_lang_service = new Href_Lang_Service_Weglot();
		$out               = $href_lang_service->generate_href_lang_tags(); //phpcs:ignore

		$this->assertFalse( strpos( $out, 'hreflang="fr"' ) );
		$this->assertFalse( strpos( $out, 'hreflang="es"' ) );
	}
}
