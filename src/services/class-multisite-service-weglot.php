<?php

namespace WeglotWP\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WeglotWP\Models\Mediator_Service_Interface_Weglot;


/**
 * Multisite service
 *
 * @since 2.0
 */
class Multisite_Service_Weglot implements Mediator_Service_Interface_Weglot {

	/**
	 * @since 2.0
	 * @see Mediator_Service_Interface_Weglot
	 * @param array $services
	 * @return void
	 */
	public function use_services( $services ) {
		$this->request_url_service = $services['Request_Url_Service_Weglot'];
	}


	/**
	 * @since 2.0
	 *
	 * @return array
	 */
	public function get_list_of_network_path() {
		$paths = [];

		if ( is_multisite() ) {
			$sites = get_sites( [
				'number' => 0,
			] );

			foreach ( $sites as $site ) {
				$path = $site->path;
				array_push( $paths, $path );
			}
		} else {
			array_push( $paths, $this->request_url_service->get_home_wordpress_directory() );
		}

		return $paths;
	}
}

