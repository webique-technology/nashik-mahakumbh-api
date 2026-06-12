<?php

namespace App\Services;

use Stichoza\GoogleTranslate\GoogleTranslate;

class TranslationService
{
    public static function translate($text, $lang)
    {
        try {

            $tr = new GoogleTranslate($lang);

            return $tr->translate($text);

        } catch (\Exception $e) {

            return $text;
        }
    }
}