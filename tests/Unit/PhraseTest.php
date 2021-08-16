<?php

namespace Wazza\DomTranslate\Tests\Unit;

use Wazza\DomTranslate\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Wazza\DomTranslate\Phrase;
use Wazza\DomTranslate\Translation;

class PhraseTest extends TestCase
{
   // use transactions to rollback data
   use DatabaseTransactions;

   protected $phrase;

   public function setUp(): void {
       parent::setUp();
       // $this->phrase = factory(Phrase::class)->create(); // generic phrase - not used...
   }

   /**
    * Test the ability to add a translation to a phrase
    * @return void
    */
   public function testAssociateTranslationToPhrase()
   {
       // given (input)
       $phrase = factory(Phrase::class)->create();
       $translationOne = factory(Translation::class)->create(['language_id' => 2]);
       $translationTwo = factory(Translation::class)->create(['language_id' => 3]);

       // when (read)
       $phrase->addTranslation($translationOne);
       $phrase->addTranslation($translationTwo);

       // then (compare)
       $this->assertEquals(2, $phrase->countTranslations()); // there should be 2
   }

   /**
    * Test the ability to add multiple translations to a phrase
    * @return void
    */
   public function testAssociateMultipleTranslationsToPhrase()
   {
       // given (input)
       $phrase = factory(Phrase::class)->create();
       $translationOne = factory(Translation::class)->create(['language_id' => 4]); // single
       $translationTwo = factory(Translation::class)->create(['language_id' => 5]); // single
       $translationMultiple = factory(Translation::class, 3)->create(); // multiple (collection)

       // when (read)
       $phrase->addTranslation($translationOne); // insert single into singular
       $phrase->addTranslations($translationTwo); // insert single into plural
       $phrase->addTranslations($translationMultiple); // insert multiple into plural

       // then (compare)
       $this->assertEquals(5, $phrase->countTranslations()); // there should be 5...
   }

   /**
    * Test the ability to remove a translation (and multiple) from a phrase
    * @return void
    */
   public function testDissociateTranslationFromPhrase()
   {
       // given
       $phrase = factory(Phrase::class)->create();
       $translationOne = factory(Translation::class)->create(); // single
       $translationTwo = factory(Translation::class)->create(); // single
       $translationThree = factory(Translation::class)->create(); // single
       $translationMany = factory(Translation::class,5)->create(); // multiple (5)

       // when
       $phrase->addTranslation($translationOne); // will remain
       $phrase->addTranslation($translationTwo); // will be removed
       $phrase->addTranslation($translationThree); // will remain
       $phrase->addTranslations($translationMany); // will all be removed
       $this->assertEquals(8, $phrase->countTranslations()); // full count = 8

       // lets remove a phrase
       $phrase->removeTranslation($translationTwo); // single
       $phrase->removeTranslations($translationMany); // multiple (5)

       // then
       $this->assertEquals(2, $phrase->countTranslations()); // full count = 2
   }

    /**
    * Test the ability to remove all translations from a phrase
    * @return void
    */
   public function testDissociateAllTranslationsFromPhrase()
   {
       // given
       $phrase = factory(Phrase::class)->create();
       $translations = factory(Translation::class, 5)->create();

       // when
       $phrase->addTranslations($translations);

       // lets remove all
       $phrase->removeAllTranslations();

       // then
       $this->assertEquals(0, $phrase->countTranslations());
   }
}
