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
        Schema::create('tour_translations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tour_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('language_code', 10);

            $table->string('title');

            $table->longText('description')->nullable();

            $table->json('highlights')->nullable();

            $table->json('inclusions')->nullable();

            $table->json('routes')->nullable();

            $table->timestamps();

            $table->unique([
                'tour_id',
                'language_code'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_translations');
    }
};
