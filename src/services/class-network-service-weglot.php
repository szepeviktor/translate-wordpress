<?php

namespace WeglotWP\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WeglotWP\Models\Mediator_Service_Interface_Weglot;

/**
 * Network service
 *
 * @since 2.0
 */
class Network_Service_Weglot implements Mediator_Service_Interface_Weglot {

	/**
	 * @since 2.0
	 * @var array|null
	 */
	protected $networks = null;

	/**
	 * @see Mediator_Service_Interface_Weglot
	 * @since 2.0
	 * @param array $services
	 * @return void
	 */
	public function use_services( $services ) {
		$this->request_url_services = $services['Request_Url_Service'];
	}

	/**
	 * @since 2.0
	 *
	 * @return Network_Service_Weglot
	 */
	public function set_networks() {
		if ( is_multisite() ) {
			$sites = get_sites( [
				'number' => 0,
			] );

			foreach ( $sites as $site ) {
				$path = $site->path;
				array_push( $this->networks, $path );
			}
		} else {
			array_push( $this->networks, $this->request_url_services->get_home_wordpress_directory() . '/' );
		}

		return $this;
	}


	/**
	 * @since 2.0
	 * @return array
	 */
	public function get_networks() {
		if ( null === $this->networks ) {
			$this->set_networks();
		}

		return $this->networks;
	}
}
