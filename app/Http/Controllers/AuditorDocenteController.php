<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Docente;
use App\Models\ExpedienteAlumno;
use App\Models\FacturaDocente;
use App\Models\NovedadAuditoria;
use App\Models\DocenteDocumento;
use Illuminate\Support\Facades\Schema;

class AuditorDocenteController extends Controller
{
    /**
     * Dashboard de Auditoría de Legajos de Docentes
     */
    public function indexLegajos()
    {
        $docentes = Docente::with('documentos')->get();
        
        $totalDocentes = $docentes->count();
        $alDia = $docentes->where('estado_legajo', 'al_dia')->count();
        $incompletos = $docentes->where('estado_legajo', 'incompleto')->count();
        $suspendidos = $docentes->where('estado_legajo', 'suspendido')->count();

        return view('dashboards.auditor_docentes', compact(
            'docentes',
            'totalDocentes',
            'alDia',
            'incompletos',
            'suspendidos'
        ));
    }

    /**
     * Feed de Novedades y Expedientes en Tiempo Real
     */
    public function novedades()
    {
        $novedades = NovedadAuditoria::with(['docente', 'expediente', 'factura'])
                        ->orderBy('created_at', 'desc')
                        ->get();

        $expedientesPendientes = ExpedienteAlumno::with(['docente', 'alumno', 'escuela', 'facturas'])
                                    ->where('estado_auditoria', 'pendiente')
                                    ->get();

        $facturasPendientes = FacturaDocente::with(['docente', 'alumno'])
                                ->where('estado_auditoria', 'pendiente')
                                ->get();

        return view('dashboards.auditor_novedades', compact('novedades', 'expedientesPendientes', 'facturasPendientes'));
    }

    /**
     * Aprobar Expediente de Alumno
     */
    public function aprobarExpediente($id)
    {
        $exp = ExpedienteAlumno::findOrFail($id);
        $exp->estado_auditoria = 'aprobado';
        $exp->motivo_rechazo = null;
        $exp->save();

        return back()->with('success', 'Expediente del alumno aprobado exitosamente por Auditoría.');
    }

    /**
     * Rechazar Expediente de Alumno con Argumentación
     */
    public function rechazarExpediente(Request $request, $id)
    {
        $request->validate(['motivo_rechazo' => 'required|string|max:1000']);

        $exp = ExpedienteAlumno::findOrFail($id);
        $exp->estado_auditoria = 'rechazado';
        $exp->motivo_rechazo = $request->motivo_rechazo;
        $exp->save();

        return back()->with('success', 'Expediente observado/rechazado. Se notificó la causa al docente.');
    }

    /**
     * Aprobar Factura ARCA
     */
    public function aprobarFactura($id)
    {
        $factura = FacturaDocente::findOrFail($id);
        $factura->estado_auditoria = 'aprobado';
        $factura->motivo_rechazo = null;
        $factura->save();

        return back()->with('success', 'Factura ARCA aprobada en 1-clic. El saldo pasa a líquido disponible.');
    }

    /**
     * Rechazar Factura ARCA con Argumentación
     */
    public function rechazarFactura(Request $request, $id)
    {
        $request->validate(['motivo_rechazo' => 'required|string|max:1000']);

        $factura = FacturaDocente::findOrFail($id);
        $factura->estado_auditoria = 'rechazado';
        $factura->motivo_rechazo = $request->motivo_rechazo;
        $factura->save();

        return back()->with('success', 'Factura rechazada. Se envió notificación en tiempo real a la docente.');
    }

    /**
     * Aprobar Documento del Legajo
     */
    public function aprobarDocumentoLegajo($id)
    {
        $doc = DocenteDocumento::findOrFail($id);
        $doc->estado_auditoria = 'aprobado';
        $doc->motivo_rechazo = null;
        $doc->save();

        // Recalcular % legajo del docente
        $this->recalcularLegajoDocente($doc->docente_id);

        return back()->with('success', 'Documento de legajo aprobado.');
    }

    /**
     * Rechazar Documento del Legajo
     */
    public function rechazarDocumentoLegajo(Request $request, $id)
    {
        $request->validate(['motivo_rechazo' => 'required|string|max:1000']);

        $doc = DocenteDocumento::findOrFail($id);
        $doc->estado_auditoria = 'rechazado';
        $doc->motivo_rechazo = $request->motivo_rechazo;
        $doc->save();

        $this->recalcularLegajoDocente($doc->docente_id);

        return back()->with('success', 'Documento rechazado con motivo.');
    }

    /**
     * Cargar Documento de Legajo en nombre del Docente por el Auditor/Admin
     */
    public function subirDocumentoAuditor(Request $request)
    {
        $request->validate([
            'docente_id' => 'required|exists:docentes,id',
            'tipo_documento' => 'required|string',
            'documento' => 'required|file|max:15240',
            'fecha_vencimiento' => 'nullable|date',
            'es_frente_dorso' => 'nullable|boolean'
        ]);

        $docente = Docente::findOrFail($request->docente_id);
        $path = $request->file('documento')->store('documentos_docentes', 'public');

        DocenteDocumento::create([
            'docente_id' => $docente->id,
            'tipo_documento' => $request->tipo_documento,
            'ruta_archivo' => $path,
            'fecha_vencimiento' => $request->fecha_vencimiento,
            'estado_auditoria' => 'aprobado',
            'es_frente_dorso' => $request->has('es_frente_dorso'),
            'comentarios' => 'Documento verificado y cargado directamente por Auditoría/Administración',
        ]);

        $this->recalcularLegajoDocente($docente->id);

        return back()->with('success', "Documento '{$request->tipo_documento}' cargado y verificado exitosamente para {$docente->nombre}.");
    }

    protected function recalcularLegajoDocente($docenteId)
    {
        $docente = Docente::find($docenteId);
        if (!$docente) return;

        $docs = DocenteDocumento::where('docente_id', $docenteId)->get();
        $total = $docs->count();
        if ($total === 0) {
            $docente->porcentaje_legajo = 0;
            $docente->estado_legajo = 'incompleto';
            $docente->save();
            return;
        }

        $aprobados = $docs->where('estado_auditoria', 'aprobado')->count();
        $pct = (int) round(($aprobados / $total) * 100);

        $docente->porcentaje_legajo = $pct;
        $docente->estado_legajo = ($pct === 100) ? 'al_dia' : 'incompleto';
        $docente->save();
    }
}
