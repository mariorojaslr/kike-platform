<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Titular;
use Illuminate\Support\Facades\Auth;
use App\Exports\Tenant\TitularesExport;
use App\Exports\Tenant\TitularesTemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class TitularController extends Controller
{
    /**
     * Obtiene el ID de la empresa en contexto (ya sea la del usuario o la suplantada)
     */
    private function getEmpresaId()
    {
        // En un escenario Tenant completo, este ID viene de la relación User->Empresa.
        // Si no la tiene directamente (Owner), y está suplantando, la leemos de la sesión.
        // Por ahora lo haremos de la forma directa:
        return Auth::user()->empresa_id ?? session('impersonated_tenant_id');
    }

    /**
     * Listado principal (Read)
     */
    public function index(Request $request)
    {
        $empresaId = $this->getEmpresaId();

        if (!$empresaId) {
            return redirect()->route('dashboard')->withErrors('No tienes una empresa asignada activa.');
        }

        // Búsqueda en Vivo
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $query = Titular::where('empresa_id', $empresaId);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('dni', 'like', "%{$search}%")
                  ->orWhere('n_afiliado', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");

                // Lógica de descompresión del Número de Afiliado/Obra Social
                // Formato: 1 + DNI(7/8) + DigitoHijo(2) = Ej: 12233344401
                if (preg_match('/^1(\d{7,8})\d{2}$/', $search, $matches)) {
                    $dniExtraido = $matches[1];
                    $q->orWhere('dni', $dniExtraido);
                }
            });
        }

        $titulares = $query->orderBy('nombre', 'asc')->paginate($perPage)->appends(request()->query());

        // Respuesta JSON para el LiveSearch
        if ($request->ajax()) {
            return response()->json([
                'html' => view('dashboards.tenant.partials.titulares_table_rows', compact('titulares', 'search'))->render(),
                'pagination' => (string) $titulares->links('pagination::bootstrap-5')
            ]);
        }

        return view('dashboards.tenant.titulares.index', compact('titulares', 'search', 'perPage'));
    }

    /**
     * Crear un nuevo Titular (Create)
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'dni' => 'required|string|max:20',
            'email' => 'nullable|email',
            'telefono' => 'nullable|string',
            'foto_perfil' => 'nullable|image|max:3072' // max 3MB
        ]);

        $rutafoto = null;
        if($request->hasFile('foto_perfil')) {
            $rutafoto = $request->file('foto_perfil')->store('avatares/titulares', 'public');
        }

        Titular::create([
            'empresa_id' => $this->getEmpresaId(),
            'nombre' => $request->nombre,
            'dni' => $request->dni,
            'cuil' => $request->cuil ?? null,
            'n_afiliado' => $request->n_afiliado,
            'resolucion' => $request->resolucion,
            'foto_perfil' => $rutafoto
        ]);

        return redirect()->back()->with('success', 'Referente/Titular añadido correctamente.');
    }

    /**
     * Actualizar Titular (Update)
     */
    public function update(Request $request, $id)
    {
        $titular = Titular::where('empresa_id', $this->getEmpresaId())->findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'dni' => 'required|string|max:20',
            'foto_perfil' => 'nullable|image|max:3072'
        ]);

        $rutafoto = $titular->foto_perfil;
        if($request->hasFile('foto_perfil')) {
            $rutafoto = $request->file('foto_perfil')->store('avatares/titulares', 'public');
        }

        $titular->update([
            'nombre' => $request->nombre,
            'dni' => $request->dni,
            'cuil' => $request->cuil,
            'n_afiliado' => $request->n_afiliado,
            'resolucion' => $request->resolucion,
            'foto_perfil' => $rutafoto
        ]);

        return redirect()->back()->with('success', 'Datos del Titular actualizados.');
    }

    /**
     * Eliminar Titular (Delete)
     */
    public function destroy($id)
    {
        $titular = Titular::where('empresa_id', $this->getEmpresaId())->findOrFail($id);
        $titular->delete();

        return redirect()->route('tenant.titulares.index')->with('success', 'Titular eliminado exitosamente.');
    }

    // -- REPORTES Y EXPORTACIÓN --

    public function exportExcel()
    {
        $titulares = Titular::where('empresa_id', $this->getEmpresaId())->orderBy('nombre')->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=padrón_titulares.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($titulares) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8
            fputcsv($file, ['ID Titular', 'Nombre Completo', 'DNI', 'CUIL', 'Nº Afiliado', 'Resolución'], ';');

            foreach ($titulares as $row) {
                fputcsv($file, [
                    $row->id,
                    $row->nombre,
                    $row->dni,
                    $row->cuil,
                    $row->n_afiliado,
                    $row->resolucion
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf()
    {
        $titulares = Titular::where('empresa_id', $this->getEmpresaId())->orderBy('nombre')->get();
        $pdf = Pdf::loadView('dashboards.tenant.titulares.pdf', compact('titulares'));
        return $pdf->download('padrón_titulares.pdf');
    }

    public function importTemplate()
    {
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=plantilla_base_titulares.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, ['Apellido y Nombre', 'DNI', 'N° Afiliado/a', 'Dirección', 'Número de teléfono', 'N° de Resolución'], ';');
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
                if (count($row) < 2 || empty(trim($row[0]))) continue;

                $nombre = $row[0];
                $dni = preg_replace('/[^0-9]/', '', $row[1] ?? '');
                $n_afiliado = $row[2] ?? null;
                $direccion = $row[3] ?? null;
                $tel = $row[4] ?? null;
                $resolucion = $row[5] ?? null;

                // Validación Upsert (Insertar o Actualizar) para no chocar con SQL UNIQUE
                if ($dni) {
                    $existente = Titular::where('dni', $dni)->first();
                    if ($existente) {
                        // Si ya existe y es de mi empresa, actualizo datos. Si es de otra, lo ignoro.
                        if ($existente->empresa_id == $this->getEmpresaId() || empty($existente->empresa_id)) {
                            $existente->update([
                                'empresa_id' => $this->getEmpresaId(),
                                'nombre' => trim($nombre),
                                'n_afiliado' => trim($n_afiliado),
                                'resolucion' => trim($resolucion)
                            ]);
                        }
                        $count++;
                        continue; 
                    }
                }

                Titular::create([
                    'empresa_id' => $this->getEmpresaId(),
                    'nombre' => trim($nombre),
                    'dni' => trim($dni),
                    'n_afiliado' => trim($n_afiliado),
                    'resolucion' => trim($resolucion)
                ]);
                
                $count++;
            }
            fclose($file);
            return redirect()->back()->with('success', "El lote masivo ($count Titulares) se ha cargado con éxito.");
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Error general en la importación. Guarde su archivo como CSV separado por comas: ' . $e->getMessage());
        }
    }
}
