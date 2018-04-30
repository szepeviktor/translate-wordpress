<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

spl_autoload_register( 'weglot_autoload' );

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

function weglot_init() {
	$services = [
		'test' => 'test',
	];

	$actions = [
		'test' => 'test',
	];

	if ( function_exists( 'apache_get_modules' ) && ! in_array( 'mod_rewrite', apache_get_modules() ) ) {
		add_action( 'admin_notices', [ '\Weglot\Notices\Rewrite_Module_Weglot', 'admin_notice' ] );
	}
}



