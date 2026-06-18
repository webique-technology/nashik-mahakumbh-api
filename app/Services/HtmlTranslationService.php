<?php

namespace App\Services;

use DOMDocument;
use DOMXPath;

class HtmlTranslationService
{
    public static function translateHtml($html, $lang)
    {
        libxml_use_internal_errors(true);

        $dom = new DOMDocument('1.0', 'UTF-8');

        $dom->loadHTML(
            '<?xml encoding="UTF-8">' . $html,
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

        $output = html_entity_decode(
            $dom->saveHTML(),
            ENT_QUOTES | ENT_HTML5,
            'UTF-8'
        );

        // Remove XML declaration
        $output = str_replace(
            '<?xml encoding="UTF-8">',
            '',
            $output
        );

        return trim($output);
    }
}
