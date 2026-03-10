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
        Schema::table('escuelas', function (Blueprint $table) {
            $table->string('cue')->nullable()->after('nombre');
            $table->string('email')->nullable()->after('telefono');
            $table->boolean('activo')->default(1)->after('contacto_principal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('escuelas', function (Blueprint $table) {
            $table->dropColumn(['cue', 'email', 'activo']);
        });
    }
};
