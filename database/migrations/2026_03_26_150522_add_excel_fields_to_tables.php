<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('familiares', function (Blueprint $table) {
            $table->string('n_afiliado')->nullable()->after('dni');
            $table->string('grado_division')->nullable();
            $table->string('turno')->nullable();
            $table->string('horario')->nullable();
        });

        Schema::table('docentes', function (Blueprint $table) {
            $table->string('resolucion')->nullable()->after('dni');
        });
    }

    public function down(): void
    {
        Schema::table('docentes', function (Blueprint $table) {
            $table->dropColumn('resolucion');
        });

        Schema::table('familiares', function (Blueprint $table) {
            $table->dropColumn(['n_afiliado', 'grado_division', 'turno', 'horario']);
        });
    }
};
