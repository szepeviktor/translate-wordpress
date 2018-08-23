<?php

namespace WeglotWP\Actions\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WeglotWP\Models\Hooks_Interface_Weglot;
use WeglotWP\Helpers\Helper_Post_Meta_Weglot;

/**
 *
 * @since 2.1.0
 */
class Metabox_Url_Translate_Weglot implements Hooks_Interface_Weglot {


	/**
	 * @since 2.1.0
	 */
	public function __construct() {
		$this->language_services   = weglot_get_service( 'Language_Service_Weglot' );
	}

	/**
	 * @see Hooks_Interface_Weglot
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function hooks() {
		add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes_url_translate' ] );
		add_action( 'save_post', [ $this, 'save_post_meta_boxes_url_translate' ] );
	}

	/**
	 * @since 2.1.0
	 * @return void
	 */
	public function add_meta_boxes_url_translate() {
		add_meta_box( 'weglot-url-translate', __( 'Weglot URL Translate', 'weglot' ), [ $this, 'weglot_url_translate_box' ] );
	}

	/**
	 * @since 2.1.0
	 * @return void
	 * @param mixed $post
	 */
	public function weglot_url_translate_box( $post ) {
		$languages_available = $this->language_services->get_languages_configured();
		$post_name_weglot    = maybe_unserialize( get_post_meta( $post->ID, Helper_Post_Meta_Weglot::POST_NAME_WEGLOT, true ) );

		foreach ( $languages_available as $language ) {
			$post_name = ( empty( $post_name_weglot ) ) ? $post->post_name : $post_name_weglot[ $language->getIso639() ]; ?>
			<label for="lang-<?php echo esc_attr( $language->getIso639() ); ?>">
				<strong><?php echo esc_attr( $language->getLocalName() ); ?></strong>
			</label>
			<p><?php echo esc_url( home_url() ); ?>/<input type="text" id="lang-<?php echo esc_attr( $language->getIso639() ); ?>" name="post_name_weglot[<?php echo esc_attr( $language->getIso639() ); ?>]" value="<?php echo esc_attr( $post_name ); ?>" /></p>

			<?php
		}
	}

	/**
	 * @since 2.1.0
	 *
	 * @param mixed $post_id
	 * @return void
	 */
	public function save_post_meta_boxes_url_translate( $post_id ) {
		// Add nonce for security and authentication.
		$post_name_weglot   = isset( $_POST[ Helper_Post_Meta_Weglot::POST_NAME_WEGLOT ] ) ? $_POST[ Helper_Post_Meta_Weglot::POST_NAME_WEGLOT ] : ''; //phpcs:ignore

		if ( ! isset( $post_name_weglot ) ) {
			return;
		}

		// Check if user has permissions to save data.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Check if not an autosave.
		if ( wp_is_post_autosave( $post_id ) ) {
			return;
		}

		// Check if not a revision.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		update_post_meta( $post_id, Helper_Post_Meta_Weglot::POST_NAME_WEGLOT, $post_name_weglot );
	}
}
