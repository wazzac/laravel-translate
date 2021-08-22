<?php

namespace Wazza\DomTranslate\Tests\Unit;

use Wazza\DomTranslate\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Wazza\DomTranslate\Translation;

class TranslationTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Test the ability to add a translation to a phrase
     * @return void
     */
    public function testTranslationCreation()
    {
        // singular
        $translation = factory(Translation::class)->create(['value' => 'ping pong']);
        $this->assertTrue($translation->value == 'ping pong');
        $translation->delete();
        $this->assertDeleted($translation);

        // multiple
        $translations = factory(Translation::class, 3)->create();
        $this->assertEquals(3, $translations->count());
    }
}
