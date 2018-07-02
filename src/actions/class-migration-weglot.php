<?php

namespace WeglotWP\Actions;

defined( 'ABSPATH' ) or die( 'Cheatin&#8217; uh?' );

use WeglotWP\Models\Hooks_Interface_Weglot;

/**
 * Migration Weglot
 *
 * @since 2.0
 */
class Migration_Weglot implements Hooks_Interface_Weglot {


	/**
	 * @since 2.0
	 */
	public function __construct() {
		$this->migration_services   = weglot_get_service( 'Migration_Service_Weglot' );
		return $this;
	}


	/**
	 * @see HooksInterface
	 * @return void
	 */
	public function hooks() {
		$version = weglot_get_version();
		// var_dump($version); die;
	}

}
