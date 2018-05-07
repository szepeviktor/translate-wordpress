<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Weglot\Helpers\Helper_Tabs_Admin_Weglot;

?>

<div class="wrap">
	<?php include_once WEGLOT_TEMPLATES_ADMIN_PAGES . '/nav.php'; ?>

	<form method="post" id="mainform" action="" enctype="multipart/form-data">
		<?php
		switch ( $this->tab_active ) {
			case Helper_Tabs_Admin_Weglot::SETTINGS:
			default:
				include_once WEGLOT_TEMPLATES_ADMIN_PAGES . '/tabs/settings.php';
				break;
			case Helper_Tabs_Admin_Weglot::ADVANCED:
				include_once WEGLOT_TEMPLATES_ADMIN_PAGES . '/tabs/advanced.php';
				break;
			case Helper_Tabs_Admin_Weglot::STATUS:
				include_once WEGLOT_TEMPLATES_ADMIN_PAGES . '/tabs/status.php';
				break;
		}
		?>
	</form>
</div>
