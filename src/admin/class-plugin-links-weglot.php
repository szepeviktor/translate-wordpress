<?php

namespace Weglot\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Weglot\Models\Hooks_Interface_Weglot;
use Weglot\Helpers\Helper_Pages_Weglot;

class Plugin_Links_Weglot implements Hooks_Interface_Weglot {
	public function hooks() {
		add_filter( 'plugin_action_links_' . WEGLOT_BNAME, [ $this, 'weglot_plugin_action_links' ] );
	}

	public function weglot_plugin_action_links( $links ) {
		$url  = get_admin_url( null, sprintf( 'admin.php?page=%s', Helper_Pages_Weglot::SETTINGS ) );
		$text = __( 'Settings', 'weglot' );

		$links[] = sprintf( '<a href="%s">%s</a>', $url, $text );
		return $links;
	}
}

