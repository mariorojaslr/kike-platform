<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Docente;
use App\Models\Familiar;
use App\Models\Titular;
use App\Models\Escuela;
use App\Models\ExpedienteAlumno;
use App\Models\FacturaDocente;
use App\Models\NovedadAuditoria;
use App\Models\NotificacionSistema;
use App\Models\DocenteDocumento;
use Illuminate\Support\Facades\Schema;

class AuditorDocenteController extends Controller
{
    /**
     * Dashboard de Auditoría de Docentes + Bandeja Inmediata en Vivo al alcance de la mano
     */
    public function indexLegajos()
    {
        $docentes = Docente::with('documentos')->orderBy('apellido')->orderBy('nombre')->get();

        $totalDocentes = $docentes->count();
        $alDia = $docentes->where('estado_legajo', 'al_dia')->count();
        $incompletos = $docentes->where('estado_legajo', 'incompleto')->count();
        $suspendidos = $docentes->where('estado_legajo', 'suspendido')->count();

        // Novedades en tiempo real (vinculaciones de alumno, resoluciones cargadas, documentos)
        $novedades = NovedadAuditoria::with(['docente', 'expediente.alumno.titular', 'expediente.escuela', 'factura'])
            ->orderByDesc('created_at')
            ->limit(25)
            ->get();

        // Expedientes pendientes de visado inmediato por el Auditor (al alcance de 1 clic)
        $expedientesPendientes = ExpedienteAlumno::with(['docente', 'alumno.titular', 'titular', 'escuela', 'facturas'])
            ->where('estado_auditoria', 'pendiente')
            ->orderByDesc('updated_at')
            ->get();

        // Últimos expedientes activos/aprobados vinculados (para que el auditor siempre vea las vinculaciones recientes)
        $expedientesRecientes = ExpedienteAlumno::with(['docente', 'alumno.titular', 'titular', 'escuela'])
            ->orderByDesc('updated_at')
            ->limit(15)
            ->get();

        // Listas para alta rápida de Resolución / Vinculación desde mesa de entrada o auditoría
        $alumnosApos = Familiar::with(['titular', 'escuela'])->orderBy('apellido')->orderBy('nombre')->get();
        $escuelasApos = Escuela::orderBy('nombre')->get();

        return view('dashboards.auditor_docentes', compact(
            'docentes',
            'totalDocentes',
            'alDia',
            'incompletos',
            'suspendidos',
            'novedades',
            'expedientesPendientes',
            'expedientesRecientes',
            'alumnosApos',
            'escuelasApos'
        ));
    }

    /**
     * Feed de Novedades y Expedientes en Tiempo Real
     */
    public function novedades()
    {
        $novedades = NovedadAuditoria::with(['docente', 'expediente.alumno.titular', 'expediente.escuela', 'factura'])
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        $expedientesPendientes = ExpedienteAlumno::with(['docente', 'alumno.titular', 'titular', 'escuela', 'facturas'])
            ->where('estado_auditoria', 'pendiente')
            ->orderByDesc('updated_at')
            ->get();

        $facturasPendientes = FacturaDocente::with(['docente', 'alumno'])
            ->where('estado_auditoria', 'pendiente')
            ->orderByDesc('created_at')
            ->get();

        return view('dashboards.auditor_novedades', compact('novedades', 'expedientesPendientes', 'facturasPendientes'));
    }

    /**
     * Endpoint JSON en Tiempo Real para que la pantalla del Auditor detecte al instante
     * cualquier nueva vinculación Docente-Alumno-Padre o alta de Resolución sin recargar
     */
    public function apiNovedadesLive(Request $request)
    {
        $pendientesCount = ExpedienteAlumno::where('estado_auditoria', 'pendiente')->count();
        $novedadesCount = NovedadAuditoria::where('estado', 'pendiente')->count();
        $ultimaNovedad = NovedadAuditoria::with(['docente', 'expediente.alumno.titular', 'expediente.escuela'])
            ->orderByDesc('id')
            ->first();

        return response()->json([
            'pendientes_count' => $pendientesCount,
            'novedades_pendientes_count' => $novedadesCount,
            'latest_id' => $ultimaNovedad ? $ultimaNovedad->id : 0,
            'latest_docente' => $ultimaNovedad && $ultimaNovedad->docente ? $ultimaNovedad->docente->nombre_completo : null,
            'latest_descripcion' => $ultimaNovedad ? $ultimaNovedad->descripcion : null,
            'latest_time' => $ultimaNovedad ? $ultimaNovedad->created_at->diffForHumans() : null,
        ]);
    }

