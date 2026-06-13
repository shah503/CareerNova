<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_teacher', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->string('subject')->nullable(); // Which subject does the teacher teach this student
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamps();
            
            // Ensure no duplicates
            $table->unique(['student_id', 'teacher_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_teacher');
    }
};