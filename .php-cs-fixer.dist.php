<?php

$finder =  PhpCsFixer\Finder::create()
    ->exclude('vendor')
    ->in([__DIR__.'/src/', __DIR__.'/tests/']);
$config = new PhpCsFixer\Config();

return $config->setRules([
    '@PSR2' => true,
    'binary_operator_spaces' => true,
    'blank_line_after_opening_tag' => true,
    'compact_nullable_typehint' => true,
    'declare_equal_normalize' => true,
    'lowercase_cast' => true,
    'lowercase_static_reference' => true,
    'new_with_braces' => true,
    'no_unused_imports' => true,
    'no_blank_lines_after_class_opening' => true,
    'no_leading_import_slash' => true,
    'no_whitespace_in_blank_line' => true,
    'heredoc_to_nowdoc' => true,
    'phpdoc_trim_consecutive_blank_line_separation' => true,
    'ordered_class_elements' => [
        'order' => [
            'use_trait',
        ],
    ],
    'ordered_imports' => [
        'imports_order' => [
            'class',
            'function',
            'const',
        ],
        'sort_algorithm' => 'none',
    ],
    'return_type_declaration' => true,
    'short_scalar_cast' => true,
    'single_blank_line_before_namespace' => true,
    'single_trait_insert_per_statement' => true,
    'ternary_operator_spaces' => true,
    'unary_operator_spaces' => true,
    'visibility_required' => [
        'elements' => [
            'const',
            'method',
            'property',
        ],
    ],
])->setFinder($finder);
