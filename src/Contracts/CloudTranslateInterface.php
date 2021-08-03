<?php

namespace Wazza\DomTranslate\Contracts;

interface CloudTranslateInterface
{
    /**
     * Method that will initiate a translate API request from a given Provider
     *
     * @param string|null $phrase The phrase to be translated
     * @param string|null $langdest The destination language code in ISO-639-1 - i.e. fr (defaults would be retrieved from the config file)
     * @param string|null $langsrc The source language code in ISO-639-1 - i.e. en (defaults would be retrieved from the config file)
     * @return string Translated string
     * @throws Exception
     */
    public function cloudTranslate(?string $phrase = null, ?string $langdest = null, ?string $langsrc = null);
}
