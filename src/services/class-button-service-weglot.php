<?php

namespace WeglotWP\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Button services
 *
 * @since 2.0
 */
class Button_Service_Weglot {
	public function get_html() {
		return apply_filters( 'weglot_button_html', '<aside data-wg-notranslate="" class="wg-drop country-selector closed weglot-selector"><div data-wg-notranslate="" class="wgcurrent wg-li wg-flags en"><a href="#" onclick="return false;">English</a></div><ul><li class="wg-li wg-flags fr"><a data-wg-notranslate="" href="/fr/">Français</a></li></ul></aside>' );
	}
}
