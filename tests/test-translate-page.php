<?php

use WeglotWP\Services\Translate_Service_Weglot;
use Weglot\Client\Api\Enum\BotType;
use Weglot\Parser\ConfigProvider\ManualConfigProvider;

class TranslatePageTest extends WP_UnitTestCase {
	public function test_translation_content_links_internal() {
		add_filter( 'weglot_get_options', function( $options ) {
			$options['api_key'] = getenv( 'API_KEY' );
			$options['destination_language'] = [ 'fr' ];
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

		$translate_page_weglot = new Translate_Service_Weglot();
		$translate_page_weglot->set_original_language( 'en' );
		$translate_page_weglot->set_current_language( 'fr' );
		$content               = $translate_page_weglot->weglot_treat_page( file_get_contents( __DIR__ . '/templates/twentyseventeen.html' ) ); //phpcs:ignore

		$dom = \WGSimpleHtmlDom\str_get_html(
			$content,
			true,
			true,
			DEFAULT_TARGET_CHARSET,
			false
		);

		$this->assertEquals( $dom->find( '.site-title a', 0 )->href, 'http://weglot-plugin.local/fr/' );
		$this->assertEquals( $dom->find( '#post-1 .entry-title a', 0 )->href, 'http://weglot-plugin.local/fr/hello-world/' );
		$this->assertNotEquals( $dom->find( '.site-branding .site-description', 0 )->innertext, 'Just another WordPress site' );
	}
}
