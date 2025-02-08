<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__ . "/src")
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PER-CS2.0' => true,
        '@PHP83Migration' => true,
        //'strict_param' => true,
        'array_syntax' => ['syntax' => 'short'],
    ])
    ->setFinder($finder)
    ;
