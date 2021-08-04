<?php
// return config
return [
    // If true, located 'phrase' => 'translation' pairs will be stored in local session too.
    // The normal functionality (DB storing) will still exists, this simply adds a layer above
    // to not call the DB if a session translation can be found.
    'use_session' => env('DOM_TRANSLATE_USE_SESSION', true),
    // The level of logging. We suggest using either 0 or 1 for Prod environments.
    'logging' => [
        'level' => env('DOM_TRANSLATE_LOG_LEVEL', 3), // 0=None; 1=High-Level; 2=Mid-Level or 3=Low-Level
        'indicator' => env('DOM_TRANSLATE_LOG_INDICATOR', 'dom-translate'), // Log indicator to find items in the log file.
    ],
    // 3rd party translation service providers.
    'api' => [
        'provider' => env('DOM_TRANSLATE_PROVIDER', 'google'),
        'google' => [
            'controller' => "Wazza\DomTranslate\Controllers\ApiTranslate\GoogleTranslate",
            'endpoint' => "https://www.googleapis.com/language/translate/v2",
            'action' => "POST",
            'key' => env('DOM_TRANSLATE_GOOGLE_KEY'), // https://console.cloud.google.com/apis/credentials
        ],
        // @todo - for developers wanting to contibute:
        // fork the project and add more translate providers here... (and their \ApiTranslate\Class implementing CloudTranslateInterface)
        // ... thanks ;)
    ],
    // Below details will be used to hash a given phrase for quick loading (via index)
    'hash' => [
        'salt' => env('DOM_TRANSLATE_HASH_SALT', 'zBQ2DxKhNa'),
        'algo' => env('DOM_TRANSLATE_HASH_ALGO', 'sha1'), // https://www.php.net/manual/en/function.hash-algos.php
    ],
    // Only ISO-639-1 formats: @link: https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes
    'language' => [
        'src' => env('DOM_TRANSLATE_LANG_SRC', 'en'), // default source language
        'dest' => env('DOM_TRANSLATE_LANG_DEST', 'af'), // default destination language
    ]
];
