<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('docentes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('dni')->unique();
            $table->foreignId('formacion_id')->constrained('formacions');
            $table->boolean('validado_auditoria')->default(false);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('docentes'); }
};
