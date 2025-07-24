<?php

namespace Wazza\DomTranslate\Helpers;

use Wazza\DomTranslate\Controllers\TranslateController;
use Wazza\DomTranslate\Controllers\LogController;
use Illuminate\Http\Request;

class TranslateHelper
{
    private const DEFAULT_SYSTEM_LANGUAGE = self::DEFAULT_SYSTEM_LANGUAGE;

    /**
     * Get the current user's preferred language
     *
     * @return string
     */
    public static function currentDefinedLanguageCode(string $sessionAndCookieName = 'app_language_code'): string
    {
        return session($sessionAndCookieName) // preferred language set in session (user select a language from a dropdown and we set it in a long session)
            ?? request()->cookie($sessionAndCookieName) // check for a cookie set by the app (..or, we set the selected language in a cookie)
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
    public static function autoTransl8(
        string $text,
        ?string $targetLanguage = null,
        string $sessionAndCookieName = 'app_language_code'
    ): string {
        // use the current language if no target specified
        $language = $targetLanguage ?? self::currentDefinedLanguageCode($sessionAndCookieName);

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

    /**
     * Set the user's preferred language and return a json response with a cookie
     *
     * @param string $langCode
     * @param string $sessionAndCookieName
     * @return \Illuminate\Http\JsonResponse
     */
    public static function setLanguage(
        string $langCode = 'en',
        string $sessionAndCookieName = 'app_language_code'
    ) {
        // store in session
        session([$sessionAndCookieName => $langCode]);

        // also set a cookie for 1 year as backup (with proper path and domain)
        return cookie($sessionAndCookieName, $langCode, 60 * 24 * 365, '/', null, false, false);

        // return a JSON response with the new language preference
        return response()->json([
            'message' => 'Language preference set successfully.',
            'language' => $langCode
        ])->cookie($cookie);
    }
}