    /**
     * Alta o Vinculación de Resolución + Alumno + Padre + Docente desde Auditoría / Mesa de Entrada
     */
    public function registrarResolucionVinculo(Request $request)
    {
        $request->validate([
            'alumno_id' => 'required|exists:familiares,id',
            'docente_id' => 'required|exists:docentes,id',
            'nro_resolucion' => 'nullable|string|max:120',
            'escuela_id' => 'nullable|exists:escuelas,id',
            'horarios_atencion' => 'nullable|string|max:255',
            'horas_mensuales_asignadas' => 'nullable|integer|min:1|max:120',
        ]);

        $alumno = Familiar::with(['titular', 'escuela'])->findOrFail($request->alumno_id);
        $docente = Docente::findOrFail($request->docente_id);
        $titular = $alumno->titular;
        $escuelaId = $request->escuela_id ?: $alumno->escuela_id;
        $escuela = $escuelaId ? Escuela::find($escuelaId) : null;
        $empresaId = $docente->empresa_id ?? 2;

        $nroRes = trim((string)$request->nro_resolucion);
        $tieneRes = $nroRes !== '';

        // Vincular en tabla familiares
        $alumno->docente_id = $docente->id;
        if ($escuelaId) {
            $alumno->escuela_id = $escuelaId;
        }
        $alumno->save();

        // Crear o actualizar expediente en estado 'pendiente' (o 'aprobado' si el auditor ya lo deja visado)
        $estadoInicial = $request->input('accion_inmediata') === 'aprobar_ya' ? 'aprobado' : 'pendiente';

        $exp = ExpedienteAlumno::updateOrCreate(
            [
                'empresa_id' => $empresaId,
                'alumno_id' => $alumno->id,
            ],
            [
                'docente_id' => $docente->id,
                'titular_id' => $titular ? $titular->id : $alumno->titular_id,
                'escuela_id' => $escuelaId,
                'origen_resolucion' => $tieneRes ? 'resolucion_apos' : 'autorizacion_auditoria',
                'nro_resolucion' => $tieneRes ? $nroRes : ('AUT-AUDITOR-' . date('Y') . '-' . str_pad((string)$alumno->id, 3, '0', STR_PAD_LEFT)),
                'diagnostico' => $alumno->diagnostico_detalle ?: 'CUD Activo',
                'horarios_atencion' => $request->horarios_atencion ?: 'Lunes a Viernes de 08:00 a 11:00 hs (3 hs/día)',
                'horas_mensuales_asignadas' => $request->horas_mensuales_asignadas ?: 60,
                'estado_auditoria' => $estadoInicial,
                'motivo_rechazo' => null,
            ]
        );

        $titularTexto = $titular ? ($titular->nombre_completo . " (Af. {$titular->nro_afiliado})") : 'Titular APOS';
        $resLabel = $exp->nro_resolucion;

        NovedadAuditoria::create([
            'empresa_id' => $empresaId,
            'docente_id' => $docente->id,
            'expediente_id' => $exp->id,
            'tipo_novedad' => 'alta_resolucion_vinculo',
            'descripcion' => "Se dio de alta la Resolución {$resLabel} vinculando al alumno {$alumno->nombre_completo} (Padre/Titular: {$titularTexto}) con la docente {$docente->nombre_completo}" . ($escuela ? " en {$escuela->nombre_completo}." : "."),
            'estado' => $estadoInicial,
        ]);

        if (Schema::hasTable('notificaciones_sistema')) {
            NotificacionSistema::create([
                'empresa_id' => $empresaId,
                'titulo' => "🔔 Resolución {$resLabel} Vinculada",
                'mensaje' => "Alumno {$alumno->nombre_completo} (Titular: {$titularTexto}) vinculado con Docente {$docente->nombre_completo}.",
                'link' => route('auditor.docentes.legajos'),
                'leido' => false,
            ]);
        }

        return back()->with('success', "✅ Resolución {$resLabel} registrada: Alumno {$alumno->nombre_completo} + Titular {$titularTexto} vinculados con la Docente {$docente->nombre_completo}.");
    }

