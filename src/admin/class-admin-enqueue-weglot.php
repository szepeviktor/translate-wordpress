<?php

namespace WeglotWP\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WeglotWP\Models\Hooks_Interface_Weglot;
use WeglotWP\Helpers\Helper_Pages_Weglot;

/**
 * Enqueue CSS / JS on administration
 *
 * @since 2.0
 *
 */
class Admin_Enqueue_Weglot implements Hooks_Interface_Weglot {
	/**
	 * @see Hooks_Interface_Weglot
	 *
	 * @since 2.0
	 * @return void
	 */
	public function hooks() {
		add_action( 'admin_enqueue_scripts', [ $this, 'weglot_admin_enqueue_scripts' ] );
	}

	/**
	 * Register CSS and JS
	 *
	 * @see admin_enqueue_scripts
	 * @since 2.0
	 * @param string $page
	 * @return void
	 */
	public function weglot_admin_enqueue_scripts( $page ) {
		if ( ! in_array( $page, [ 'toplevel_page_' . Helper_Pages_Weglot::SETTINGS ], true ) ) {
			return;
		}

		wp_enqueue_script( 'weglot-admin-selectize-js', WEGLOT_URL_DIST . '/selectize/js/selectize.min.js', [ 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ] );
		wp_enqueue_style( 'weglot-admin-selectize-css', WEGLOT_URL_DIST . '/selectize/css/selectize.css' );

		wp_enqueue_script( 'weglot-admin', WEGLOT_URL_DIST . '/bundle.js', [ 'weglot-admin-selectize-js' ] );
	}
}
