<?php

namespace Wazza\DomTranslate\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Wazza\DomTranslate\Http\Controllers\LanguageController;

class LanguageControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_can_set_language_preference()
    {
        $response = $this->postJson('/api/translate/set-language', [
            'language' => 'fr'
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Language preference set successfully.',
                     'language' => 'fr'
                 ]);

        // Check if session was set
        $this->assertEquals('fr', session('app_language_code'));
    }

    /** @test */
    public function it_validates_language_input()
    {
        $response = $this->postJson('/api/translate/set-language', [
            'language' => 'x' // too short
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['language']);
    }

    /** @test */
    public function it_requires_language_input()
    {
        $response = $this->postJson('/api/translate/set-language', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['language']);
    }

    /** @test */
    public function it_can_get_current_language_preference()
    {
        // Set a language preference first
        session(['app_language_code' => 'de']);

        $response = $this->getJson('/api/translate/get-language');

        $response->assertStatus(200)
                 ->assertJson([
                     'language' => 'de'
                 ]);
    }

    /** @test */
    public function it_returns_default_language_when_no_preference_set()
    {
        $response = $this->getJson('/api/translate/get-language');

        $response->assertStatus(200)
                 ->assertJsonStructure(['language']);

        // Should return either config default or fallback
        $language = $response->json('language');
        $this->assertNotEmpty($language);
    }
}
