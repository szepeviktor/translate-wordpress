<?php

namespace WeglotWP\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WeglotWP\Helpers\Helper_Json_Inline_Weglot;
use WeglotWP\Helpers\Helper_Keys_Json_Weglot;

use Weglot\Client\Api\WordCollection;
use Weglot\Client\Api\TranslateEntry;
use Weglot\Client\Endpoint\Translate;
use Weglot\Client\Api\WordEntry;
use Weglot\Client\Api\Enum\WordType;
use JsonPath\JsonObject;
use Weglot\Parser\ConfigProvider\ServerConfigProvider;

/**
 * @since 3.0.3
 */
class Translate_Json_Ld {

	/**
	 * @since 3.0.3
	 * @var integer
	 */
	protected $index       = 0;

	/**
	 * @since 3.0.3
	 * @var integer
	 */
	protected $limit       = 0;

	/**
	 * @since 3.0.3
	 * @var array
	 */
	protected $indexes     = [];

	/**
	 * @since 3.0.3
	 * @var array
	 */
	protected $collections = [];

	/**
	 * @since 3.0.3
	 * @var integer
	 */
	protected $index_json_collections = [];

	/**
	 * @since 3.0.3
	 * @var integer
	 */
	protected $limit_json_collections = [];

	/**
	 * @since 3.0.3
	 * @var array
	 */
	protected $keys_json_ld_translate = [
		'name',
		'description',
		'headline',
		'articleSection'
	];

	/**
	 * @since 3.0.3
	 */
	public function __construct() {
		$this->replace_url_services             = weglot_get_service( 'Replace_Url_Service_Weglot' );
		$this->replace_link_services            = weglot_get_service( 'Replace_Link_Service_Weglot' );
		$this->parser_services                  = weglot_get_service( 'Parser_Service_Weglot' );
	}

	/**
	 * @since 3.0.3
	 *
	 * @param array $json
	 * @param string $path
	 * @return array
	 */
	protected function check_json_to_translate( $json, $path = '$' ) {
		foreach ( $json as $key => $val ) {
			if ( is_array( $val ) ) {
				if ( is_string( $key ) ) {
					if ( false === strpos( $key, '.' ) && false === strpos( $key, '@' ) ) {
						$newpath = "$path.$key";
					} else {
						$newpath = sprintf( '%s["%s"]', $path, $key );
					}
				} else {
					$newpath = sprintf( '%s[%s]', $path, $key );
				}

				$this->check_json_to_translate( $val, $newpath );
			} else {
				if ( false === strpos( $key, '.' ) ) {
					$newpath = "$path.$key";
				} else {
					$newpath = sprintf( '%s["%s"]', $path, $key );
				}

				if ( Helper_Json_Inline_Weglot::is_ajax_html( $val ) ) {
					try {
						$parser                            = $this->parser_services->get_parser();
						$words                             = $parser->parse( $val );
						if ( ! $words instanceof WordCollection || $words->count() < 1 ) {
							continue;
						}
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
				} else {
					if ( in_array( $key, $this->keys_json_ld_translate, true ) ) {
						try {
							$parser                    = $this->parser_services->get_parser();
							$words                     = $parser->parse( $val );
							if ( ! $words instanceof WordCollection || $words->count() < 1 ) {
								continue;
							}
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
	 * @since 3.0.3
	 * @return string
	 */
	protected function handle_translate_jsons() {
		$parser = $this->parser_services->get_parser();

		// Translate endpoint parameters
		$params = [
			'language_from' => weglot_get_original_language(),
			'language_to'   => weglot_get_current_language(),
		];

		if ( $parser->getConfigProvider() instanceof ServerConfigProvider ) {
			$parser->getConfigProvider()->loadFromServer();
		}

		$params      = array_merge( $params, $parser->getConfigProvider()->asArray() );

		$translate       = new TranslateEntry( $params );
		$word_collection = $translate->getInputWords();
		foreach ( $this->collections as $value ) {
			$word_collection->addOne( new WordEntry( $value['w'], $value['t'] ) );
		}

		$translate   = new Translate( $translate, $parser->getClient() );
		return $translate->handle();

	}

	/**
	 *
	 * @param string $json
	 * @param string $key
	 * @param array $data
	 * @return void
	 */
	protected function replace_json_content( $json, $key, $data ){

		$json_object = new JsonObject( $json );

		list( $output_words, $input_words ) = $data;

		$array_keys_indexes = array_keys($this->indexes);
		for ( $i= $this->index_json_collections[$key]; $i < $this->limit_json_collections[$key] ; $i++) {

			$path = $array_keys_indexes[$i];
			$index = $this->indexes[ $path ];
			$y = 0;

			do {
				if ( is_null( $input_words[ $y ] ) || is_null( $output_words[ $y ] ) ) {
					$y++;
					continue;
				}

				$input_word  = $input_words[ $y ]->getWord();
				$output_word = $output_words[ $y ]->getWord();
				$str         = $json_object->get( $path )[0];

				$json_object->set( $path, str_replace( $input_word, $output_word, $str ) );
				$y++;
			} while ( $y < $index['limit'] );
		}

		return json_decode( $json_object->getJson(), JSON_PRETTY_PRINT );
	}

	/**
	 * @since 3.0.3
	 * @param array simple_html_dom_node $jsons
	 * @param array $translated_words
	 * @return array simple_html_dom_node
	 */
	public function replace_jsons_translated( $jsons, $translated_words ){
		$output_words = $translated_words->getOutputWords();

		if ( $output_words->count() !== count( $this->collections ) || $output_words->count() === 0 ) {
			return $jsons;
		}

		$input_words = $translated_words->getInputWords();
		$i           = 0;

		foreach ( $jsons as $key => $row ) {
			$json = json_decode($row->innertext, true);
			if ( json_last_error() !== JSON_ERROR_NONE) {
				continue;
			}

			$json = $this->replace_json_content($json, $key, [
				$output_words,
				$input_words
			]);

			$row->innertext = json_encode($json, JSON_PRETTY_PRINT);

		}

		return $jsons;

	}

	/**
	 * @since 3.0.3
	 * @param string $dom
	 * @return string
	 */
	public function handle( $dom ) {

		$dom = \WGSimpleHtmlDom\str_get_html(
			$dom,
			true,
			true,
			WG_DEFAULT_TARGET_CHARSET,
			false
		);

		$this->keys_json_ld_translate = apply_filters( 'weglot_keys_json_ld_translate', $this->keys_json_ld_translate );

		$jsons = $dom->find( 'script[type="application/ld+json"]' );
		foreach ( $jsons as $key => $row ) {
			$json = json_decode($row->innertext, true);
			if ( json_last_error() !== JSON_ERROR_NONE) {
				continue;
			}

			$this->index_json_collections[$key] = count($this->collections);
			$this->check_json_to_translate($json);
			$this->limit_json_collections[$key] = count($this->collections);

		}

		$translated_words = null;
		try {
			$translated_words = $this->handle_translate_jsons();
		} catch (\Exception $e) {
			return $dom->save();
		}


		$translated_jsons = $this->replace_jsons_translated( $jsons, $translated_words );

		return $dom->save();
	}
}



