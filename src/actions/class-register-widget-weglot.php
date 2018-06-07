<?php

namespace Delipress\WordPress\Optin;

defined( 'ABSPATH' ) or die( 'Cheatin&#8217; uh?' );

use WeglotWP\Models\Hooks_Interface_Weglot;

/**
 * Registe widget weglot
 *
 * @since 2.0.0
 */
class Register_Widget_Weglot implements Hooks_Interface_Weglot {

	/**
	 * @see HooksInterface
	 * @return void
	 */
	public function hooks() {
		add_action( 'widgets_init', [ $this, 'register_widget_weglot' ] );
	}

	public function register_widget_weglot() {
		register_widget( 'Delipress\WordPress\Optin\WidgetClassWP' );
	}
}
