<?php

namespace WeglotWP\Third\Woocommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Weglot\Client\Api\WordCollection;
use Weglot\Client\Api\WordEntry;
use Weglot\Client\Api\Enum\WordType;
use Weglot\Client\Client;
use Weglot\Client\Endpoint\Translate;
use Weglot\Client\Api\TranslateEntry;
use Weglot\Client\Api\Enum\BotType;

use WeglotWP\Helpers\Helper_Json_Inline_Weglot;



/**
 * WC_Translate_Weglot
 *
 * @since 2.0
 */
class WC_Translate_Weglot {

	/**
	 * @since 2.0
	 *
	 * @param string $content
	 * @return array
	 */
	protected function translate_adresse_i18n( $content ) {
		preg_match( '#wc_address_i18n_params(.*?);#', $content, $match );

		if ( ! isset( $match[1] ) ) {
			return [];
		}

		preg_match_all( '#(label|placeholder)\\\":\\\"(.*?)\\\"#', $match[1], $all );

		return $all[2];
	}

	/**
	 * @since 2.0
	 *
	 * @param string $content
	 * @return array
	 */
	protected function translate_add_to_cart_params( $content ) {
		preg_match( '#wc_add_to_cart_params(.*?);#', $content, $match );

		if ( ! isset( $match[1] ) ) {
			return [];
		}

		preg_match_all( '#i18n_view_cart\":\"(.*?)\"#', $match[1], $all );

		return $all[1];
	}

	/**
	 * @since 2.0
	 *
	 * @param string $content
	 * @return WordCollection
	 */
	public function translate_words( $content ) {
		$all_words = $this->translate_adresse_i18n( $content );
		$all_words = array_merge( $all_words, $this->translate_add_to_cart_params( $content ) );

		// TranslateEntry
		$params = [
			'language_from' => weglot_get_original_language(),
			'language_to'   => weglot_get_current_language(),
			'request_url'   => weglot_get_current_full_url(),
			'bot'           => BotType::HUMAN,
		];

		$translate       = new TranslateEntry( $params );
		$word_collection = $translate->getInputWords();
		foreach ( $all_words as $value ) {
			$value = Helper_Json_Inline_Weglot::format_for_api( $value );
			$word_collection->addOne( new WordEntry( $value, WordType::TEXT ) );
		}

		$client    = new Client( weglot_get_option( 'api_key' ) );
		$translate = new Translate( $translate, $client );

		$object = $translate->handle();
		foreach ( $object->getInputWords() as $key => $input_word ) {
			$from_input = Helper_Json_Inline_Weglot::unformat_from_api( $input_word->getWord() );
			$to_output  = Helper_Json_Inline_Weglot::unformat_from_api( $object->getOutputWords()[ $key ]->getWord() );

			$content    = str_replace( '\"' . $from_input . '\"', '\"' . $to_output . '\"', $content );
		}

		return $content;
	}
}
