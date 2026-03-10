<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class LimpiezaController extends Controller
{
    public function ejecutar()
    {
        try {
            DB::beginTransaction();

            // 1. Definimos IDs de La Rioja
            $idOriginal = 1;
            $idDuplicado = 2;

            // 2. Movemos las localidades de la provincia 2 a la 1
            DB::table('localidades')
                ->where('provincia_id', $idDuplicado)
                ->update(['provincia_id' => $idOriginal]);

            // 3. Borramos la provincia duplicada
            DB::table('provincias')->where('id', $idDuplicado)->delete();

            // 4. Limpiamos localidades duplicadas
            // Buscamos nombres repetidos en la provincia 1
            $repetidos = DB::table('localidades')
                ->select('nombre')
                ->where('provincia_id', $idOriginal)
                ->groupBy('nombre')
                ->having(DB::raw('count(*)'), '>', 1)
                ->pluck('nombre');

            foreach ($repetidos as $nombre) {
                // Obtenemos todos los IDs de ese nombre (ej. todos los "ARAUCO")
                $ids = DB::table('localidades')
                    ->where('nombre', $nombre)
                    ->where('provincia_id', $idOriginal)
                    ->orderBy('id', 'asc') // El más viejo (ID menor) se queda
                    ->pluck('id')
                    ->toArray();

                $idQueSeQueda = array_shift($ids); // Quitamos el primero del array para no borrarlo

                // Borramos el resto de los IDs duplicados
                DB::table('localidades')->whereIn('id', $ids)->delete();
            }

            DB::commit();
            return "¡Base de datos saneada! Se eliminó la provincia 2 y se quitaron las localidades repetidas. Las tablas están limpias.";

        } catch (\Exception $e) {
            DB::rollBack();
            return "Error durante la limpieza: " . $e->getMessage();
        }
    }
}
