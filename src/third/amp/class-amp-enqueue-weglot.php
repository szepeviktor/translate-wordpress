<?php

namespace WeglotWP\Third\Amp;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WeglotWP\Models\Hooks_Interface_Weglot;

/**
 * Amp_Enqueue_Weglot
 *
 * @since 2.0
 */
class Amp_Enqueue_Weglot implements Hooks_Interface_Weglot {

	/**
	 * @since 2.0
	 */
	public function __construct() {
		$this->option_services           = weglot_get_service( 'Option_Service_Weglot' );
	}

	/**
	 * @since 2.0
	 * @see Hooks_Interface_Weglot
	 *
	 * @return void
	 */
	public function hooks() {
		if ( ! defined( 'AMPFORWP_PLUGIN_DIR' ) && ! defined( 'AMP__VERSION' ) ) {
			return;
		}

		add_action( 'amp_post_template_css', [ $this, 'weglot_amp_post_template_css' ] );
		add_action( 'amp_post_template_head', [ $this, 'weglot_amp_post_template_head' ] );
	}

	/**
	 * @since 2.0
	 *
	 * @return void
	 */
	public function weglot_amp_post_template_head() {
		?>
		<link rel="stylesheet" href="<?php echo esc_url( WEGLOT_URL_DIST . '/css/front-css.css' ); ?>" />
		<?php
	}

	/**
	 * @since 2.0
	 *
	 * @return void
	 */
	public function weglot_amp_post_template_css() {
		echo $this->option_services->get_css_custom_inline(); //phpcs:ignore
	}
}
