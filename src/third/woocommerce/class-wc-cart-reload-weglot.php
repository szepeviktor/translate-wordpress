<?php

namespace WeglotWP\Third\Woocommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WeglotWP\Models\Hooks_Interface_Weglot;

/**
 * WC_Cart_Reload_Weglot
 *
 * @since 2.4.0
 */
class WC_Cart_Reload_Weglot implements Hooks_Interface_Weglot {
	protected $name_transient = 'weglot_wc_clean_cart';

	/**
	 * @since 2.4.0
	 * @return void
	 */
	public function __construct() {
		$this->wc_active_services        = weglot_get_service( 'WC_Active_Weglot' );
	}

	/**
	 * @since 2.4.0
	 * @see Hooks_Interface_Weglot
	 * @return void
	 */
	public function hooks() {
		if ( ! $this->wc_active_services->is_active() ) {
			return;
		}

		$active_wc_reload = weglot_get_option( 'active_wc_reload' );

		if ( ! $active_wc_reload ) {
			return;
		}

		add_action( 'wp_footer', [ $this, 'weglot_wc_footer' ] );
	}

	/**
	 * @since 2.4.0
	 * @return void
	 */
	public  function weglot_wc_footer() {
		$click_selector = apply_filters( 'weglot_wc_reload_selector', '.weglot-lang a' );
		?>
		<script>
			document.addEventListener('DOMContentLoaded', function(){

                Object.keys(window.sessionStorage).forEach(function(element) {
                    if(element.startsWith("wc_cart_hash_") || element.startsWith("wc_fragments_")) {
                        window.sessionStorage.removeItem(element);
                    }
                });


				jQuery( '<?php echo esc_attr( $click_selector ); ?>' ).on('click', function(e) {
					e.preventDefault();
					var href = jQuery(this).attr('href')

                    Object.keys(window.sessionStorage).forEach(function(element) {
                        if(element.startsWith("wc_cart_hash_") || element.startsWith("wc_fragments_")) {
                            window.sessionStorage.removeItem(element);
                        }
                    });
					window.location.href = href
				})
			})
		</script>
		<?php
	}
}
