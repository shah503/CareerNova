<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_sessions', function (Blueprint $table) {
            // Add missing columns
            if (!Schema::hasColumn('exam_sessions', 'wrong_answers')) {
                $table->integer('wrong_answers')->default(0)->after('correct_answers');
            }
            
            if (!Schema::hasColumn('exam_sessions', 'unanswered_count')) {
                $table->integer('unanswered_count')->default(0)->after('wrong_answers');
            }
            
            if (!Schema::hasColumn('exam_sessions', 'is_submitted')) {
                $table->boolean('is_submitted')->default(false)->after('status');
            }
            
            if (!Schema::hasColumn('exam_sessions', 'last_saved_at')) {
                $table->timestamp('last_saved_at')->nullable()->after('completed_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('exam_sessions', function (Blueprint $table) {
            if (Schema::hasColumn('exam_sessions', 'wrong_answers')) {
                $table->dropColumn('wrong_answers');
            }
            if (Schema::hasColumn('exam_sessions', 'unanswered_count')) {
                $table->dropColumn('unanswered_count');
            }
            if (Schema::hasColumn('exam_sessions', 'is_submitted')) {
                $table->dropColumn('is_submitted');
            }
            if (Schema::hasColumn('exam_sessions', 'last_saved_at')) {
                $table->dropColumn('last_saved_at');
            }
        });
    }
};