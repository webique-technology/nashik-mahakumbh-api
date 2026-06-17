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
        Schema::create('itinerary_translations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('itinerary_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('language_code', 10);

            $table->string('itinerary_title');

            $table->longText('description')->nullable();

            $table->timestamps();

            $table->unique([
                'itinerary_id',
                'language_code'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itinerary_translations');
    }
};
