<?php

namespace WeglotWP\Actions\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WeglotWP\Models\Hooks_Interface_Weglot;
use WeglotWP\Helpers\Helper_Pages_Weglot;
use WeglotWP\Helpers\Helper_Tabs_Admin_Weglot;

/**
 * Enqueue CSS / JS on administration
 *
 * @since 2.0
 *
 */
class Admin_Enqueue_Weglot implements Hooks_Interface_Weglot {

	/**
	 * @since 2.0
	 */
	public function __construct() {
		$this->language_services = weglot_get_service( 'Language_Service_Weglot' );
		$this->option_services   = weglot_get_service( 'Option_Service_Weglot' );
	}

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

		wp_enqueue_script( 'weglot-admin-selectize-js', WEGLOT_URL_DIST . '/selectize.js', [ 'jquery' ] );

		wp_enqueue_script( 'weglot-admin', WEGLOT_URL_DIST . '/admin-js.js', [ 'weglot-admin-selectize-js' ] );

		wp_localize_script( 'weglot-admin', 'weglot_languages', [
			'available' => $this->language_services->get_languages_available(),
		]);

		wp_enqueue_style( 'weglot-admin-css', WEGLOT_URL_DIST . '/css/admin-css.css' );

		if ( isset( $_GET['tab'] ) && Helper_Tabs_Admin_Weglot::APPEARANCE === $_GET['tab'] ) { //phpcs:ignore
			wp_enqueue_style( 'weglot-css', WEGLOT_URL_DIST . '/css/front-css.css' );
			wp_localize_script( 'weglot-admin', 'weglot_css', [
				'inline' => $this->option_services->get_css_custom_inline(),
			]);
		}
	}
}
