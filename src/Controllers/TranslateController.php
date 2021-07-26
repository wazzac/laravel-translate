<?php

namespace Wazza\DomTranslate\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Wazza\DomTranslate\Phrase;
use Wazza\DomTranslate\Language;
use Wazza\DomTranslate\Translation;

class TranslateController extends Controller
{
    /**
     * Sanitise a given String, removing any single of double quotes.
     *
     * @param string $string
     * @return string
     */
    private static function sanitise($string)
    {
        return trim($string, '\'"');
    }

    /**
     * Hash a given String
     *
     * @param string $string
     * @return string
     */
    private static function hash(string $string)
    {
        return hash_hmac(
            config('dom_translate.hash.algo'),
            $string,
            config('dom_translate.hash.salt')
        );
    }


    public static function phrase(?string $source = null, ?string $langdest = null, ?string $langsrc = null)
    {
        // sanitise the source
        $source = self::sanitise($source);

        // hash the source
        $srcHash = self::hash($source);

        // do we have a destination language defined
        if (empty($langdest)) {
            $langdest = config('dom_translate.language.dest');
        }

        // do we have a source language defined
        if (empty($langsrc)) {
            $langsrc = config('dom_translate.language.src');
        }

        // (1) Search for the direct Translation (ideal scenario)
        $translation = Translation::select('value')
            ->whereHas('language', function ($query) use ($langdest) {
                $query->where('code', $langdest);
            })->whereHas('phrase', function ($queryl1) use ($srcHash, $langsrc) {
                $queryl1->where('hash', $srcHash);
                $queryl1->whereHas('language', function ($queryl2) use ($langsrc) {
                    $queryl2->where('code', $langsrc);
                });
            })->first();

        // did we locate a direct translation?
        if (!is_null($translation)) {
            // yes we did - awesome... return it.
            return $translation->value;
        }

        // (2) We could not find a direct translation in the DB... We need to call the API
        // (2.1) ... first see if we have the correct Phrase
        $phrase = Phrase::where('hash', $srcHash)->whereHas('language', function ($q) use ($langsrc) {
            $q->where('code', $langsrc);
        })->first();

        // if no phrase were located, insert a new record
        if (is_null($phrase)) {
            // find the source language id
            $langSource = Language::select('id')->where('code', $langsrc)->first();

            if (!is_null($langSource)) {
                // insert the new phrase
                $phrase = new Phrase();
                $phrase->language_id = $langSource->id;
                $phrase->hash = $srcHash;
                $phrase->value = $source;
                $phrase->save();
            }
        }

        // (2.2) ...ok, we have a Phrase... Let's get the correct Translation and insert it into the DB
        $defaultProvider = config('dom_translate.api.provider');
        $apiConnection = new \GuzzleHttp\Client([
            'http_errors' => false
        ]);

        // send api request
        $apiUri = config('dom_translate.api.' . $defaultProvider . '.endpoint') . '?key=' . config('dom_translate.api.' . $defaultProvider . '.key');
        $apiResponse = $apiConnection->request(
            config('dom_translate.api.' . $defaultProvider . '.action'),
            $apiUri,
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'target' => $langdest,
                    'source' => $langsrc,
                    'q' => $source
                ]
            ]
        );

        // update the reponse
        $responseCode = $apiResponse->getStatusCode(); // 200
        $responseBody = json_decode($apiResponse->getBody(), true); // json decode to array

        // inspect result
        if ($responseCode != 200) {
            // something went wrong
            return '[' . $responseBody['code'] ?? 500 . '] ' . $responseBody['message'] ?? 'Unknown error';
        }

        // great, we received 200 back... lets add the translation to the translations table (if we can find it)
        if (isset($responseBody['data']['translations'][0]['translatedText'])) {
            // find the language id
            $langDest = Language::select('id')->where('code', $langdest)->first();

            if (!is_null($langDest)) {
                // insert new tranlation
                $translation = new Translation();
                $translation->language_id = $langDest->id;
                $translation->phrase_id = $phrase->id;
                $translation->value = $responseBody['data']['translations'][0]['translatedText'];
                $translation->save();
            }

            // all good
            return $responseBody['data']['translations'][0]['translatedText'];
        }

        // something went wrong, return the source
        return $source;
    }
}
