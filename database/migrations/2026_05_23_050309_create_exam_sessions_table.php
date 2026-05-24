<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('exam_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->integer('total_questions')->default(0);
            $table->integer('correct_answers')->default(0);
            $table->integer('wrong_answers')->default(0);
            $table->integer('unanswered')->default(0);
            $table->decimal('percentage', 5, 2)->default(0);
            $table->integer('score')->default(0);
            $table->integer('duration_minutes')->default(0); // Allocated time
            $table->integer('time_taken_minutes')->default(0); // Time actually taken
            $table->timestamp('started_at');
            $table->timestamp('finished_at')->nullable();
            $table->enum('status', ['ongoing', 'completed', 'expired'])->default('ongoing');
            $table->boolean('is_locked')->default(false); // Session locked after start
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('subject_id');
            $table->index('status');
            $table->index(['user_id', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_sessions');
    }
};