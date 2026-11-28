
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->string('report_type', 20)->default('listing');

            $table->foreignId('conversation_id')
                ->nullable()
                ->constrained('conversations')
                ->nullOnDelete();

            $table->foreignId('reported_message_id')
                ->nullable()
                ->constrained('messages')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['conversation_id']);
            $table->dropForeign(['reported_message_id']);

            $table->dropColumn([
                'report_type',
                'conversation_id',
                'reported_message_id',
            ]);
        });
    }
};
