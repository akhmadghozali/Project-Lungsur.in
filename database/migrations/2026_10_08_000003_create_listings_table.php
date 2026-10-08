<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->string('title', 200);
            $table->text('description');
            $table->decimal('price', 12, 2);
            $table->json('condition_checklist')->nullable();
            $table->string('addresses', 255);
            $table->string('status', 30)->default('available'); // 'available' | 'booked' | 'sold' | 'archived' | 'lelang'
            $table->boolean('is_boosted')->default(false);
            $table->timestamp('boosted_until')->nullable();
            $table->unsignedInteger('view_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement('ALTER TABLE listings ADD CONSTRAINT check_listing_price_modulo CHECK (price >= 1000 AND (price % 1000 = 0));');
    }

    public function down(): void {
        DB::statement('ALTER TABLE listings DROP CONSTRAINT IF EXISTS check_listing_price_modulo;');
        Schema::dropIfExists('listings');
    }
};
