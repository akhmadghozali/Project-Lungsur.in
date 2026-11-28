
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('ban_reason')->nullable();
            $table->timestamp('banned_at')->nullable();
            $table->timestamp('ban_expires_at')->nullable();

            $table->foreignId('banned_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['banned_by']);

            $table->dropColumn([
                'ban_reason',
                'banned_at',
                'ban_expires_at',
                'banned_by',
            ]);
        });
    }
};
