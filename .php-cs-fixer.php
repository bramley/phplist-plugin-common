<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude('plugins/CommonPlugin/ext')
    ->exclude('plugins/CommonPlugin/vendor')
    ->exclude('plugins/CommonPlugin/images')
    ->exclude('plugins/CommonPlugin/lan')
    ->exclude('tests')
    ->notPath(['.tpl.php'])
;

$config = new PhpCsFixer\Config();

return $config->setRules([
        '@PSR1' => true,
        '@PSR2' => true,
        '@Symfony' => true,
        'concat_space' => false,
        'phpdoc_no_alias_tag' => false,
        'yoda_style' => false,
        'array_syntax' => false,
        'no_superfluous_phpdoc_tags' => false,
        'ordered_imports' => [
            'sort_algorithm' => 'alpha',
            'imports_order' => ['class', 'function', 'const'],
        ],
        'blank_line_after_namespace' => true,
        'single_line_comment_style' => false,
        'visibility_required' => false,
        'phpdoc_to_comment' => false,
        'type_declaration_spaces' => false,
        'global_namespace_import' => false,
        'operator_linebreak' => false,
        'no_null_property_initialization' => false,
        'nullable_type_declaration_for_default_null_value' => false,
        'fully_qualified_strict_types' => false,
    ])
    ->setFinder($finder)
;
