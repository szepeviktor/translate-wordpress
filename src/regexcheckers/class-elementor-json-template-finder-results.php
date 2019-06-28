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
class Elementor_Json_Template_Finder_Results extends RegexChecker
{
    const REGEX = '#<span class="elementor-screen-only">(.*?)<\/span>#';
    
    const TYPE = SourceType::SOURCE_TEXT;

    const KEYS = array(  );
}
