<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('boost_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('listing_id')->constrained('listings')->cascadeOnDelete();
            $table->string('invoice_code', 60)->unique();
            $table->decimal('amount', 12, 2)->default(5000.00);
            $table->string('payment_channel', 50);
            $table->string('proof_image', 255);
            $table->string('status', 30)->default('pending'); // 'pending' | 'verified' | 'rejected'
            $table->text('rejection_note')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('boost_payments');
    }
};
