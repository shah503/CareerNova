<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('leaderboards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('total_tests')->default(0);
            $table->integer('total_mcqs_answered')->default(0);
            $table->integer('total_correct_answers')->default(0);
            $table->decimal('overall_percentage', 5, 2)->default(0);
            $table->integer('overall_rank')->nullable();
            $table->decimal('weekly_score', 8, 2)->default(0);
            $table->decimal('monthly_score', 8, 2)->default(0);
            $table->integer('weekly_rank')->nullable();
            $table->integer('monthly_rank')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('overall_rank');
            $table->index('weekly_rank');
            $table->index('monthly_rank');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leaderboards');
    }
};