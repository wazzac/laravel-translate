# domTranslate

Library that will use the build in Laravel Derictive and provide automated translations to all your blade phrases or words.

## Process

The library contains 3 database tables (phrases, translations and languages) that are used to retrieve translations using an indexed hash.

1. Search for a translation using the provided phrase (in the transl8 directive).
2. If found, return and display it.
3. If not found, call the Google Translate API endpoint to retrieve the correct translation.
4. Insert the translation into the DB so that we don't have to call the API again for the given phrase.

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

## Example
Find below a few examples how to use the translate Blade directive in your HTML (Blade) files
```
<div>
    {{-- Fully dependant on the source and destination language settings, only provide a phrase --}}
    <p>@transl8("I like this feature.")</p>
    {{-- Overwrite the default (1) Destination language by including a second (destination) argument --}}
    <p>@transl8("We need to test it in the staging environment.","de")</p>
    {{-- Overwrite the default (1) Source and (2) Destination languages by including a second (destination) and third (source) argument --}}
    <p>@transl8("Wie weet waar Willem Wouter woon?","af","en")</p>
    {{-- Use a Blade Language Specific directive for each language --}}
    <p>@transl8fr("This phrase will be translated to French.")</p>
    <p>@transl8de("This phrase will be translated to German.")</p>
    <p>@transl8je("This phrase will be translated to Japanese.")</p>
    {{-- ...you can update the Laravel AppServiceProvider register() method and add more of you own directives  --}}
    {{-- ...and lastly, a phrase that will not be translated --}}
    <p>This phrase will not be translated.</p>
</div>
```
