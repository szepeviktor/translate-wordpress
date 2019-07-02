<?php

namespace WeglotWP\Third\Ninjaforms\Regexcheckers;

use Weglot\Parser\Check\Regex\RegexChecker;
use Weglot\Util\SourceType;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 2.0.7
 */
class Ninja_Form_Json_Fields extends RegexChecker
{
    const REGEX = '#form.fields=(.*?);#';

    const TYPE = SourceType::SOURCE_JSON;

    public static $KEYS = array( "label" , "help_text" , "value" );
}
