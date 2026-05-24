<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ai_mcq_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('chapter')->nullable();
            $table->string('topic');
            $table->string('sub_topic')->nullable();
            $table->integer('quantity_requested')->default(1);
            $table->integer('quantity_generated')->default(0);
            $table->longText('prompt')->nullable();
            $table->longText('response_json')->nullable();
            $table->enum('status', ['pending', 'generating', 'completed', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('status');
            $table->index('topic');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_mcq_logs');
    }
};