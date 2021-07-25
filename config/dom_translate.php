<?php
// return config
return [
    'api' => [
        'provider' => "google",
        'google' => [
            'endpoint' => "https://www.googleapis.com/language/translate/v2",
            'action' => "POST",
            'key' => "AIzaSyBuZ_9lrMTGydfE7j-4EnMRU6PTd0q2p4k", // you will have to generate your own
        ],
        // add more translate providers here...
    ],
    'hash' => [
        'salt' => "zBQ2DxKhNa",
        'algo' => "sha1", // https://www.php.net/manual/en/function.hash-algos.php
    ],
    // Only ISO-639-1 format: @link: https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes
    'language' => [
        'src' => 'en',
        'dest' => 'af',
    ]
];
