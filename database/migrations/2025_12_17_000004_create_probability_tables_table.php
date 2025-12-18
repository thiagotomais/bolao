<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
       Schema::create('probability_tables', function (Blueprint $table) {
        $table->id();
        $table->integer('game_size'); // 6 a 15
        $table->bigInteger('sena_odds');   // Ex: 50063860
        $table->bigInteger('quina_odds');  // Ex: 154518
        $table->bigInteger('quadra_odds'); // Ex: 2332
        $table->integer('year');
        $table->timestamps();
        $table->unique(['game_size', 'year']);
        $table->index('year');
    });

    }

    public function down(): void
    {
        Schema::dropIfExists('probability_tables');
    }
};
