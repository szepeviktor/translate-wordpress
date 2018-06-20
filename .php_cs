<?php

$finder = PhpCsFixer\Finder::create()
            ->files()
            ->in(__DIR__)
            ->exclude('vendor')
            ->notName("*.txt")
            ->notPath("weglot.php")
            ->ignoreDotFiles(true)
            ->ignoreVCS(true);
;

return PhpCsFixer\Config::create()
    ->setRules([
        'array_syntax' => ['syntax' => 'short'],
		'binary_operator_spaces' => [
			'align_double_arrow' => true,
			'align_equals' => true
		],
		'single_quote' => true,
		'method_argument_space' => [
			'keep_multiple_spaces_after_comma' => true
		],
		'indentation_type' => true,
		'concat_space' => [
			"spacing" => "one"
		],
		'not_operator_with_space' => true,
		'phpdoc_add_missing_param_annotation' => [
			'only_untyped' => false
		],
		'braces' => [
			'position_after_functions_and_oop_constructs' => 'same'
		]
    ])
	->setIndent("\t")
    ->setFinder($finder);
