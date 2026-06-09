<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('revenue_analytics', function (Blueprint $table) {
            $table->id();
            $table->decimal('daily_income', 12, 2)->default(0);
            $table->decimal('monthly_income', 12, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('revenue_analytics');
    }
};