<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('game_numbers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')
                ->constrained('games')
                ->onDelete('cascade');
            $table->integer('number'); // 1 a 60
            $table->integer('year');
            $table->timestamps();
            $table->unique(['game_id', 'number']);
            $table->index(['game_id', 'year']);
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('game_numbers');
    }
};
