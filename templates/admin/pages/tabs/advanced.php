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
		'description' => __( 'Check if you want to redirect users based on their browser language.', 'weglot' ),
	],
	'email_translate' => [
		'key'         => 'email_translate',
		'label'       => __( 'Translate email', 'weglot' ),
		'description' => __( 'Check to translate all emails who use function wp_mail', 'weglot' ),
	],
	'translate_amp' => [
		'key'         => 'translate_amp',
		'label'       => __( 'Translate AMP', 'weglot' ),
		'description' => __( 'Exclude translation on AMP page', 'weglot' ),
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
				<p class="description"><?php echo esc_html( $options_available['auto_redirect']['description'] ); ?></p>
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
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $options_available['translate_amp']['key'] ); ?>">
					<?php echo esc_html( $options_available['translate_amp']['label'] ); ?>
				</label>
			</th>
			<td class="forminp forminp-text">
				<input
					name="<?php echo esc_attr( sprintf( '%s[%s]', WEGLOT_SLUG, $options_available['translate_amp']['key'] ) ); ?>"
					id="<?php echo esc_attr( $options_available['translate_amp']['key'] ); ?>"
					type="checkbox"
					<?php checked( $this->options[ $options_available['translate_amp']['key'] ], 1 ); ?>
				>
				<p class="description"><?php echo esc_html( $options_available['translate_amp']['description'] ); ?></p>
			</td>
		</tr>
	</tbody>
</table>
