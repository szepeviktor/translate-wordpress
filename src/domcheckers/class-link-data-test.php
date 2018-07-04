<?php

namespace WeglotWP\Domcheckers;

use Weglot\Parser\Check\Dom\AbstractDomChecker;
use Weglot\Client\Api\Enum\WordType;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 2.0
 */
class Link_Data_Test extends AbstractDomChecker {

	/**
	 * {@inheritdoc}
	 */
	const DOM = 'a';

	/**
	 * {@inheritdoc}
	 */
	const PROPERTY = 'data-test';

	/**
	 * {@inheritdoc}
	 */
	const WORD_TYPE = WordType::TEXT;
}
