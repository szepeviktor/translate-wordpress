<?php

namespace Weglot;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Bootstrap_Weglot {
	protected $actions = [];

	protected $services = [];

	public function set_actions( $actions ) {
		$this->actions = $actions;
		return $this;
	}

	public function set_services( $services ) {
		$this->services = $services;
		return $this;
	}

	public function init_plugin() {
		foreach ( $this->actions as $action ) {
			$action->hooks();
		}
	}
}
