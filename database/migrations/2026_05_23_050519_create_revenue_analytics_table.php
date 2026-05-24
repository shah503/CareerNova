<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('revenue_analytics', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->decimal('daily_income', 10, 2)->default(0);
            $table->decimal('monthly_income', 10, 2)->default(0);
            $table->integer('total_transactions')->default(0);
            $table->integer('successful_transactions')->default(0);
            $table->integer('failed_transactions')->default(0);
            $table->timestamps();
            
            // Indexes
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('revenue_analytics');
    }
};