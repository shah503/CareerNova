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
        Schema::table('mcqs', function (Blueprint $table) {
            // Change difficulty to VARCHAR(50) if it's too small
            $table->string('difficulty', 50)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mcqs', function (Blueprint $table) {
            $table->string('difficulty', 20)->change();
        });
    }
};