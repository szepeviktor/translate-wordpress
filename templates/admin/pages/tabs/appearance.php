<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use WeglotWP\Helpers\Helper_Tabs_Admin_Weglot;

use Weglot\Client\Endpoint\Languages;
use Weglot\Client\Client;

$options_available = [
	'is_fullname' => [
		'key'         => 'is_fullname',
		'label'       => __( 'Is fullname', 'weglot' ),
		'description' => '',
	],
	'with_name' => [
		'key'         => 'with_name',
		'label'       => __( 'With name', 'weglot' ),
		'description' => '',
	],
	'is_dropdown' => [
		'key'         => 'is_dropdown',
		'label'       => __( 'Is dropdown', 'weglot' ),
		'description' => '',
	],
	'with_flags' => [
		'key'         => 'with_flags',
		'label'       => __( 'With flags', 'weglot' ),
		'description' => '',
	],
];


?>

<h3><?php esc_html_e( 'Appearance', 'weglot' ); ?></h3>
<hr>
<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $options_available['is_fullname']['key'] ); ?>">
					<?php echo esc_html( $options_available['is_fullname']['label'] ); ?>
				</label>
			</th>
			<td class="forminp forminp-text">
				<input
					name="<?php echo esc_attr( sprintf( '%s[%s]', WEGLOT_SLUG, $options_available['is_fullname']['key'] ) ); ?>"
					id="<?php echo esc_attr( $options_available['is_fullname']['key'] ); ?>"
					type="checkbox"
					<?php checked( $this->options[ $options_available['is_fullname']['key'] ], 1 ); ?>
				>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $options_available['with_name']['key'] ); ?>">
					<?php echo esc_html( $options_available['with_name']['label'] ); ?>
				</label>
			</th>
			<td class="forminp forminp-text">
				<input
					name="<?php echo esc_attr( sprintf( '%s[%s]', WEGLOT_SLUG, $options_available['with_name']['key'] ) ); ?>"
					id="<?php echo esc_attr( $options_available['with_name']['key'] ); ?>"
					type="checkbox"
					<?php checked( $this->options[ $options_available['with_name']['key'] ], 1 ); ?>
				>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $options_available['is_dropdown']['key'] ); ?>">
					<?php echo esc_html( $options_available['is_dropdown']['label'] ); ?>
				</label>
			</th>
			<td class="forminp forminp-text">
				<input
					name="<?php echo esc_attr( sprintf( '%s[%s]', WEGLOT_SLUG, $options_available['is_dropdown']['key'] ) ); ?>"
					id="<?php echo esc_attr( $options_available['is_dropdown']['key'] ); ?>"
					type="checkbox"
					<?php checked( $this->options[ $options_available['is_dropdown']['key'] ], 1 ); ?>
				>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $options_available['with_flags']['key'] ); ?>">
					<?php echo esc_html( $options_available['with_flags']['label'] ); ?>
				</label>
			</th>
			<td class="forminp forminp-text">
				<input
					name="<?php echo esc_attr( sprintf( '%s[%s]', WEGLOT_SLUG, $options_available['with_flags']['key'] ) ); ?>"
					id="<?php echo esc_attr( $options_available['with_flags']['key'] ); ?>"
					type="checkbox"
					<?php checked( $this->options[ $options_available['with_flags']['key'] ], 1 ); ?>
				>
			</td>
		</tr>
	</tbody>
</table>
