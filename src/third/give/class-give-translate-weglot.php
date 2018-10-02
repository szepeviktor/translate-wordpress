<?php

namespace WeglotWP\Third\Give;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WeglotWP\Models\Hooks_Interface_Weglot;

/**
 * Give_Translate_Weglot
 *
 * @since 2.1.1
 */
class Give_Translate_Weglot implements Hooks_Interface_Weglot {

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

		add_filter( 'weglot_ajax_no_translate', [ $this, 'weglot_ajax_no_translate_give' ] );
		// add_filter( 'weglot_begin_treat_page_content', [ $this, 'weglot_begin_treat_page_content' ] );
		// add_filter( 'weglot_render_dom', [ $this, 'weglot_render_dom' ] );
	}

	public function weglot_ajax_no_translate_give( $array ){
		$array[] = 'get_receipt';
		return $array;
	}

	/**
	 * @since 2.1.1
	 *
	 * @param string $content
	 * @return string
	 */
	public function weglot_begin_treat_page_content( $content ){
		if ( isset( $_GET['action'] ) && 'get_receipt' === $_GET['action']) {
			return stripcslashes( $content );
		}

		return $content;
	}

	/**
	 * @since 2.1.1
	 *
	 * @param string $content
	 * @return string
	 */
	public function weglot_render_dom( $content ){
		if ( isset($_GET['action']) && $_GET['action'] === 'get_receipt') {
			return substr( substr( json_encode( $content ), 2 ), 0, -3 ) . '"';
		}

		return $content;
	}
}



