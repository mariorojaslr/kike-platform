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

    /**
     * Muestra el panel de documentos subidos esperando auditoría
     */
    public function documentos()
    {
        $user = Auth::user();
        
        $documentos = \App\Models\DocumentoSubido::with('tipoDocumento')
                    ->where('empresa_id', $user->empresa_id)
                    ->orderByRaw("FIELD(estado, 'pendiente', 'observado', 'aprobado', 'rechazado')")
                    ->orderBy('created_at', 'desc')
                    ->paginate(15);
                    
        return view('dashboards.auditor_documentos', compact('documentos'));
    }

    /**
     * Permite al auditor cambiar el estado del documento subido
     */
    public function updateDocumentoStatus(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:aprobado,rechazado,observado,pendiente',
            'comentarios_auditor' => 'nullable|string|max:500'
        ]);

        $documento = \App\Models\DocumentoSubido::where('empresa_id', Auth::user()->empresa_id)->findOrFail($id);
        $documento->estado = $request->estado;
        
        if ($request->has('comentarios_auditor')) {
            $documento->comentarios_auditor = $request->comentarios_auditor;
        }
        
        $documento->save();

        return redirect()->back()->with('success', 'El documento ha sido marcado como: ' . strtoupper($request->estado));
    }
}
