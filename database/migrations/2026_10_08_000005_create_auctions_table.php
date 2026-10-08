<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('auctions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->unique()->constrained('listings')->cascadeOnDelete();
            $table->decimal('starting_price', 12, 2);
            $table->decimal('bid_increment', 12, 2);
            $table->decimal('bid_cap', 12, 2);
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->string('status', 30)->default('pending_payment'); // 'pending_payment' | 'pending_approval' | 'active' | 'closed' | 'canceled'
            $table->foreignId('winner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        DB::statement('ALTER TABLE auctions ADD CONSTRAINT check_auction_starting_price_modulo CHECK (starting_price >= 1000 AND (starting_price % 1000 = 0));');
        DB::statement('ALTER TABLE auctions ADD CONSTRAINT check_auction_bid_increment_modulo CHECK (bid_increment >= 1000 AND (bid_increment % 1000 = 0));');
    }

    public function down(): void {
        DB::statement('ALTER TABLE auctions DROP CONSTRAINT IF EXISTS check_auction_starting_price_modulo;');
        DB::statement('ALTER TABLE auctions DROP CONSTRAINT IF EXISTS check_auction_bid_increment_modulo;');
        Schema::dropIfExists('auctions');
    }
};
