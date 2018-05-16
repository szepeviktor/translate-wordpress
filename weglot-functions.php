<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get all options
 * @since 2.0
 *
 * @return array
 */
function weglot_get_options() {
	return Context_Weglot::weglot_get_context()->get_service( 'Option_Service_Weglot' )->get_options();
}

/**
 * Get option
 * @since 2.0
 * @param string $key
 * @return any
 */
function weglot_get_option( $key ) {
	return Context_Weglot::weglot_get_context()->get_service( 'Option_Service_Weglot' )->get_option( $key );
}

/**
 * Get original language
 * @since 2.0
 * @return string
 */
function weglot_get_original_language() {
	return weglot_get_option( 'original_language' );
}
