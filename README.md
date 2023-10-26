<p align="center">
<a href="https://github.com/WarrenGIT/domTranslate/issues"><img alt="GitHub issues" src="https://img.shields.io/github/issues/WarrenGIT/domTranslate"></a>
<a href="https://github.com/WarrenGIT/domTranslate/stargazers"><img alt="GitHub stars" src="https://img.shields.io/github/stars/WarrenGIT/domTranslate"></a>
<a href="https://github.com/WarrenGIT/domTranslate/blob/main/LICENSE"><img alt="GitHub license" src="https://img.shields.io/github/license/WarrenGIT/domTranslate"></a>
</p>

# domTranslate

Library that will use the build in Laravel Derictive and provide automated translations to all your blade phrases or words.

_Example: Write HTML static data in English and display it in a different language on run time._

## Overview

The library contains 3 database tables (_domt_phrases_, _domt_translations_ and _domt_languages_) that are used to retrieve translations using an indexed hash.

1. On page load, the system will search for a specific translation using the provided phrase (in the `@transl8()` directive) in the _domt_translations_ table.
2. If the translation was found, it will be returned and display on the page _(no API call was made)_.
3. If no transalation were found _(not previously translated)_, call the Google Translate API endpoint _(or any other Provider)_ to retrieve the correct translation.
4. Insert the newly translated text into the DB so that we don't have to call the API again for the given phrase _(step 1 above)_.

## Installation

> PHP 8.0 is a min requirement for this project.

1. Follow below steps to install the package

```bash
composer require wazza/dom-translate
php artisan vendor:publish --tag="dom-translate-config"
php artisan vendor:publish --tag="dom-translate-migrations"
php artisan migrate
```

2. Add `DOM_TRANSLATE_GOOGLE_KEY={key value from Google}` to your _.env_ file and run...

```bash
php artisan config:cache
```

Example below of all the supported env keys that can be added with their current default values if not given. The `KEY` (i.e. `DOM_TRANSLATE_GOOGLE_KEY`) items are required.

```
DOM_TRANSLATE_USE_SESSION=true
DOM_TRANSLATE_LOG_LEVEL=3
DOM_TRANSLATE_LOG_INDICATOR=dom-translate
DOM_TRANSLATE_PROVIDER=google
DOM_TRANSLATE_GOOGLE_KEY=
DOM_TRANSLATE_BING_KEY=
DOM_TRANSLATE_HASH_SALT=DzBQ2DxKhNaF
DOM_TRANSLATE_HASH_ALGO=sha256
DOM_TRANSLATE_LANG_SRC=en
DOM_TRANSLATE_LANG_DEST=af
```

> **Note:** If you don't have a [Google Cloud Platform](https://cloud.google.com/gcp) account yet, click on the link and sign up. Create a new Project and add the _Cloud Translation API_ to it. You can use [Insomnia](https://insomnia.rest/download) (image below) to test your API key.

<a href="https://ibb.co/R0dwJ78" target="_blank">
    <img src="https://i.ibb.co/wWjm2Yt/insomnia.png" alt="insomnia" border="0" width="100%" />
</a>

3. Done. Review any configuration file changes that you might want to change. The config file was published to the main config folder.

> All done: Start your service again and update your Blade files with the @transl8 directive. Only new un-translated phrases will be translated via the API call. Any future requests, for the same phrase, will be retrieved from the database.

## HTML Blade Example

Find below a few examples how to use the translate Blade directive in your HTML (Blade) files.

```blade
<div>
    {{-- Fully dependant on the source and destination language settings, only provide a phrase (this is the default way) --}}
    <p>@transl8("I like this feature.")</p>

    {{-- Overwrite the default (1) Destination language by including a second (destination) argument --}}
    <p>@transl8("We need to test it in the staging environment.","de")</p>

    {{-- Overwrite the default (1) Source and (2) Destination languages by including a second (destination) and third (source) argument --}}
    <p>@transl8("Wie weet waar Willem Wouter woon?","en","af")</p>

    {{-- Use a Blade Language Specific directive for each language --}}
    <p>@transl8fr("This phrase will be translated to French.")</p>
    <p>@transl8de("This phrase will be translated to German.")</p>
    <p>@transl8je("This phrase will be translated to Japanese.")</p>

    {{-- ...you can update the Laravel AppServiceProvider register() method and add more of you own directives but ultimately the default @transl8() should be sufficient --}}

    {{-- ...and lastly, a phrase that will not be translated --}}
    <p>This phrase will not be translated.</p>
</div>
```

## Blade Directive Example:

The below 4 directives are available by default (`@transl8()` is the **main one**).

You are welcome to add more directly in your Laravel _AppServiceProvider_ file _(under the register() method)_

```php
// (1) Register the DEFAULT Blade Directives - @transl8()
// With `transl8`, only the first argument is required (phrase).
// If you do not supply the destination or source languages, the default values would be sourced from the Config file.
// -- Format: transl8('Phrase','target','source')
// -- Example: transl8('This must be translated to French.','fr')
Blade::directive('transl8', function ($string) {
    return \Wazza\DomTranslate\Controllers\TranslateController::phrase($string);
});

// (2) Register DIRECT (language specific) Blade directives, all from English (source)
// (2.1) French - @transl8fr('phrase')
Blade::directive('transl8fr', function ($string) {
    return \Wazza\DomTranslate\Controllers\TranslateController::translate($string, "fr", "en");
});
// (2.2) German - @transl8de('phrase')
Blade::directive('transl8de', function ($string) {
    return \Wazza\DomTranslate\Controllers\TranslateController::translate($string, "de", "en");
});
// (2.3) Japanese - @transl8je('phrase')
Blade::directive('transl8je', function ($string) {
    return \Wazza\DomTranslate\Controllers\TranslateController::translate($string, "je", "en");
});
// (2.4) etc. You can create your own in Laravel's AppServiceProvider register() method, but ultimately the default @transl8() should be sufficient.
```

## Outstanding Development (Backlog)

- Create an alternative Translation Engine/s. Google Translate is currenlty the only supported option via `Wazza\DomTranslate\Controllers\ApiTranslate\GoogleTranslate()`. Other possible options: 'NLP Translation', 'Microsoft Translator', etc. (Important: Add the Translation path to the config file - see below)

```php
    // line 14 in 'wazza\dom-translate\config\dom_translate.php'
    // 3rd party translation service providers.
    'api' => [
        'provider' => env('DOM_TRANSLATE_PROVIDER', 'google'),
        'google' => [
            'controller' => "Wazza\DomTranslate\Controllers\ApiTranslate\GoogleTranslate",
            'endpoint' => "https://www.googleapis.com/language/translate/v2",
            'action' => "POST",
            'key' => env('DOM_TRANSLATE_GOOGLE_KEY', null), // https://console.cloud.google.com/apis/credentials
        ],
        // @todo - for developers wanting to contibute:
        // fork the project and add more translate providers here... (and their \ApiTranslate\Class implementing CloudTranslateInterface)
        // ... thanks ;)
    ],
```

## Run local tests

```
.\vendor\bin\phpunit
```

**Important:** For the final 2 assert Tests to work, you would have to add your own personal [Google Translate key](https://console.cloud.google.com/apis/credentials)
as DOM_TRANSLATE_GOOGLE_KEY=xxx in .env (at the time of writing there was a free options available)
