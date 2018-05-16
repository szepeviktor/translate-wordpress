<?php

namespace WeglotWP\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Weglot\Util\Url;

use WeglotWP\Models\Mediator_Service_Interface_Weglot;

/**
 * Request URL
 *
 * @since 2.0
 */
class Request_Url_Weglot implements Mediator_Service_Interface_Weglot {
	/**
	 * @since 2.0
	 *
	 * @var string
	 */
	protected $weglot_url = null;

	public function use_services( $services ) {
		$this->option_services = $services['Option_Service_Weglot'];
	}

	/**
	 * @since 2.0
	 *
	 * @return string
	 */
	public function init_weglot_url() {
		$this->weglot_url = new Url(
			$this->get_full_url(),
			$this->option_services->get_option( 'original_language' ),
			$this->option_services->get_option( 'destination_language' )
		); // @TODO : View path prefix

		return $this;
	}

	/**
	 * Get request URL in process
	 * @since 2.0
	 * @return \Weglot\Util\Url
	 */
	public function get_weglot_url() {
		if ( null === $this->weglot_url ) {
			$this->init_weglot_url();
		}

		return $this->weglot_url;
	}

	/**
	 * Abstraction of \Weglot\Util\Url
	 * @since 2.0
	 * @return string
	 */
	public function get_current_language() {
		return $this->get_weglot_url()->detectCurrentLanguage();
	}

	/**
	 * Abstraction of \Weglot\Util\Url
	 * @since 2.0
	 *
	 * @return boolean
	 */
	public function is_translatable_url() {
		return $this->get_weglot_url()->isTranslable();
	}


	/**
	 * @since 2.0
	 *
	 * @return string
	 */
	public function get_full_url() {
		return $this->get_url_origin( $use_forwarded_host ) . $_SERVER['REQUEST_URI']; //phpcs:ignore
	}

	/**
	 * @since 2.0
	 *
	 * @param boolean $use_forwarded_host
	 * @return string
	 */
	protected function get_url_origin( $use_forwarded_host = false ) {
		$s = $_SERVER; //phpcs:ignore

		$ssl      = ( ! empty( $s['HTTPS'] ) && 'on' === $s['HTTPS'] ) ? true : false;
		$sp       = strtolower( $s['SERVER_PROTOCOL'] );
		$protocol = substr( $sp, 0, strpos( $sp, '/' ) ) . ( ( $ssl ) ? 's' : '' );
		$port     = $s['SERVER_PORT'];
		$port     = ( ( ! $ssl && '80' === $port ) || ( $ssl && '443' === $port ) ) ? '' : ':' . $port;
		$host     = ( $use_forwarded_host && isset( $s['HTTP_X_FORWARDED_HOST'] ) ) ? $s['HTTP_X_FORWARDED_HOST'] : (isset( $s['HTTP_HOST'] ) ? $s['HTTP_HOST'] : null );
		$host     = isset( $host ) ? $host : $s['SERVER_NAME'] . $port;

		return $protocol . '://' . $host;
	}
}


