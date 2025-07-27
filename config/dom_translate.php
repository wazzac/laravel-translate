<?php

return [
    // If set to true, 'phrase' => 'translation' pairs will also be stored in the local session.
    // This feature adds an additional layer above the normal DB storing functionality,
    // enabling the avoidance of unnecessary DB calls by retrieving translations from the session when available.
    // ------------------------------------------------------------
    'use_session' => env('DOM_TRANSLATE_USE_SESSION', false),

    // If set to true, 'phrase' => 'translation' pairs will be stored in the database.
    // This feature enables the storage of translations in the database, allowing for quick retrieval of translations.
    // By default this should be set to `true` to ensure that we don't make repeated API calls.
    // ------------------------------------------------------------
    'use_database' => env('DOM_TRANSLATE_USE_DATABASE', true),

    // Determines the level of logging. For production environments, we recommend using either 0 or 1.
    // `level`: 0=None; 1=High-Level; 2=Mid-Level or 3=Low-Level
    // `indicator`: Log indicator used to locate specific items in the log file.
    // ------------------------------------------------------------
    'logging' => [
        'level' => env('DOM_TRANSLATE_LOG_LEVEL', 3),
        'indicator' => env('DOM_TRANSLATE_LOG_INDICATOR', 'dom-translate'),
    ],

    // 3rd party translation service providers.
    // ------------------------------------------------------------
    'api' => [
        'provider' => env('DOM_TRANSLATE_PROVIDER', 'google'),
        'google' => [
            'controller' => "Wazza\DomTranslate\Controllers\ApiTranslate\GoogleTranslate",
            'endpoint' => "https://www.googleapis.com/language/translate/v2",
            'action' => "POST",
            'key' => env('DOM_TRANSLATE_GOOGLE_KEY', null), // https://console.cloud.google.com/apis/credentials
        ],
        'bing' => [
            'controller' => "Wazza\DomTranslate\Controllers\ApiTranslate\BingTranslate",
            'endpoint' => "https://api.cognitive.microsofttranslator.com/translate",
            'action' => "POST",
            'key' => env('DOM_TRANSLATE_BING_KEY', null), // https://portal.azure.com/#home
        ],
        // ... add more providers here
    ],

    // Below details will be used to hash a given phrase for quick loading (via index)
    // ------------------------------------------------------------
    'hash' => [
        'salt' => env('DOM_TRANSLATE_HASH_SALT', 'DzBQ2DxKhNaF'),
        'algo' => env('DOM_TRANSLATE_HASH_ALGO', 'sha256'),
    ],

    // Only ISO-639-1 formats:
    // @link: https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes
    // ------------------------------------------------------------
    'language' => [
        'src' => env('DOM_TRANSLATE_LANG_SRC', 'en'), // default source language
        'dest' => env('DOM_TRANSLATE_LANG_DEST', 'af'), // default destination language
    ],

    // Route configuration for language endpoints
    // ------------------------------------------------------------
    'routes' => [
        'enabled' => env('DOM_TRANSLATE_ROUTES_ENABLED', true), // enable/disable automatic route registration
        'prefix' => env('DOM_TRANSLATE_ROUTES_PREFIX', 'api/translate'), // route prefix
        'middleware' => env('DOM_TRANSLATE_ROUTES_MIDDLEWARE', 'web'), // middleware to apply
    ],

    // Session configuration for language preference storage
    // ------------------------------------------------------------
    'session' => [
        'language_key' => env('DOM_TRANSLATE_SESSION_KEY', 'app_language_code'), // session/cookie key name
    ]
];
