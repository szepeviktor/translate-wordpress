<?php

use WeglotWP\Actions\Front\Translate_Page_Weglot;

class TranslatePageTest extends WP_UnitTestCase {
	public function test_translation_content() {
		add_filter( 'weglot_get_options', function( $options ) {
			$options['api_key'] = 'wg_854c61e592406618d8afbc2de6cebd967';
			$options['destination_language'] = [ 'fr' ];
			$options['allowed'] = true;
			return $options;
		});

		add_filter( 'weglot_translate_current_language', function() {
			return 'fr';
		});

		$translate_page_weglot = new Translate_Page_Weglot();
		$translate_page_weglot->set_original_language( 'en' );
		$content               = $translate_page_weglot->weglot_treat_page( file_get_contents( __DIR__ . '/templates/twentyseventeen.html' ) );
		// echo $content;
	}
}
