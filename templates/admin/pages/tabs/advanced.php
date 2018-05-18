<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Weglot\Client\Endpoint\Languages;
use Weglot\Client\Client;

use WeglotWP\Helpers\Helper_Tabs_Admin_Weglot;

$options_available = [
	'auto_redirect' => [
		'key'         => 'auto_redirect',
		'label'       => __( 'Auto redirection', 'weglot' ),
		'description' => '',
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
	</tbody>
</table>

<input type="hidden" name="tab" value="<?php echo esc_attr( Helper_Tabs_Admin_Weglot::ADVANCED ); ?>">
