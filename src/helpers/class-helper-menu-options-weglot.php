<?php

namespace WeglotWP\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 2.5.0
 */
abstract class Helper_Menu_Options_Weglot {

	/**
	 * @var string
	 */
	const HIDE_NO_TRANSLATION = 'hide_if_no_translation';

	/**
	 * @var string
	 */
	const HIDE_CURRENT = 'hide_current';

	/**
	 * @var string
	 */
	const SHOW_FLAGS = 'show_flags';

	/**
	 * @var string
	 */
	const SHOW_FULL_NAME = 'show_full_name';

	/**
	 * @var string
	 */
	const SHOW_SHORT_NAME = 'show_short_name';
	/**
	 * @var string
	 */
	const DROPDOWN = 'dropdown';

	/**
	 * @since 2.5.0
	 * @static
	 * @return array
	 */
	public static function get_menu_switcher_list_options() {
		return apply_filters( 'weglot_menu_switcher_options', [
			[
				'key'   => self::HIDE_NO_TRANSLATION,
				'title' => __('Hide if no translation', 'weglot')
			],
			[
				'key'   => self::HIDE_CURRENT,
				'title' => __('Hide current', 'weglot')
			],
			[
				'key'   => self::SHOW_FLAGS,
				'title' => __('Show flags', 'weglot')
			],
			[
				'key'   => self::SHOW_FULL_NAME,
				'title' => __('Show full name', 'weglot')
			],
			[
				'key'   => self::SHOW_SHORT_NAME,
				'title' => __('Show short name', 'weglot')
			],
			[
				'key'   => self::DROPDOWN,
				'title' => __('Dropdown', 'weglot')
			],
		]);
	}

	/**
	 * @since 2.5.0
	 * @static
	 * @return array
	 */
	public static function get_keys() {
		return apply_filters( 'weglot_menu_switcher_options_keys', [
			self::HIDE_NO_TRANSLATION,
			self::HIDE_CURRENT,
			self::SHOW_FLAGS,
			self::SHOW_FULL_NAME,
			self::SHOW_SHORT_NAME,
			self::DROPDOWN,
		]);
	}
}
