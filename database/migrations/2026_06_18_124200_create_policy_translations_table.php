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
        Schema::create('policy_translations', function (Blueprint $table) {
           
            $table->id();

            $table->foreignId('policy_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->string('language_code',10);

            $table->longText('content');

            $table->timestamps();

            $table->unique([
                'policy_id',
                'language_code'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('policy_translations');
    }
};
