<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payment_account_info', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value');
            $table->timestamps();
            $table->softDeletes();

            $table->index('key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_account_info');
    }
};