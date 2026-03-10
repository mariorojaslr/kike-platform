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
        Schema::create('tipo_documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->nullable()->constrained('empresas')->onDelete('cascade');
            $table->enum('entidad_tipo', ['docente', 'alumno', 'factura', 'general'])->default('docente');
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->boolean('es_obligatorio')->default(true);
            $table->integer('vencimiento_dias')->nullable()->comment('Días de vigencia, ej: 180 para 6 meses. Null si no vence.');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipo_documentos');
    }
};
