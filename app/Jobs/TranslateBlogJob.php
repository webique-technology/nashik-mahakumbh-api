<?php

namespace App\Jobs;

use App\Models\Blog;
use App\Models\BlogTranslation;

use App\Services\TranslationService;
use App\Services\HtmlTranslationService;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class TranslateBlogJob implements ShouldQueue
{
    use Queueable;

    public $blog;

    public function __construct(Blog $blog)
    {
        $this->blog = $blog;
    }

    public function handle(): void
    {
        $languages = [
            'hi',
            'mr',
            'ta',
            'te',
            'gu',
            'ml',
            'sa'
        ];

        foreach ($languages as $lang) {

            BlogTranslation::updateOrCreate(

                [
                    'blog_id' => $this->blog->id,
                    'language_code' => $lang
                ],

                [
                    'title' =>
                        TranslationService::translate(
                            $this->blog->title,
                            $lang
                        ),

                    'description' =>
                        HtmlTranslationService::translateHtml(
                            $this->blog->description,
                            $lang
                        )
                ]
            );
        }
    }
}