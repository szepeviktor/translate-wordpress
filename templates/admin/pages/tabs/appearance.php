<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use WeglotWP\Helpers\Helper_Tabs_Admin_Weglot;

$options_available = [
	'type_flags' => [
		'key'         => 'type_flags',
		'label'       => __( 'Type of flags', 'weglot' ),
		'description' => '',
	],
	'is_fullname' => [
		'key'         => 'is_fullname',
		'label'       => __( 'Is fullname', 'weglot' ),
		'description' => __( "Check if you want the name of the languge. Don't check if you want the language code.", 'weglot' ),
	],
	'with_name' => [
		'key'         => 'with_name',
		'label'       => __( 'With name', 'weglot' ),
		'description' => __( 'Check if you want to display the name of languages.', 'weglot' ),
	],
	'is_dropdown' => [
		'key'         => 'is_dropdown',
		'label'       => __( 'Is dropdown', 'weglot' ),
		'description' => __( 'Check if you want the button to be a dropdown box.', 'weglot' ),
	],
	'with_flags' => [
		'key'         => 'with_flags',
		'label'       => __( 'With flags', 'weglot' ),
		'description' => __( 'Check if you want flags in the language button.', 'weglot' ),
	],
	'override_css' => [
		'key'         => 'override_css',
		'label'       => __( 'Override CSS', 'weglot' ),
		'description' => __( "Don't change it unless you want a specific style for your button.", 'weglot' ),
	],
];


?>
<style id="weglot-css-inline"></style>
<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label>
				<?php echo esc_html__( 'Button preview', 'weglot' ); ?>
				</label>
			</th>
			<td class="forminp forminp-text">
				<?php echo $this->button_services->get_html( 'weglot-preview' ); //phpcs:ignore ?>
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
				<p class="description"><?php echo esc_html( $options_available['is_dropdown']['description'] ); ?></p>
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
				<p class="description"><?php echo esc_html( $options_available['with_flags']['description'] ); ?></p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $options_available['type_flags']['key'] ); ?>">
					<?php echo esc_html( $options_available['type_flags']['label'] ); ?>
				</label>
			</th>
			<td class="forminp forminp-text">
				<select
					class="wg-input-select"
					name="<?php echo esc_attr( sprintf( '%s[%s]', WEGLOT_SLUG, $options_available['type_flags']['key'] ) ); ?>"
					id="<?php echo esc_attr( $options_available['type_flags']['key'] ); ?>"
				>
					<option <?php selected( $this->options[ $options_available['type_flags']['key'] ], '0' ); ?> value="0">
						<?php esc_html_e( 'Rectangle mat', 'weglot' ); ?>
					</option>
					<option <?php selected( $this->options[ $options_available['type_flags']['key'] ], '1' ); ?> value="1">
						<?php esc_html_e( 'Rectangle shiny', 'weglot' ); ?>
					</option>
					<option <?php selected( $this->options[ $options_available['type_flags']['key'] ], '2' ); ?> value="2">
						<?php esc_html_e( 'Square', 'weglot' ); ?>
					</option>
					<option <?php selected( $this->options[ $options_available['type_flags']['key'] ], '3' ); ?> value="3">
						<?php esc_html_e( 'Circle', 'weglot' ); ?>
					</option>
				</select>
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
				<p class="description"><?php echo esc_html( $options_available['with_name']['description'] ); ?></p>
			</td>
		</tr>
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
				<p class="description"><?php echo esc_html( $options_available['is_fullname']['description'] ); ?></p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $options_available['override_css']['key'] ); ?>">
					<?php echo esc_html( $options_available['override_css']['label'] ); ?>
				</label>
		<p class="sub-label"><?php echo esc_html( $options_available['override_css']['description'] ); ?></p>
		</td>
			</th>
			<td class="forminp forminp-text">
				<textarea
					class="wg-input-textarea"
					id="<?php echo esc_attr( $options_available['override_css']['key'] ); ?>"
					type="text"
					rows="10"
					cols="30"
					name="<?php echo esc_attr( sprintf( '%s[%s]', WEGLOT_SLUG, $options_available['override_css']['key'] ) ); ?>" placeholder=".weglot-selector {
  margin-bottom: 20px;
}"><?php echo $this->options[ $options_available['override_css']['key'] ]; //phpcs:ignore?></textarea>
		</tr>
	</tbody>
</table>
