<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->integer('game_size'); // quantidade de números
            $table->decimal('total_value', 10, 2); // custo do jogo
            $table->string('status'); // simulated | confirmed
            $table->integer('year');
            $table->timestamps();
            $table->index(['game_size', 'year']);
            $table->index(['status', 'year']);
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
