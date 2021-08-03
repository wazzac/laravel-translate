<?php

namespace Wazza\DomTranslate\Controllers\ApiTranslate;

use Wazza\DomTranslate\Contracts\CloudTranslateInterface;
use Wazza\DomTranslate\Controllers\LogController;
use Exception;

class GoogleTranslate implements CloudTranslateInterface
{
    protected $defaultProvider;

    /**
     * Constructor to set the config file
     */
    public function __construct()
    {
        $this->defaultProvider = config('dom_translate.api.google');
    }


    /**
     * Method that will initiate a translate API request from Google
     *
     * @param string|null $phrase The phrase to be translated
     * @param string|null $langdest The destination language code - i.e. fr (defaults would be retrieved from the config file)
     * @param string|null $langsrc The source language code - i.e. en (defaults would be retrieved from the config file)
     * @return string Translated string
     * @throws Exception
     */
    public function cloudTranslate(?string $phrase = null, ?string $langdest = null, ?string $langsrc = null)
    {
        LogController::log('notice', 2, '[Google] Translate API request initiated.');

        // init a new Guzzle Http Client
        $client = new \GuzzleHttp\Client(['http_errors' => false]);

        // send api request
        $response = $client->request(
            $this->defaultProvider['action'],
            $this->defaultProvider['endpoint'] . '?key=' . $this->defaultProvider['key'],
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'target' => $langdest,
                    'source' => $langsrc,
                    'q' => $phrase
                ]
            ]
        );

        // inspect result
        if ($response->getStatusCode() != 200) {
            // something went wrong with the API call
            throw new Exception($response->getReasonPhrase() ?? 'Unknown error');
        }

        // convert body response (json) to array
        $responseBody = json_decode($response->getBody(), true); // json decode to array
        LogController::log('notice', 3, '[Google] API response:', $responseBody);

        // great, we received 200 back... lets add the translation to the translations table (if we can find it)
        if (!isset($responseBody['data']['translations'][0]['translatedText'])) {
            // translation is null
            throw new Exception('No Translation returned from the API.');
        }
        LogController::log('notice', 1, '[Google] Translation located via API.');

        // return the new translated phrase
        return $responseBody['data']['translations'][0]['translatedText'];
    }
}
