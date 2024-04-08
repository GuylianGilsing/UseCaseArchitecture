<?php

declare(strict_types=1);

return [

    'diff_context' => 3,

    /*
    |--------------------------------------------------------------------------
    | Default Preset
    |--------------------------------------------------------------------------
    |
    | This option controls the default preset that will be used by PHP Insights
    | to make your code reliable, simple, and clean. However, you can always
    | adjust the `Metrics` and `Insights` below in this configuration file.
    |
    | Supported: "default", "laravel", "symfony", "magento2", "drupal"
    |
    */

    'preset' => 'default',

    /*
    |--------------------------------------------------------------------------
    | IDE
    |--------------------------------------------------------------------------
    |
    | This options allow to add hyperlinks in your terminal to quickly open
    | files in your favorite IDE while browsing your PhpInsights report.
    |
    | Supported: "textmate", "macvim", "emacs", "sublime", "phpstorm",
    | "atom", "vscode".
    |
    | If you have another IDE that is not in this list but which provide an
    | url-handler, you could fill this config with a pattern like this:
    |
    | myide://open?url=file://%f&line=%l
    |
    */

    'ide' => 'vscode',

    /*
    |--------------------------------------------------------------------------
    | Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may adjust all the various `Insights` that will be used by PHP
    | Insights. You can either add, remove or configure `Insights`. Keep in
    | mind, that all added `Insights` must belong to a specific `Metric`.
    |
    */

    'exclude' => [
        'test-utils',
        'tests',
    ],

    'add' => [
        //  ExampleMetric::class => [
        //      ExampleInsight::class,
        //  ]
    ],

    'remove' => [
        \PHP_CodeSniffer\Standards\Generic\Sniffs\Formatting\SpaceAfterNotSniff::class,
        \SlevomatCodingStandard\Sniffs\Classes\SuperfluousInterfaceNamingSniff::class,
        \SlevomatCodingStandard\Sniffs\Classes\SuperfluousAbstractClassNamingSniff::class,
        \NunoMaduro\PhpInsights\Domain\Sniffs\ForbiddenSetterSniff::class,
        \SlevomatCodingStandard\Sniffs\TypeHints\DisallowMixedTypeHintSniff::class,
        \NunoMaduro\PhpInsights\Domain\Insights\ForbiddenDefineFunctions::class,
        \NunoMaduro\PhpInsights\Domain\Insights\ForbiddenDefineGlobalConstants::class,
        \NunoMaduro\PhpInsights\Domain\Insights\ForbiddenTraits::class,
        \SlevomatCodingStandard\Sniffs\Classes\SuperfluousTraitNamingSniff::class,
    ],

    'config' => [
        \PhpCsFixer\Fixer\Basic\BracesFixer::class => [
            'allow_single_line_closure' => false,
            'position_after_anonymous_constructs' => 'same',
            'position_after_control_structures' => 'same',
            'position_after_functions_and_oop_constructs' => 'next',
        ],
        \SlevomatCodingStandard\Sniffs\Commenting\DocCommentSpacingSniff::class => [
            'linesCountBeforeFirstContent' => 0,
            'linesCountBetweenDescriptionAndAnnotations' => 1,
            'linesCountBetweenDifferentAnnotationsTypes' => 1,
            'linesCountBetweenAnnotationsGroups' => 0,
            'linesCountAfterLastContent' => 0,
            'annotationsGroups' => [],
        ],
        \PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff::class => [
            'lineLimit' => 120,
            'absoluteLineLimit' => 240,
            'ignoreComments' => false,
        ],
        \SlevomatCodingStandard\Sniffs\Namespaces\UseSpacingSniff::class => [
            'linesCountBeforeFirstUse' => 1,
            'linesCountBetweenUseTypes' => 1,
            'linesCountAfterLastUse' => 1,
        ],
        \PhpCsFixer\Fixer\Import\OrderedImportsFixer::class => [
            'imports_order' => ['class', 'const', 'function'],
            'sort_algorithm' => 'alpha',
        ],
        \NunoMaduro\PhpInsights\Domain\Insights\CyclomaticComplexityIsHigh::class => [
            'exclude' => [
                './src/API/Application/Wrappers/SlimApplicationWrapper/SlimRequestHandler.php',
            ],
        ],
        \SlevomatCodingStandard\Sniffs\Functions\FunctionLengthSniff::class => [
            'exclude' => [
                './src/API/Application/Wrappers/SlimApplicationWrapper/SlimRequestHandler.php',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Requirements
    |--------------------------------------------------------------------------
    |
    | Here you may define a level you want to reach per `Insights` category.
    | When a score is lower than the minimum level defined, then an error
    | code will be returned. This is optional and individually defined.
    |
    */

    'requirements' => [
        'min-quality' => 100,
        'min-complexity' => 0,
        'min-architecture' => 100,
        'min-style' => 100,
        'disable-security-check' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Threads
    |--------------------------------------------------------------------------
    |
    | Here you may adjust how many threads (core) PHPInsights can use to perform
    | the analyse. This is optional, don't provide it and the tool will guess
    | the max core number available. This accept null value or integer > 0.
    |
    */

    'threads' => null,

];
