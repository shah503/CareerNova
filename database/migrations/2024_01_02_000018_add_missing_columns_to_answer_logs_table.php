<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('answer_logs', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('answer_logs', 'selected_answer')) {
                $table->char('selected_answer', 1)->nullable()->after('answer');
            }
            
            if (!Schema::hasColumn('answer_logs', 'correct_answer')) {
                $table->char('correct_answer', 1)->nullable()->after('selected_answer');
            }
            
            if (!Schema::hasColumn('answer_logs', 'is_correct')) {
                $table->boolean('is_correct')->default(false)->after('correct_answer');
            }
        });
    }

    public function down(): void
    {
        Schema::table('answer_logs', function (Blueprint $table) {
            if (Schema::hasColumn('answer_logs', 'selected_answer')) {
                $table->dropColumn('selected_answer');
            }
            if (Schema::hasColumn('answer_logs', 'correct_answer')) {
                $table->dropColumn('correct_answer');
            }
            if (Schema::hasColumn('answer_logs', 'is_correct')) {
                $table->dropColumn('is_correct');
            }
        });
    }
};