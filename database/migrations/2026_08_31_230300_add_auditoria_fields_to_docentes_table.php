<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('docentes', function (Blueprint $table) {
            if (!Schema::hasColumn('docentes', 'estado_legajo')) {
                $table->string('estado_legajo')->default('incompleto')->after('activo'); // al_dia, incompleto, suspendido
            }
            if (!Schema::hasColumn('docentes', 'porcentaje_legajo')) {
                $table->integer('porcentaje_legajo')->default(0)->after('estado_legajo');
            }
        });

        Schema::table('docente_documentos', function (Blueprint $table) {
            if (!Schema::hasColumn('docente_documentos', 'es_frente_dorso')) {
                $table->boolean('es_frente_dorso')->default(false)->after('ruta_archivo');
            }
            if (!Schema::hasColumn('docente_documentos', 'dorso_url')) {
                $table->string('dorso_url')->nullable()->after('es_frente_dorso');
            }
            if (!Schema::hasColumn('docente_documentos', 'tipo_archivo')) {
                $table->string('tipo_archivo')->nullable()->after('dorso_url'); // image, pdf, excel, word
            }
            if (!Schema::hasColumn('docente_documentos', 'estado_auditoria')) {
                $table->string('estado_auditoria')->default('pendiente')->after('tipo_archivo');
            }
            if (!Schema::hasColumn('docente_documentos', 'motivo_rechazo')) {
                $table->text('motivo_rechazo')->nullable()->after('estado_auditoria');
            }
        });
    }

    public function down(): void
    {
        Schema::table('docentes', function (Blueprint $table) {
            $table->dropColumn(['estado_legajo', 'porcentaje_legajo']);
        });

        Schema::table('docente_documentos', function (Blueprint $table) {
            $table->dropColumn(['es_frente_dorso', 'dorso_url', 'tipo_archivo', 'estado_auditoria', 'motivo_rechazo']);
        });
    }
};
