<?php

if (!class_exists('PhpCsFixer\Config', true)) {
    fwrite(STDERR, "Your php-cs-version is outdated: please upgrade it.\n");
    die(16);
}

return PhpCsFixer\Config::create()
    ->setCacheFile(__DIR__.'/.php_cs.cache')
    ->setRiskyAllowed(true)
    ->setRules([
        // Let's start with the standard Symfony rules
        '@Symfony' => true,
        // Force short array syntax
        'array_syntax' => ['syntax' => 'short'],
        // A single space between cast and variable
        'cast_spaces' => true,
        // Concatenation should be spaced with a space
        'concat_space' => [
            'spacing' => 'one',
        ],
        // Don't force an empty line before namespace declaration
        'single_blank_line_before_namespace' => false,
        'no_blank_lines_before_namespace' => true,
        'blank_line_after_opening_tag' => false,
        // Don't vertically align phpdoc tags
        'phpdoc_align' => false,
        // Allow double-quoted strings, don't force single-quoted strings
        'single_quote' => false,        
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
        ->in(__DIR__)
    )
;
