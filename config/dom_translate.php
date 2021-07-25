<?php
// return config
return [
    'api' => [
        'provider' => env('DOM_TRANSLATE_PROVIDER', 'google'),
        'google' => [
            'endpoint' => "https://www.googleapis.com/language/translate/v2",
            'action' => "POST",
            'key' => env('DOM_TRANSLATE_GOOGLE_KEY'), // https://console.cloud.google.com/apis/credentials
        ],
        // add more translate providers here...
    ],
    'hash' => [
        'salt' => env('DOM_TRANSLATE_HASH_SALT', 'zBQ2DxKhNa'),
        'algo' => env('DOM_TRANSLATE_HASH_ALGO', 'sha1'), // https://www.php.net/manual/en/function.hash-algos.php
    ],
    // Only ISO-639-1 format: @link: https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes
    'language' => [
        'src' => env('DOM_TRANSLATE_LANG_SRC', 'en'), // default source language
        'dest' => env('DOM_TRANSLATE_LANG_DEST', 'af'), // default destication language
    ]
];
