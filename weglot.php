<?php
/**
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

if ( ! defined('ABSPATH')) {
	exit;
}


define('WEGLOT_NAME', 'Weglot');
define('WEGLOT_SLUG', 'weglot-translate');
define('WEGLOT_OPTION_GROUP', 'group-weglot-translate');
define('WEGLOT_VERSION', '2.0');
define('WEGLOT_PHP_MIN', '5.4');
define('WEGLOT_DIR', __DIR__);
define('WEGLOT_BNAME', plugin_basename(__FILE__));
define('WEGLOT_DIRURL', plugin_dir_url(__FILE__));
define('WEGLOT_DIR_LANGUAGES', dirname(WEGLOT_BNAME) . '/languages');
define('WEGLOT_URL_DIST', WEGLOT_DIRURL . '/dist');
define('WEGLOT_LATEST_VERSION', '1.13.1');

define('WEGLOT_TEMPLATES', WEGLOT_DIR . '/templates');
define('WEGLOT_TEMPLATES_ADMIN', WEGLOT_TEMPLATES . '/admin');
define('WEGLOT_TEMPLATES_ADMIN_NOTICES', WEGLOT_TEMPLATES_ADMIN . '/notices');
define('WEGLOT_TEMPLATES_ADMIN_PAGES', WEGLOT_TEMPLATES_ADMIN . '/pages');

/**
 * Check compatibility this Weglot with WordPress config.
 */
function weglot_is_compatible() {
	// Check php version.
	if (version_compare(PHP_VERSION, WEGLOT_PHP_MIN) < 0) {
		add_action('admin_notices', 'weglot_php_min_compatibility' );
		return false;
	}

	return true;
}

function weglot_php_min_compatibility() {
	if ( ! file_exists( WEGLOT_TEMPLATES_ADMIN_NOTICES . '/php-min.php' ) ) {
		return;
	}

	include_once WEGLOT_TEMPLATES_ADMIN_NOTICES . '/php-min.php';
}

/**
 * Activate Weglot.
 *
 * @since 2.0
 */
function weglot_plugin_activate() {
	if ( ! weglot_is_compatible() ) {
		return;
	}

	require_once __DIR__ . '/bootstrap.php';

	Context_Weglot::weglot_get_context()->activate_plugin();
}

/**
 * Deactivate Weglot.
 *
 * @since 2.0
 */
function weglot_plugin_deactivate() {
}

/**
 * Uninstall Weglot.
 *
 * @since 2.0
 */
function weglot_plugin_uninstall() {
}

/**
 * Rollback v2 => v1
 *
 * @return void
 */
function weglot_rollback( ) {
	if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'weglot_rollback' ) ) {
		wp_nonce_ays( '' );
	}



	$plugin_transient = get_site_transient( 'update_plugins' );
	$plugin_folder    = WEGLOT_BNAME;
	$plugin_file      = basename( __FILE__ );
	$version          = WEGLOT_LATEST_VERSION;
	$url              = sprintf( 'https://downloads.wordpress.org/plugin/weglot.%s.zip', WEGLOT_LATEST_VERSION );


	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
	$plugin        = 'weglot/weglot.php';
	$title         = sprintf( __( '%s Update Rollback', 'weglot' ), WEGLOT_NAME );
	$nonce         = 'upgrade-plugin_' . $plugin;
	$url           = 'update.php?action=upgrade-plugin&plugin=' . rawurlencode( $plugin );
	$version       = '1.13.1';
	$upgrader_skin = new Plugin_Upgrader_Skin( compact( 'title', 'nonce', 'url', 'plugin', 'version' ) );

	$rollback = new \WeglotWP\Helpers\Helper_Rollback_Weglot( $upgrader_skin );
	var_dump($rollback);
	wp_die(
		// translators: %s is the plugin name.
		'', sprintf( __( '%s Update Rollback', 'weglot' ), WEGLOT_NAME ), array(
			'response' => 200,
		)
	);
}

/**
 * Load Weglot.
 *
 * @since 2.0
 */
function weglot_plugin_loaded() {
	require_once __DIR__ . '/vendor/autoload.php';
	require_once __DIR__ . '/weglot-autoload.php';
	require_once __DIR__ . '/weglot-compatibility.php';

	if ( ! weglot_is_compatible() ) {
		add_action( 'admin_post_weglot_rollback', 'weglot_rollback' );
	} else {
		require_once __DIR__ . '/bootstrap.php';
		require_once __DIR__ . '/weglot-functions.php';

		weglot_init();
	}
}

register_activation_hook(__FILE__, 'weglot_plugin_activate');
register_deactivation_hook(__FILE__, 'weglot_plugin_deactivate');
register_uninstall_hook(__FILE__, 'weglot_plugin_uninstall');

add_action('plugins_loaded', 'weglot_plugin_loaded');
