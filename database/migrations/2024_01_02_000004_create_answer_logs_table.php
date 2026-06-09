<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('answer_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_session_id')->constrained('exam_sessions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('mcq_id')->constrained('mcqs')->onDelete('cascade');
            $table->string('selected_answer')->nullable();
            $table->string('correct_answer');
            $table->boolean('is_correct')->default(false);
            $table->integer('time_taken')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('exam_session_id');
            $table->index('user_id');
            $table->index('mcq_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('answer_logs');
    }
};