<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Wazza\DomTranslate\Translation;
use Wazza\DomTranslate\Language;
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

$factory->define(Translation::class, function (Faker $faker) {
    return [
        'language_id' => Language::inRandomOrder()->first(),
        'phrase_id' => Phrase::inRandomOrder()->first(),
        'value' => $faker->sentence(40),
        'created_at' => Carbon\Carbon::now(),
        'updated_at' => Carbon\Carbon::now(),
    ];
});
