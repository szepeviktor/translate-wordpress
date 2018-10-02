<?php

namespace WeglotWP\Third\Give;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


use WeglotWP\Models\Third_Active_Interface_Weglot;


/**
 * Give_Active_Weglot
 *
 * @since 2.1.1
 */
class Give_Active_Weglot implements Third_Active_Interface_Weglot {

	/**
	 * Give is active ?
	 * @since 2.1.1
	 *
	 * @return boolean
	 */
	public function is_active() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		if ( ! is_plugin_active( 'give/give.php' ) ) {
			return false;
		}

		return true;
	}
}
