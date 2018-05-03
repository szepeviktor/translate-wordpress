<?php

namespace Weglot\Notices;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Weglot\Models\Abstract_Notices_Weglot;

class Rewrite_Module_Weglot extends Abstract_Notices_Weglot {
	public static function get_template_file() {
		return WEGLOT_TEMPLATES_ADMIN_NOTICES . '/rewrite-module.php';
	}
}
