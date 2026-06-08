<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotel_enquiries', function (Blueprint $table) {

            $table->unsignedBigInteger('hotel_id')->after('id');

            // Optional Foreign Key
            $table->foreign('hotel_id')
                ->references('id')
                ->on('hotels')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('hotel_enquiries', function (Blueprint $table) {

            $table->dropForeign(['hotel_id']);
            $table->dropColumn('hotel_id');
        });
    }
};