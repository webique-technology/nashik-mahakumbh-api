<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('seo_meta_translations', function (Blueprint $table) {
            
            $table->id();

            $table->foreignId('seo_meta_id')
                ->constrained('seo_meta')
                ->cascadeOnDelete();

            $table->string('language_code', 10);

            $table->string('title');

            $table->longText('desc')->nullable();

            $table->timestamps();

            $table->unique([
                'seo_meta_id',
                'language_code'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_meta_translations');
    }
};
