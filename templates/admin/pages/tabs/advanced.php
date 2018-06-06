<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Weglot\Client\Client;

use WeglotWP\Helpers\Helper_Tabs_Admin_Weglot;

$options_available = [
	'auto_redirect' => [
		'key'         => 'auto_redirect',
		'label'       => __( 'Auto redirection', 'weglot' ),
		'description' => '',
	],
	'email_translate' => [
		'key'         => 'email_translate',
		'label'       => __( 'Email translate', 'weglot' ),
		'description' => __( 'Translate email who use function wp_mail', 'weglot' ),
	],
];

?>

<h3><?php esc_html_e( 'Advanced', 'weglot' ); ?></h3>
<hr>
<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $options_available['auto_redirect']['key'] ); ?>">
					<?php echo esc_html( $options_available['auto_redirect']['label'] ); ?>
				</label>
			</th>
			<td class="forminp forminp-text">
				<input
					name="<?php echo esc_attr( sprintf( '%s[%s]', WEGLOT_SLUG, $options_available['auto_redirect']['key'] ) ); ?>"
					id="<?php echo esc_attr( $options_available['auto_redirect']['key'] ); ?>"
					type="checkbox"
					<?php checked( $this->options[ $options_available['auto_redirect']['key'] ], 1 ); ?>
				>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $options_available['email_translate']['key'] ); ?>">
					<?php echo esc_html( $options_available['email_translate']['label'] ); ?>
				</label>
			</th>
			<td class="forminp forminp-text">
				<input
					name="<?php echo esc_attr( sprintf( '%s[%s]', WEGLOT_SLUG, $options_available['email_translate']['key'] ) ); ?>"
					id="<?php echo esc_attr( $options_available['email_translate']['key'] ); ?>"
					type="checkbox"
					<?php checked( $this->options[ $options_available['email_translate']['key'] ], 1 ); ?>
				>
				<p class="description"><?php echo esc_html( $options_available['email_translate']['description'] ); ?></p>
			</td>
		</tr>
	</tbody>
</table>
