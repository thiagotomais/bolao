<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('participations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_id')
                ->constrained('participants')
                ->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('unit_value', 10, 2); // ex: 50.00
            $table->decimal('total_value', 10, 2); // quantity * unit_value
            $table->boolean('is_admin')->default(false);
            $table->integer('year');
            $table->timestamps();
            $table->index(['participant_id', 'year']);
            $table->index(['is_admin', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('participations');
    }
};
