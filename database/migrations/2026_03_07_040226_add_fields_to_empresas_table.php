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
            $table->string('logo')->nullable();
            $table->decimal('limite_mb', 8, 2)->default(500); // 500 MB default
            $table->decimal('consumo_actual_mb', 8, 2)->default(0);
            $table->integer('limite_usuarios')->default(50);
            $table->enum('estado_cuenta', ['al_dia', 'pendiente', 'suspendida'])->default('al_dia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn(['logo', 'limite_mb', 'consumo_actual_mb', 'limite_usuarios', 'estado_cuenta']);
        });
    }
};
