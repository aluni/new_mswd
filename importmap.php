<?php

/**
 * Returns the importmap for this application.
 *
 * - "path" is a path inside the asset mapper system. Use the
 *     "debug:asset-map" command to see the full list of paths.
 *
 * - "entrypoint" (JavaScript only) set to true for any module that will
 *     be used as an "entrypoint" (and passed to the importmap() Twig function).
 *
 * The "importmap:require" command can be used to add new entries to this file.
 */
return [
    'app' => [
        'path' => './assets/app.js',
        'entrypoint' => true,
    ],
    '@hotwired/stimulus' => [
        'version' => '3.2.2',
    ],
    '@symfony/stimulus-bundle' => [
        'path' => './vendor/symfony/stimulus-bundle/assets/dist/loader.js',
    ],
    '@hotwired/turbo' => [
        'version' => '7.3.0',
    ],
    '@flowjs/ng-flow' => [
        'version' => '~2',
    ],
    'angular' => [
        'version' => '1.8.3',
    ],
    'angular-route' => [
        'version' => '>=1.3.7',
    ],
    'angular-resource' => [
        'version' => '>=1.3.7',
    ],
    'angular-sanitize' => [
        'version' => '>=1.3.7',
    ],
    'angular-animate' => [
        'version' => '>=1.3.7',
    ],
    'bootstrap' => [
        'version' => '3.4.1',
    ],
    '@popperjs/core' => [
        'version' => '2.11.8',
    ],
    'bootstrap/dist/css/bootstrap.min.css' => [
        'version' => '3.4.1',
        'type' => 'css',
    ],
    'bootstrap-datepicker' => [
        'version' => '1.10.0',
    ],
    'jquery' => [
        'version' => '3.7.1',
    ],
];
