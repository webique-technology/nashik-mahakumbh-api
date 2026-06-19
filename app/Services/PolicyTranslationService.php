<?php

namespace App\Services;

use App\Models\Policy;
use App\Models\PolicyTranslation;

class PolicyTranslationService
{
    public function translate(
        Policy $policy,
        string $lang
    )
    {
        PolicyTranslation::updateOrCreate(
            [
                'policy_id' => $policy->id,
                'language_code' => $lang
            ],
            [
                'content' => HtmlTranslationService::translateHtml(
                    $policy->content,
                    $lang
                )
            ]
        );
    }
}