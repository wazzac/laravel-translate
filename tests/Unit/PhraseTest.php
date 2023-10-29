<?php

namespace Wazza\DomTranslate\Tests\Unit;

use Wazza\DomTranslate\Tests\TestCase;
use Wazza\DomTranslate\Phrase;
use Wazza\DomTranslate\Translation;

class PhraseTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testAssociateTranslationToPhrase()
    {
        /**
         * @var Phrase $phrase
         */
        $phrase = Phrase::factory()->create();

        /**
         * @var Translation $translationOne
         */
        $translationOne = Translation::factory()->create(['language_id' => 2]);

        /**
         * @var Translation $translationTwo
         */
        $translationTwo = Translation::factory()->create(['language_id' => 3]);

        // when
        $phrase->addTranslation($translationOne);
        $phrase->addTranslation($translationTwo);

        // then
        $this->assertEquals(2, $phrase->translations()->count());
    }

    public function testAssociateMultipleTranslationsToPhrase()
    {
        // given
        $phrase = Phrase::factory()->create();
        $translationOne = Translation::factory()->create(['language_id' => 4]);
        $translationTwo = Translation::factory()->create(['language_id' => 5]);
        $translationMultiple = Translation::factory()->count(3)->create();

        // when
        $phrase->addTranslation($translationOne);
        $phrase->addTranslation($translationTwo);
        $phrase->addTranslations($translationMultiple);

        // then
        $this->assertEquals(5, $phrase->translations()->count());
    }

    public function testDissociateTranslationFromPhrase()
    {
        // given
        $phrase = Phrase::factory()->create();
        $translationOne = Translation::factory()->create();
        $translationTwo = Translation::factory()->create();
        $translationThree = Translation::factory()->create();
        $translationMany = Translation::factory()->count(5)->create();

        // when
        $phrase->addTranslation($translationOne);
        $phrase->addTranslation($translationTwo);
        $phrase->addTranslation($translationThree);
        $phrase->addTranslations($translationMany);

        $this->assertEquals(8, $phrase->translations()->count());

        // then
        $phrase->removeTranslation($translationTwo);
        $phrase->removeTranslations($translationMany);

        // then
        $this->assertEquals(2, $phrase->translations()->count());
    }

    public function testDissociateAllTranslationsFromPhrase()
    {
        // given
        $phrase = Phrase::factory()->create();
        $translations = Translation::factory()->count(5)->create();

        // when
        $phrase->addTranslations($translations);

        // then
        $phrase->removeAllTranslations();

        // then
        $this->assertEquals(0, $phrase->translations()->count());
    }
}
