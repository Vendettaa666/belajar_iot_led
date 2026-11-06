<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('number');      // LED ke-1 sampai ke-4
            $table->string('name');                     // contoh: "Lampu Teras"
            $table->unsignedTinyInteger('gpio_pin');    // pin di ESP (2, 0, 4, 5)
            $table->boolean('target_on')->default(false); // perintah dari web
            $table->boolean('is_on')->default(false);     // status nyata yang dilaporkan ESP
            $table->timestamps();

            $table->unique(['device_id', 'number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leds');
    }
};
