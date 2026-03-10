<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sesiones_terapias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Profesional
            $table->foreignId('afiliado_id')->constrained('afiliados')->onDelete('cascade'); // Paciente
            $table->date('fecha_sesion');
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();
            $table->string('firma_padre_url')->nullable(); // Firma digital (Local -> Bunny)
            $table->text('observaciones')->nullable();
            $table->enum('estado_auditoria', ['pendiente', 'validada', 'observada'])->default('pendiente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sesiones_terapias');
    }
};
