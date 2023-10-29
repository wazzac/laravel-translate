<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Wazza\DomTranslate\Language;
use Wazza\DomTranslate\Phrase;
use Wazza\DomTranslate\Translation;

class TranslationFactory extends Factory
{
    protected $model = Translation::class;

    public function definition()
    {
        $phrase = Phrase::factory()->create();

        return [
            'language_id' => $phrase->language_id,
            'phrase_id' => $phrase->id,
            'value' => $phrase->value . ' (translated)',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
