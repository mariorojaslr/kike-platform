<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('novedades_auditoria')) {
            Schema::create('novedades_auditoria', function (Blueprint $table) {
                $table->id();
                $table->foreignId('empresa_id')->nullable()->constrained('empresas')->onDelete('cascade');
                $table->foreignId('docente_id')->constrained('docentes')->onDelete('cascade');
                $table->foreignId('expediente_id')->nullable()->constrained('expedientes_alumno')->onDelete('cascade');
                $table->foreignId('factura_id')->nullable()->constrained('facturas_docente')->onDelete('cascade');
                
                $table->string('tipo_novedad'); // nueva_resolucion, nuevo_certificado, nueva_factura_arca, nuevo_documento_legajo
                $table->text('descripcion');
                $table->string('estado')->default('pendiente'); // pendiente, revisado, resuelto
                $table->boolean('leido')->default(false);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('novedades_auditoria');
    }
};
