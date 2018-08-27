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
		$original_language   = weglot_get_original_language();
		foreach ( $languages_available as $language ) {
			$code                = $language->getIso639();
			if ( $code === $original_language ) {
				continue;
			}
			$post_name_weglot    = get_post_meta( $post->ID, sprintf( '%s_%s', Helper_Post_Meta_Weglot::POST_NAME_WEGLOT, $code ), true ); ?>
			<label for="lang-<?php echo esc_attr( $code ); ?>">
				<strong><?php echo esc_attr( $language->getLocalName() ); ?></strong>
			</label>
			<p><?php echo esc_url( home_url() ); ?>/<?php echo ($code !== $original_language ) ? esc_attr( $code . '/' ) : ''; ?><input type="text" id="lang-<?php echo esc_attr( $code ); ?>" name="post_name_weglot[<?php echo esc_attr( $code ); ?>]" value="<?php echo esc_attr( $post_name_weglot ); ?>" /></p>

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

		foreach ( $post_name_weglot as $key => $post_name ) {
			$meta_key = sprintf( '%s_%s', Helper_Post_Meta_Weglot::POST_NAME_WEGLOT, $key );
			if ( empty( $post_name ) ) {
				delete_post_meta( $post_id, $meta_key );
			} else {
				update_post_meta( $post_id, sprintf( '%s_%s', Helper_Post_Meta_Weglot::POST_NAME_WEGLOT, $key ), $post_name );
			}
		}
	}
}
