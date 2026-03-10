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
        $tablas = ['titulares', 'titulars', 'familiares', 'familiars', 'docentes', 'diagnosticos', 'formaciones'];

        foreach ($tablas as $tabla) {
            if (Schema::hasTable($tabla) && !Schema::hasColumn($tabla, 'empresa_id')) {
                Schema::table($tabla, function (Blueprint $table) {
                    $table->unsignedBigInteger('empresa_id')->nullable()->after('id');
                    
                    // Añadimos la relación foránea
                    $table->foreign('empresa_id')
                          ->references('id')
                          ->on('empresas')
                          ->onDelete('cascade'); // Si la empresa muere, mueren sus registros
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablas = ['titulares', 'titulars', 'familiares', 'familiars', 'docentes', 'diagnosticos', 'formaciones'];

        foreach ($tablas as $tabla) {
            if (Schema::hasTable($tabla) && Schema::hasColumn($tabla, 'empresa_id')) {
                Schema::table($tabla, function (Blueprint $table) {
                    $table->dropForeign(['empresa_id']);
                    $table->dropColumn('empresa_id');
                });
            }
        }
    }
};
