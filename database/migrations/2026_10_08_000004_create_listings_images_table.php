<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('listings_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained('listings')->cascadeOnDelete();
            $table->string('image_url', 255);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('listings_images');
    }
};
