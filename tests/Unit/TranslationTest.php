<?php

namespace Wazza\DomTranslate\Tests\Unit;

use Wazza\DomTranslate\Tests\TestCase;
use Wazza\DomTranslate\Translation;
use Wazza\DomTranslate\Phrase;
use Wazza\DomTranslate\Language;

class TranslationTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testTranslationCreation()
    {
        // singular
        $translation = Translation::factory()->create(['value' => '(translated text)']);
        $this->assertTrue($translation->value == '(translated text)');

        // delete
        $translation->delete();
        $this->assertModelMissing($translation);

        // multiple
        $translations = Translation::factory()->count(3)->create();
        $this->assertEquals(3, $translations->count());
    }
}
