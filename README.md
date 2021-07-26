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

```
<p>This phrase is not translated.</p>
<p>@transl8('This phrase will be automatically translated.')</p>
```
