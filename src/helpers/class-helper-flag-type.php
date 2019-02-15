<?php

namespace WeglotWP\Helpers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 3.0.0
 */
class Helper_Flag_Type {

	/**
	 * @var string
	 */
	const RECTANGLE_MAT = 'rectangle_mat';

	/**
	 * @var string
	 */
	const SHINY = 'shiny';

	/**
	 * @var string
	 */
	const SQUARE = 'square';

	/**
	 * @var string
	 */
	const CIRCLE = 'circle';


	public static function get_flags_type() {
		return [
			RECTANGLE_MAT,
			SHINY,
			SQUARE,
			CIRCLE,
		];
	}
}
