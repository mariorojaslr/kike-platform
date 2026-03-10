<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('titulars', function (Blueprint $table) {
            // Agregamos cuil después de dni, permitiendo que sea nulo por si acaso
            $table->string('cuil')->nullable()->after('dni');
        });
    }

    public function down(): void {
        Schema::table('titulars', function (Blueprint $table) {
            $table->dropColumn('cuil');
        });
    }
};
