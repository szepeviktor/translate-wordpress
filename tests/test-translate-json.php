<?php

use WeglotWP\Services\Translate_Service_Weglot;
use Weglot\Client\Api\Enum\BotType;
use Weglot\Parser\ConfigProvider\ManualConfigProvider;

class TranslateJsonTest extends WP_UnitTestCase {
	protected function init_filters() {
		add_filter( 'weglot_get_options', function( $options ) {
			$options['api_key'] = getenv( 'API_KEY' );
			$options['original_language'] = 'en';
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
	}

	public function test_translation_content_links_internal_with_cart() {
		$this->init_filters();
		$translate_service = new Translate_Service_Weglot();
		$translate_service->set_original_language( 'en' );
		$translate_service->set_current_language( 'fr' );
		$content = $translate_service->weglot_treat_page( file_get_contents( __DIR__ . '/templates/wc-cart.json' ) ); //phpcs:ignore

		$dom = \WGSimpleHtmlDom\str_get_html(
			$content,
			true,
			true,
			WG_DEFAULT_TARGET_CHARSET,
			false
		);

		$this->assertContains( 'Voir le panier', $dom->__toString() );
		$this->assertContains( 'http:\/\/weglot-plugin.local\/fr\/', $dom->__toString() );
	}

	public function test_translation_content_with_cart_empty() {
		$this->init_filters();
		$translate_service = new Translate_Service_Weglot();
		$translate_service->set_original_language( 'en' );
		$translate_service->set_current_language( 'fr' );
		$content = $translate_service->weglot_treat_page( file_get_contents( __DIR__ . '/templates/wc-cart-empty.json' ) ); //phpcs:ignore

		$dom = \WGSimpleHtmlDom\str_get_html(
			$content,
			true,
			true,
			WG_DEFAULT_TARGET_CHARSET,
			false
		);

		$this->assertContains( 'Aucun produit dans le chariot', $dom->__toString() );
	}

	public function test_translation_api_request() {
		$this->init_filters();

		add_filter( 'weglot_get_rest_current_url_path', function() {
			return '/';
		});

		$translate_service = new Translate_Service_Weglot();
		$translate_service->set_original_language( 'en' );
		$translate_service->set_current_language( 'fr' );
		$content = $translate_service->weglot_treat_page( file_get_contents( __DIR__ . '/templates/wp-api-posts.json' ) ); //phpcs:ignore

		$dom = \WGSimpleHtmlDom\str_get_html(
			$content,
			true,
			true,
			WG_DEFAULT_TARGET_CHARSET,
			false
		);

		$this->assertContains( "Petit titre sur l'extrait", $dom->__toString() );
		$this->assertContains( "C'est votre premier post", $dom->__toString() );
		$this->assertContains( 'http:\/\/weglot-plugin.local\/fr\/2019\/01\/09\/hello-world\/', $dom->__toString() );
		$this->assertContains( 'http:\/\/weglot-plugin.local\/fr\/2019\/01\/28\/a-little-test\/', $dom->__toString() );
	}

	public function test_translation_api_request_with_path_empty() {
		$this->init_filters();

		$translate_service = new Translate_Service_Weglot();
		$translate_service->set_original_language( 'en' );
		$translate_service->set_current_language( 'fr' );
		$content = $translate_service->weglot_treat_page( file_get_contents( __DIR__ . '/templates/wp-api-posts.json' ) ); //phpcs:ignore

		$dom = \WGSimpleHtmlDom\str_get_html(
			$content,
			true,
			true,
			WG_DEFAULT_TARGET_CHARSET,
			false
		);

		$this->assertContains( "Petit titre sur l'extrait", $dom->__toString() );
		$this->assertContains( "C'est votre premier post", $dom->__toString() );
		$this->assertContains( 'http:\/\/weglot-plugin.local\/fr\/2019\/01\/09\/hello-world\/', $dom->__toString() );
		$this->assertContains( 'http:\/\/weglot-plugin.local\/fr\/2019\/01\/28\/a-little-test\/', $dom->__toString() );
	}
}
