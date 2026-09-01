<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            if (!Schema::hasColumn('empresas', 'modalidad_cobro')) {
                $table->string('modalidad_cobro')->default('cuota_fija')->after('plan_tipo'); // cuota_fija, por_afiliado, por_alumno, demo
            }
            if (!Schema::hasColumn('empresas', 'monto_por_afiliado')) {
                $table->decimal('monto_por_afiliado', 10, 2)->default(0.00)->after('monto_cuota_mensual');
            }
        });
    }

    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn(['modalidad_cobro', 'monto_por_afiliado']);
        });
    }
};
