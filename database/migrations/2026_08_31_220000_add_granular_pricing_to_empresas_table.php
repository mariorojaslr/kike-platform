<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            if (!Schema::hasColumn('empresas', 'monto_por_titular')) {
                $table->decimal('monto_por_titular', 10, 2)->default(0.00)->after('monto_cuota_mensual');
            }
            if (!Schema::hasColumn('empresas', 'monto_por_alumno')) {
                $table->decimal('monto_por_alumno', 10, 2)->default(0.00)->after('monto_por_titular');
            }
            if (!Schema::hasColumn('empresas', 'monto_por_docente')) {
                $table->decimal('monto_por_docente', 10, 2)->default(0.00)->after('monto_por_alumno');
            }
        });
    }

    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn(['monto_por_titular', 'monto_por_alumno', 'monto_por_docente']);
        });
    }
};
