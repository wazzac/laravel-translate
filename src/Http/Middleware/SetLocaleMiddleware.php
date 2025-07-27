<?php

namespace Wazza\DomTranslate\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Wazza\DomTranslate\Helpers\TranslateHelper;

/**
 * Middleware to automatically set Laravel's application locale
 * based on the user's language preference stored in session/cookie.
 *
 * This ensures Laravel's built-in localization features (__(), trans(), etc.)
 * work consistently with the dom-translate package.
 */
class SetLocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the user's preferred language using the package's helper
        $sessionAndCookieName = config('dom_translate.session.language_key', 'app_language_code');
        $language = TranslateHelper::currentDefinedLanguageCode($sessionAndCookieName);

        // Set the application locale for Laravel's built-in localization
        app()->setLocale($language);

        // Optional: Set locale for Carbon dates as well
        if (class_exists('\Carbon\Carbon')) {
            try {
                \Carbon\Carbon::setLocale($language);
            } catch (\Exception $e) {
                // Fallback to English if the locale is not supported by Carbon
                \Carbon\Carbon::setLocale('en');
            }
        }

        return $next($request);
    }
}
