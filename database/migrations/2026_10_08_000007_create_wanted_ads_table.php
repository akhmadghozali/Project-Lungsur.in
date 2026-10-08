<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('wanted_ads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->string('title', 200);
            $table->text('description');
            $table->decimal('budget_max', 12, 2);
            $table->string('addresses', 255);
            $table->string('status', 30)->default('active'); // 'active' | 'fulfilled' | 'canceled'
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('wanted_ads');
    }
};
