<?php

namespace WeglotWP\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Weglot\Util\Regex\RegexEnum;

/**
 * @since 3.0.0
 */
class Helper_Excluded_Type {
	public static function get_excluded_type() {
		return [
			RegexEnum::START_WITH,
			RegexEnum::END_WITH,
			RegexEnum::CONTAIN,
			RegexEnum::IS_EXACTLY,
			RegexEnum::MATCH_REGEX,
		];
	}
}


