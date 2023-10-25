<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Wazza\DomTranslate\Phrase;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Phrase::class, function (Faker $faker) {
    $phrase = $faker->sentence(40);
    $hash = \Wazza\DomTranslate\Helpers\phraseHelper::hash($phrase);
    return [
        'language_id' => config('dom_translate.language.src'),
        'hash' => $phrase,
        'value' => $hash,
        'created_at' => Carbon\Carbon::now(),
        'updated_at' => Carbon\Carbon::now(),
    ];
});
