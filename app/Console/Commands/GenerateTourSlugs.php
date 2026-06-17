<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tour;
use Illuminate\Support\Str;

class GenerateTourSlugs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-tour-slugs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Tour::whereNull('slug')
        ->get()
        ->each(function ($tour) {

            $tour->slug =
                Str::slug($tour->title);

            $tour->save();
        });
    }
}
