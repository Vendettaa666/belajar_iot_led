<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('led_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('led_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_on');                   // LED jadi nyala (1) atau mati (0)
            $table->string('source', 10);               // 'web' = perintah user, 'device' = laporan ESP
            $table->timestamp('created_at')->useCurrent();

            $table->index(['led_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('led_logs');
    }
};
