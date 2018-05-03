<?php

namespace Weglot\Models;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class Abstract_Notices_Weglot {
	public static function get_template_file() {
		return '';
	}

	public static function admin_notice() {
		$class_call = get_called_class();
		if ( ! file_exists( $class_call::get_template_file() ) ) {
			return;
		}

		include_once $class_call::get_template_file();
	}
}
