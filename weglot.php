<?php

/**
 * @package Weglot
 * @version 2.0
 */

/*
Plugin Name: Weglot Translate v2
Plugin URI: http://wordpress.org/plugins/weglot/
Description: Translate your website into multiple languages in minutes without doing any coding. Fully SEO compatible.
Author: Weglot Translate team
Author URI: https://weglot.com/
Text Domain: weglot
Domain Path: /languages/
Version: 2.0
*/


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WEGLOT_VERSION', '2.0' );
define( 'WEGLOT_PHP_MIN', '5.5' );
define( 'WEGLOT_DIR', dirname( __FILE__ ) );
define( 'WEGLOT_BNAME', plugin_basename( __FILE__ ) );
define( 'WEGLOT_DIRURL', plugin_dir_url( __FILE__ ) );

define( 'WEGLOT_TEMPLATES', WEGLOT_DIR . '/templates' );
define( 'WEGLOT_TEMPLATES_NOTICES', WEGLOT_TEMPLATES . '/notices' );


function weglot_check_compatibility() {

	// Check php version.
	if ( version_compare( phpversion(), WEGLOT_PHP_MIN ) < 0 ) {
		if ( current_filter() !== 'activate_' . WEGLOT_BNAME ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			deactivate_plugins( WEGLOT_BNAME, true );
		}

		/* translators: 1 is a plugin name, 2 is Weglot version, 3 is current php version. */
		wp_die( sprintf( esc_html__( '%1$s  requires PHP %2$s minimum, your website is actually running version %3$s.', 'weglot' ), '<strong>Weglot</strong>', '<code>' . esc_attr( WEGLOT_PHP_MIN ) . '</code>', '<code>' . esc_attr( phpversion() ) . '</code>' ) );
	}
}


function weglot_plugin_activate() {
	weglot_check_compatibility();
}

function weglot_plugin_deactivate() {
}

function weglot_plugin_uninstall() {
}

function weglot_plugin_loaded() {
	weglot_check_compatibility();

	require_once __DIR__ . '/bootstrap.php';

	weglot_init();
}


register_activation_hook( __FILE__, 'weglot_plugin_activate' );
register_deactivation_hook( __FILE__, 'weglot_plugin_deactivate' );
register_uninstall_hook( __FILE__, 'weglot_plugin_uninstall' );

add_action( 'plugins_loaded', 'weglot_plugin_loaded' );
