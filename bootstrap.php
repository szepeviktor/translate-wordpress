<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Weglot\Bootstrap_Weglot;

use Weglot\Admin\Pages_Weglot;
use Weglot\Admin\Plugin_Links_Weglot;

use Weglot\Services\Option_Service_Weglot;

spl_autoload_register( 'weglot_autoload' );

/**
 * Weglot autoload class PHP with class-name.php
 * @since 2.0
 * @param string $class_name
 * @return void
 */
function weglot_autoload( $class_name ) {
	$dir_class = __DIR__ . '/src/';
	$prefix    = 'class-';

	$file_parts = explode( '\\', $class_name );

	$total_parts = count( $file_parts ) - 1;
	$dir_file    = $dir_class;
	for ( $i = 1; $i <= $total_parts; $i++ ) {
		if ( $total_parts !== $i ) {
			$dir_file .= strtolower( $file_parts[ $i ] ) . '/';
		} else {
			$string    = str_replace( '_', '-', strtolower( $file_parts[ $i ] ) );
			$file_load = $dir_file . $prefix . $string . '.php';

			if ( file_exists( $file_load ) ) {
				include_once $file_load;
			}
		}
	}
}

/**
 * Only use for get one context
 *
 * @since 2.0
 */
abstract class Context_Weglot {
	protected static $context;

	public static function weglot_get_context() {
		if ( null !== self::$context ) {
			return self::$context;
		}

		$services = [
			new Option_Service_Weglot(),
		];

		$actions = [
			new Pages_Weglot(),
			new Plugin_Links_Weglot(),
		];

		self::$context = new Bootstrap_Weglot();
		self::$context->set_actions( $actions )
					->set_services( $services );

		return self::$context;
	}
}


/**
 * Init plugin
 * @since 2.0
 * @return void
 */
function weglot_init() {
	if ( function_exists( 'apache_get_modules' ) && ! array_search( 'mod_rewrite', apache_get_modules(), true ) ) {
		add_action( 'admin_notices', [ '\Weglot\Notices\Rewrite_Module_Weglot', 'admin_notice' ] );
	}

	load_plugin_textdomain( 'weglot', false, WEGLOT_DIR_LANGUAGES );

	Context_Weglot::weglot_get_context()->init_plugin();
}
