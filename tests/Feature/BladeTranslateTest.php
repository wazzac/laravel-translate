<?php

namespace Wazza\DomTranslate\Tests\Feature;

use Wazza\DomTranslate\Tests\TestCase;
use Wazza\DomTranslate\Controllers\TranslateController;

class BladeTranslateTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * A basic translate test
     * IMPORTANT: make sure you added your own API key
     *
     * @return void
     */
    public function testGenericTranslate()
    {
        $phrase = "This is a test.";

        // in default English to German
        $translation = TranslateController::translate($phrase, 'de', 'en');
        $this->assertEquals($translation, "Das ist ein Test.");

        // ... and then to Dutch
        $this->assertEquals(TranslateController::translate($translation, 'nl', 'de'), "Dit is een test.");
    }
}
