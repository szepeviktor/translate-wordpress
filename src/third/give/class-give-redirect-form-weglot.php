<?php

namespace WeglotWP\Third\Give;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WeglotWP\Models\Hooks_Interface_Weglot;

/**
 * Give_Redirect_Form_Weglot
 *
 * @since 2.1.1
 */
class Give_Redirect_Form_Weglot implements Hooks_Interface_Weglot {

	/**
	 * @since 2.1.1
	 * @return void
	 */
	public function __construct() {
		$this->give_active_services      = weglot_get_service( 'Give_Active_Weglot' );
	}

	/**
	 * @since 2.1.1
	 * @see Hooks_Interface_Weglot
	 *
	 * @return void
	 */
	public function hooks() {
		if ( ! $this->give_active_services->is_active() ) {
			return;
		}

		add_filter( 'give_success_page_redirect', [ '\WeglotWP\Helpers\Helper_Filter_Url_Weglot', 'filter_url_without_ajax' ], 99 );

	}

}
