<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')
                ->constrained('games')
                ->onDelete('cascade');
            $table->string('file_path'); // caminho do PDF/imagem
            $table->string('file_type'); // pdf, jpg, png
            $table->integer('year');
            $table->timestamps();
            $table->index(['game_id', 'year']);
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
