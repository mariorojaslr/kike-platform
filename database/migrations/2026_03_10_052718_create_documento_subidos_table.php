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
        Schema::create('documento_subidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->nullable()->constrained('empresas')->onDelete('cascade');
            $table->foreignId('tipo_documento_id')->constrained('tipo_documentos')->onDelete('cascade');
            
            $table->enum('entidad_tipo', ['docente', 'alumno', 'factura', 'general']);
            $table->foreignId('entidad_id')->nullable()->comment('ID de Docente o Familiar');
            
            $table->string('ruta_archivo');
            
            // Verificación / Auditoría
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado', 'observado'])->default('pendiente');
            $table->text('comentarios_auditor')->nullable();
            
            // Vigencia
            $table->date('fecha_vencimiento')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documento_subidos');
    }
};
