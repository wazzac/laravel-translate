<?php

namespace Wazza\DomTranslate\Controllers\ApiTranslate;

use GuzzleHttp\Client;
use Wazza\DomTranslate\Contracts\CloudTranslateInterface;
use Wazza\DomTranslate\Controllers\LogController;
use Exception;

class GoogleTranslate implements CloudTranslateInterface
{
    protected array $defaultProvider;

    /**
     * Constructor to set the config file.
     */
    public function __construct()
    {
        $this->defaultProvider = config('dom_translate.api.google');
    }

    /**
     * Initiate a translate API request via Google Cloud Translation v2.
     *
     * @param string|null $phrase   The phrase to translate.
     * @param string|null $langdest Destination language code (ISO-639-1).
     * @param string|null $langsrc  Source language code (ISO-639-1).
     * @throws Exception
     */
    public function cloudTranslate(?string $phrase = null, ?string $langdest = null, ?string $langsrc = null): string
    {
        LogController::log('notice', 2, '[Google] Translate API request initiated.');

        $client = new Client(['http_errors' => false]);

        $response = $client->request(
            $this->defaultProvider['action'],
            $this->defaultProvider['endpoint'] . '?key=' . $this->defaultProvider['key'],
            [
                'headers' => [
                    'Accept'       => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'target' => $langdest,
                    'source' => $langsrc,
                    'q'      => $phrase,
                ],
            ]
        );

        if ($response->getStatusCode() !== 200) {
            throw new Exception($response->getReasonPhrase() ?: 'Unknown Google API error');
        }

        $responseBody = json_decode((string) $response->getBody(), true);
        LogController::log('notice', 3, '[Google] API response:', $responseBody ?? []);

        if (!isset($responseBody['data']['translations'][0]['translatedText'])) {
            throw new Exception('No translation returned from the Google API.');
        }

        LogController::log('notice', 1, '[Google] Translation located via API.');

        return $responseBody['data']['translations'][0]['translatedText'];
    }
}
