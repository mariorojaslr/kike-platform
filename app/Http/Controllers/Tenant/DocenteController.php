<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Docente;
use App\Models\Formacion;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class DocenteController extends Controller
{
    private function getEmpresaId()
    {
        return Auth::user()->empresa_id ?? session('impersonated_tenant_id');
    }

    public function index(Request $request)
    {
        $empresaId = $this->getEmpresaId();

        if (!$empresaId) {
            return redirect()->route('dashboard')->withErrors('Sin entidad asignada.');
        }

        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        // Cargamos a sus formaciones (Ej: Terapista ocupacional)
        // Y cargamos documentos para saber si hay alertas
        $query = Docente::with(['formacion', 'documentos'])->where('empresa_id', $empresaId);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere(function($subQ) use ($search) {
                      $subQ->where('dni', 'like', "%{$search}%");
                  });
            });
        }

        $docentes = $query->orderBy('nombre')->paginate($perPage)->appends(request()->query());

        // El Docente hereda las profesiones/formaciones globales 
        $formaciones = Formacion::orderBy('nombre')->get();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('dashboards.tenant.partials.docentes_table_rows', compact('docentes'))->render(),
                'pagination' => (string) $docentes->links('pagination::bootstrap-5')
            ]);
        }

        return view('dashboards.tenant.docentes.index', compact('docentes', 'search', 'perPage', 'formaciones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'dni' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'formacion_id' => 'required|exists:formaciones,id',
            'foto_perfil' => 'nullable|image|max:3072'
        ]);

        $rutafoto = null;
        if($request->hasFile('foto_perfil')) {
            $rutafoto = $request->file('foto_perfil')->store('avatares/docentes', 'public');
        }

        Docente::create([
            'empresa_id' => $this->getEmpresaId(),
            'nombre' => $request->nombre,
            'dni' => $request->dni,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'formacion_id' => $request->formacion_id,
            'foto_perfil' => $rutafoto
        ]);

        return redirect()->back()->with('success', 'Terapeuta/Docente habilitado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $docente = Docente::where('empresa_id', $this->getEmpresaId())->findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'dni' => 'required|string|max:20',
            'formacion_id' => 'required|exists:formaciones,id',
            'foto_perfil' => 'nullable|image|max:3072'
        ]);

        $rutafoto = $docente->foto_perfil;
        if($request->hasFile('foto_perfil')) {
            $rutafoto = $request->file('foto_perfil')->store('avatares/docentes', 'public');
        }

        $docente->update([
            'nombre' => $request->nombre,
            'dni' => $request->dni,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'formacion_id' => $request->formacion_id,
            'foto_perfil' => $rutafoto
        ]);

        return redirect()->back()->with('success', 'Ficha del Terapeuta actualizada.');
    }

    public function destroy($id)
    {
        $docente = Docente::where('empresa_id', $this->getEmpresaId())->findOrFail($id);
        
        // cascade borra documentos asociados por DB
        $docente->delete();

        return redirect()->back()->with('success', 'Prestador removido de la institución.');
    }

    // -- REPORTES Y EXPORTACIÓN --

    public function exportExcel()
    {
        $docentes = Docente::with('formacion')->where('empresa_id', $this->getEmpresaId())->orderBy('nombre')->get();
        
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=padrón_docentes.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($docentes) {
            $file = fopen('php://output', 'w');
            // Formato UTF-8 BOM para que Excel lea acentos
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, ['ID Docente', 'Nombre Completo', 'DNI', 'Teléfono', 'Email', 'Título / Formación'], ';');

            foreach ($docentes as $row) {
                fputcsv($file, [
                    $row->id,
                    $row->nombre,
                    $row->dni,
                    $row->telefono,
                    $row->email,
                    $row->formacion ? $row->formacion->nombre : 'Sin Especialidad'
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf()
    {
        $docentes = Docente::with(['formacion', 'documentos'])->where('empresa_id', $this->getEmpresaId())->orderBy('nombre')->get();
        $pdf = Pdf::loadView('dashboards.tenant.docentes.pdf', compact('docentes'))->setPaper('a4', 'landscape');
        return $pdf->download('padrón_docentes.pdf');
    }

    public function importTemplate()
    {
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=plantilla_base_docentes.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, ['NOMBRE_COMPLETO', 'DNI', 'ESPECIALIDAD_O_ROL', 'TELEFONO', 'EMAIL', 'DIRECCION'], ';');
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'archivo_excel' => 'required|mimes:csv,txt|max:5120',
        ]);

        try {
            $file = fopen($request->file('archivo_excel')->getRealPath(), "r");
            $isFirstRow = true;
            $count = 0;
            
            while (($row = fgetcsv($file, 1000, ";")) !== FALSE) {
                if ($isFirstRow) { $isFirstRow = false; continue; } // Saltar cabecera
                if (count($row) < 2 || empty(trim($row[0]))) continue; // Línea vacía

                $nombre = $row[0] ?? null;
                $dni = $row[1] ?? null;
                $codForm = $row[2] ?? null;   // AHORA EN LA 3ERA COLUMNA
                $tel = $row[3] ?? null;
                $email = $row[4] ?? null;
                $dir = $row[5] ?? null;

                $formacionId = null;
                if (!empty(trim($codForm))) {
                    // Buscar o crear la especialidad en caliente si no existe, asignada a esta empresa
                    $f = Formacion::where('nombre', 'LIKE', '%' . trim($codForm) . '%')
                             ->where(function($q) {
                                 $q->whereNull('empresa_id')->orWhere('empresa_id', $this->getEmpresaId());
                             })->first();

                    if (!$f) {
                        $f = Formacion::create([
                            'empresa_id' => $this->getEmpresaId(),
                            'nombre' => trim($codForm)
                        ]);
                    }
                    $formacionId = $f->id;
                }

                $dniLimpio = trim(preg_replace('/[^0-9]/', '', $dni ?? ''));

                Docente::updateOrCreate(
                    [
                        'empresa_id' => $this->getEmpresaId(),
                        'dni'        => $dniLimpio,
                    ],
                    [
                        'nombre'     => trim($nombre),
                        'telefono'   => trim($tel),
                        'email'      => trim($email),
                        'direccion'  => trim($dir),
                        'formacion_id' => $formacionId,
                        // validado_auditoria se deja intacto o por defecto 0 si se crea recién
                    ]
                );
                $count++;
            }
            fclose($file);

            return redirect()->back()->with('success', "El lote masivo ($count Docentes) se ha cargado con éxito.");
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Error general en la importación. Asegúrese de guardar el Excel como CSV (separado por comas/punto y coma): ' . $e->getMessage());
        }
    }
}
