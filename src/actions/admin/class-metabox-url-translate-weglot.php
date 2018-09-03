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
		add_action( 'wp_ajax_weglot_post_name', [ $this, 'weglot_post_name' ] );

		if ( apply_filters( 'weglot_wp_unique_post_slug', true ) ) {
			add_filter( 'wp_unique_post_slug', [ $this, 'weglot_wp_unique_post_slug' ], 10, 6 );
		}
	}

	/**
	 * Filters the unique post slug.
	 *
	 * @since 2.1.0
	 *
	 * @param string $slug          The post slug.
	 * @param int    $post_ID       Post ID.
	 * @param string $post_status   The post status.
	 * @param string $post_type     Post type.
	 * @param int    $post_parent   Post parent ID
	 * @param string $original_slug The original post slug.
	 * @param mixed $post_id
	 */
	public function weglot_wp_unique_post_slug( $slug, $post_id, $post_status, $post_type, $post_parent, $original_slug ) {
		$args            = [
			'meta_value'     => $slug, //phpcs:ignore
			'meta_compare'   => '=',
			'post_type'      => get_post_types( apply_filters( 'weglot_request_post_type_for_uri', [
				'public' => true,
			] ) ),
		];

		$query    = new \WP_Query( $args );
		if ( 1 === $query->post_count ) {
			$suffix   = 2;
			do {
				$alt_post_name      = _truncate_post_slug( $slug, 200 - ( strlen( $suffix ) + 1 ) ) . "-$suffix";
				$args['meta_value'] = $alt_post_name; //phpcs:ignore
				$query              = new \WP_Query( $args );
				$suffix++;
			} while ( 1 === $query->post_count );
			$slug = $alt_post_name;
		}

		return $slug;
	}

	/**
	 * @since 2.1.0
	 * @return void
	 */
	public function weglot_post_name() {
		$weglot_post_name = ( isset( $_POST['post_name'] ) && ! empty( $_POST['post_name'] ) ) ? sanitize_title( $_POST['post_name'] ) : null ; //phpcs:ignore
		$code_language    = ( isset( $_POST['lang'] ) && ! empty( $_POST['lang'] ) ) ? sanitize_text_field( $_POST['lang'] ) : null ; //phpcs:ignore
		$post_id          = ( isset( $_POST['id'] ) && ! empty( $_POST['id'] ) ) ? sanitize_text_field( $_POST['id'] ) : null ; //phpcs:ignore

		if ( ! $weglot_post_name || ! $code_language || ! $post_id ) {
			wp_send_json_error( [
				'success' => false,
				'code'    => 'missing_parameter',
			] );
			return;
		}

		$post             = get_post( $post_id );
		if ( $post->post_name === $weglot_post_name ) {
			wp_send_json_error( [
				'success'  => true,
				'code'     => 'same_post_name',
			] );
			return;
		}

		$meta_key        = sprintf( '%s_%s', Helper_Post_Meta_Weglot::POST_NAME_WEGLOT, $code_language );
		$args            = [
			'meta_key'       => $meta_key, //phpcs:ignore
			'meta_value'     => $weglot_post_name, //phpcs:ignore
			'meta_compare'   => '=',
			'post_type'      => get_post_types( apply_filters( 'weglot_request_post_type_for_uri', [
				'public' => true,
			] ) ),
		];

		$query    = new \WP_Query( $args );

		if ( 1 === $query->post_count ) {
			wp_send_json_error( [
				'success'  => true,
				'code'     => 'same_post_name',
			] );
			return;
		}


		$weglot_post_name = wp_unique_post_slug( $weglot_post_name, $post->ID, $post->post_status, $post->post_type, $post->post_parent );

		update_post_meta( $post_id, $meta_key, $weglot_post_name );

		wp_send_json_success( [
			'success' => true,
			'result'  => [
				'slug' => $weglot_post_name,
			],
		] );
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
		include_once WEGLOT_TEMPLATES_ADMIN_METABOXES . '/url-translate.php';
	}

	/**
	 * @since 2.1.0
	 *
	 * @param mixed $post_id
	 * @return void
	 */
	public function save_post_meta_boxes_url_translate( $post_id ) {
		// Add nonce for security and authentication.
		$post_name_weglot   = isset( $_POST[ Helper_Post_Meta_Weglot::POST_NAME_WEGLOT ] ) ? $_POST[ Helper_Post_Meta_Weglot::POST_NAME_WEGLOT ] : []; //phpcs:ignore

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
