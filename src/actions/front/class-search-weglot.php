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
		$this->option_services  = weglot_get_service( 'Option_Service_Weglot' );
		$this->parser_services  = weglot_get_service( 'Parser_Service_Weglot' );
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

	public function only_search_for_full_phrase( $query ) {
		if ( ! $query->is_search() || ! $query->is_main_query() ) {
			return;
		}

		$query_vars_check = apply_filters( 'weglot_query_vars_check', 's' );
		if ( empty( $query->query_vars[ $query_vars_check ] ) ) {
			return;
		}
		$original_language = weglot_get_original_language();
		$current_language  = weglot_get_current_language();

		if ( $original_language === $current_language ) {
			return;
		}

		try {
			$parser   = $this->parser_services->get_parser();
			$result   = $parser->translate( $query->query_vars[ 's' ], $current_language, $original_language ); //phpcs:ignore

			if ( empty( $result ) ) {
				return;
			}

			set_query_var( $query_vars_check, $result );
		} catch ( \Exception $th ) {
			return;
		}
	}
}
