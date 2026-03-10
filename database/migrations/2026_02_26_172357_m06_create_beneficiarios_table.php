<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('beneficiarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('titular_id')->constrained('titulars');
            $table->string('nombre');
            $table->string('dni')->unique();
            $table->foreignId('escuela_id')->constrained('escuelas');
            $table->string('diagnostico')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('beneficiarios'); }
};
