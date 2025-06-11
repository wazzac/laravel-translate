<?php

namespace Wazza\DomTranslate\Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Wazza\DomTranslate\Helpers\PhraseHelper;
use Wazza\DomTranslate\Phrase;
use Wazza\DomTranslate\Language;

class PhraseFactory extends Factory
{
    protected $model = Phrase::class;

    public function definition()
    {
        $phrase = $this->faker->sentence(40);
        $hash = PhraseHelper::hash($phrase);

        return [
            'language_id' => Language::where('code', config('dom_translate.language.src'))->first()->id,
            'hash' => $hash,
            'value' => $phrase,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
