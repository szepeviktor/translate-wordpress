<?php

function weglot_get_options() {
	return Context_Weglot::weglot_get_context()->get_service( 'Option_Service_Weglot' )->get_options();
}

function weglot_get_option( $key ) {
	return Context_Weglot::weglot_get_context()->get_service( 'Option_Service_Weglot' )->get_option( $key );
}
