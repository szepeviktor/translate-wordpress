<?php

namespace WeglotWP\Regexcheckers;

use Weglot\Parser\Check\Regex\RegexChecker;
use Weglot\Util\SourceType;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 2.0.7
 */
class Ninja_Form_Json_Setting extends RegexChecker
{
    const REGEX = '#form.settings=(.*?);#';

    const TYPE = SourceType::SOURCE_JSON;

    const KEYS = array( "title" , "label" , "validateRequiredField" , "formErrorsCorrectErrors");
}
