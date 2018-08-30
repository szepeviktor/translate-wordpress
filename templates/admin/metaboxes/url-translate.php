<?php

use WeglotWP\Helpers\Helper_Post_Meta_Weglot;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$languages_available     = $this->language_services->get_languages_configured();
$original_language       = weglot_get_original_language();
list( $permalink )       = get_sample_permalink( $post->ID );
$display_link            = str_replace( array( '%pagename%', '%postname%', home_url() ), '', $permalink );
$display_link            = implode( '/', array_filter( explode( '/', $display_link ), 'strlen' ) );

foreach ( $languages_available as $language ) {
	$code                = $language->getIso639();
	if ( $code === $original_language ) {
		continue;
	}
	$post_name_weglot    = get_post_meta( $post->ID, sprintf( '%s_%s', Helper_Post_Meta_Weglot::POST_NAME_WEGLOT, $code ), true ); ?>
	<label for="lang-<?php echo esc_attr( $code ); ?>">
		<strong><?php echo esc_attr( $language->getLocalName() ); ?></strong>
	</label>
	<p><?php echo esc_url( home_url() ); ?>/<?php echo esc_attr( $code . '/' . $display_link . '/' ); ?><input type="text" id="lang-<?php echo esc_attr( $code ); ?>" name="post_name_weglot[<?php echo esc_attr( $code ); ?>]" value="<?php echo esc_attr( $post_name_weglot ); ?>" /></p>

	<?php
}
