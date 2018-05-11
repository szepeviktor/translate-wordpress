<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Weglot\Client\Factory\Languages;

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
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $options_available['original_language']['key'] ); ?>">
					<?php echo esc_html( $options_available['original_language']['label'] ); ?>
				</label>
			</th>
			<td class="forminp forminp-text">
				<select
					class="weglot-select"
					name="<?php echo esc_attr( sprintf( '%s[%s]', WEGLOT_SLUG, $options_available['original_language']['key'] ) ); ?>"
					id="<?php echo esc_attr( $options_available['original_language']['key'] ); ?>"
				>
					<?php foreach ( Languages::data() as $key_lang => $language ) : ?>
						<option value="<?php echo esc_attr( $language['code'] ); ?>"><?php echo esc_html( $language['local'] ); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $options_available['destination_language']['key'] ); ?>">
					<?php echo esc_html( $options_available['destination_language']['label'] ); ?>
				</label>
			</th>
			<td class="forminp forminp-text">
				<select
					class="weglot-select"
					name="<?php echo esc_attr( sprintf( '%s[%s]', WEGLOT_SLUG, $options_available['destination_language']['key'] ) ); ?>"
					id="<?php echo esc_attr( $options_available['destination_language']['key'] ); ?>"
					multiple="true"
				>
					<?php foreach ( Languages::data() as $key_lang => $language ) : ?>
						<option value="<?php echo esc_attr( $language['code'] ); ?>"><?php echo esc_html( $language['local'] ); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
	</tbody>
</table>

<?php settings_fields( WEGLOT_OPTION_GROUP ); ?>
