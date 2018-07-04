<?php

namespace WeglotWP\Third\Woocommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * WC_Translate_Weglot
 *
 * @since 2.0
 */
class WC_Translate_Weglot {
	public function translate( $html ) {
		preg_match('#i18n_view_cart(.*?);#', $html, $match);
		var_dump($match);

		// if ( ! isset($match[1])) {
		// 	return $count;
		// }

		preg_match_all('#(label|placeholder)\\\":\\\"(.*?)\\\"#', $match[1], $all);

		$allWords = $all[2];
		var_dump($allWords);
		die;
		// foreach ($allWords as $value) {
		// 	$value = $this->formatForApi($value);
		// 	array_push(
		// 	   $words,
		// 	   array(
		// 		   't' => 1,
		// 		   'w' => $value,
		// 	   )
		// 	);
		// 	$count++;
		// }

		// return $count;
	}

	public function format_for_api( $string ) {
		$string = '"' . $string . '"';
		return json_decode( str_replace( '\\/', '/', str_replace( '\\\\', '\\', $string ) ) );
	}
}
