<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('game_prices', function (Blueprint $table) {
            $table->id();
            $table->integer('game_size'); // Ex: 6 a 15
            $table->decimal('price', 10, 2); // Valor do jogo
            $table->integer('year'); // Para reutilização anual
            $table->timestamps(); // created_at, updated_at
            $table->unique(['game_size', 'year']);
            $table->index('year');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_prices');
    }
};
