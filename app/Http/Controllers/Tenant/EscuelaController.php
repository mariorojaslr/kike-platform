<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Escuela;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class EscuelaController extends Controller
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

        $query = Escuela::where('empresa_id', $empresaId);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('cue', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $escuelas = $query->orderBy('nombre')->paginate($perPage)->appends(request()->query());

        if ($request->ajax()) {
            return response()->json([
                'html' => view('dashboards.tenant.partials.escuelas_table_rows', compact('escuelas', 'search'))->render(),
                'pagination' => (string) $escuelas->links('pagination::bootstrap-5')
            ]);
        }

        return view('dashboards.tenant.escuelas.index', compact('escuelas', 'search', 'perPage'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'cue' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string',
            'direccion' => 'nullable|string',
            'contacto_principal' => 'nullable|string',
        ]);

        Escuela::create([
            'empresa_id' => $this->getEmpresaId(),
            'nombre' => $request->nombre,
            'cue' => $request->cue,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
            'contacto_principal' => $request->contacto_principal,
            'activo' => 1
        ]);

        return redirect()->back()->with('success', 'Escuela/Institución añadida correctamente.');
    }

    public function update(Request $request, $id)
    {
        $escuela = Escuela::where('empresa_id', $this->getEmpresaId())->findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'cue' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string',
            'direccion' => 'nullable|string',
            'contacto_principal' => 'nullable|string',
        ]);

        $escuela->update([
            'nombre' => $request->nombre,
            'cue' => $request->cue,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
            'contacto_principal' => $request->contacto_principal,
        ]);

        return redirect()->back()->with('success', 'Ficha de la escuela actualizada.');
    }

    public function destroy($id)
    {
        $escuela = Escuela::where('empresa_id', $this->getEmpresaId())->findOrFail($id);
        
        $escuela->delete();

        return redirect()->back()->with('success', 'Escuela removida correctamente.');
    }

    // -- REPORTES Y EXPORTACIÓN --

    public function exportExcel()
    {
        $escuelas = Escuela::where('empresa_id', $this->getEmpresaId())->orderBy('nombre')->get();
        
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=padron_escuelas.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($escuelas) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM
            fputcsv($file, ['ID Escuela', 'Nombre Institucion', 'CUE', 'Direccion', 'Telefono', 'Email Contacto', 'Contacto/Dtor'], ';');

            foreach ($escuelas as $row) {
                fputcsv($file, [
                    $row->id,
                    $row->nombre,
                    $row->cue,
                    $row->direccion,
                    $row->telefono,
                    $row->email,
                    $row->contacto_principal
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf()
    {
        $escuelas = Escuela::where('empresa_id', $this->getEmpresaId())->orderBy('nombre')->get();
        // Usamos una vista pdf genérica o creamos una view específica (tenant.escuelas.pdf)
        $pdf = Pdf::loadView('dashboards.tenant.escuelas.pdf', compact('escuelas'))->setPaper('a4', 'landscape');
        return $pdf->download('padron_escuelas.pdf');
    }

    public function importTemplate()
    {
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=plantilla_escuelas.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, ['NOMBRE_INSTITUCION', 'CUE', 'DIRECCION', 'TELEFONO', 'EMAIL_CONTACTO', 'DIRECTOR_CONTECTO'], ';');
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
                if ($isFirstRow) { $isFirstRow = false; continue; }
                if (count($row) < 1 || empty(trim($row[0]))) continue;

                $nombre = $row[0] ?? null;
                $cue = $row[1] ?? null;
                $dir = $row[2] ?? null;
                $tel = $row[3] ?? null;
                $email = $row[4] ?? null;
                $contacto = $row[5] ?? null;

                $escuela = Escuela::where('empresa_id', $this->getEmpresaId())
                                  ->where('nombre', trim($nombre))
                                  ->first();

                if ($escuela) {
                    $escuela->update([
                        'cue' => trim($cue) ?: $escuela->cue,
                        'direccion' => trim($dir) ?: $escuela->direccion,
                        'telefono' => trim($tel) ?: $escuela->telefono,
                        'email' => trim($email) ?: $escuela->email,
                        'contacto_principal' => trim($contacto) ?: $escuela->contacto_principal,
                    ]);
                } else {
                    Escuela::create([
                        'empresa_id' => $this->getEmpresaId(),
                        'nombre' => trim($nombre),
                        'cue' => trim($cue),
                        'direccion' => trim($dir),
                        'telefono' => trim($tel),
                        'email' => trim($email),
                        'contacto_principal' => trim($contacto),
                        'activo' => 1
                    ]);
                }
                
                $count++;
            }
            fclose($file);

            return redirect()->back()->with('success', "¡Excelente! Lote de $count escuelas importado y sincronizado exitosamente.");
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Error general verificando archivo. Asegúrese de guardar el Excel como "CSV delimitado por comillas/punto y coma": ' . $e->getMessage());
        }
    }
}
