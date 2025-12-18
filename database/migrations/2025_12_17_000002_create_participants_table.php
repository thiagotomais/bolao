<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 20);
            $table->string('hash1', 128);
            $table->string('hash2', 128);
            $table->integer('year');
            $table->timestamps();

            // Índices e unicidade por ano
            $table->unique(['phone', 'year']);
            $table->unique(['hash1', 'hash2', 'year']);
            $table->index('year');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
