<?php

namespace WeglotWP\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Function helper for URL replace DOM
 *
 * @since 2.0
 */
abstract class Helper_Replace_Link_Weglot {
	public function is_eligible_url( $url ) {
		$url = urldecode(self::URLToRelative($url));

		//Format exclude URL
		$excludeURL = weglot_get_exclude_urls();

		if ( ! empty($excludeURL)) {
			$excludeURL    = preg_replace('#\s+#', ',', trim($excludeURL));

			$excludedUrls  = explode(',', $excludeURL);
			foreach ($excludedUrls as &$ex_url) {
				$ex_url = self::URLToRelative($ex_url);
			}
			$excludeURL = implode(',', $excludedUrls);
		}


		$exclusions = preg_replace('#\s+#', ',', $excludeURL);

		$listRegex = [];
		if ( ! empty($exclusions)) {
			$listRegex  = explode(',', $exclusions);
		}


		// $excludeAmp = get_option('wg_exclude_amp', 'on');
		// if ($excludeAmp === 'on') {
		// 	$listRegex[] = apply_filters('weglot_regex_amp', '([&\?/])amp(/)?$');
		// }

		foreach ($listRegex as $regex) {
			$str          = self::escapeSlash($regex);
			$prepareRegex = sprintf('/%s/', $str);
			if (preg_match($prepareRegex, $url) === 1) {
				return false;
			}
		}

		return true;
	}

	public static function escapeSlash($str) // Change this in another helper and remove static
	{
		return str_replace('/', '\/', $str);
	}


	public static function URLToRelative($url) // Change this in another helper and remove static
	{
		if ((substr($url, 0, 7) == 'http://') || (substr($url, 0, 8) == 'https://')) {
			// the current link is an "absolute" URL - parse it to get just the path
			$parsed   = parse_url($url);
			$path     = isset($parsed['path']) ? $parsed['path'] : '';
			$query    = isset($parsed['query']) ? '?' . $parsed['query'] : '';
			$fragment = isset($parsed['fragment']) ? '#' . $parsed['fragment'] : '';

			// if (self::getHomeDirectory()) {
			// 	$relative = str_replace(self::getHomeDirectory(), '', $path);

			// 	return ($relative == '') ? '/' : $relative;
			// } else {
			return $path . $query . $fragment;
			// }
		}
		return $url;
	}


	public static function replace_url( $url ) {
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


		if ( $current_and_original['current'] === $current_and_original['original'] ) {
			return $url;
		} else {
			$url_translated = ( strlen( $path ) > 2 && substr( $path, 0, 4 ) === "/$l/" ) ?
				"$scheme$user$pass$host$port$path$query$fragment" : "$scheme$user$pass$host$port/$l$path$query$fragment";

			foreach ( array_reverse( $this->network_paths ) as $np ) {
				if ( strlen( $np ) > 2 && strpos( $url_translated, $np ) !== false ) {
					$url_translated = str_replace(
						'/' . $l . $np,
						$np . $l . '/',
						$url_translated
					);
				}
			}

			return $url_translated;
		}
	}

	public function replace_a( $translated_page, $current_url, $quote1, $quote2, $sometags = null, $sometags2 = null ) {
		$current_language  = weglot_get_current_language();
		$translated_page   = preg_replace( '/<a' . preg_quote( $sometags, '/' ) . 'href=' . preg_quote( $quote1 . $current_url . $quote2, '/' ) . '/', '<a' . $sometags . 'href=' . $quote1 . self::replace_url( $current_url, $current_language ) . $quote2, $translated_page );

		return $translated_page;
	}
}
