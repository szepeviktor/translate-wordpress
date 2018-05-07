<?php

namespace Weglot;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Init plugin
 *
 * @since 2.0
 */
class Bootstrap_Weglot {
	/**
	 * List actions WordPress
	 * @since 2.0
	 * @var array
	 */
	protected $actions = [];

	/**
	 * List class services
	 * @since 2.0
	 * @var array
	 */
	protected $services = [];

	/**
	 * Set actions
	 *
	 * @since 2.0
	 * @param array $actions
	 * @return BootStrap_Weglot
	 */
	public function set_actions( $actions ) {
		$this->actions = $actions;
		return $this;
	}
	/**
	 * Set services
	 *
	 * @param array $services
	 * @return BootStrap_Weglot
	 */
	public function set_services( $services ) {
		$this->services = $services;
		return $this;
	}

	/**
	 * Init plugin
	 * @since 2.0
	 * @return void
	 */
	public function init_plugin() {
		foreach ( $this->actions as $action ) {
			$action->hooks();
		}
	}
}
