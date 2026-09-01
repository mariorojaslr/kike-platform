<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('facturas_docente')) {
            Schema::create('facturas_docente', function (Blueprint $table) {
                $table->id();
                $table->foreignId('docente_id')->constrained('docentes')->onDelete('cascade');
                $table->foreignId('expediente_id')->nullable()->constrained('expedientes_alumno')->onDelete('set null');
                $table->foreignId('alumno_id')->nullable()->constrained('familiars')->onDelete('set null');
                $table->foreignId('empresa_id')->nullable()->constrained('empresas')->onDelete('cascade');
                
                // Datos ARCA / AFIP leídos por IA
                $table->string('nro_factura')->nullable();
                $table->string('punto_venta')->nullable();
                $table->string('cuit_emisor')->nullable();
                $table->string('razon_social_emisor')->nullable();
                $table->string('domicilio_emisor')->nullable();
                $table->string('cae')->nullable();
                $table->date('vencimiento_cae')->nullable();
                $table->decimal('monto_total', 10, 2)->default(0.00);
                $table->integer('periodo_mes')->nullable();
                $table->integer('periodo_anio')->nullable();
                
                $table->string('comprobante_url')->nullable();
                $table->text('qr_raw_data')->nullable();
                
                $table->string('estado_auditoria')->default('pendiente'); // pendiente, aprobado, rechazado
                $table->text('motivo_rechazo')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('facturas_docente');
    }
};
