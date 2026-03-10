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
        Schema::create('notificaciones_sistema', function (Blueprint $blueprint) {
            $blueprint->id();

            // Título de la notificación (ej: 'Nuevo Docente')
            $blueprint->string('titulo');

            // Mensaje detallado (ej: 'El docente Juan Pérez requiere validación')
            $blueprint->text('mensaje');

            // Tipo para usar colores (info, warning, danger, success)
            $blueprint->string('tipo')->default('info');

            // Estado de lectura
            $blueprint->boolean('leido')->default(false);

            // Relación opcional con un usuario si la notificación es para alguien específico
            $blueprint->unsignedBigInteger('user_id')->nullable();

            // Icono de FontAwesome para mostrar en la interfaz
            $blueprint->string('icono')->default('fas fa-bell');

            $blueprint->timestamps();

            // Clave foránea (asumiendo que usas la tabla users de Laravel)
            $blueprint->foreign('user_id')
                      ->references('id')
                      ->on('users')
                      ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificaciones_sistema');
    }
};
