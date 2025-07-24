<?php

namespace Wazza\DomTranslate\Helpers;

use Wazza\DomTranslate\Controllers\TranslateController;
use Wazza\DomTranslate\Controllers\LogController;

class TranslateHelper
{
    private const DEFAULT_SYSTEM_LANGUAGE = self::DEFAULT_SYSTEM_LANGUAGE;

    /**
     * Get the current user's preferred language
     *
     * @return string
     */
    public static function currentDefinedLanguageCode(): string
    {
        return session('app_language_code') // preferred language set in session (user select a language from a dropdown and we set it in a long session)
            ?? request()->cookie('app_language_code') // check for a cookie set by the app (..or, we set the selected language in a cookie)
            ?? config('dom_translate.language.dest') // our default destination language defined in the translation config
            ?? config('app.locale', self::DEFAULT_SYSTEM_LANGUAGE) // absolute fallback to the app's locale if nothing else is set
            ?? self::DEFAULT_SYSTEM_LANGUAGE; // if all else fails, default to English
    }

    /**
     * Auto-translate using the current user's language preference
     *
     * @param string $text
     * @param string|null $targetLanguage
     * @return string
     */
    public static function autoTransl8(string $text, ?string $targetLanguage = null): string
    {
        // use the current language if no target specified
        $language = $targetLanguage ?? self::currentDefinedLanguageCode();

        // if it's English or no translation needed, return original
        if ($language === self::DEFAULT_SYSTEM_LANGUAGE) {
            return $text;
        }

        // try and translate using the TranslateController
        try {
            // Access the TranslateController singleton from the service container
            $translator = new TranslateController();

            // return the translated text
            return $translator->translate(
                $text,
                $language,
                self::DEFAULT_SYSTEM_LANGUAGE
            );
        } catch (\Exception $e) {
            // if translation fails, log it and return original text
            LogController::log('notice', 1, 'Translation failed: ' . $e->getMessage(), [
                'text' => $text,
                'target_language' => $language
            ]);

            // return the original text if translation fails
            return $text;
        }
    }
}
