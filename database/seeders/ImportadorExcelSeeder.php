<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Formacion;
use App\Models\Diagnostico;
use App\Models\Docente;
use App\Models\Familiar;

class ImportadorExcelSeeder extends Seeder
{
    public function run()
    {
        // 1. Extraemos y limpiamos Formaciones de tu Excel de Docentes
        // He detectado: "Prof de educacion especial", "profesora de educacion especial", etc.
        $datosDocentes = [ /* Aquí van las filas de tu Excel */ ];

        foreach ($datosDocentes as $fila) {
            // Limpiamos el texto: quitamos espacios extras y pasamos a Mayúscula la primer letra
            $nombreFormacion = trim(ucfirst(strtolower($fila['formacion'])));

            // firstOrCreate: Si no existe lo crea, si existe lo trae. ¡Evita duplicados!
            $formacion = Formacion::firstOrCreate(['nombre' => $nombreFormacion]);

            Docente::create([
                'nombre' => $fila['nombre'],
                'dni' => $fila['dni'],
                'formacion_id' => $formacion->id, // VÍNCULO LIMPIO
                'telefono' => $fila['telefono']
            ]);
        }

        // 2. Hacemos lo mismo con los Diagnósticos del Excel de Alumnos
        $datosAlumnos = [ /* Filas de Alumnos */ ];

        foreach ($datosAlumnos as $fila) {
            $nombreDiagnostico = trim(ucfirst(strtolower($fila['diagnostico'])));

            $diag = Diagnostico::firstOrCreate(['nombre' => $nombreDiagnostico]);

            Familiar::create([
                'nombre' => $fila['nombre'],
                'diagnostico_id' => $diag->id, // VÍNCULO LIMPIO
                'parentesco' => 'Hijo'
            ]);
        }
    }
}
