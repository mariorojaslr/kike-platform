<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta las migraciones para afiliados.
     */
    public function up(): void
    {
        Schema::create('afiliados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained()->onDelete('cascade');
            $table->foreignId('escuela_id')->nullable()->constrained()->onDelete('set null'); // Puede no tener escuela asignada aún
            $table->string('nombre');
            $table->string('apellido');
            $table->string('dni')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('diagnostico')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('afiliados');
    }
};
