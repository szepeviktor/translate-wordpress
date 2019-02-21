<?php

namespace WeglotWP\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 3.0.0
 */
class Helper_Excluded_Type {

	/**
	 * @var string
	 */
	const START_WITH = 'START_WITH';

	/**
	 * @var string
	 */
	const END_WITH = 'END_WITH';

	/**
	 * @var string
	 */
	const CONTAIN = 'CONTAIN';

	/**
	 * @var string
	 */
	const IS_EXACTLY = 'IS_EXACTLY';

	/**
	 * @var string
	 */
	const MATCH_REGEX = 'MATCH_REGEX';


	public static function get_excluded_type() {
		return [
			START_WITH,
			END_WITH,
			CONTAIN,
			IS_EXACTLY,
			MATCH_REGEX,
		];
	}
}


