<?php

namespace WeglotWP\Actions\Front;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WeglotWP\Models\Hooks_Interface_Weglot;

/**
 * @since 2.4.0
 */
class Search_Weglot implements Hooks_Interface_Weglot {

	/**
	 * @since 2.4.0
	 */
	public function __construct() {
		$this->option_services         = weglot_get_service( 'Option_Service_Weglot' );
	}

	/**
	 * @see Hooks_Interface_Weglot
	 *
	 * @since 2.4.0
	 * @return void
	 */
	public function hooks() {
		$search_active = $this->option_services->get_option( 'active_search' );

		if ( $search_active ) {
			add_action( 'pre_get_posts', [ $this, 'only_search_for_full_phrase' ] );
		}
	}

	public function only_search_for_full_phrase() {
		if ( ! $query->is_search() || ! $query->is_main_query() ) {
			return;
		}

		// if ($query->query_vars['s'] === 'bonjour') {
		// 	// API Weglot

		// 	// Type : human...
		// 	// Type: SEARCH

		// 	// "bonjour" => "hello"
		// 	set_query_var('s', 'hello');
		// }
		// if ($query->query_vars['s'] === 'carotte') {
		// 	set_query_var('s', 'carot');
		// }
	}
}
