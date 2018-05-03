<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Weglot\Admin\Pages_Weglot;

spl_autoload_register( 'weglot_autoload' );

// Weglot autoload class PHP with class-name.php
function weglot_autoload( $class_name ) {
	$dir_class = __DIR__ . '/src/';
	$prefix    = 'class-';

	$file_parts = explode( '\\', $class_name );

	$total_parts = count( $file_parts ) - 1;
	$dir_file    = $dir_class;
	for ( $i = 1 ; $i <= $total_parts; $i++ ) {
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
 * Init plugin
 *
 * @return void
 */
function weglot_init() {
	$services = [
		'test' => 'test',
	];

	$actions = [
		new Pages_Weglot(),
	];

	if ( function_exists( 'apache_get_modules' ) && ! array_search( 'mod_rewrite', apache_get_modules(), true ) ) {
		add_action( 'admin_notices', [ '\Weglot\Notices\Rewrite_Module_Weglot', 'admin_notice' ] );
	}

	load_plugin_textdomain( 'weglot', false, WEGLOT_DIR_LANGUAGES );

	foreach ( $actions as $action ) {
		$action->hooks();
	}
}