    /**
     * Aprobar Expediente de Alumno (y confirmar o asignar N° de Resolución / Autorización Expresa)
     */
    public function aprobarExpediente(Request $request, $id)
    {
        $exp = ExpedienteAlumno::with(['docente', 'alumno.titular', 'titular'])->findOrFail($id);

        if ($request->filled('nro_resolucion')) {
            $exp->nro_resolucion = trim($request->input('nro_resolucion'));
            $exp->origen_resolucion = 'resolucion_apos';
        } elseif (!$exp->nro_resolucion || str_contains($exp->nro_resolucion, 'TRÁMITE') || str_contains($exp->nro_resolucion, 'PENDIENTE')) {
            $exp->nro_resolucion = 'AUT-AUDITOR-APOS-' . date('Y') . '-' . str_pad((string)$exp->id, 4, '0', STR_PAD_LEFT);
            $exp->origen_resolucion = 'autorizacion_expresa_auditor';
        }

        $exp->estado_auditoria = 'aprobado';
        $exp->motivo_rechazo = null;
        $exp->save();

        // Marcar las novedades asociadas a este expediente como aprobadas
        NovedadAuditoria::where('expediente_id', $exp->id)->update(['estado' => 'aprobado']);

        $alumnoNom = $exp->alumno ? $exp->alumno->nombre_completo : 'Alumno';
        $docenteNom = $exp->docente ? $exp->docente->nombre_completo : 'Docente';
        $titularNom = $exp->titular ? $exp->titular->nombre_completo : ($exp->alumno && $exp->alumno->titular ? $exp->alumno->titular->nombre_completo : 'Titular');

        if (Schema::hasTable('notificaciones_sistema')) {
            NotificacionSistema::create([
                'empresa_id' => $exp->empresa_id ?? 2,
                'titulo' => "✅ Expediente Aprobado por Auditoría ({$exp->nro_resolucion})",
                'mensaje' => "Quedó activo el vínculo de {$docenteNom} con el alumno {$alumnoNom} (Titular: {$titularNom}).",
                'link' => route('auditor.docentes.legajos'),
                'leido' => false,
            ]);
        }

        return back()->with('success', "✅ Vínculo y Resolución ({$exp->nro_resolucion}) aprobados por Auditoría para {$alumnoNom} (Docente: {$docenteNom}).");
    }

    /**
     * Rechazar / Observar Expediente de Alumno con Argumentación
     */
    public function rechazarExpediente(Request $request, $id)
    {
        $request->validate(['motivo_rechazo' => 'required|string|max:1000']);

        $exp = ExpedienteAlumno::with(['docente', 'alumno'])->findOrFail($id);
        $exp->estado_auditoria = 'rechazado';
        $exp->motivo_rechazo = $request->motivo_rechazo;
        $exp->save();

        NovedadAuditoria::where('expediente_id', $exp->id)->update(['estado' => 'rechazado']);

        if (Schema::hasTable('notificaciones_sistema')) {
            NotificacionSistema::create([
                'empresa_id' => $exp->empresa_id ?? 2,
                'titulo' => "⚠️ Expediente Observado por Auditoría",
                'mensaje' => "Observación para " . ($exp->docente->nombre_completo ?? 'Docente') . ": {$request->motivo_rechazo}",
                'link' => route('auditor.docentes.legajos'),
                'leido' => false,
            ]);
        }

        return back()->with('success', 'Expediente observado/rechazado. Se notificó la causa a la docente.');
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

        return back()->with('success', "Documento '{$request->tipo_documento}' cargado y verificado exitosamente para {$docente->nombre_completo}.");
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
