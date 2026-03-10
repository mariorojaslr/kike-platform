<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Factura;
use Illuminate\Support\Facades\Auth;

class AuditorController extends Controller
{
    /**
     * Muestra el panel de facturas pendientes de la empresa actual
     */
    public function index()
    {
        $user = Auth::user();
        
        // El auditor solo ve facturas de SU empresa (Tenant)
        // Por ahora cargamos todas, priorizando las pendientes
        $facturas = Factura::where('empresa_id', $user->empresa_id)
                    ->with('user') // Cargar al terapeuta
                    ->orderByRaw("FIELD(estado, 'pendiente', 'observada', 'aprobada', 'rechazada')")
                    ->orderBy('created_at', 'asc')
                    ->paginate(15);
                    
        return view('dashboards.auditor', compact('facturas'));
    }

    /**
     * Permite al auditor cambiar el estado de la factura (Aprobar/Rechazar)
     */
    public function updateStatus(Request $request, Factura $factura)
    {
        $request->validate([
            'estado' => 'required|in:aprobada,rechazada,pendiente',
            'notas_auditor' => 'nullable|string|max:500'
        ]);

        $factura->estado = $request->estado;
        if ($request->notas_auditor) {
            $factura->notas_auditor = $request->notas_auditor;
        }
        $factura->save();

        return redirect()->back()->with('success', 'El estado de la Factura #' . $factura->id . ' ha sido actualizado a: ' . strtoupper($request->estado));
    }
}
