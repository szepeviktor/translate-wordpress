<?php

namespace WeglotWP\Third\WPForms;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


use WeglotWP\Helpers\Helper_Json_Inline_Weglot;

use Weglot\Client\Api\WordEntry;
use Weglot\Client\Api\Enum\WordType;
use Weglot\Client\Client;
use Weglot\Client\Endpoint\Translate;
use Weglot\Client\Api\TranslateEntry;
use Weglot\Client\Api\Enum\BotType;

/**
 * WPForms_Translate
 *
 * @since 3.0.5
 */
class WPForms_Translate {

	/**
	 * @since 3.0.5
	 */
	public function __construct() {
		$this->parser_services               = weglot_get_service( 'Parser_Service_Weglot' );
	}

	/**
	 * @since 3.0.5
	 * @param array $all_words
	 * @return array
	 */
	protected function translate_entries( $all_words ) {
		// TranslateEntry
		$params = [
			'language_from' => weglot_get_original_language(),
			'language_to'   => weglot_get_current_language(),
			'request_url'   => weglot_get_current_full_url(),
			'bot'           => BotType::HUMAN,
		];

		$translate = new TranslateEntry( $params );

		$word_collection = $translate->getInputWords();
		foreach ( $all_words as $value ) {
			$value = Helper_Json_Inline_Weglot::format_for_api( $value );
			$word_collection->addOne( new WordEntry( $value, WordType::TEXT ) );
		}

		$client           = $this->parser_services->get_client();
		$translate        = new Translate( $translate, $client );

		return $translate->handle();
	}


	/**
	 * @since 3.0.5
	 * @param string $content
	 * @return array
	 */
	protected function translate_json_inline( $content ) {
		$regex = apply_filters( 'weglot_wpforms_match_form_fields', '#wpforms_settings(.*)(\s*)\/\* ]]> \*\/#' );
		preg_match( $regex, $content, $match );

		if ( ! isset( $match[1] ) ) {
			return $content;
		}

		$regex = apply_filters( 'weglot_wpforms_translate_json_inline', '#(val_required|val_url|val_email|val_email_suggestion|val_email_suggestion_title|val_number|val_confirm|val_fileextension|val_filesize|val_time12h|val_time24h|val_requiredpayment|val_creditcard|val_smart_phone|val_post_max_size|val_checklimit|val_checklimit)":"(.*?)",#' );
		preg_match_all( $regex, $match[1], $all );
		if ( empty( $all[1] ) ) {
			return $content;
		}

		$object = $this->translate_entries( $all[2] );

		foreach ( $object->getInputWords() as $key => $input_word ) {
			$from_input_encoding = apply_filters( 'weglot_wpforms_translate_need_json_encode', true );
			$to_output_encoding  = apply_filters( 'weglot_wpforms_translate_need_json_encode', true );
			$from_input          = $input_word->getWord();
			$to_output           = $object->getOutputWords()[ $key ]->getWord();
			if ( '' === $from_input ) {
				continue;
			}

			if ( $from_input_encoding ) {
				$from_input          = Helper_Json_Inline_Weglot::need_json_encode_api( $from_input );
			}
			if ( $to_output_encoding ) {
				$to_output          = Helper_Json_Inline_Weglot::need_json_encode_api( $to_output );
			}

			$content    = str_replace( '"' . $from_input . '"', '"' . $to_output . '"', $content );
		}

		return $content;
	}

	/**
	 * @since 2.5.0
	 * @param string $content
	 * @return string
	 */
	public function translate_words( $content ) {
		$content = $this->translate_json_inline( $content );

		return $content;
	}
}
