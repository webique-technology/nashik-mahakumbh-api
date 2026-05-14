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
        Schema::create('hotels', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->longText('description');
                $table->decimal('rating', 2, 1)->nullable();
                $table->string('category');
                $table->string('location');
                $table->json('features')->nullable();
                $table->string('meals')->nullable();
                $table->decimal('base_price', 10, 2);
                $table->decimal('offer_price', 10, 2)->nullable();
                $table->json('images')->nullable();
                $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
