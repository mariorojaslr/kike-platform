<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pago_empresas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->decimal('monto', 12, 2)->default(0.00);
            $table->string('nro_comprobante')->nullable();
            $table->string('banco_origen')->nullable();
            $table->dateTime('fecha_pago')->nullable();
            $table->string('comprobante_url');
            $table->enum('estado', ['pendiente_verificacion', 'aprobado', 'rechazado'])->default('pendiente_verificacion');
            $table->text('notas_owner')->nullable();
            $table->json('datos_extraidos_ia')->nullable();
            $table->timestamps();

            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pago_empresas');
    }
};
