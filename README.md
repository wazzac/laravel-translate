<p align="center">
    <a href="https://github.com/wazzac/domTranslate/issues"><img alt="GitHub issues" src="https://img.shields.io/github/issues/wazzac/domTranslate"></a>
    <a href="https://github.com/WarrenGIT/domTranslate/stargazers"><img alt="GitHub stars" src="https://img.shields.io/github/stars/wazzac/domTranslate"></a>
    <a href="https://github.com/WarrenGIT/domTranslate/blob/main/LICENSE"><img alt="GitHub license" src="https://img.shields.io/github/license/WarrenGIT/domTranslate"></a>
</p>

# Laravel Translate Package

A library that leverages Laravel Directives to provide automated translations for all your Blade phrases or words.

_Example: Write HTML static data in English and display it in a different language in real-time._

## Overview

The library uses three database tables (_domt_phrases_, _domt_translations_, and _domt_languages_) to manage translations efficiently.

1.  On page load, the system searches for a specific translation using the provided phrase in the `@transl8()` directive from the _domt_translations_ table.
2.  If the translation is found, it is returned and displayed on the page without making an API call.
3.  If the translation is not found _(not translated yet)_, the Google Translate API (or another defined provider) is called to retrieve the new translation.
4.  The newly translated text is then inserted into the database to avoid future API calls for the same phrase.

> Note: To ensure quick retrieval of translations, each phrase is hashed and stored in an indexed table column. All searches are performed against this indexed column for optimal performance.

## Installation

> PHP 8.0 is the minimum requirement for this project.

Follow these steps to install the package:

```bash
composer require wazza/dom-translate
php artisan vendor:publish --tag="dom-translate-config"
php artisan vendor:publish --tag="dom-translate-migrations"
php artisan migrate
```

Add `DOM_TRANSLATE_GOOGLE_KEY={your_google_api_key}` to your `.env` file and run:

```bash
php artisan config:cache
```

Below are all the supported `.env` keys with their default values if not provided. The `KEY` (i.e., `DOM_TRANSLATE_GOOGLE_KEY`) is required.

```
DOM_TRANSLATE_USE_SESSION=true
DOM_TRANSLATE_USE_DATABASE=true
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

-   If DOM_TRANSLATE_USE_SESSION is set to true, translations will be saved in the session and used as the first point of retrieval.
-   If no translations are found in the session, or if DOM_TRANSLATE_USE_SESSION is set to false, translations will be retrieved from the database, provided they have been previously stored there.
-   If translations are still not found, or if both DOM_TRANSLATE_USE_SESSION and DOM_TRANSLATE_USE_DATABASE are set to false, translations will be sourced from a third-party translation service (e.g., Google Translate).
-   Depending on whether DOM_TRANSLATE_USE_SESSION and DOM_TRANSLATE_USE_DATABASE are set to true, the retrieved translation will be saved to either the session or the database.

> **Note:** If you don't have a [Google Cloud Platform](https://cloud.google.com/gcp) account, sign up and create a new project. Add the _Cloud Translation API_ to it. You can use [Insomnia](https://insomnia.rest/download) to test your API key.

<a href="https://ibb.co/R0dwJ78" target="_blank"> <img src="https://i.ibb.co/wWjm2Yt/insomnia.png" alt="insomnia" border="0" width="100%" /> </a>

Review any configuration file changes that you might want to make. The config file is published to the main config folder.

> You're all set! 😉

Restart your service and update your Blade files with the `@transl8` directive. Only new untranslated phrases will trigger an API call. Future requests for the same phrase will be retrieved from the database.

## HTML Blade Example

Here are a few examples of how to use the translate Blade directive in your HTML (Blade) files:

```blade
<div>
    {{-- Default usage: Only provide a phrase --}}
    <p>@transl8("I like this feature.")</p>

    {{-- Specify a destination language --}}
    <p>@transl8("We need to test it in the staging environment.", "de")</p>

    {{-- Specify both source and destination languages --}}
    <p>@transl8("Wie weet waar Willem Wouter woon?", "en", "af")</p>

    {{-- Language-specific directives --}}
    <p>@transl8fr("This phrase will be translated to French.")</p>
    <p>@transl8de("This phrase will be translated to German.")</p>
    <p>@transl8je("This phrase will be translated to Japanese.")</p>

    {{-- A phrase that will not be translated --}}
    <p>This phrase will not be translated.</p>
</div>
```

## Blade Directive Example

Four directives are available by default (`@transl8()` is the main one). You can add more in your Laravel _AppServiceProvider_ file (under the `register()` method).

```php
// Register the default Blade directive - @transl8()
// Only the phrase argument is required. Default source and destination languages are sourced from the config file.
// - Format: transl8('Phrase','target','source')
// - Example: transl8('This must be translated to French.','fr')
Blade::directive('transl8', function ($string) {
    return \Wazza\DomTranslate\Controllers\TranslateController::phrase($string);
});

// Register language-specific Blade directives
// French - @transl8fr('phrase')
Blade::directive('transl8fr', function ($string) {
    return \Wazza\DomTranslate\Controllers\TranslateController::translate($string, "fr", "en");
});
// German - @transl8de('phrase')
Blade::directive('transl8de', function ($string) {
    return \Wazza\DomTranslate\Controllers\TranslateController::translate($string, "de", "en");
});
// Japanese - @transl8je('phrase')
Blade::directive('transl8je', function ($string) {
    return \Wazza\DomTranslate\Controllers\TranslateController::translate($string, "je", "en");
});
```

## Future Development (Backlog)

-   Translations are not always perfect. Create a Phrase vs Translation admin section that will allow a user to change (update) a translated phase with corrections.
-   Create alternative translation engines. Currently, only Google Translate is supported via `Wazza\DomTranslate\Controllers\ApiTranslate\GoogleTranslate()`. Other options to consider include NLP Translation, Microsoft Translator, etc.

```php
// Line 14 in 'wazza\dom-translate\config\dom_translate.php'
// Third-party translation service providers
'api' => [
    'provider' => env('DOM_TRANSLATE_PROVIDER', 'google'),
    'google' => [
        'controller' => "Wazza\DomTranslate\Controllers\ApiTranslate\GoogleTranslate",
        'endpoint' => "https://www.googleapis.com/language/translate/v2",
        'action' => "POST",
        'key' => env('DOM_TRANSLATE_GOOGLE_KEY', null), // https://console.cloud.google.com/apis/credentials
    ],
    // To contribute, fork the project and add more translation providers here, implementing CloudTranslateInterface
],
```

## Running Local Tests

Run the following command to execute tests:

```bash
.\vendor\bin\phpunit
```

**Important:** For the final two assert tests to work, add your personal [Google Translate key](https://console.cloud.google.com/apis/credentials) as `DOM_TRANSLATE_GOOGLE_KEY=xxx` in your `.env` file (free options are available at the time of writing).
