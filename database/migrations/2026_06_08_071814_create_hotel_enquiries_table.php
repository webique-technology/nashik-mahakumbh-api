<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotel_enquiries', function (Blueprint $table) {

            $table->id();

            $table->string('full_name');
            $table->string('email');
            $table->string('mobile_number');

            $table->string('room_type');

            $table->date('check_in_date');
            $table->date('check_out_date');

            $table->integer('adults')->default(1);
            $table->integer('children')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_enquiries');
    }
};