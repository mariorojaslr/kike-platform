<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Docente;
use App\Models\Titulo; // Suponiendo que creamos este modelo para Formación Académica

class DocenteSeeder extends Seeder
{
    public function run()
    {
        // Datos extraídos de tu Excel
        $docentesExcel = [
            ['nombre' => 'Lopez Maria Eugenia del Rosario', 'dni' => '39234418', 'titulo' => 'Profesora en Psicopedagogia', 'email' => 'mariaeugenialopez18@gmail.com', 'tel' => '3884173057'],
            ['nombre' => 'Mediavilla cynthia mercedes', 'dni' => '33394014', 'titulo' => 'profesora de educacion especial', 'email' => 'mediavillacynthia@gmail.com', 'tel' => '3804321974'],
            ['nombre' => 'Herrera Analia Edith', 'dni' => '29387215', 'titulo' => 'Profesora en Educacion Especial', 'email' => 'ani.03herrera@gmail.com', 'tel' => '3804686640'],
            // ... Aquí meteríamos el resto de las filas
        ];

        foreach ($docentesExcel as $data) {
            // 1. Buscamos o creamos el título (Formación Académica) para no repetirlo
            $titulo = Titulo::firstOrCreate(['nombre' => ucwords(strtolower($data['titulo']))]);

            // 2. Creamos el docente y lo vinculamos con el ID del título
            Docente::create([
                'nombre' => $data['nombre'],
                'dni' => $data['dni'],
                'email' => $data['email'],
                'telefono' => $data['tel'],
                'titulo_id' => $titulo->id, // Aquí se hace el vínculo
            ]);
        }
    }
}
