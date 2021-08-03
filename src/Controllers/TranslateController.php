<?php

namespace Wazza\DomTranslate\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Wazza\DomTranslate\Phrase;
use Wazza\DomTranslate\Language;
use Wazza\DomTranslate\Translation;
use Wazza\DomTranslate\Helpers\phraseHelper;
use Wazza\DomTranslate\Controllers\LogController;
use Exception;

class TranslateController extends Controller
{
    /**
     * Function that will receive a single string containing the phrase to be translated as well as the destination and source languages
     * This would most lickely be used from the Blade custom directive.
     *
     * @param string|null $string String containing - "phrase to translate","fr","en" (or single quotes)
     * @return void
     */
    public static function phrase(?string $string = null)
    {
        // split the string (not using explode)
        $arguments = phraseHelper::splitTranslateArgs($string);

        // sanitise the items
        foreach ($arguments as $key => $argument) {
            $arguments[$key] = phraseHelper::sanitise($argument);
        }

        // call the translation method
        return self::translate($arguments[0] ?? null, $arguments[1] ?? null, $arguments[2] ?? null);
    }


    /**
     * Primary translation method
     *
     * @todo - decouple the translate API from this method.
     * @param string|null $srcPhrase The phrase to be translated
     * @param string|null $destCode The destination language code - i.e. fr (defaults would be retrieved from the config file)
     * @param string|null $srcCode The source language code - i.e. en (defaults would be retrieved from the config file)
     * @return void
     * @throws Exception
     */
    public static function translate(?string $srcPhrase = null, ?string $destCode = null, ?string $srcCode = null)
    {
        LogController::log('notice', 1, 'New phrase to translate.'); // high-level = 1

        // sanitise the phrase
        $srcPhrase = phraseHelper::sanitise($srcPhrase);
        LogController::log('notice', 3, 'Phrase Sanitised: ' . $srcPhrase); // low-level = 3

        // hash the phrase
        $srcHash = phraseHelper::hash($srcPhrase);
        LogController::log('notice', 3, 'Phrase Hashed: ' . $srcHash);

        // do we have a destination language defined
        if (empty($destCode)) {
            $destCode = config('dom_translate.language.dest');
        }
        LogController::log('notice', 2, 'Destination language code set as: ' . $destCode);

        // do we have a source language defined
        if (empty($srcCode)) {
            $srcCode = config('dom_translate.language.src');
        }
        LogController::log('notice', 2, 'Source language code set as: ' . $srcCode);

        // ok, ready to rock-and-roll...
        try {
            // (1) Search for the direct Translation in the DB using the Phrase HASH (ideal scenario)
            $translation = Translation::select('value')
                ->whereHas('language', function ($query) use ($destCode) {
                    $query->where('code', $destCode);
                })->whereHas('phrase', function ($queryl1) use ($srcHash, $srcCode) {
                    $queryl1->where('hash', $srcHash);
                    $queryl1->whereHas('language', function ($queryl2) use ($srcCode) {
                        $queryl2->where('code', $srcCode);
                    });
                })->first();

            // did we locate a direct translation?
            if (!is_null($translation)) {
                // yes we did - awesome... return it.
                LogController::log('notice', 1, 'Translation located in DB. Return translation...');
                return $translation->value;
            }
            LogController::log('notice', 1, 'Translation NOT located in DB. Continue...');

            // (2) We could not find a direct translation in the DB... We need to call the Cloud API
            // (2.1) ... first see if we have the correct Phrase
            $phrase = Phrase::where('hash', $srcHash)->whereHas('language', function ($q) use ($srcCode) {
                $q->where('code', $srcCode);
            })->first();

            // if no phrase were located, insert a new record
            if (is_null($phrase)) {
                LogController::log('notice', 1, 'Could not locate the Phrase in the DB, we would need to insert it.');

                // find the source language id
                $languageSrc = Language::select('id')->where('code', $srcCode)->first();
                if (is_null($languageSrc)) {
                    throw new Exception('Phrase could not be inserted into DB because the source language (code: ' . $srcCode . ') could not be loaded.');
                }

                // insert the new phrase into the DB
                $phrase = new Phrase();
                $phrase->language_id = $languageSrc->id;
                $phrase->hash = $srcHash;
                $phrase->value = $srcPhrase;
                $phrase->save();
                LogController::log('notice', 1, 'New Phrase saved in DB as ID - ' . $phrase->id);
            } else {
                LogController::log('notice', 1, 'Phrase located under ID - ' . $phrase->id);
            }

            // (2.2) ...ok, the phrase is in the DB. Let's get the correct Translation and insert it into the DB against the phrase
            LogController::log('notice', 1, 'Call API for Translation.');

            $defaultProvider = config('dom_translate.api.provider');
            LogController::log('notice', 2, 'Default API provider - ' . $defaultProvider);

            $providerController = config('dom_translate.api.' . $defaultProvider . '.controller');
            LogController::log('notice', 2, 'Provider Controller - ' . $providerController);

            // bind the Provider Translation Controller with the `Cloud Translate Interface`
            App::bind(Wazza\DomTranslate\Contracts\CloudTranslateInterface::class, $providerController);
            LogController::log('notice', 3, 'Provider Controller ' . $providerController . ' binded to the CloudTranslateInterface Class.');

            // initiate the cloud translate request on the binded provider class
            $translatedString = App::make(Wazza\DomTranslate\Contracts\CloudTranslateInterface::class)->cloudTranslate($srcPhrase, $destCode, $srcCode);

            // (3) insert translated text into db
            // (3.1) find the destination language id
            $languageDest = Language::select('id')->where('code', $destCode)->first();
            if (is_null($languageDest)) {
                throw new Exception('Translation could not be inserted into DB because the destincation language could not be loaded.');
            }
            LogController::log('notice', 1, 'Destination Language (code: ' . $languageDest->id . ') located at ID - ' . $languageDest->id);

            // (3.2) insert new tranlation into db
            $translation = new Translation();
            $translation->language_id = $languageDest->id;
            $translation->phrase_id = $phrase->id;
            $translation->value = $translatedString;
            $translation->save();
            LogController::log('notice', 1, 'New Translation inserted into DB at ID - ' . $translation->id);

            // return the newly translated text
            return $translatedString;
        } catch (Exception $e) {
            // something went wrong, return the source and log the error
            LogController::log('error', 1, 'Exception Error: ' . $e->getMessage());
            return $srcPhrase;
        }
    }
}
