<?php

namespace Wazza\DomTranslate\Tests\Feature;

use PHPUnit\Framework\Attributes\Group;
use Wazza\DomTranslate\Tests\TestCase;
use Wazza\DomTranslate\Controllers\TranslateController;

class BladeTranslateTest extends TestCase
{
    /**
     * A basic translate test.
     *
     * Requires a valid DOM_TRANSLATE_GOOGLE_KEY in .env / phpunit.xml.
     * Skip this test in CI environments without a live API key.
     */
    #[Group('requires-api-key')]
    public function testGenericTranslate(): void
    {
        if (empty(config('dom_translate.api.google.key'))) {
            $this->markTestSkipped('DOM_TRANSLATE_GOOGLE_KEY is not set — skipping live API test.');
        }

        $phrase = 'This is a test.';

        /** @var TranslateController $translator */
        $translator = app(TranslateController::class);

        // English to German
        $translation = $translator->translate($phrase, 'de', 'en');
        $this->assertEquals('Das ist ein Test.', $translation);

        // German to Dutch
        $this->assertEquals('Dit is een test.', $translator->translate($translation, 'nl', 'de'));
    }
}
