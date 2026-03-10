<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvinciasLocalidadesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiamos las tablas (importante el orden por las foráneas)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('localidades')->truncate();
        DB::table('provincias')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Provincias (Las 24 Jurisdicciones de Argentina)
        $provincias = [
            'Buenos Aires', 'Capital Federal', 'Catamarca', 'Chaco', 'Chubut', 'Córdoba',
            'Corrientes', 'Entre Ríos', 'Formosa', 'Jujuy', 'La Pampa', 'La Rioja',
            'Mendoza', 'Misiones', 'Neuquén', 'Río Negro', 'Salta', 'San Juan',
            'San Luis', 'Santa Cruz', 'Santa Fe', 'Santiago del Estero', 'Tierra del Fuego', 'Tucumán'
        ];

        foreach ($provincias as $provincia) {
            DB::table('provincias')->insert(['nombre' => $provincia, 'created_at' => now(), 'updated_at' => now()]);
        }

        // 2. Algunas Localidades Principales (Para no sobrecargar el seeder con miles en esta DEMO)
        // En Producción este array provendrá de un JSON o API Oficial. Ahora insertaremos las capitales y principales como muestra funcional.
        
        $localidades = [
            // Buenos Aires (1)
            ['provincia_id' => 1, 'nombre' => 'La Plata'],
            ['provincia_id' => 1, 'nombre' => 'Mar del Plata'],
            ['provincia_id' => 1, 'nombre' => 'Bahía Blanca'],
            ['provincia_id' => 1, 'nombre' => 'Tandil'],
            ['provincia_id' => 1, 'nombre' => 'Morón'],
            ['provincia_id' => 1, 'nombre' => 'San Isidro'],
            // Capital Federal (2)
            ['provincia_id' => 2, 'nombre' => 'Belgrano'],
            ['provincia_id' => 2, 'nombre' => 'Palermo'],
            ['provincia_id' => 2, 'nombre' => 'San Telmo'],
            // Córdoba (6)
            ['provincia_id' => 6, 'nombre' => 'Córdoba Capital'],
            ['provincia_id' => 6, 'nombre' => 'Villa Carlos Paz'],
            ['provincia_id' => 6, 'nombre' => 'Río Cuarto'],
            ['provincia_id' => 6, 'nombre' => 'Villa María'],
            // Santa Fe (21)
            ['provincia_id' => 21, 'nombre' => 'Santa Fe Capital'],
            ['provincia_id' => 21, 'nombre' => 'Rosario'],
            ['provincia_id' => 21, 'nombre' => 'Rafaela'],
            // Mendoza (13)
            ['provincia_id' => 13, 'nombre' => 'Mendoza Capital'],
            ['provincia_id' => 13, 'nombre' => 'San Rafael'],
            // Tucumán (24)
            ['provincia_id' => 24, 'nombre' => 'San Miguel de Tucumán'],
            ['provincia_id' => 24, 'nombre' => 'Tafí Viejo'],
            // Neuquén (15)
            ['provincia_id' => 15, 'nombre' => 'Neuquén Capital'],
            ['provincia_id' => 15, 'nombre' => 'San Martín de los Andes'],
            // Salta (17)
            ['provincia_id' => 17, 'nombre' => 'Salta Capital'],
            ['provincia_id' => 17, 'nombre' => 'Cafayate'],
            // San Juan (18)
            ['provincia_id' => 18, 'nombre' => 'San Juan Capital'],
            ['provincia_id' => 18, 'nombre' => 'Rawson'],
            // Chubut (5)
            ['provincia_id' => 5, 'nombre' => 'Rawson'],
            ['provincia_id' => 5, 'nombre' => 'Puerto Madryn'],
            ['provincia_id' => 5, 'nombre' => 'Comodoro Rivadavia'],
            // Río Negro (16)
            ['provincia_id' => 16, 'nombre' => 'Viedma'],
            ['provincia_id' => 16, 'nombre' => 'San Carlos de Bariloche'],
            // La Rioja (12)
            ['provincia_id' => 12, 'nombre' => 'La Rioja Capital'],
            ['provincia_id' => 12, 'nombre' => 'Chilecito'],
            ['provincia_id' => 12, 'nombre' => 'Chamical'],
            ['provincia_id' => 12, 'nombre' => 'Aimogasta'],
            ['provincia_id' => 12, 'nombre' => 'Villa Unión'],
            ['provincia_id' => 12, 'nombre' => 'Chepes'],
            ['provincia_id' => 12, 'nombre' => 'Olta'],
            ['provincia_id' => 12, 'nombre' => 'Famatina'],
            ['provincia_id' => 12, 'nombre' => 'Sanagasta'],
            ['provincia_id' => 12, 'nombre' => 'Vinchina'],
            ['provincia_id' => 12, 'nombre' => 'Milagro'],
        ];

        foreach ($localidades as $loc) {
            DB::table('localidades')->insert([
                'provincia_id' => $loc['provincia_id'],
                'nombre' => $loc['nombre'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        
    }
}
