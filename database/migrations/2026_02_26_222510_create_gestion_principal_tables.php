<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabla Docentes
        if (!Schema::hasTable('docentes')) {
            Schema::create('docentes', function (Blueprint $table) {
                $table->id();
                $table->string('nombre');
                $table->string('dni')->unique();
                $table->string('email')->nullable();
                $table->string('telefono')->nullable();

                // IMPORTANTE: Cambié 'formaciones' por 'formacions'
                // ya que vi en tu log que se crea como 'formacions'
                $table->foreignId('formacion_id')
                      ->nullable()
                      ->constrained('formacions')
                      ->onDelete('set null');

                $table->timestamps();
            });
        }

        // 2. Tabla Familiares
        if (!Schema::hasTable('familiares')) {
            Schema::create('familiares', function (Blueprint $table) {
                $table->id();
                $table->string('nombre');
                $table->string('dni')->nullable();

                $table->unsignedBigInteger('titular_id')->nullable();
                $table->unsignedBigInteger('diagnostico_id')->nullable();

                // --- CORRECCIÓN AQUÍ ---
                // Tu tabla se llama 'titulars' según el log de Artisan
                $table->foreign('titular_id')
                      ->references('id')
                      ->on('titulars')
                      ->onDelete('cascade');

                // Solo crea la relación si la tabla diagnósticos existe
                if (Schema::hasTable('diagnosticos')) {
                    $table->foreign('diagnostico_id')
                          ->references('id')
                          ->on('diagnosticos')
                          ->onDelete('set null');
                }

                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('familiares');
        Schema::dropIfExists('docentes');
    }
};
