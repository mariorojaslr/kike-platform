<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Solo creamos si no existen
        if (!Schema::hasTable('formaciones')) {
            Schema::create('formaciones', function (Blueprint $table) {
                $table->id();
                $table->string('nombre')->unique();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('diagnosticos')) {
            Schema::create('diagnosticos', function (Blueprint $table) {
                $table->id();
                $table->string('nombre')->unique();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('diagnosticos');
        Schema::dropIfExists('formaciones');
    }
};
