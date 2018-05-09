<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$options_available = [
	'api_key' => [
		'key'         => 'api_key',
		'label'       => __( 'Api key', 'weglot' ),
		'description' => '',
	],
	'original_language' => [
		'key'         => 'original_language',
		'label'       => __( 'Original language', 'weglot' ),
		'description' => '',
	],
	'destination_language' => [
		'key'         => 'destination_language	',
		'label'       => __( 'Destination language', 'weglot' ),
		'description' => '',
	],
];

?>

<h2><?php esc_html_e( 'Settings', 'weglot' ); ?></h2>

<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $options_available['api_key']['key'] ); ?>">
					<?php echo esc_html( $options_available['api_key']['label'] ); ?>
				</label>
			</th>
			<td class="forminp forminp-text">
				<input
					name="<?php echo esc_attr( sprintf( '%s[%s]', WEGLOT_SLUG, $options_available['api_key']['key'] ) ); ?>"
					id="<?php echo esc_attr( $options_available['api_key']['key'] ); ?>"
					type="text"
					value="<?php echo esc_attr( $this->options[ $options_available['api_key']['key'] ] ); ?>"
				>
			</td>
		</tr>
	</tbody>
</table>

<?php settings_fields( WEGLOT_OPTION_GROUP ); ?>
