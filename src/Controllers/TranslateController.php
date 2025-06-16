<?php

namespace Wazza\DomTranslate\Controllers;

use Wazza\DomTranslate\Controllers\BaseController;
use Wazza\DomTranslate\Controllers\LogController;
use Illuminate\Support\Facades\App;
use Wazza\DomTranslate\Phrase;
use Wazza\DomTranslate\Language;
use Wazza\DomTranslate\Translation;
use Wazza\DomTranslate\Helpers\PhraseHelper;
use Wazza\DomTranslate\Contracts\CloudTranslateInterface;
use Exception;

class TranslateController extends BaseController
{
    /**
     * Function that will receive a single string containing the phrase to be translated as well as the destination and source languages
     * This would most likely be used from the Blade custom directive.
     *
     * @param string|null $string String containing - "phrase to translate","fr","en" (or single quotes)
     * @return string
     */
    public function phrase(?string $string = null): string
    {
        // split the string (not using explode)
        $arguments = PhraseHelper::splitTranslateArgs($string);

        // sanitise the items
        foreach ($arguments as $key => $argument) {
            $arguments[$key] = PhraseHelper::sanitise($argument);
        }

        // call the translation method
        return $this->translate($arguments[0] ?? null, $arguments[1] ?? null, $arguments[2] ?? null);
    }


    /**
     * Primary translation method
     *
     * @todo - decouple the translate API from this method.
     * @param string|null $srcPhrase The phrase to be translated
     * @param string|null $destCode The destination language code - i.e. fr (defaults would be retrieved from the config file)
     * @param string|null $srcCode The source language code - i.e. en (defaults would be retrieved from the config file)
     * @return string
     * @throws Exception
     */
    public function translate(?string $srcPhrase = null, ?string $destCode = null, ?string $srcCode = null): string
    {
        LogController::log('notice', 1, '----- Translation request start -----');
        LogController::log('notice', 1, 'New phrase to translate.'); // high-level = 1

        // sanitise the phrase
        $srcPhrase = PhraseHelper::sanitise($srcPhrase);
        LogController::log('notice', 3, 'Phrase Sanitised ............... : ' . $srcPhrase); // low-level = 3

        // hash the phrase
        $srcHash = PhraseHelper::hash($srcPhrase);
        LogController::log('notice', 3, 'Phrase Hashed .................. : ' . $srcHash);

        // do we have a destination language defined
        $destCode = PhraseHelper::prepDestLanguage($destCode);
        LogController::log('notice', 2, 'Destination language code set as : ' . $destCode);

        // do we have a source language defined
        $srcCode = PhraseHelper::prepSrcLanguage($srcCode);
        LogController::log('notice', 2, 'Source language code set as .... : ' . $srcCode);

        // ok, ready to rock-and-roll...
        try {
            // ------------------------------------------------------------
            // (1) First search the Session for the unique hash (ideal scenario) - if enabled in config
            if (config('dom_translate.use_session', false) === true) {
                LogController::log('notice', 3, 'Use Session is enabled. Searching for translation in Session...');

                if (session()->has($srcHash . $srcCode . $destCode)) {
                    // session translation located
                    LogController::log('notice', 1, 'Translation located in Session. Returning translation...');
                    return session()->get($srcHash . $srcCode . $destCode);
                }
            } else {
                LogController::log('notice', 3, 'Use Session is disabled. Skipping Session search...');
            }

            // ------------------------------------------------------------
            // (2) Search for the direct Translation in the DB using the Phrase HASH (ok scenario as well)
            if (config('dom_translate.use_database', true) === true) {
                LogController::log('notice', 3, 'Use Database is enabled. Searching for translation in DB...');

                // try to locate the translation in the DB
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
                    // yes we did - awesome...
                    LogController::log('notice', 1, 'Translation located in DB. Return translation...');

                    // save to session so that future request (for this session) can be returned at step 1 above
                    if (config('dom_translate.use_session', false) === true && !session()->has($srcHash . $srcCode . $destCode)) {
                        LogController::log('notice', 2, 'DB Located translation saved to Session.');
                        session()->put($srcHash . $srcCode . $destCode, $translation->value);
                    }

                    // ...and return.
                    return $translation->value;
                }
                LogController::log('notice', 1, 'Translation NOT located in DB. Continue...');
            } else {
                LogController::log('notice', 3, 'Use Database is disabled. Skipping DB search...');
            }

            // ------------------------------------------------------------
            // We could not find a direct translation in the DB nor Session... We need to call the Cloud API
            // ------------------------------------------------------------

            // (3) We need to first locate the Phrase in the DB (if enabled in config), and insert it if not found
            $phrase = null;
            if (config('dom_translate.use_database', true) === true) {
                // first see if we have the correct Phrase
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
            } else {
                LogController::log('notice', 3, 'Use Database is disabled. Skipping Phrase check and insertion...');
            }

            // ------------------------------------------------------------
            // (4) We need to call the Cloud API to get the translation
            LogController::log('notice', 1, 'Call API for Translation.');

            $defaultProvider = config('dom_translate.api.provider');
            LogController::log('notice', 2, 'Default API provider - ' . $defaultProvider);

            $providerController = config('dom_translate.api.' . $defaultProvider . '.controller');
            LogController::log('notice', 2, 'Provider Controller - ' . $providerController);

            // bind the Provider Translation Controller with the `Cloud Translate Interface`
            App::bind(CloudTranslateInterface::class, $providerController);
            LogController::log('notice', 3, 'Provider Controller ' . $providerController . ' binded to the CloudTranslateInterface Class.');

            // initiate the cloud translate request on the binded provider class
            $translatedString = App::make(CloudTranslateInterface::class)->cloudTranslate(
                $srcPhrase,
                $destCode,
                $srcCode
            );

            // ------------------------------------------------------------
            // (5) insert translated text into db (if enabled in config)
            if (!is_null($phrase) && config('dom_translate.use_database', true) === true) {
                LogController::log('notice', 3, 'Use Database is enabled. Inserting translation into DB...');

                // (5.1) find the destination language id
                $languageDest = Language::select('id')->where('code', $destCode)->first();
                if (is_null($languageDest)) {
                    throw new Exception('Translation could not be inserted into DB because the destincation language could not be loaded.');
                }
                LogController::log('notice', 1, 'Destination Language (code: ' . $languageDest->id . ') located at ID - ' . $languageDest->id);

                // (5.2) insert new translation into db
                $translation = new Translation();
                $translation->language_id = $languageDest->id;
                $translation->phrase_id = $phrase->id;
                $translation->value = $translatedString;
                $translation->save();
                LogController::log('notice', 1, 'New Translation inserted into DB at ID - ' . $translation->id);
            } else {
                LogController::log('notice', 3, 'Use Database is disabled (or source phrase object is null). Skipping DB insertion...');
            }

            // (6) save to session so that future request (for this session) can be returned at step 1 above
            if (config('dom_translate.use_session', false) === true) {
                LogController::log('notice', 3, 'Use Session is enabled. Inserting translation into Session...');
                session()->put($srcHash . $srcCode . $destCode, $translatedString);
            } else {
                LogController::log('notice', 3, 'Use Session is disabled. Skipping Session insertion...');
            }

            // ------------------------------------------------------------
            // return the newly translated text
            return $translatedString;
        } catch (Exception $e) {
            // something went wrong, return the source and log the error
            LogController::log('error', 1, 'Exception Error: ' . $e->getMessage());
            return $srcPhrase;
        }
    }
}
