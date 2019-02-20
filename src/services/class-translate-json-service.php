<?php

namespace WeglotWP\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WeglotWP\Helpers\Helper_Json_Inline_Weglot;
use WeglotWP\Helpers\Helper_Keys_Json_Weglot;

use Weglot\Client\Api\TranslateEntry;
use Weglot\Client\Endpoint\Translate;
use Weglot\Client\Api\WordEntry;
use Weglot\Client\Api\Enum\WordType;
use JsonPath\JsonObject;

/**
 * @since 2.6.0
 */
class Translate_Json_Service {

	/**
	 * @since 2.6.0
	 * @var integer
	 */
	protected $index       = 0;

	/**
	 * @since 2.6.0
	 * @var integer
	 */
	protected $limit       = 0;

	/**
	 * @since 2.6.0
	 * @var array
	 */
	protected $indexes     = [];

	/**
	 * @since 2.6.0
	 * @var array
	 */
	protected $collections = [];

	/**
	 * @since 2.6.0
	 */
	public function __construct() {
		$this->option_services                  = weglot_get_service( 'Option_Service_Weglot' );
		$this->request_url_services             = weglot_get_service( 'Request_Url_Service_Weglot' );
		$this->replace_url_services             = weglot_get_service( 'Replace_Url_Service_Weglot' );
		$this->replace_link_services            = weglot_get_service( 'Replace_Link_Service_Weglot' );
		$this->parser_services                  = weglot_get_service( 'Parser_Service_Weglot' );
	}

	/**
	 * @since 2.6.0
	 *
	 * @param array $json
	 * @param string $path
	 * @return array
	 */
	protected function check_json_to_translate( $json, $path = '$' ) {
		$array_not_ajax_html = apply_filters( 'weglot_array_not_ajax_html', [ 'redirecturl', 'url' ] );

		foreach ( $json as $key => $val ) {
			if ( is_array( $val ) ) {
				if ( is_string( $key ) ) {
					$newpath = "$path.$key";
				} else {
					$newpath = $path . "[$key]";
				}

				$this->check_json_to_translate( $val, $newpath );
			} else {
				if ( Helper_Json_Inline_Weglot::is_ajax_html( $val ) ) {
					try {
						$newpath                           = "$path.$key";
						$parser                            = $this->parser_services->get_parser();
						$words                             = $parser->parse( $val );
						$this->collections                 = array_merge( $this->collections, $words->jsonSerialize() );
						$this->limit                       = $this->index + $words->count();
						$this->indexes[ $newpath ]         = [
							'start' => $this->index,
							'limit' => $this->limit,
						]; //phpcs:ignore
						$this->index += $words->count();
					} catch ( \Exception $e ) {
						continue;
					}
				} elseif ( in_array( $key,  $array_not_ajax_html, true ) ) {
					// $array[$key] = $this->replace_link_services->replace_url( $val ); //phpcs:ignore
				} else {
					if ( Helper_Keys_Json_Weglot::translate_key_for_path( $key ) ) {
						try {
							$newpath = "$path.$key";

							$parser                    = $this->parser_services->get_parser();
							$words                     = $parser->parse( $val );
							$this->collections         = array_merge( $this->collections, $words->jsonSerialize() );
							$this->limit               = $this->index + $words->count();
							$this->indexes[ $newpath ] = [
								'start' => $this->index,
								'limit' => $this->limit,
							];
							$this->index += $words->count();
						} catch ( \Exception $e ) {
							continue;
						}
					}
				}
			}
		}

		return [
			$this->indexes,
			$this->collections,
		];
	}


	/**
	 * @since 2.6.0
	 * @param array $json
	 * @param mixed $path
	 * @return array
	 */
	public function translate_json( $json ) {
		list( $indexes, $words ) = $this->check_json_to_translate( $json );

		$parser = $this->parser_services->get_parser();

		// Translate endpoint parameters
		$params = [
			'language_from' => weglot_get_original_language(),
			'language_to'   => weglot_get_current_language(),
		];

		$parser->getConfigProvider()->loadFromServer();
		$params = array_merge( $params, $parser->getConfigProvider()->asArray() );

		try {
			$translate       = new TranslateEntry( $params );
			$word_collection = $translate->getInputWords();
			foreach ( $words as $value ) {
				$word_collection->addOne( new WordEntry( $value['w'], $value['t'] ) );
			}
		} catch ( \Exception $e ) {
		}

		$translate  = new Translate( $translate, $parser->getClient() );
		$translated = $translate->handle();

		$output_words = $translated->getOutputWords();

		if ( $output_words->count() !== count( $words ) ) {
			return $json;
		}

		$input_words = $translated->getInputWords();

		$json_object = new JsonObject( $json );
		$i           = 0;
		foreach ( $indexes as $path => $index ) {
			do {
				$input_word = $input_words[ $i ]->getWord();
				$ouput_word = $output_words[ $i ]->getWord();
				$str        = $json_object->get( $path )[0];

				$json_object->set( $path, str_replace( $input_word, $ouput_word, $str ) );
				$i++;
			} while ( $i < $index['limit'] );
		}

		return $json_object;
	}
}



