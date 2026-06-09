<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('leaderboards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('points')->default(0);
            $table->integer('rank')->default(0);
            $table->integer('weekly_score')->default(0);
            $table->integer('monthly_score')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->unique('user_id');
            $table->index('rank');
            $table->index('points');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leaderboards');
    }
};