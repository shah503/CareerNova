<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mcqs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('created_by')->nullable(); // Change this line
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
            $table->timestamps();
            
            // Indexes
            $table->index('subject_id');
            $table->index('status');
            $table->index('difficulty');
            $table->index('created_by');
            
            // Foreign key constraint - add after table creation
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mcqs');
    }
};