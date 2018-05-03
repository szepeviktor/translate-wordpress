<?php

namespace Weglot\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Weglot\Models\Hooks_Interface_Weglot;

class Pages_Weglot implements Hooks_Interface_Weglot {
	public function hooks() {
		add_action( 'admin_menu', [ $this, 'weglot_plugin_menu' ] );
	}

	public function weglot_plugin_menu() {
		add_menu_page( 'Weglot', 'Weglot', 'manage_options', 'weglot', [ $this, 'weglot_plugin_settings_page' ] );
		// , WEGLOT_DIRURL . '/images/weglot_fav_bw.png'
	}

	public function weglot_plugin_settings_page() {
		include_once WEGLOT_TEMPLATES_ADMIN_PAGES . '/settings.php';
	}
}
