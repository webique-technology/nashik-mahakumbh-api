<?php

namespace App\Services;

use DOMDocument;
use DOMXPath;

class HtmlTranslationService
{
    public static function translateHtml($html, $lang)
    {
        libxml_use_internal_errors(true);

        $dom = new DOMDocument();

        $dom->loadHTML(
            mb_convert_encoding(
                $html,
                'HTML-ENTITIES',
                'UTF-8'
            ),
            LIBXML_HTML_NOIMPLIED |
            LIBXML_HTML_NODEFDTD
        );

        $xpath = new DOMXPath($dom);

        $textNodes = $xpath->query('//text()');

        foreach ($textNodes as $node) {

            $text = trim($node->nodeValue);

            if (!empty($text)) {

                $node->nodeValue =
                    TranslationService::translate(
                        $text,
                        $lang
                    );
            }
        }

        return $dom->saveHTML();
    }
}