<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Asegurarnos de que las tablas existen antes de manipularlas
        if (Schema::hasTable('formacions') && Schema::hasTable('formaciones')) {
            // Transferir datos SOLO si formaciones está vacía (para evitar error duplicado si falló a la mitad)
            $count = DB::table('formaciones')->count();
            if ($count === 0) {
                DB::statement('INSERT INTO formaciones (id, nombre, created_at, updated_at) SELECT id, nombre, created_at, updated_at FROM formacions');
            }
            
            // 2. Redirigir la llave foránea de 'docentes'
            if (Schema::hasTable('docentes')) {
                // Hacemos drop de la llave vieja si existe (ignoramos error silenciosamente)
                try {
                    Schema::table('docentes', function (Blueprint $table) {
                        $table->dropForeign(['formacion_id']); 
                    });
                } catch (\Exception $e) {}

                // Forzar la columna a ser NULLABLE nativamente en MySQL sin Doctrine DBAL
                DB::statement('ALTER TABLE docentes MODIFY formacion_id BIGINT UNSIGNED NULL');

                // Crear nueva Foreign Key
                Schema::table('docentes', function (Blueprint $table) {
                    $table->foreign('formacion_id')->references('id')->on('formaciones')->onDelete('set null');
                });
            }

            // 3. Destruir tabla 'Spanglish'
            Schema::dropIfExists('formacions');
        }
    }

    public function down(): void
    {
        // Si hiciéramos rollback, recrearíamos la tabla Spanglish
        if (!Schema::hasTable('formacions')) {
            Schema::create('formacions', function (Blueprint $table) {
                $table->id();
                $table->string('nombre');
                $table->timestamps();
            });

            if (Schema::hasTable('formaciones')) {
                $count = DB::table('formacions')->count();
                if ($count === 0) {
                    DB::statement('INSERT INTO formacions (id, nombre, created_at, updated_at) SELECT id, nombre, created_at, updated_at FROM formaciones');
                }
                DB::table('formaciones')->truncate(); 
            }

            if (Schema::hasTable('docentes')) {
                try {
                    Schema::table('docentes', function (Blueprint $table) {
                        $table->dropForeign(['formacion_id']);
                    });
                } catch (\Exception $e) {}

                DB::statement('ALTER TABLE docentes MODIFY formacion_id BIGINT UNSIGNED NOT NULL');

                Schema::table('docentes', function (Blueprint $table) {
                    $table->foreign('formacion_id')->references('id')->on('formacions')->onDelete('cascade');
                });
            }
        }
    }
};
