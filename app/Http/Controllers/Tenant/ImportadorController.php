<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Titular;
use App\Models\Familiar;
use App\Models\Docente;
use App\Models\Diagnostico;
use App\Models\Escuela;

class ImportadorController extends Controller
{
    private function getEmpresaId()
    {
        return Auth::user()->empresa_id ?? session('impersonated_tenant_id');
    }

    public function index()
    {
        return view('dashboards.tenant.importador.index');
    }

    public function procesar(Request $request)
    {
        $request->validate([
            'archivo_csv' => 'required|mimes:csv,txt|max:10240',
            'tipo_importacion' => 'required|in:resumen,alumnos'
        ]);

        try {
            $file = fopen($request->file('archivo_csv')->getRealPath(), "r");
            $isFirstRow = true;
            $count = 0;
            $empresaId = $this->getEmpresaId();

            if ($request->tipo_importacion == 'resumen') {
                // Lógica para planilla de RESUMEN GENERAL (Titulares, Beneficiarios, Docentes)
                while (($row = fgetcsv($file, 2000, ";")) !== FALSE) {
                    if ($isFirstRow) { $isFirstRow = false; continue; }
                    if (count($row) < 5 || empty(trim($row[0]))) continue;

                    // Columnas estimadas según la primera imagen
                    $titularNombre = trim($row[0] ?? ''); // Apellido y Nombre Titular
                    $titularAfiliado = trim($row[1] ?? ''); // N° Afiliado Titular
                    
                    $benefNombre = trim($row[2] ?? ''); // Apellido y Nombre Beneficiario
                    $benefAfiliado = trim($row[3] ?? ''); // N° Afiliado Beneficiario
                    
                    $docenteNombre = trim($row[4] ?? ''); // Apellido y Nombre Docente
                    $docenteDni = trim($row[5] ?? ''); // DNI
                    $docenteRes = trim($row[6] ?? ''); // Resolucion

                    // 1. Crear o Buscar Titular
                    $titular = null;
                    if ($titularAfiliado) {
                        $titular = Titular::updateOrCreate(
                            ['n_afiliado' => $titularAfiliado],
                            ['nombre' => $titularNombre]
                        );
                    }

                    // 2. Crear o Buscar Beneficiario (Hijo)
                    if ($benefAfiliado && $titular) {
                        Familiar::updateOrCreate(
                            ['n_afiliado' => $benefAfiliado],
                            [
                                'titular_id' => $titular->id,
                                'nombre' => $benefNombre,
                                'dni' => 'S/D-' . rand(1000, 9999), // DNI Temp si no viene en esta planilla
                                'parentesco' => 'Hijo'
                            ]
                        );
                    }

                    // 3. Crear o Buscar Docente
                    if ($docenteDni) {
                        Docente::updateOrCreate(
                            ['dni' => $docenteDni, 'empresa_id' => $empresaId],
                            [
                                'nombre' => $docenteNombre,
                                'resolucion' => $docenteRes,
                                'formacion_id' => 1 // ID por defecto o dinámico
                            ]
                        );
                    }
                    
                    $count++;
                }
            } else {
                // Lógica para planilla ALUMNOS (Diagnósticos, Escuelas)
                while (($row = fgetcsv($file, 2000, ";")) !== FALSE) {
                    if ($isFirstRow) { $isFirstRow = false; continue; }
                    if (count($row) < 4 || empty(trim($row[0]))) continue;

                    // Columnas estimadas según la segunda imagen
                    $alumnoNombre = trim($row[0] ?? '');
                    $alumnoDni = trim($row[1] ?? '');
                    $nAfiliado = trim($row[2] ?? '');
                    $diagnosticoStr = trim($row[3] ?? '');
                    $escuelaStr = trim($row[4] ?? '');
                    $gradoStr = trim($row[5] ?? '');
                    $turnoStr = trim($row[6] ?? '');
                    $horarioStr = trim($row[7] ?? '');

                    // Tratar Diagnóstico
                    if ($diagnosticoStr) {
                        Diagnostico::firstOrCreate(['nombre' => $diagnosticoStr]);
                    }

                    // Tratar Escuela de este Tenant
                    $escuelaId = null;
                    if ($escuelaStr) {
                        $escuela = Escuela::firstOrCreate(
                            ['nombre' => $escuelaStr, 'empresa_id' => $empresaId]
                        );
                        $escuelaId = $escuela->id;
                    }

                    // Actualizar o Crear Beneficiario con sus datos correctos
                    if ($nAfiliado) {
                        Familiar::updateOrCreate(
                            ['n_afiliado' => $nAfiliado],
                            [
                                'nombre' => $alumnoNombre,
                                'dni' => $alumnoDni,
                                'tiene_patologia' => !empty($diagnosticoStr),
                                'diagnostico' => $diagnosticoStr,
                                'escuela_id' => $escuelaId,
                                'grado_division' => $gradoStr,
                                'turno' => $turnoStr,
                                'horario' => $horarioStr,
                                // En caso de que se cree nuevo sin Titular aún, ponemos null o un ID dummy. 
                                // Idealmente subir primero el Resumen para que el Titular exista.
                            ]
                        );
                        $count++;
                    }
                }
            }

            fclose($file);
            return redirect()->back()->with('success', "Importación completada. Se procesaron $count registros integrados al sistema.");

        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Ocurrió un error leyendo el CSV. Asegúrate de configurar Excel para guardar CSV delimitado por Puntos y Comas (;). Error del sistema: ' . $e->getMessage());
        }
    }
}
