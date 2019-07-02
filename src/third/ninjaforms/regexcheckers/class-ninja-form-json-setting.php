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
class Ninja_Form_Json_Setting extends RegexChecker
{
    const REGEX = '#form.settings=(.*?);#';

    const TYPE = SourceType::SOURCE_JSON;

    public static $KEYS = array( "title" , "changeEmailErrorMsg" , "changeDateErrorMsg" , "confirmFieldErrorMsg" , "fieldNumberNumMinError" , "fieldNumberNumMaxError" , "fieldNumberIncrementBy" , "fieldTextareaRTEInsertLink" , "fieldTextareaRTEInsertMedia" , "fieldTextareaRTESelectAFile" , "formErrorsCorrectErrors" , "validateRequiredField" , "honeypotHoneypotError" , "fileUploadOldCodeFileUploadInProgress" , "previousMonth" , "nextMonth" , "fieldsMarkedRequired");
}
