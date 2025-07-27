<?php

use Illuminate\Support\Facades\Route;
use Wazza\DomTranslate\Http\Controllers\LanguageController;

/*
|--------------------------------------------------------------------------
| Dom Translate Routes
|--------------------------------------------------------------------------
|
| These routes provide API endpoints for setting and getting the user's
| preferred language. They can be disabled via config if needed.
|
*/

Route::group([
    'prefix' => config('dom_translate.routes.prefix', 'api/translate'),
    'middleware' => config('dom_translate.routes.middleware', 'web'),
], function () {
    // Language preference routes
    Route::post('/set-language', [LanguageController::class, 'setLanguage'])->name('dom-translate.language.set');
    Route::get('/get-language', [LanguageController::class, 'getLanguage'])->name('dom-translate.language.get');
});
