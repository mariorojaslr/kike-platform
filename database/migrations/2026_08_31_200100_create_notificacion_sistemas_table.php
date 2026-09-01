<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('notificaciones_sistema')) {
            Schema::create('notificaciones_sistema', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('empresa_id')->nullable();
                $table->string('titulo');
                $table->text('mensaje');
                $table->string('link')->nullable();
                $table->boolean('leido')->default(false);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('notificaciones_sistema');
    }
};
