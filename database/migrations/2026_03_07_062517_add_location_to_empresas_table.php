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
            $table->unsignedBigInteger('provincia_id')->nullable()->after('estado_cuenta');
            $table->unsignedBigInteger('localidad_id')->nullable()->after('provincia_id');
            // Constraints si las requieres
            $table->foreign('provincia_id')->references('id')->on('provincias')->onDelete('set null');
            $table->foreign('localidad_id')->references('id')->on('localidades')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropForeign(['provincia_id']);
            $table->dropForeign(['localidad_id']);
            $table->dropColumn(['provincia_id', 'localidad_id']);
        });
    }
};
