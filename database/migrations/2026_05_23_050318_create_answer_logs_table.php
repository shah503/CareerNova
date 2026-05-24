<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('answer_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('mcq_id')->constrained()->onDelete('cascade');
            $table->enum('selected_answer', ['A', 'B', 'C', 'D'])->nullable();
            $table->enum('correct_answer', ['A', 'B', 'C', 'D']);
            $table->boolean('is_correct')->default(false);
            $table->integer('time_taken_seconds')->default(0);
            $table->integer('question_order')->default(0); // To track randomization
            $table->timestamps();
            
            // Indexes
            $table->index('exam_session_id');
            $table->index('user_id');
            $table->index('mcq_id');
            $table->index(['exam_session_id', 'mcq_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('answer_logs');
    }
};