<?php

namespace Wazza\DomTranslate\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Wazza\DomTranslate\Helpers\TranslateHelper;

class LanguageController extends Controller
{
    /**
     * Set the user's preferred language
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function setLanguage(Request $request): JsonResponse
    {
        $request->validate([
            'language' => 'required|string|min:2|max:5'
        ]);

        $langCode = $request->input('language');

        return TranslateHelper::setLanguage($langCode);
    }

    /**
     * Get the current user's preferred language
     *
     * @return JsonResponse
     */
    public function getLanguage(): JsonResponse
    {
        return TranslateHelper::getLanguage();
    }
}
