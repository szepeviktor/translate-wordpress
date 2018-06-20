<?php // phpcs:ignore

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WeglotWP\Bootstrap_Weglot;

use WeglotWP\Actions\Email_Translate_Weglot;
use WeglotWP\Actions\Register_Widget_Weglot;

use WeglotWP\Actions\Admin\Pages_Weglot;
use WeglotWP\Actions\Admin\Plugin_Links_Weglot;
use WeglotWP\Actions\Admin\Options_Weglot;
use WeglotWP\Actions\Admin\Admin_Enqueue_Weglot;
use WeglotWP\Actions\Admin\Customize_Menu_Weglot;

use WeglotWP\Actions\Front\Translate_Page_Weglot;
use WeglotWP\Actions\Front\Front_Enqueue_Weglot;
use WeglotWP\Actions\Front\Shortcode_Weglot;
use WeglotWP\Actions\Front\Redirect_Log_User_Weglot;

use WeglotWP\Services\Button_Service_Weglot;
use WeglotWP\Services\Request_Url_Service_Weglot;
use WeglotWP\Services\Option_Service_Weglot;
use WeglotWP\Services\Redirect_Service_Weglot;
use WeglotWP\Services\Network_Service_Weglot;
use WeglotWP\Services\Language_Service_Weglot;
use WeglotWP\Services\Replace_Url_Service_Weglot;
use WeglotWP\Services\Multisite_Service_Weglot;
use WeglotWP\Services\Replace_Link_Service_Weglot;

use WeglotWP\Models\Mediator_Service_Interface_Weglot;

use WeglotWP\Third\Woocommerce\WC_Filter_Urls_Weglot;

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
			new Option_Service_Weglot(),
			new Button_Service_Weglot(),
			new Request_Url_Service_Weglot(),
			new Redirect_Service_Weglot(),
			new Network_Service_Weglot(),
			new Language_Service_Weglot(),
			new Replace_Url_Service_Weglot(),
			new Multisite_Service_Weglot(),
			new Replace_Link_Service_Weglot(),
		];

		self::$context->set_services( $services );

		$actions = [
			new Shortcode_Weglot(),
			new Front_Enqueue_Weglot(),
			new Pages_Weglot(),
			new Plugin_Links_Weglot(),
			new Options_Weglot(),
			new Admin_Enqueue_Weglot(),
			new Translate_Page_Weglot(),
			new WC_Filter_Urls_Weglot(),
			new Redirect_Log_User_Weglot(),
			new Email_Translate_Weglot(),
			new Register_Widget_Weglot(),
			new Customize_Menu_Weglot(),
		];

		foreach ( $actions as $action ) {
			if ( $action instanceof Mediator_Service_Interface_Weglot ) {
				$action->use_services( self::$context->get_services() );
			}
		}

		foreach ( self::$context->get_services() as $service ) {
			if ( $service instanceof Mediator_Service_Interface_Weglot ) {
				$service->use_services( self::$context->get_services() );
				self::$context->set_service( $service );
			}
		}

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
