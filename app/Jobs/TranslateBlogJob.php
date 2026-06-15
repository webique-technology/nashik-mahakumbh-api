<?php

namespace App\Jobs;

use App\Models\Blog;
use App\Models\BlogTranslation;

use App\Services\TranslationService;
use App\Services\HtmlTranslationService;

use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Foundation\Queue\Queueable;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class TranslateBlogJob implements ShouldQueue
{
    // use Queueable;
      use Dispatchable,
        InteractsWithQueue,
        Queueable,
        SerializesModels;

    public $blog;

    public function __construct(Blog $blog)
    {
        $this->blog = $blog;
    }

    public function handle(): void
    {

        \Log::info(
            'TranslateBlogJob Started',
            ['blog_id' => $this->blog->id]
        );

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

            try {

                BlogTranslation::updateOrCreate(
                    [
                        'blog_id' => $this->blog->id,
                        'language_code' => $lang
                    ],
                    [
                        'title' => TranslationService::translate(
                            $this->blog->title,
                            $lang
                        ),

                        'description' => HtmlTranslationService::translateHtml(
                            $this->blog->description,
                            $lang
                        )
                    ]
                );

            } catch (\Exception $e) {

                \Log::error(
                    'Translation Failed',
                    [
                        'blog_id' => $this->blog->id,
                        'lang' => $lang,
                        'error' => $e->getMessage()
                    ]
                );
            }
        }

        // foreach ($languages as $lang) {

        //     BlogTranslation::updateOrCreate(

        //         [
        //             'blog_id' => $this->blog->id,
        //             'language_code' => $lang
        //         ],

        //         [
        //             'title' =>
        //                 TranslationService::translate(
        //                     $this->blog->title,
        //                     $lang
        //                 ),

        //             'description' =>
        //                 HtmlTranslationService::translateHtml(
        //                     $this->blog->description,
        //                     $lang
        //                 )
        //         ]
        //     );
        // }
    }
}