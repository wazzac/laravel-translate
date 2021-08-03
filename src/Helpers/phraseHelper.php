<?php

namespace Wazza\DomTranslate\Helpers;

class phraseHelper
{
    /**
     * Sanitise a given Phrase, removing any single of double quotes.
     *
     * @param string $string
     * @return string
     */
    public static function sanitise($string)
    {
        return trim($string, '\'"');
    }

    /**
     * Split a given string, generally as it comes form the Blade drective, and split it into an array.
     * Important: When splitting the string by commas, make sure commas inside the enclosed quotes are ignored.
     *
     * @param string $string The string to be split into array. format: "phrase", "dest lang", "src lang"
     * @return array [0] Phrase, [1] Destiation Language, [2] Source Language
     * @todo a better method might be found
     */
    public static function splitTranslateArgs(string $string): array
    {
        return str_getcsv($string, ",", substr($string, 0, 1) == '"' ? "\"" : "'");
    }

    /**
     * Hash a given Phrase
     *
     * @param string $string
     * @return string
     */
    public static function hash(string $string)
    {
        return hash_hmac(
            config('dom_translate.hash.algo'),
            $string,
            config('dom_translate.hash.salt')
        );
    }
}
