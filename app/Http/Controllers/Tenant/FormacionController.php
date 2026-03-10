<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Formacion;

class FormacionController extends Controller
{
    /**
     * Lista todas las formaciones/especialidades. Solo Lectura en el Tenant.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        // Mostramos las formaciones globales (empresa_id nulo) y las propias de la empresa actual
        $empresaId = auth()->user()->empresa_id;

        $query = Formacion::where(function($q) use ($empresaId) {
            $q->whereNull('empresa_id');
            if ($empresaId) {
                $q->orWhere('empresa_id', $empresaId);
            }
        });

        if ($search) {
            $query->where('nombre', 'like', "%{$search}%");
        }

        $formaciones = $query->orderBy('nombre')->paginate($perPage)->appends(request()->query());

        if ($request->ajax()) {
            return response()->json([
                'html' => view('dashboards.tenant.partials.formaciones_table_rows', compact('formaciones'))->render(),
                'pagination' => (string) $formaciones->links('pagination::bootstrap-5')
            ]);
        }

        return view('dashboards.tenant.formaciones.index', compact('formaciones', 'search', 'perPage'));
    }

    public function store(Request $request)
    {
        $request->validate(['nombre' => 'required|string|max:255']);
        Formacion::create([
            'nombre' => $request->nombre,
            'empresa_id' => auth()->user()->empresa_id
        ]);
        return redirect()->back()->with('success', 'Especialidad creada con éxito.');
    }

    public function update(Request $request, $id)
    {
        $request->validate(['nombre' => 'required|string|max:255']);
        $formacion = Formacion::findOrFail($id);
        
        // Evitar que editen maestras globales
        if (is_null($formacion->empresa_id)) {
            return redirect()->back()->with('error', 'No puedes editar una especialidad global del sistema.');
        }
        
        if ($formacion->empresa_id !== auth()->user()->empresa_id) {
            return redirect()->back()->with('error', 'Acceso denegado a esta especialidad.');
        }

        $formacion->update(['nombre' => $request->nombre]);
        return redirect()->back()->with('success', 'Especialidad actualizada.');
    }

    public function destroy($id)
    {
        $formacion = Formacion::findOrFail($id);

        if (is_null($formacion->empresa_id)) {
            return redirect()->back()->with('error', 'No puedes eliminar una especialidad global del sistema.');
        }

        if ($formacion->empresa_id !== auth()->user()->empresa_id) {
            return redirect()->back()->with('error', 'Acceso denegado a esta especialidad.');
        }

        $formacion->delete();
        return redirect()->back()->with('success', 'Especialidad eliminada correctamente.');
    }
}
