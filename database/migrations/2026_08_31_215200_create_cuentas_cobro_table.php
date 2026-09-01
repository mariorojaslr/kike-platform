<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('cuentas_cobro')) {
            Schema::create('cuentas_cobro', function (Blueprint $table) {
                $table->id();
                $table->string('banco_nombre'); // Ej: Banco Santander, Banco Provincia, DollarApp, Efectivo
                $table->string('titular')->nullable();
                $table->string('cbu_cvu')->nullable();
                $table->string('alias')->nullable();
                $table->text('instrucciones')->nullable();
                $table->boolean('activo')->default(true);
                $table->timestamps();
            });

            // Insertar datos por defecto editables
            DB::table('cuentas_cobro')->insert([
                [
                    'banco_nombre' => 'Banco Santander',
                    'titular' => 'Mario Rojas',
                    'cbu_cvu' => '0720000020000012345678',
                    'alias' => 'INTEGRA.SANTANDER',
                    'instrucciones' => 'Transferencia directa CBU',
                    'activo' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'banco_nombre' => 'Banco Provincia',
                    'titular' => 'Mario Rojas',
                    'cbu_cvu' => '0140000011000087654321',
                    'alias' => 'INTEGRA.PROVINCIA',
                    'instrucciones' => 'Transferencia directa CBU',
                    'activo' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'banco_nombre' => 'Billetera ARQ / DollarApp',
                    'titular' => 'Mario Rojas',
                    'cbu_cvu' => '0000003100087654321098',
                    'alias' => 'INTEGRA.ARQ',
                    'instrucciones' => 'CVU o Alias para acreditación inmediata',
                    'activo' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cuentas_cobro');
    }
};
