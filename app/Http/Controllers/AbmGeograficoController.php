<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AbmGeograficoController extends Controller
{
    /**
     * Devuelve la vista principal del ABM Geográfico (Provincias y Localidades)
     */
    public function index(Request $request)
    {
        // Paginación personalizable (Por defecto 10, pero el usuario elige en la UI)
        $perPage = $request->input('per_page', 10);
        
        // Búsqueda en vivo
        $search = $request->input('search');

        $query = \App\Models\Localidad::with('provincia')->orderBy('nombre', 'asc');

        if (!empty($search)) {
            $query->where('nombre', 'like', '%' . $search . '%')
                  ->orWhereHas('provincia', function($q) use ($search) {
                      $q->where('nombre', 'like', '%' . $search . '%');
                  });
        }

        $localidades = $query->paginate($perPage)->appends(request()->query());
        $provincias = \App\Models\Provincia::orderBy('nombre', 'asc')->get();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('dashboards.partials.geografia_table_rows', compact('localidades'))->render(),
                'pagination' => (string) $localidades->links('pagination::bootstrap-5')
            ]);
        }

        // The original return view for owner_geografia
        return view('dashboards.owner_geografia', compact('localidades', 'provincias', 'perPage', 'search'));
    }

    /**
     * Crea una nueva Localidad
     */
    public function storeLocalidad(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'provincia_id' => 'required|exists:provincias,id'
        ]);

        \App\Models\Localidad::create([
            'nombre' => $request->nombre,
            'provincia_id' => $request->provincia_id
        ]);

        return redirect()->back()->with('success', 'Localidad agregada correctamente al padrón.');
    }

    /**
     * Elimina una Localidad
     */
    public function destroyLocalidad($id)
    {
        $localidad = \App\Models\Localidad::findOrFail($id);
        $localidad->delete();
        
        return redirect()->back()->with('success', 'Localidad eliminada exitosamente.');
    }
}
