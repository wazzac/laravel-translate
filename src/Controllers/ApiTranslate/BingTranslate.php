<?php

namespace Wazza\DomTranslate\Controllers\ApiTranslate;

use GuzzleHttp\Client;
use Wazza\DomTranslate\Contracts\CloudTranslateInterface;
use Wazza\DomTranslate\Controllers\LogController;
use Exception;

/**
 * Azure Cognitive Translator v3 implementation.
 *
 * Requires an Azure Cognitive Services subscription key.
 * @see https://docs.microsoft.com/en-us/azure/cognitive-services/translator/
 */
class BingTranslate implements CloudTranslateInterface
{
    protected array $defaultProvider;

    /**
     * Constructor to set the config file.
     */
    public function __construct()
    {
        $this->defaultProvider = config('dom_translate.api.bing');
    }

    /**
     * Initiate a translate API request via Azure Cognitive Translator v3.
     *
     * @param string|null $phrase   The phrase to translate.
     * @param string|null $langdest Destination language code (ISO-639-1).
     * @param string|null $langsrc  Source language code (ISO-639-1).
     * @throws Exception
     */
    public function cloudTranslate(?string $phrase = null, ?string $langdest = null, ?string $langsrc = null): string
    {
        LogController::log('notice', 2, '[Bing] Translate API request initiated.');

        $client = new Client(['http_errors' => false]);

        $response = $client->request(
            $this->defaultProvider['action'],
            $this->defaultProvider['endpoint'],
            [
                'query' => [
                    'api-version' => '3.0',
                    'from'        => $langsrc,
                    'to'          => $langdest,
                ],
                'headers' => [
                    'Ocp-Apim-Subscription-Key' => $this->defaultProvider['key'],
                    'Content-Type'              => 'application/json',
                    'Accept'                    => 'application/json',
                ],
                'json' => [
                    ['Text' => $phrase],
                ],
            ]
        );

        if ($response->getStatusCode() !== 200) {
            throw new Exception('[Bing] API error: ' . ($response->getReasonPhrase() ?: 'Unknown error'));
        }

        $responseBody = json_decode((string) $response->getBody(), true);
        LogController::log('notice', 3, '[Bing] API response:', $responseBody ?? []);

        if (!isset($responseBody[0]['translations'][0]['text'])) {
            throw new Exception('No translation returned from the Bing/Azure API.');
        }

        LogController::log('notice', 1, '[Bing] Translation located via API.');

        return $responseBody[0]['translations'][0]['text'];
    }
}
