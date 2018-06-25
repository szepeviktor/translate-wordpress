<?php // phpcs:ignore

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WeglotWP\Bootstrap_Weglot;

/**
 * Only use for get one context
 *
 * @since 2.0
 */
abstract class Context_Weglot {

	/**
	 * @static
	 * @since 2.0
	 * @var Bootstrap_Weglot|null
	 */
	protected static $context;

	/**
	 * Create context if not exist
	 *
	 * @static
	 * @since 2.0
	 * @return void
	 */
	public static function weglot_get_context() {
		if ( null !== self::$context ) {
			return self::$context;
		}

		self::$context = new Bootstrap_Weglot();

		$services = [
			\WeglotWP\Services\Button_Service_Weglot::class,
			\WeglotWP\Services\Request_Url_Service_Weglot::class,
			\WeglotWP\Services\Option_Service_Weglot::class,
			\WeglotWP\Services\Redirect_Service_Weglot::class,
			\WeglotWP\Services\Network_Service_Weglot::class,
			\WeglotWP\Services\Language_Service_Weglot::class,
			\WeglotWP\Services\Replace_Url_Service_Weglot::class,
			\WeglotWP\Services\Multisite_Service_Weglot::class,
			\WeglotWP\Services\Replace_Link_Service_Weglot::class,
		];

		self::$context->set_services( $services );

		$actions = [
			\WeglotWP\Actions\Email_Translate_Weglot::class,
			\WeglotWP\Actions\Register_Widget_Weglot::class,
			\WeglotWP\Actions\Admin\Pages_Weglot::class,
			\WeglotWP\Actions\Admin\Plugin_Links_Weglot::class,
			\WeglotWP\Actions\Admin\Options_Weglot::class,
			\WeglotWP\Actions\Admin\Admin_Enqueue_Weglot::class,
			\WeglotWP\Actions\Admin\Customize_Menu_Weglot::class,
			\WeglotWP\Actions\Front\Translate_Page_Weglot::class,
			\WeglotWP\Actions\Front\Front_Enqueue_Weglot::class,
			\WeglotWP\Actions\Front\Shortcode_Weglot::class,
			\WeglotWP\Actions\Front\Redirect_Log_User_Weglot::class,
			\WeglotWP\Third\Woocommerce\WC_Filter_Urls_Weglot::class,
		];

		self::$context->set_actions( $actions );

		return self::$context;
	}
}


/**
 * Init plugin
 * @since 2.0
 * @return void
 */
function weglot_init() {
	if ( function_exists( 'apache_get_modules' ) && ! in_array( 'mod_rewrite', apache_get_modules(), true ) ) {
		add_action( 'admin_notices', [ '\WeglotWP\Notices\Rewrite_Module_Weglot', 'admin_notice' ] );
	}

	load_plugin_textdomain( 'weglot', false, WEGLOT_DIR_LANGUAGES );

	Context_Weglot::weglot_get_context()->init_plugin();
}
