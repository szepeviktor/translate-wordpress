<?php

namespace WeglotWP\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WeglotWP\Models\Mediator_Service_Interface_Weglot;


/**
 * @since 2.0
 */
class Replace_Link_Service_Weglot implements Mediator_Service_Interface_Weglot {

	/**
	 * @since 2.0
	 * @see Mediator_Service_Interface_Weglot
	 * @param array $services
	 * @return void
	 */
	public function use_services( $services ) {
		$this->multisite_service = $services['Multisite_Service_Weglot'];
	}

	/**
	 * Replace an URL
	 * @since 2.0
	 * @param string $url
	 * @return string
	 */
	public function replace_url( $url ) {
		$current_and_original = weglot_get_current_and_original_language();

		$parsed_url = wp_parse_url( $url );
		$scheme     = isset( $parsed_url['scheme'] ) ? $parsed_url['scheme'] . '://' : '';
		$host       = isset( $parsed_url['host'] ) ? $parsed_url['host'] : '';
		$port       = isset( $parsed_url['port'] ) ? ':' . $parsed_url['port'] : '';
		$user       = isset( $parsed_url['user'] ) ? $parsed_url['user'] : '';
		$pass       = isset( $parsed_url['pass'] ) ? ':' . $parsed_url['pass'] : '';
		$pass       = ($user || $pass) ? "$pass@" : '';
		$path       = isset( $parsed_url['path'] ) ? $parsed_url['path'] : '/';
		$query      = isset( $parsed_url['query'] ) ? '?' . $parsed_url['query'] : '';
		$fragment   = isset( $parsed_url['fragment'] ) ? '#' . $parsed_url['fragment'] : '';

		$current_language = $current_and_original['current'];

		if ( $current_and_original['current'] === $current_and_original['original'] ) {
			return $url;
		} else {
			$url_translated = ( strlen( $path ) > 2 && substr( $path, 0, 4 ) === "/$current_language/" ) ?
				"$scheme$user$pass$host$port$path$query$fragment" : "$scheme$user$pass$host$port/$current_language$path$query$fragment";

			foreach ( array_reverse( $this->multisite_service->get_list_of_network_path() ) as $np ) {
				if ( strlen( $np ) > 2 && strpos( $url_translated, $np ) !== false ) {
					$url_translated = str_replace(
						'/' . $current_language . $np,
						$np . $current_language . '/',
						$url_translated
					);
				}
			}

			return $url_translated;
		}
	}

	/**
	 * Replace <a>
	 * @since 2.0
	 * @param string $translated_page
	 * @param string $current_url
	 * @param string $quote1
	 * @param string $quote2
	 * @param string $sometags
	 * @param string $sometags2
	 * @return string
	 */
	public function replace_a( $translated_page, $current_url, $quote1, $quote2, $sometags = null, $sometags2 = null ) {
		$current_language  = weglot_get_current_language();
		$translated_page   = preg_replace( '/<a' . preg_quote( $sometags, '/' ) . 'href=' . preg_quote( $quote1 . $current_url . $quote2, '/' ) . '/', '<a' . $sometags . 'href=' . $quote1 . $this->replace_url( $current_url, $current_language ) . $quote2, $translated_page );

		return $translated_page;
	}
}
