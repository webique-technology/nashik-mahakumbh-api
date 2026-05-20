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
        Schema::create('tour_enquiries', function (Blueprint $table) {
            $table->id();

            $table->string('full_name');
            $table->string('email');
            $table->string('phone_number');

            $table->integer('number_of_travelers');

            $table->string('preferred_dates')->nullable();

            $table->unsignedBigInteger('tour_id');

            $table->longText('special_requirements')->nullable();

            $table->timestamps();

            // foreign key
            $table->foreign('tour_id')
                  ->references('id')
                  ->on('tours')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_enquiries');
    }
};
