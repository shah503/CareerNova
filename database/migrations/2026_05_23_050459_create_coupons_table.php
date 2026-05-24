<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->integer('discount_percent');
            $table->timestamp('expiry_date');
            $table->integer('usage_limit')->nullable();
            $table->integer('times_used')->default(0);
            $table->enum('status', ['active', 'inactive', 'expired'])->default('active');
            $table->timestamps();
            
            // Indexes
            $table->index('code');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};