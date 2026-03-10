<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('familiars', function (Blueprint $table) {
            $table->id();
            // Esta línea es la que fallaba: ahora 'titulars' ya va a existir
            $table->foreignId('titular_id')->constrained('titulars')->onDelete('cascade');

            $table->string('nombre');
            $table->string('dni')->unique();
            $table->string('parentesco'); // 'Hijo', 'Conyuge'
            $table->boolean('tiene_patologia')->default(false);
            $table->string('diagnostico')->nullable();
            $table->unsignedBigInteger('escuela_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('familiars');
    }
};
