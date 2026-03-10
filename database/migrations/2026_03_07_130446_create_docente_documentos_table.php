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
        Schema::create('docente_documentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('docente_id');
            $table->string('tipo_documento'); // Ej: "Certificado Buena Conducta", "Título"
            $table->string('ruta_archivo'); // Path físico al PDF/Imagen
            $table->date('fecha_vencimiento')->nullable(); // Para alertas
            
            // Estado de auditoría simple
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado', 'vencido'])->default('pendiente');
            
            $table->timestamps();

            // Relación
            $table->foreign('docente_id')->references('id')->on('docentes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('docente_documentos');
    }
};
