<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('expedientes_alumno')) {
            Schema::create('expedientes_alumno', function (Blueprint $table) {
                $table->id();
                $table->foreignId('docente_id')->constrained('docentes')->onDelete('cascade');
                $table->foreignId('empresa_id')->nullable()->constrained('empresas')->onDelete('cascade');
                $table->foreignId('titular_id')->nullable()->constrained('titulars')->onDelete('set null');
                $table->foreignId('alumno_id')->nullable()->constrained('familiars')->onDelete('set null');
                $table->foreignId('escuela_id')->nullable()->constrained('escuelas')->onDelete('set null');
                
                $table->string('origen_resolucion')->default('externa_papel'); // externa_papel, interna_sistema
                $table->string('nro_resolucion')->nullable();
                $table->string('resolucion_url')->nullable();
                $table->json('resolucion_datos_ia')->nullable();
                
                $table->string('certificado_medico_url')->nullable();
                $table->text('diagnostico')->nullable();
                $table->string('horarios_atencion')->nullable();
                $table->integer('horas_mensuales_asignadas')->default(3); // Límite por defecto (3hs/mes)
                
                $table->string('estado_auditoria')->default('pendiente'); // pendiente, aprobado, rechazado
                $table->text('motivo_rechazo')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('expedientes_alumno');
    }
};
