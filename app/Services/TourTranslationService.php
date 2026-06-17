<?php

namespace App\Services;

use App\Models\Tour;
use App\Models\TourTranslation;
use App\Models\ItineraryTranslation;
use App\Models\SeoMetaTranslation;

class TourTranslationService
{
    public function translate(
        Tour $tour,
        string $lang
    )
    {
        $translatedHighlights = [];

        foreach (
            ($tour->highlights ?? [])
            as $item
        ) {
            $translatedHighlights[] =
                TranslationService::translate(
                    $item,
                    $lang
                );
        }

        $translatedInclusions = [];

        foreach (
            ($tour->inclusions ?? [])
            as $item
        ) {

            if (is_array($item)) {

                $translatedInclusions[] = [
                    'label' =>
                        TranslationService::translate(
                            $item['label'] ?? '',
                            $lang
                        ),
                    'in_icon' =>
                        $item['in_icon'] ?? null
                ];

            } else {

                $translatedInclusions[] =
                    TranslationService::translate(
                        $item,
                        $lang
                    );
            }
        }

        $translatedRoutes = [];

        foreach (
            ($tour->routes ?? [])
            as $route
        ) {
            $translatedRoutes[] =
                TranslationService::translate(
                    $route,
                    $lang
                );
        }

        TourTranslation::updateOrCreate(
            [
                'tour_id' => $tour->id,
                'language_code' => $lang
            ],
            [
                'title' =>
                    TranslationService::translate(
                        $tour->title,
                        $lang
                    ),

                'description' =>
                    HtmlTranslationService::translateHtml(
                        $tour->description,
                        $lang
                    ),

                'highlights' =>
                    $translatedHighlights,

                'inclusions' =>
                    $translatedInclusions,

                'routes' =>
                    $translatedRoutes
            ]
        );

        foreach (
            $tour->itineraries as $itinerary
        ) {

            ItineraryTranslation::updateOrCreate(
                [
                    'itinerary_id' =>
                        $itinerary->id,

                    'language_code' =>
                        $lang
                ],
                [
                    'itinerary_title' =>
                        TranslationService::translate(
                            $itinerary->itinerary_title,
                            $lang
                        ),

                    'description' =>
                        HtmlTranslationService::translateHtml(
                            $itinerary->description,
                            $lang
                        )
                ]
            );
        }

        if ($tour->seoMeta) {

            SeoMetaTranslation::updateOrCreate(
                [
                    'seo_meta_id' => $tour->seoMeta->id,
                    'language_code' => $lang
                ],
                [
                    'title' => TranslationService::translate(
                        $tour->seoMeta->title,
                        $lang
                    ),

                    'desc' => HtmlTranslationService::translateHtml(
                        $tour->seoMeta->desc,
                        $lang
                    )
                ]
            );
        }
    }
}