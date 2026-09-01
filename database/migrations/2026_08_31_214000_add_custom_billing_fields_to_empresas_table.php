<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            if (!Schema::hasColumn('empresas', 'plan_tipo')) {
                $table->string('plan_tipo')->default('estandar')->after('estado_cuenta'); // estandar, demo, personalizado, bonificado
            }
            if (!Schema::hasColumn('empresas', 'monto_cuota_mensual')) {
                $table->decimal('monto_cuota_mensual', 10, 2)->default(50.00)->after('plan_tipo');
            }
            if (!Schema::hasColumn('empresas', 'periodo_gracia_hasta')) {
                $table->date('periodo_gracia_hasta')->nullable()->after('monto_cuota_mensual');
            }
            if (!Schema::hasColumn('empresas', 'notas_facturacion')) {
                $table->text('notas_facturacion')->nullable()->after('periodo_gracia_hasta');
            }
        });
    }

    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn(['plan_tipo', 'monto_cuota_mensual', 'periodo_gracia_hasta', 'notas_facturacion']);
        });
    }
};
