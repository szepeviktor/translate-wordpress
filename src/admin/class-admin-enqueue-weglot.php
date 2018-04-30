<?php

namespace Weglot\Admin;

class Admin_Enqueue_Weglot implements Hooks_Interface_Weglot {
	public function hooks() {
		add_action( 'admin_enqueue_scripts', [ $this, 'weglot_admin_enqueue_scripts' ] );
	}

	public function weglot_admin_enqueue_scripts( $page ) {
		if ( ! in_array( $page, [ 'toplevel_page_weglot', 'settings_page_weglot-status' ] ) ) {
			return;
		}

		// Add Admin JS
		wp_register_script( 'wp-weglot-admin-js', WEGLOT_RESURL . 'wp-weglot-admin-js.js', [ 'jquery' ], WEGLOT_VERSION, true );
		wp_enqueue_script( 'wp-weglot-admin-js' );

		// Add Admin CSS
		wp_register_style( 'wp-weglot-admin-css', WEGLOT_RESURL . 'wp-weglot-admin-css.css', false, WEGLOT_VERSION, false );
		wp_enqueue_style( 'wp-weglot-admin-css' );

		// Add Selectize JS
		wp_enqueue_script( 'jquery-ui', WEGLOT_RESURL . 'selectize/js/jquery-ui.min.js', [ 'jquery' ], WEGLOT_VERSION, true);
		wp_enqueue_script( 'jquery-selectize', WEGLOT_RESURL . 'selectize/js/selectize.min.js', [ 'jquery' ], WEGLOT_VERSION, true);
		// wp_enqueue_style( 'selectize-css',     WEGLOT_RESURL . 'selectize/css/selectize.css', array(),          $ver );
		wp_enqueue_style( 'selectize-defaut-css', WEGLOT_RESURL . 'selectize/css/selectize.default.css', [], WEGLOT_VERSION);

		if ( ! function_exists('is_plugin_active')) {
			require_once(ABSPATH . '/wp-admin/includes/plugin.php');
		}

		if ($this->services['AdminNotices']->hasGTranslatePlugin()) {
			$customCss = '
                        .gt-admin-notice{
                            display:none !important;
                        }
                    ';

			wp_add_inline_style('wp-weglot-css', $customCss);
		}
	}
}
