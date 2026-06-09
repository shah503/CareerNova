<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add columns to exam_sessions for new features
        Schema::table('exam_sessions', function (Blueprint $table) {
            if (!Schema::hasColumn('exam_sessions', 'marked_for_review_count')) {
                $table->integer('marked_for_review_count')->default(0)->after('wrong_answers');
            }
            if (!Schema::hasColumn('exam_sessions', 'unanswered_count')) {
                $table->integer('unanswered_count')->default(0)->after('marked_for_review_count');
            }
            if (!Schema::hasColumn('exam_sessions', 'last_saved_at')) {
                $table->timestamp('last_saved_at')->nullable()->after('unanswered_count');
            }
            if (!Schema::hasColumn('exam_sessions', 'is_submitted')) {
                $table->boolean('is_submitted')->default(false)->after('last_saved_at');
            }
        });

        // Add columns to answer_logs for marking/review
        Schema::table('answer_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('answer_logs', 'marked_for_review')) {
                $table->boolean('marked_for_review')->default(false)->after('is_correct');
            }
            if (!Schema::hasColumn('answer_logs', 'visited')) {
                $table->boolean('visited')->default(false)->after('marked_for_review');
            }
        });

        // Create question_progress table for session recovery
        if (!Schema::hasTable('question_progress')) {
            Schema::create('question_progress', function (Blueprint $table) {
                $table->id();
                $table->foreignId('exam_session_id')->constrained('exam_sessions')->onDelete('cascade');
                $table->integer('question_number');
                $table->string('status')->default('not_visited'); // not_visited, visited, answered, marked, answered_marked
                $table->string('selected_answer')->nullable();
                $table->timestamp('last_visited_at')->nullable();
                $table->timestamps();

                $table->unique(['exam_session_id', 'question_number']);
                $table->index(['exam_session_id', 'status']);
            });
        }

        // Create exam_analytics table
        if (!Schema::hasTable('exam_analytics')) {
            Schema::create('exam_analytics', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
                $table->integer('total_tests')->default(0);
                $table->decimal('average_score', 5, 2)->default(0);
                $table->integer('tests_passed')->default(0);
                $table->integer('tests_failed')->default(0);
                $table->integer('total_questions_attempted')->default(0);
                $table->integer('total_correct_answers')->default(0);
                $table->integer('current_rank')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->unique(['user_id', 'subject_id']);
                $table->index(['current_rank']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_analytics');
        Schema::dropIfExists('question_progress');
        
        Schema::table('answer_logs', function (Blueprint $table) {
            $table->dropColumn(['marked_for_review', 'visited']);
        });

        Schema::table('exam_sessions', function (Blueprint $table) {
            $table->dropColumn([
                'marked_for_review_count',
                'unanswered_count',
                'last_saved_at',
                'is_submitted'
            ]);
        });
    }
};