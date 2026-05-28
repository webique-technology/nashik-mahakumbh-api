<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // add column only if missing
        Schema::table('vehicles', function (Blueprint $table) {
            if (!Schema::hasColumn('vehicles', 'category_id')) {
                $table->unsignedBigInteger('category_id')
                    ->nullable()
                    ->after('features');
            }
        });

        /*
         |---------------------------------------
         | Move old category values if column exists
         |---------------------------------------
         */
        if (Schema::hasColumn('vehicles', 'category')) {

            $vehicles = DB::table('vehicles')->get();

            foreach ($vehicles as $vehicle) {

                if (!empty($vehicle->category)) {

                    $existingCategory = DB::table('vehicle_categories')
                        ->where('category', $vehicle->category)
                        ->first();

                    if (!$existingCategory) {

                        $categoryId = DB::table('vehicle_categories')
                            ->insertGetId([
                                'category'   => $vehicle->category,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);

                    } else {
                        $categoryId = $existingCategory->id;
                    }

                    DB::table('vehicles')
                        ->where('id', $vehicle->id)
                        ->update([
                            'category_id' => $categoryId
                        ]);
                }
            }

            // remove old column
            Schema::table('vehicles', function (Blueprint $table) {
                $table->dropColumn('category');
            });
        }

        /*
         |---------------------------------------
         | Add foreign key if missing
         |---------------------------------------
         */
        Schema::table('vehicles', function (Blueprint $table) {

            try {
                $table->foreign('category_id')
                    ->references('id')
                    ->on('vehicle_categories')
                    ->onDelete('cascade');

            } catch (\Exception $e) {
                // already exists
            }
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {

            if (!Schema::hasColumn('vehicles', 'category')) {
                $table->string('category')->nullable();
            }

            try {
                $table->dropForeign(['category_id']);
            } catch (\Exception $e) {
            }

            if (Schema::hasColumn('vehicles', 'category_id')) {
                $table->dropColumn('category_id');
            }
        });
    }
};