<?php

namespace Weglot\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Weglot\Models\Hooks_Interface_Weglot;
use Weglot\Helpers\Helper_Pages_Weglot;
use Weglot\Helpers\Helper_Tabs_Admin_Weglot;

/**
 * Register pages administration
 *
 * @since 2.0
 *
 */
class Pages_Weglot implements Hooks_Interface_Weglot {
	/**
	 * @see Hooks_Interface_Weglot
	 *
	 * @since 2.0
	 * @return void
	 */
	public function hooks() {
		add_action( 'admin_menu', [ $this, 'weglot_plugin_menu' ] );
	}

	/**
	 * Add menu and sub pages
	 *
	 * @see admin_menu
	 *
	 * @since 2.0
	 * @return void
	 */
	public function weglot_plugin_menu() {
		add_menu_page( 'Weglot', 'Weglot', 'manage_options', Helper_Pages_Weglot::SETTINGS, [ $this, 'weglot_plugin_settings_page' ] );
		// , WEGLOT_DIRURL . '/images/weglot_fav_bw.png'
	}

	public function weglot_plugin_settings_page() {
		$this->tabs       = Helper_Tabs_Admin_Weglot::get_full_tabs();
		$this->tab_active = Helper_Tabs_Admin_Weglot::SETTINGS;

		if ( isset( $_GET['tab'] ) ) {
			$this->tab_active = sanitize_text_field( wp_unslash( $_GET['tab'] ) );
		}

		include_once WEGLOT_TEMPLATES_ADMIN_PAGES . '/settings.php';
	}
}
