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
        Schema::table('tours', function (Blueprint $table) {
            $table->json('vehicle_category_ids')
                ->nullable()
                ->after('category');

            $table->json('routes')
                ->nullable()
                ->after('vehicle_category_ids');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
              $table->dropColumn([
                'vehicle_category_ids',
                'routes'
            ]);
        });
    }
};
