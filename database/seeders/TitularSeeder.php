<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Titular;
use App\Models\Familiar;
use Illuminate\Support\Facades\DB;

class TitularSeeder extends Seeder
{
    /**
     * Carga de datos de Titulares y Grupo Familiar
     */
    public function run(): void
    {
        // Desactivamos chequeo de llaves foráneas para limpiar bien
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Titular::truncate();
        Familiar::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Titular con 2 hijos (Uno con patología)
        $t1 = Titular::create([
            'nombre' => 'Mario Rojas',
            'dni'    => '22333444',
            'cuil'   => '20-22333444-9'
        ]);

        Familiar::create([
            'titular_id'      => $t1->id,
            'nombre'          => 'Lucas Rojas',
            'dni'             => '55111222',
            'parentesco'      => 'Hijo',
            'tiene_patologia' => true,
            'diagnostico'     => 'Diabetes Tipo 1'
        ]);

        Familiar::create([
            'titular_id'      => $t1->id,
            'nombre'          => 'Ana Rojas',
            'dni'             => '55111333',
            'parentesco'      => 'Hijo',
            'tiene_patologia' => false
        ]);

        // 2. Titular con 1 hijo (Sin patología)
        $t2 = Titular::create([
            'nombre' => 'Elena Gomez',
            'dni'    => '30555666',
            'cuil'   => '27-30555666-4'
        ]);

        Familiar::create([
            'titular_id'      => $t2->id,
            'nombre'          => 'Pedro Gomez',
            'dni'             => '60000001',
            'parentesco'      => 'Hijo',
            'tiene_patologia' => false
        ]);

        // 3. Titular solo (0 hijos)
        Titular::create([
            'nombre' => 'Juan Perez',
            'dni'    => '18999000',
            'cuil'   => '20-18999000-1'
        ]);
    }
}
