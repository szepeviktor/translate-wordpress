<?php

namespace WeglotWP\Third\CalderaForms;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


use WeglotWP\Helpers\Helper_Json_Inline_Weglot;

/**
 * Caldera_Translate
 *
 * @since 2.6.0
 */
class Caldera_Translate {
	protected function translate_entries( $dom ) {
		$parser = weglot_get_service( 'Parser_Service_Weglot' )->get_parser();
		return $parser->translate( $dom, weglot_get_original_language(), weglot_get_current_language() ); // phpcs:ignore
	}

	/**
	 * @since 2.6.0
	 *
	 * @param string $content
	 * @return array
	 */
	protected function translate_script_html_template( $content ) {
		preg_match_all( '#<script type="text\/html"(.*?)>([\s\S]*)<\/script>#mU', $content, $match );

		if ( ! isset( $match[2] ) || empty( $match[2][0] ) ) {
			return $content;
		}

		foreach ( $match[2] as $key => $template_html ) {
			$dom_translate  = $this->translate_entries( $template_html );
			$content        = str_replace( $match[2][$key], $dom_translate, $content );
		}

		return $content;
	}

	/**
	 * @since 2.6.0
	 *
	 * @param string $content
	 * @return string
	 */
	public function translate_words( $content ) {
		$content = $this->translate_script_html_template( $content );

		return $content;
	}
}
