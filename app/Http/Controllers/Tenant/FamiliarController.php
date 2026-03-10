<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Familiar;
use App\Models\Titular;
use App\Models\Diagnostico;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class FamiliarController extends Controller
{
    private function getEmpresaId()
    {
        return Auth::user()->empresa_id ?? session('impersonated_tenant_id');
    }

    public function index(Request $request)
    {
        $empresaId = $this->getEmpresaId();

        if (!$empresaId) {
            return redirect()->route('dashboard')->withErrors('No tienes una empresa asignada activa.');
        }

        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        // Cargamos relaciones para el listado global
        $query = Familiar::with(['titular', 'diagnostico'])->where('empresa_id', $empresaId);

        if ($search) {
            $query->where(function($q) use ($search) {
                // Nombre o DNI del Paciente
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('dni', 'like', "%{$search}%");

                // Búsqueda por Número Obra Social Completo (Ej: 13344455502)
                 if (preg_match('/^1(\d{7,8})\d{2}$/', $search, $matches)) {
                    $dniTitular = $matches[1];
                    // Envolvemos en un query para aplacar alertas Linter en algunos IDs de PHP
                    $q->orWhere(function($subQ) use ($dniTitular) {
                        $subQ->whereHas('titular', function($qTit) use ($dniTitular) {
                            $qTit->where('dni', $dniTitular);
                        });
                    });
                }
            });
        }

        $familiares = $query->orderBy('created_at', 'desc')->paginate($perPage)->appends(request()->query());

        // Necesitamos listas para los Selects del modal de Creación/Edición
        $titularesDisponibles = Titular::where('empresa_id', $empresaId)->orderBy('nombre')->get();
        // El tenant debe poder ver TODOS los diagnósticos globales (o al menos los habilitados en la DB)
        $diagnosticosDisponibles = Diagnostico::orderBy('nombre')->get();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('dashboards.tenant.partials.familiares_table_rows', compact('familiares'))->render(),
                'pagination' => (string) $familiares->links('pagination::bootstrap-5')
            ]);
        }

        return view('dashboards.tenant.familiares.index', compact('familiares', 'search', 'perPage', 'titularesDisponibles', 'diagnosticosDisponibles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titular_id' => 'required|exists:titulares,id',
            'nombre' => 'required|string|max:255',
            'dni' => 'nullable|string|max:20',
            'parentesco' => 'nullable|string',
            'tiene_patologia' => 'boolean',
            'diagnostico_id' => 'nullable|exists:diagnosticos,id',
            'foto_perfil' => 'nullable|image|max:3072'
        ]);

        // Verificar que el titular pertenezca a la empresa
        $titular = Titular::where('empresa_id', $this->getEmpresaId())->findOrFail($request->titular_id);

        $rutafoto = null;
        if($request->hasFile('foto_perfil')) {
            $rutafoto = $request->file('foto_perfil')->store('avatares/familiares', 'public');
        }

        Familiar::create([
            'empresa_id' => $this->getEmpresaId(),
            'titular_id' => $titular->id,
            'nombre' => $request->nombre,
            'dni' => $request->dni,
            'parentesco' => $request->parentesco ?? 'Hijo', // Valor por defecto
            'tiene_patologia' => $request->has('tiene_patologia') ? 1 : 0,
            'diagnostico_id' => $request->diagnostico_id,
            'foto_perfil' => $rutafoto
        ]);

        return redirect()->back()->with('success', 'Paciente/Familiar inscripto correctamente.');
    }

    public function update(Request $request, $id)
    {
        $familiar = Familiar::where('empresa_id', $this->getEmpresaId())->findOrFail($id);

        $request->validate([
            'titular_id' => 'required|exists:titulares,id',
            'nombre' => 'required|string|max:255',
            'dni' => 'nullable|string|max:20',
            'diagnostico_id' => 'nullable|exists:diagnosticos,id',
            'foto_perfil' => 'nullable|image|max:3072'
        ]);

        // Asegurar propiedad de titular
        $titular = Titular::where('empresa_id', $this->getEmpresaId())->findOrFail($request->titular_id);

        $rutafoto = $familiar->foto_perfil;
        if($request->hasFile('foto_perfil')) {
            $rutafoto = $request->file('foto_perfil')->store('avatares/familiares', 'public');
        }

        $familiar->update([
            'titular_id' => $titular->id,
            'nombre' => $request->nombre,
            'dni' => $request->dni,
            'parentesco' => $request->parentesco ?? $familiar->parentesco,
            'tiene_patologia' => $request->has('tiene_patologia') ? 1 : 0,
            'diagnostico_id' => $request->diagnostico_id,
            'foto_perfil' => $rutafoto
        ]);

        return redirect()->back()->with('success', 'Datos del Paciente actualizados.');
    }

    public function destroy($id)
    {
        $familiar = Familiar::where('empresa_id', $this->getEmpresaId())->findOrFail($id);
        $familiar->delete();

        return redirect()->back()->with('success', 'Alumno/Paciente desvinculado de la institución.');
    }

    // -- REPORTES Y EXPORTACIÓN --

    public function exportExcel()
    {
        $familiares = Familiar::with(['titular', 'diagnostico'])->where('empresa_id', $this->getEmpresaId())->orderBy('nombre')->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=padrón_familiares.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($familiares) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8
            
            fputcsv($file, [
                'ID Paciente', 
                'Nombre Alumno/Paciente', 
                'DNI Paciente',
                'Titular (Padre)',
                'DNI Titular',
                'Nº Afiliado / Obra Social',
                'Condición / Diagnóstico'
            ], ',');

            foreach ($familiares as $row) {
                fputcsv($file, [
                    $row->id,
                    $row->nombre,
                    $row->dni ?? '-',
                    $row->titular ? $row->titular->nombre : 'Sin Titular',
                    $row->titular ? $row->titular->dni : '-',
                    $row->numero_afiliado,
                    $row->diagnostico ? $row->diagnostico->nombre : 'Ninguno'
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf()
    {
        $familiares = Familiar::with(['titular', 'diagnostico'])->where('empresa_id', $this->getEmpresaId())->orderBy('nombre')->get();
        $pdf = Pdf::loadView('dashboards.tenant.familiares.pdf', compact('familiares'))->setPaper('a4', 'landscape');
        return $pdf->download('padrón_familiares.pdf');
    }

    public function importTemplate()
    {
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=plantilla_base_familiares.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, [
                'DNI_DEL_TITULAR',
                'NOMBRE_COMPLETO_PACIENTE',
                'DNI_PACIENTE',
                'PARENTESCO',
                'TIENE_PATOLOGIA_O_DIAGNOSTICO_1_SI_0_NO',
                'CODIGO_DIAGNOSTICO_CIE10'
            ], ',');
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'archivo_excel' => 'required|mimes:csv,txt|max:5120'
        ]);

        try {
            $file = fopen($request->file('archivo_excel')->getRealPath(), "r");
            $isFirstRow = true;
            $count = 0;
            
            while (($row = fgetcsv($file, 1000, ";")) !== FALSE) {
                if ($isFirstRow) { $isFirstRow = false; continue; }
                if (count($row) < 2 || empty(trim($row[1]))) continue; // Si nombre paciente vacio

                $dniTitular = trim($row[0]);
                $nombreFam = trim($row[1]);
                $dniFam = trim($row[2] ?? '');
                $parentesco = trim($row[3] ?? 'Hijo');
                $tieneDiag = trim($row[4] ?? '0');
                $codDiag = trim($row[5] ?? '');

                // Buscar Titular
                $titular = Titular::where('empresa_id', $this->getEmpresaId())->where('dni', $dniTitular)->first();
                if (!$titular) continue; // Si no hay titular, saltamos

                // Buscar Diagnóstico
                $diagId = null;
                $boolPatologia = 0;
                
                if ($tieneDiag == '1' || strtolower($tieneDiag) == 'si' || $tieneDiag === 1) {
                    $boolPatologia = 1;
                    if (!empty($codDiag)) {
                        $diag = Diagnostico::where('codigo', 'LIKE', '%' . $codDiag . '%')->first();
                        $diagId = $diag ? $diag->id : null;
                    }
                }

                Familiar::create([
                    'empresa_id' => $this->getEmpresaId(),
                    'titular_id' => $titular->id,
                    'nombre' => $nombreFam,
                    'dni' => $dniFam,
                    'parentesco' => $parentesco ?: 'Otro',
                    'tiene_patologia' => $boolPatologia,
                    'diagnostico_id' => $diagId
                ]);
                $count++;
            }
            fclose($file);
            return redirect()->back()->with('success', "El lote masivo ($count Pacientes/Familiares) se ha importado asociándose correctamente a sus titulares.");
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Error general verificando archivo. Asegúrese de guardar el Excel original como "CSV delimitado por comas": ' . $e->getMessage());
        }
    }
}
