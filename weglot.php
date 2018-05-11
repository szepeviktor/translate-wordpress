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

define( 'WEGLOT_SLUG', 'weglot-translate' );
define( 'WEGLOT_OPTION_GROUP', 'group-weglot-translate' );
define( 'WEGLOT_VERSION', '2.0' );
define( 'WEGLOT_PHP_MIN', '5.4' );
define( 'WEGLOT_DIR', dirname( __FILE__ ) );
define( 'WEGLOT_BNAME', plugin_basename( __FILE__ ) );
define( 'WEGLOT_DIRURL', plugin_dir_url( __FILE__ ) );
define( 'WEGLOT_DIR_LANGUAGES', dirname( WEGLOT_BNAME ) . '/languages' );
define( 'WEGLOT_URL_DIST', WEGLOT_DIRURL . '/dist' );

define( 'WEGLOT_TEMPLATES', WEGLOT_DIR . '/templates' );
define( 'WEGLOT_TEMPLATES_ADMIN', WEGLOT_TEMPLATES . '/admin' );
define( 'WEGLOT_TEMPLATES_ADMIN_NOTICES', WEGLOT_TEMPLATES_ADMIN . '/notices' );
define( 'WEGLOT_TEMPLATES_ADMIN_PAGES', WEGLOT_TEMPLATES_ADMIN . '/pages' );

/**
 * Check compatibility this Weglot with WordPress config
 *
 * @return void
 */
function weglot_check_compatibility() {

	// Check php version.
	if ( version_compare( phpversion(), WEGLOT_PHP_MIN ) < 0 ) {
		if ( current_filter() !== 'activate_' . WEGLOT_BNAME ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			deactivate_plugins( WEGLOT_BNAME, true );
		}

		/* translators: 1 is a plugin name, 2 is Weglot version, 3 is current php version. */
		wp_die( sprintf( esc_html__( '%1$s  requires PHP %2$s minimum, your website is actually running version %3$s.', 'weglot' ), '<strong>Weglot translate</strong>', '<code>' . esc_attr( WEGLOT_PHP_MIN ) . '</code>', '<code>' . esc_attr( phpversion() ) . '</code>' ) );
	}
}

/**
 * Activate Weglot
 * @since 2.0
 * @return void
 */
function weglot_plugin_activate() {
	weglot_check_compatibility();

	require_once __DIR__ . '/bootstrap.php';

	Context_Weglot::weglot_get_context()->activate_plugin();
}

/**
 * Deactivate Weglot
 * @since 2.0
 * @return void
 */
function weglot_plugin_deactivate() {
}

/**
 * Uninstall Weglot
 * @since 2.0
 * @return void
 */
function weglot_plugin_uninstall() {
}

/**
 * Load Weglot
 * @since 2.0
 * @return void
 */
function weglot_plugin_loaded() {
	weglot_check_compatibility();

	require_once __DIR__ . '/vendor/autoload.php';
	require_once __DIR__ . '/bootstrap.php';
	require_once __DIR__ . '/weglot-functions.php';

	weglot_init();
}


register_activation_hook( __FILE__, 'weglot_plugin_activate' );
register_deactivation_hook( __FILE__, 'weglot_plugin_deactivate' );
register_uninstall_hook( __FILE__, 'weglot_plugin_uninstall' );

add_action( 'plugins_loaded', 'weglot_plugin_loaded' );
