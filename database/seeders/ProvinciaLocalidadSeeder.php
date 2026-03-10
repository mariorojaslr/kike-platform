<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvinciaLocalidadSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Insertamos La Rioja
        $provinciaId = DB::table('provincias')->insertGetId([
            'nombre' => 'LA RIOJA',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Insertamos todos los Departamentos (Localidades)
        $departamentos = [
            'ARAUCO', 'CAPITAL', 'CASTRO BARROS', 'CHAMICAL', 'CHILECITO',
            'CORONEL FELIPE VARELA', 'FAMATINA', 'GENERAL ÁNGEL VICENTE PEÑALOZA',
            'GENERAL BELGRANO', 'GENERAL JUAN FACUNDO QUIROGA', 'GENERAL LAMADRID',
            'GENERAL OCAMPO', 'GENERAL SAN MARTÍN', 'INDEPENDENCIA', 'ROSARIO VERA PEÑALOZA',
            'SANAGASTA', 'SAN BLAS DE LOS SAUCES', 'VINCHINA'
        ];

        foreach ($departamentos as $dep) {
            DB::table('localidades')->insert([
                'provincia_id' => $provinciaId,
                'nombre' => $dep,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
