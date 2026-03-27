<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Diagnostico;

class DiagnosticoController extends Controller
{
    /**
     * Lista todos los diagnósticos globales. Solo Lectura en el Tenant.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $query = Diagnostico::query();

        if ($search) {
            $query->where('nombre', 'like', "%{$search}%")
                  ->orWhere('codigo', 'like', "%{$search}%");
        }

        $diagnosticos = $query->orderBy('nombre')->paginate($perPage)->appends(request()->query());

        if ($request->ajax()) {
            return response()->json([
                'html' => view('dashboards.tenant.partials.diagnosticos_table_rows', compact('diagnosticos', 'search'))->render(),
                'pagination' => (string) $diagnosticos->links('pagination::bootstrap-5')
            ]);
        }

        return view('dashboards.tenant.diagnosticos.index', compact('diagnosticos', 'search', 'perPage'));
    }
}
