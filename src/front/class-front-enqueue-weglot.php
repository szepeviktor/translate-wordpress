<?php

namespace Weglot\Front;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Weglot\Models\Hooks_Interface_Weglot;

class Front_Enqueue_Weglot implements Hooks_Interface_Weglot {
	public function hooks() {
		add_action( 'wp_enqueue_scripts', [ $this, 'weglot_wp_enqueue_scripts' ] );
	}

	public function weglot_wp_enqueue_scripts() {
	}
}
