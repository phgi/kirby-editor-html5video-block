<?php

Kirby::plugin('phgi/html5video-block', [
    'snippets' => [
        'editor/html5video' => __DIR__ . '/snippets/html5video.php'
    ],
    'translations' => [
        'en'    => @include_once __DIR__ . '/i18n/en.php',
        'de'    => @include_once __DIR__ . '/i18n/de.php',
    ]
]);
