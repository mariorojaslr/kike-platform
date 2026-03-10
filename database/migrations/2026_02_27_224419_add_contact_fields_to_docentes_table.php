<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('docentes', function (Blueprint $blueprint) {
            // Solo las agregamos si no existen para evitar errores
            if (!Schema::hasColumn('docentes', 'telefono')) {
                $blueprint->string('telefono')->nullable();
            }
            if (!Schema::hasColumn('docentes', 'email')) {
                $blueprint->string('email')->nullable();
            }
            if (!Schema::hasColumn('docentes', 'direccion')) {
                $blueprint->string('direccion')->nullable();
            }
            if (!Schema::hasColumn('docentes', 'validado_auditoria')) {
                $blueprint->integer('validado_auditoria')->default(0);
            }
            if (!Schema::hasColumn('docentes', 'activo')) {
                $blueprint->integer('activo')->default(1);
            }
        });
    }

    public function down(): void
    {
        Schema::table('docentes', function (Blueprint $blueprint) {
            $blueprint->dropColumn(['telefono', 'email', 'direccion', 'validado_auditoria', 'activo']);
        });
    }
};
