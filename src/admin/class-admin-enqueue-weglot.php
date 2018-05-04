<?php

namespace Weglot\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Weglot\Models\Hooks_Interface_Weglot;

class Admin_Enqueue_Weglot implements Hooks_Interface_Weglot {
	public function hooks() {
		add_action( 'admin_enqueue_scripts', [ $this, 'weglot_admin_enqueue_scripts' ] );
	}

	public function weglot_admin_enqueue_scripts( $page ) {
		if ( ! array_search( $page, [ 'toplevel_page_weglot', 'settings_page_weglot-status' ], true ) ) {
			return;
		}
	}
}
