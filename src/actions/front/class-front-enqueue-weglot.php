<?php

namespace WeglotWP\Actions\Front;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WeglotWP\Models\Hooks_Interface_Weglot;

/**
 * Enqueue CSS / JS on front
 *
 * @since 2.0
 */
class Front_Enqueue_Weglot implements Hooks_Interface_Weglot {

	/**
	 * @see Hooks_Interface_Weglot
	 *
	 * @since 2.0
	 * @return void
	 */
	public function hooks() {
		add_action( 'wp_enqueue_scripts', [ $this, 'weglot_wp_enqueue_scripts' ] );
	}

	/**
	 * @see wp_enqueue_scripts
	 * @since 2.0
	 *
	 * @return void
	 */
	public function weglot_wp_enqueue_scripts() {
		wp_enqueue_style( 'weglot-css', WEGLOT_URL_DIST . '/css/front-css.css' );
	}
}
