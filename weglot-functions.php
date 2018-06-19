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

/**
 * Get current language
 * @since 2.0
 * @return string
 */
function weglot_get_current_language() {
	return Context_Weglot::weglot_get_context()->get_service( 'Request_Url_Service_Weglot' )->get_current_language();
}

/**
 * Get destination language
 * @since 2.0
 * @return string
 */
function weglot_get_destination_language() {
	return weglot_get_option( 'destination_language' );
}

/**
 * Get Request Url Service
 * @since 2.0
 * @return Request_Url_Service_Weglot
 */
function weglot_get_request_url_service() {
	return Context_Weglot::weglot_get_context()->get_service( 'Request_Url_Service_Weglot' );
}

/**
 * Get an array with current and original language
 * @since 2.0
 * @return array
 */
function weglot_get_current_and_original_language() {
	return [
		'current'  => weglot_get_current_language(),
		'original' => weglot_get_original_language(),
	];
}

/**
 * Get languages available on Weglot
 * @since 2.0
 * @return array
 */
function weglot_get_languages_available() {
	return Context_Weglot::weglot_get_context()->get_service( 'Language_Service_Weglot' )->get_languages_available();
}

function weglot_get_languages_configured() {
	return Context_Weglot::weglot_get_context()->get_service( 'Language_Service_Weglot' )->get_languages_configured();
}

/**
 * Get button selector HTML
 * @since 2.0
 * @return string
 * @param mixed $add_class
 */
function weglot_get_button_selector_html( $add_class ) {
	return Context_Weglot::weglot_get_context()->get_service( 'Button_Service_Weglot' )->get_html( $add_class );
}

/**
 * @since 2.0
 * @return array
 */
function weglot_get_button_allowed_tags() {
	return Context_Weglot::weglot_get_context()->get_service( 'Button_Service_Weglot' )->get_allowed_tags();
}

/**
 * Get exclude urls
 * @since 2.0
 * @return array
 */
function weglot_get_exclude_urls() {
	return weglot_get_option( 'exclude_urls' );
}

/**
 * Get exclude AMP to translate
 * @since 2.0
 * @return bool
 */
function weglot_get_exclude_amp_translation() {
	return weglot_get_option( 'exclude_amp' );
}
