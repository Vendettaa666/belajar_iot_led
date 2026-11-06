<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('name');                    // contoh: "ESP8266 Kamar"
            $table->string('api_key', 64)->unique();   // kunci rahasia yang dipakai ESP
            $table->timestamp('last_seen_at')->nullable(); // terakhir ESP terhubung
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
