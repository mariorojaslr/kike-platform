<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('titulars', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('dni')->unique();
            $table->string('n_afiliado')->nullable(); // Campo que faltaba
            $table->string('resolucion')->nullable(); // Campo que faltaba
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('titulars');
    }
};
