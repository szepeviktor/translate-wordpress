<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WeglotWP\Helpers\Helper_Tabs_Admin_Weglot;

?>

<div class="wrap">
	<?php include_once WEGLOT_TEMPLATES_ADMIN_PAGES . '/nav.php'; ?>

	<form method="post" id="mainform" action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>">
		<?php
		switch ( $this->tab_active ) {
			case Helper_Tabs_Admin_Weglot::SETTINGS:
			default:
				include_once WEGLOT_TEMPLATES_ADMIN_PAGES . '/tabs/settings.php';
				break;
			case Helper_Tabs_Admin_Weglot::APPEARANCE:
				include_once WEGLOT_TEMPLATES_ADMIN_PAGES . '/tabs/appearance.php';
				break;
			case Helper_Tabs_Admin_Weglot::ADVANCED:
				include_once WEGLOT_TEMPLATES_ADMIN_PAGES . '/tabs/advanced.php';
				break;
			case Helper_Tabs_Admin_Weglot::STATUS:
				include_once WEGLOT_TEMPLATES_ADMIN_PAGES . '/tabs/status.php';
				break;
		}

		if ( ! in_array( $this->tab_active, [ Helper_Tabs_Admin_Weglot::STATUS ], true ) ) {
			settings_fields( WEGLOT_OPTION_GROUP );
			submit_button();
		}
		?>
		<input type="hidden" name="tab" value="<?php echo esc_attr( $this->tab_active ); ?>">
	</form>

    <hr>
    <br>
    <a target="_blank"
       href="http://wordpress.org/support/view/plugin-reviews/weglot?rate=5#postform">
        <?php esc_html_e('Love Weglot? Give us 5 stars on WordPress.org :)', 'weglot'); ?>
    </a>
    <br><br>
    <i class="fa fa-question-circle question-icon" aria-hidden="true"
    ></i>
    <p class="weglot-5stars"><?php echo sprintf(esc_html__('If you need any help, you can contact us via our live chat at %1$sweglot.com%2$s or email us at support@weglot.com.', 'weglot'), '<a href="https://weglot.com/" target="_blank">', '</a>') . '<br>' . sprintf(esc_html__('You can also check our %1$sFAQ%2$s', 'weglot'), '<a href="http://support.weglot.com/" target="_blank">', '</a>'); ?></p>
    <br>
</div>
