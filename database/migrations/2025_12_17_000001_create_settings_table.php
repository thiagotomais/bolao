<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
       Schema::create('settings', function (Blueprint $table) {
        $table->id();
        $table->string('key');
        $table->text('value');
        $table->string('type'); // string, int, decimal, bool, datetime
        $table->integer('year');
        $table->timestamps();
        $table->unique(['key', 'year']);
        $table->index('year');
    });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
