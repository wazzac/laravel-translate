<?php

namespace Wazza\DomTranslate\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Wazza\DomTranslate\Phrase;
use Wazza\DomTranslate\Language;
use Wazza\DomTranslate\Translation;
use Wazza\DomTranslate\Controllers\LogController;

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

    /**
     * Function that will receive a single string containing the phrase to be translated as well as the destination and source languages
     * This would most lickely be used from the Blade custom directive.
     *
     * @param string|null $string String containing - "phrase to translate","fr","en" (or single quotes)
     * @return void
     */
    public static function phrase(?string $string = null)
    {
        // split the string
        $arguments = str_getcsv($string, ",", substr($string, 0, 1) == '"' ? "\"" : "'");

        // sanitise the items
        foreach ($arguments as $key => $argument) {
            $arguments[$key] = self::sanitise($argument);
        }

        // call the translation method
        return self::translate($arguments[0] ?? null, $arguments[1] ?? null, $arguments[2] ?? null);
    }


    /**
     * Primary translation method
     *
     * @todo - decouple the translate API from this method.
     * @param string|null $source The phrase to be translated
     * @param string|null $langdest The destination language code - i.e. fr (defaults would be retrieved from the config file)
     * @param string|null $langsrc The source language code - i.e. en (defaults would be retrieved from the config file)
     * @return void
     * @throws Exception
     */
    public static function translate(?string $source = null, ?string $langdest = null, ?string $langsrc = null)
    {
        LogController::log('notice', 1, 'New phrase to translate.'); // high = 1

        // sanitise the source
        $source = self::sanitise($source);
        LogController::log('notice', 3, 'Phrase Sanitised: ' . $source); // low = 3

        // hash the source
        $srcHash = self::hash($source);
        LogController::log('notice', 3, 'Phrase Hash: ' . $srcHash); // low = 3

        // do we have a destination language defined
        if (empty($langdest)) {
            $langdest = config('dom_translate.language.dest');
        }
        LogController::log('notice', 2, 'Destination language code set as: ' . $langdest);

        // do we have a source language defined
        if (empty($langsrc)) {
            $langsrc = config('dom_translate.language.src');
        }
        LogController::log('notice', 2, 'Source language code set as: ' . $langdest);

        // ok, ready to rock-and-roll... here we go!
        try {
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
                LogController::log('notice', 1, 'Translation located in DB.');
                return $translation->value;
            }
            LogController::log('notice', 1, 'Translation NOT located in DB.');

            // (2) We could not find a direct translation in the DB... We need to call the API
            // (2.1) ... first see if we have the correct Phrase
            $phrase = Phrase::where('hash', $srcHash)->whereHas('language', function ($q) use ($langsrc) {
                $q->where('code', $langsrc);
            })->first();

            // if no phrase were located, insert a new record
            if (is_null($phrase)) {
                // find the source language id
                $langSource = Language::select('id')->where('code', $langsrc)->first();
                if (is_null($langSource)) {
                    throw new Exception('Phrase could not be inserted into DB because the source language could not be loaded.');
                }

                // insert the new phrase
                $phrase = new Phrase();
                $phrase->language_id = $langSource->id;
                $phrase->hash = $srcHash;
                $phrase->value = $source;
                $phrase->save();
                LogController::log('notice', 1, 'New Phrase saved.');
            }

            LogController::log('notice', 1, 'Call API for Translation.');

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

            // convert body response (json) to array
            $responseBody = json_decode($apiResponse->getBody(), true); // json decode to array
            LogController::log('notice', 3, 'API response:', $responseBody);

            // inspect result
            if ($apiResponse->getStatusCode() != 200) {
                // something went wrong with the API call
                LogController::log('error', 1, 'API Error: ' . $apiResponse->getReasonPhrase() ?? 'Unknown error' . "; Returning original phrase.");
                return $source;
            }

            // great, we received 200 back... lets add the translation to the translations table (if we can find it)
            if (!isset($responseBody['data']['translations'][0]['translatedText'])) {
                // translation is blank
                throw new Exception('No Translation returned from the API.');
            }
            LogController::log('notice', 1, 'Translation located via API.');

            // find the language id
            $langDest = Language::select('id')->where('code', $langdest)->first();
            if (is_null($langDest)) {
                throw new Exception('Translation could not be inserted into DB because the destincation language could not be loaded.');
            }

            // insert new tranlation
            $translation = new Translation();
            $translation->language_id = $langDest->id;
            $translation->phrase_id = $phrase->id;
            $translation->value = $responseBody['data']['translations'][0]['translatedText'];
            $translation->save();
            LogController::log('notice', 1, 'Translation inserted into DB.');

            // all good
            return trim($responseBody['data']['translations'][0]['translatedText']);
        } catch (Exception $e) {
            // something went wrong, return the source and log the error
            LogController::log('error', 1, 'Exception: ' . $e->getMessage());
            return $source;
        }
    }
}
