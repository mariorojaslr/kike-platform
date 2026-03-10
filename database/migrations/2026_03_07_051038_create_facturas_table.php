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
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Terapeuta que la sube
            $table->string('qr_data')->nullable(); // JSON o String crudo del código leido
            $table->string('imagen_url')->nullable(); // La URL final (Local ahora, Bunny luego)
            $table->string('storage_disk')->default('local'); // 'local' o 'bunny' para saber dónde está
            $table->decimal('monto', 10, 2)->nullable(); // Extraído o manual
            $table->enum('estado', ['pendiente', 'aprobada', 'rechazada'])->default('pendiente');
            $table->text('notas_auditor')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};
