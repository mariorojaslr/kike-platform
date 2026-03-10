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
        Schema::table('empresas', function (Blueprint $table) {
            $table->date('proximo_vencimiento')->nullable()->after('estado_cuenta');
            $table->decimal('deuda_actual', 10, 2)->default(0)->after('proximo_vencimiento');
            $table->integer('meses_adeudados')->default(0)->after('deuda_actual');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn(['proximo_vencimiento', 'deuda_actual', 'meses_adeudados']);
        });
    }
};
