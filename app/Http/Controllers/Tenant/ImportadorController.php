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
        // Auto-fix DB si el usuario tuvo problemas con php artisan migrate en su Cpanel/Server
        if (!\Illuminate\Support\Facades\Schema::hasColumn('familiares', 'n_afiliado')) {
            \Illuminate\Support\Facades\Schema::table('familiares', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->string('n_afiliado')->nullable()->after('dni');
                $table->string('grado_division')->nullable();
                $table->string('turno')->nullable();
                $table->string('horario')->nullable();
            });
        }
        if (!\Illuminate\Support\Facades\Schema::hasColumn('familiares', 'parentesco')) {
            \Illuminate\Support\Facades\Schema::table('familiares', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->string('parentesco')->nullable()->default('Hijo');
            });
        }
        if (!\Illuminate\Support\Facades\Schema::hasColumn('familiares', 'diagnostico_id')) {
            \Illuminate\Support\Facades\Schema::table('familiares', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->unsignedBigInteger('diagnostico_id')->nullable();
            });
        }
        if (!\Illuminate\Support\Facades\Schema::hasColumn('familiares', 'empresa_id')) {
            \Illuminate\Support\Facades\Schema::table('familiares', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->unsignedBigInteger('empresa_id')->nullable();
            });
        }
        if (!\Illuminate\Support\Facades\Schema::hasColumn('familiares', 'tiene_patologia')) {
            \Illuminate\Support\Facades\Schema::table('familiares', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->boolean('tiene_patologia')->default(false);
            });
        }
        if (!\Illuminate\Support\Facades\Schema::hasColumn('familiares', 'escuela_id')) {
            \Illuminate\Support\Facades\Schema::table('familiares', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->unsignedBigInteger('escuela_id')->nullable();
            });
        }

        if (!\Illuminate\Support\Facades\Schema::hasColumn('docentes', 'resolucion')) {
            \Illuminate\Support\Facades\Schema::table('docentes', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->string('resolucion')->nullable()->after('dni');
            });
        }

        return view('dashboards.tenant.importador.index');
    }

    public function procesar(Request $request)
    {
        // Eliminamos el mimes:csv,txt porque en algunos servidores puede colgar o fallar
        $request->validate([
            'archivo_csv' => 'required|file|max:51200',
            'tipo_importacion' => 'required|in:resumen,alumnos'
        ]);

        try {
            $file = fopen($request->file('archivo_csv')->getRealPath(), "r");
            $count = 0;
            $empresaId = $this->getEmpresaId();

            if ($request->tipo_importacion == 'resumen') {
                // Lógica para planilla de RESUMEN GENERAL (Titulares, Beneficiarios, Docentes)
                $emptyLines = 0;
                while (($row = fgetcsv($file, 2000, ";")) !== FALSE) {
                    if (count($row) < 5 || empty(trim($row[0]))) {
                        $emptyLines++;
                        if ($emptyLines > 50) break; // Excel 1M empty rows protection
                        continue;
                    }
                    $emptyLines = 0;

                    $titularNombre = trim($row[0] ?? '');
                    
                    // Saltamos filas de encabezado engañosas ("sep=;", "TITULAR_APELLIDO", etc)
                    if (str_contains(strtolower($titularNombre), 'sep=') || str_contains(strtolower($titularNombre), 'titular_apellido')) {
                        continue;
                    }

                    $titularAfiliado = mb_substr(trim($row[1] ?? ''), 0, 100);
                    $benefNombre = mb_substr(trim($row[2] ?? ''), 0, 250);
                    $benefAfiliado = mb_substr(trim($row[3] ?? ''), 0, 100);
                    $docenteNombre = mb_substr(trim($row[4] ?? ''), 0, 250);
                    $docenteDni = mb_substr(trim($row[5] ?? ''), 0, 100);
                    $docenteRes = mb_substr(trim($row[6] ?? ''), 0, 250);

                    // 1. Crear o Buscar Titular
                    $titular = null;
                    if ($titularAfiliado) {
                        $titular = Titular::where('n_afiliado', $titularAfiliado)->first();
                        if (!$titular) {
                            $titular = Titular::create([
                                'n_afiliado' => $titularAfiliado,
                                'nombre' => $titularNombre,
                                'dni' => 'S/D-T-' . rand(10000, 99999), 
                                'empresa_id' => $empresaId
                            ]);
                        } else {
                            $titular->update(['nombre' => $titularNombre]);
                        }
                    }

                    // 2. Crear o Buscar Beneficiario (Hijo)
                    if ($benefAfiliado && $titular) {
                        $familiar = Familiar::where('n_afiliado', $benefAfiliado)->first();
                        if (!$familiar) {
                            Familiar::create([
                                'n_afiliado' => $benefAfiliado,
                                'empresa_id' => $empresaId,
                                'titular_id' => $titular->id,
                                'nombre' => $benefNombre,
                                'dni' => 'S/D-' . rand(1000, 9999), 
                                'parentesco' => 'Hijo'
                            ]);
                        } else {
                            $familiar->update(['nombre' => $benefNombre, 'titular_id' => $titular->id]);
                        }
                    }

                    // 3. Crear o Buscar Docente
                    if ($docenteDni) {
                        Docente::updateOrCreate(
                            ['dni' => $docenteDni, 'empresa_id' => $empresaId],
                            [
                                'nombre' => $docenteNombre,
                                'resolucion' => $docenteRes,
                                'formacion_id' => 1 
                            ]
                        );
                    }
                    
                    $count++;
                }
            } else {
                // Lógica para planilla ALUMNOS (Diagnósticos, Escuelas)
                $emptyLines = 0;
                while (($row = fgetcsv($file, 2000, ";")) !== FALSE) {
                    if (count($row) < 4 || empty(trim($row[0]))) {
                        $emptyLines++;
                        if ($emptyLines > 50) break; // Excel 1M empty rows protection
                        continue;
                    }
                    $emptyLines = 0;

                    $alumnoNombre = trim($row[0] ?? '');
                    
                    if (str_contains(strtolower($alumnoNombre), 'sep=') || str_contains(strtolower($alumnoNombre), 'alumno_apellido')) {
                        continue;
                    }

                    $alumnoDni = trim($row[1] ?? '');
                    $nAfiliado = trim($row[2] ?? '');
                    $diagnosticoStr = trim($row[3] ?? '');
                    if (mb_strlen($diagnosticoStr) > 250) {
                        $diagnosticoStr = mb_substr($diagnosticoStr, 0, 250);
                    }
                    
                    $escuelaStr = trim($row[4] ?? '');
                    if (mb_strlen($escuelaStr) > 250) {
                        $escuelaStr = mb_substr($escuelaStr, 0, 250);
                    }
                    $gradoStr = trim($row[5] ?? '');
                    $turnoStr = trim($row[6] ?? '');
                    $horarioStr = trim($row[7] ?? '');

                    // Tratar Diagnóstico
                    $diagnosticoId = null;
                    if ($diagnosticoStr) {
                        $diagObj = Diagnostico::firstOrCreate(['nombre' => $diagnosticoStr]);
                        $diagnosticoId = $diagObj->id;
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
                        $updData = [
                            'nombre' => $alumnoNombre,
                            'tiene_patologia' => !empty($diagnosticoStr),
                            'diagnostico_id' => $diagnosticoId,
                            'escuela_id' => $escuelaId,
                            'grado_division' => $gradoStr,
                            'turno' => $turnoStr,
                            'horario' => $horarioStr,
                        ];
                        if ($alumnoDni) {
                            $updData['dni'] = $alumnoDni;
                        }
                        
                        $familiar = Familiar::where('n_afiliado', $nAfiliado)->first();
                        if ($familiar) {
                            $familiar->update($updData);
                        } else {
                            $updData['n_afiliado'] = $nAfiliado;
                            $updData['empresa_id'] = $empresaId;
                            if (!isset($updData['dni'])) $updData['dni'] = 'S/D-' . rand(1000, 9999);
                            $updData['parentesco'] = 'Hijo';
                            Familiar::create($updData);
                        }
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

    public function templateResumen()
    {
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=plantilla_vacia_resumen.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); 
            fwrite($file, "sep=;\n"); // Magia para Excel Windows
            fputcsv($file, [
                'TITULAR_APELLIDO_NOMBRE',
                'TITULAR_NRO_AFILIADO',
                'ALUMNO_APELLIDO_NOMBRE',
                'ALUMNO_NRO_AFILIADO',
                'DOCENTE_APELLIDO_NOMBRE',
                'DOCENTE_DNI',
                'DOCENTE_RESOLUCION'
            ], ';');
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function templateAlumnos()
    {
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=plantilla_vacia_alumnos.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fwrite($file, "sep=;\n"); // Magia para Excel Windows
            fputcsv($file, [
                'ALUMNO_APELLIDO_NOMBRE',
                'ALUMNO_DNI',
                'ALUMNO_NRO_AFILIADO',
                'DIAGNOSTICO_CUD',
                'ESCUELA_NOMBRE',
                'GRADO_Y_DIVISION',
                'TURNO',
                'HORARIO_ATENCION'
            ], ';');
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }
}
