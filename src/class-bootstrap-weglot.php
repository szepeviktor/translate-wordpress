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
	 * Get services
	 * @since 2.0
	 * @return array
	 */
	public function get_actions() {
		return $this->actions;
	}

	/**
	 * Set services
	 * @since 2.0
	 * @param array $services
	 * @return BootStrap_Weglot
	 */
	public function set_services( $services ) {
		foreach ( $services as $service ) {
			$this->set_service( $service );
		}
		return $this;
	}

	public function set_service( $service ) {
		$classname = get_class( $service );
		if ( preg_match( '@\\\\([\w]+)$@', $classname, $matches ) ) {
			$classname = $matches[1];
		}

		$this->services[ $classname ] = $service;
		return $this;
	}


	/**
	 * Get services
	 * @since 2.0
	 * @return array
	 */
	public function get_services() {
		return $this->services;
	}

	public function get_service( $name ) {
		if ( ! array_key_exists( $name, $this->services ) ) {
			return null;
			// @TODO : Throw exception
		}

		return $this->services[ $name ];
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
