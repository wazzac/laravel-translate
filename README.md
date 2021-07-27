<p align="center">
<a href="https://github.com/WarrenGIT/domTranslate/issues"><img alt="GitHub issues" src="https://img.shields.io/github/issues/WarrenGIT/domTranslate"></a>
<a href="https://github.com/WarrenGIT/domTranslate/stargazers"><img alt="GitHub stars" src="https://img.shields.io/github/stars/WarrenGIT/domTranslate"></a>
<a href="https://github.com/WarrenGIT/domTranslate/blob/main/LICENSE"><img alt="GitHub license" src="https://img.shields.io/github/license/WarrenGIT/domTranslate"></a>
</p>

# domTranslate

Library that will use the build in Laravel Derictive and provide automated translations to all your blade phrases or words.

## Process

The library contains 3 database tables (phrases, translations and languages) that are used to retrieve translations using an indexed hash.

1. Search for a translation using the provided phrase (in the transl8 directive) in the `translations` table.
2. If found, return and display it on the page.
3. If not found (ot previously translated), call the Google Translate API endpoint (or any other Provider) to retrieve the correct translation.
4. Insert the newly translated text into the DB so that we don't have to call the API again for the given phrase _(step 1 above)_.

## Installation

Follow below steps to install the package

```
composer require wazza/dom-translate
php artisan vendor:publish --tag="dom-translate-config"
php artisan vendor:publish --tag="dom-translate-migrations"
php artisan migrate
// add DOM_TRANSLATE_GOOGLE_KEY={key value from Google} to your .env file
php artisan config:cache
```

Once installed, start your service again and update your Blade files with the @transl8 directive. Only new un-translated phrases will be translated via the API call. Any future requests, for the same phrase, will be retrieved from the database.

## HTML Blade Example

Find below a few examples how to use the translate Blade directive in your HTML (Blade) files.

```
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

    {{-- ...you can update the Laravel AppServiceProvider register() method and add more of you own directives  --}}

    {{-- ...and lastly, a phrase that will not be translated --}}
    <p>This phrase will not be translated.</p>
</div>
```

## Blade Directive Example:

The below 4 directives are available by default (`transl8` is the main one). You are welcome to add more directly in your Laravel `AppServiceProvider` file (under the register() method)

```
// (1) Register the default Blade directives
// With `transl8` you can supply any any destination language. If non is supplied, the default in Config would be used.
// Format: transl8('Phrase','target','source')
// Example: transl8('This must be translated to French.','fr')
Blade::directive('transl8', function ($string) {
    return \Wazza\DomTranslate\Controllers\TranslateController::phrase($string);
});

// (2) Register direct (Language specific) Blade directives, all from English
// (2.1) French Example: transl8fr('This must be translated to French.')
Blade::directive('transl8fr', function ($string) {
    return \Wazza\DomTranslate\Controllers\TranslateController::translate($string, "fr", "en");
});
// (2.2) German
Blade::directive('transl8de', function ($string) {
    return \Wazza\DomTranslate\Controllers\TranslateController::translate($string, "de", "en");
});
// (2.3) Japanese
Blade::directive('transl8je', function ($string) {
    return \Wazza\DomTranslate\Controllers\TranslateController::translate($string, "je", "en");
});
// (2.4) etc. You can create your own in Laravel AppServiceProvider register method.
```
