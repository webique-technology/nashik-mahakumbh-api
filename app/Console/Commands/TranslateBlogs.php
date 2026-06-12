<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Blog;
use App\Models\BlogTranslation;
use Stichoza\GoogleTranslate\GoogleTranslate;

class TranslateBlogs extends Command
{
    /**
     * The name and signature of the console command.
     */
    // protected $signature = 'app:translate-blogs';

    /**
     * The console command description.
     */
    
    protected $signature = 'translate:blogs';

    protected $description = 'Translate all blogs';

    public function handle()
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

        $blogs = Blog::all();

        foreach ($blogs as $blog) {

            foreach ($languages as $lang) {

                $exists = BlogTranslation::where(
                    'blog_id',
                    $blog->id
                )->where(
                    'language_code',
                    $lang
                )->exists();

                if ($exists) {
                    continue;
                }

                BlogTranslation::create([
                    'blog_id' => $blog->id,
                    'language_code' => $lang,
                    'title' => GoogleTranslate::trans(
                        $blog->title,
                        $lang
                    ),
                    'description' => GoogleTranslate::trans(
                        strip_tags($blog->description),
                        $lang
                    )
                ]);

                $this->info(
                    "Translated Blog {$blog->id} -> {$lang}"
                );
            }
        }
    }
}
