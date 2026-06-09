<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mcqs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->longText('question');
            $table->text('option_a');
            $table->text('option_b');
            $table->text('option_c');
            $table->text('option_d');
            $table->enum('correct_answer', ['A', 'B', 'C', 'D']);
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->longText('explanation')->nullable();
            $table->string('source')->nullable();
            $table->enum('status', ['active', 'inactive', 'pending_review'])->default('pending_review');
            $table->boolean('verified')->default(false);
            $table->boolean('needs_review')->default(true);
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->string('question_type')->default('mcq');
            $table->timestamps();
            $table->softDeletes();

            $table->index('subject_id');
            $table->index('created_by');
            $table->index('status');
            $table->index('verified');
            $table->fullText('question');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mcqs');
    }
};