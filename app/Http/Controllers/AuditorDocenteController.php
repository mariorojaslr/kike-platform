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
    private function ensureApprovalColumns(): void
    {
        if (Schema::hasTable('expedientes_alumno') && !Schema::hasColumn('expedientes_alumno', 'visto_bueno_auditor')) {
            Schema::table('expedientes_alumno', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->boolean('visto_bueno_auditor')->default(false)->after('estado_auditoria');
                $table->boolean('visto_bueno_jefe')->default(false)->after('visto_bueno_auditor');
                $table->timestamp('aprobado_auditor_at')->nullable()->after('visto_bueno_jefe');
                $table->timestamp('aprobado_jefe_at')->nullable()->after('aprobado_auditor_at');
            });
        }
    }

    /**
     * Dashboard de Auditoría de Docentes + Bandeja Inmediata en Vivo al alcance de la mano
     */
    public function indexLegajos()
    {
        $this->ensureApprovalColumns();
        $docentes = Docente::with('documentos')->orderBy('nombre')->get();

        $totalDocentes = $docentes->count();
        $alDia = $docentes->where('estado_legajo', 'al_dia')->count();
        $incompletos = $docentes->where('estado_legajo', 'incompleto')->count();
        $suspendidos = $docentes->where('estado_legajo', 'suspendido')->count();

        // Novedades en tiempo real (vinculaciones de alumno, resoluciones cargadas, documentos)
        $novedades = NovedadAuditoria::with(['docente', 'expediente.alumno.titular', 'expediente.escuela', 'factura'])
            ->orderByDesc('created_at')
            ->limit(25)
            ->get();

        // Expedientes pendientes de visado inmediato por el Auditor / Jefe de Área (al alcance de 1 clic)
        $expedientesPendientes = ExpedienteAlumno::with(['docente', 'alumno.titular', 'titular', 'escuela', 'facturas'])
            ->whereIn('estado_auditoria', ['pendiente', 'en_camino'])
            ->orderByDesc('updated_at')
            ->get();

        // Últimos expedientes activos/aprobados vinculados (para que el auditor siempre vea las vinculaciones recientes)
        $expedientesRecientes = ExpedienteAlumno::with(['docente', 'alumno.titular', 'titular', 'escuela'])
            ->orderByDesc('updated_at')
            ->limit(15)
            ->get();

        // Listas para alta rápida de Resolución / Vinculación desde mesa de entrada o auditoría
        $alumnosApos = Familiar::with(['titular', 'escuela'])->orderBy('nombre')->get();
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
     * Alta o Emisión de Resolución APOS (Administración / Mesa de Entrada / Auditoría)
     * Vincula Padre Titular + Hijo Alumno + Docente + Escuela + Horas (máx 3 hs/día)
     * y lo envía "En Camino" a la App de la Docente o a la Bandeja de Visto Bueno.
     */
    public function registrarResolucionVinculo(Request $request)
    {
        $this->ensureApprovalColumns();

        $request->validate([
            'alumno_id' => 'required|exists:familiares,id',
            'docente_id' => 'required|exists:docentes,id',
            'nro_resolucion' => 'nullable|string|max:120',
            'escuela_id' => 'nullable|exists:escuelas,id',
            'horarios_atencion' => 'nullable|string|max:255',
            'horas_diarias' => 'nullable|integer|min:1|max:3',
            'horas_mensuales_asignadas' => 'nullable|integer|min:1|max:60',
        ]);

        $alumno = Familiar::with(['titular', 'escuela'])->findOrFail($request->alumno_id);
        $docente = Docente::findOrFail($request->docente_id);
        $titular = $alumno->titular;
        $escuelaId = $request->escuela_id ?: $alumno->escuela_id;
        $escuela = $escuelaId ? Escuela::find($escuelaId) : null;
        $empresaId = $docente->empresa_id ?? 2;

        $nroRes = trim((string)$request->nro_resolucion);
        if ($nroRes === '') {
            $nroRes = 'RES-APOS-' . date('Y') . '-' . str_pad((string)$alumno->id, 3, '0', STR_PAD_LEFT);
        } elseif (is_numeric($nroRes)) {
            $nroRes = 'RES-' . $nroRes;
        }

        $horasDiarias = min(3, max(1, (int)$request->input('horas_diarias', 3)));
        $horasMes = $horasDiarias * 20;

        // Vincular en tabla familiares y titulares para que la Resolución traiga siempre Padre + Hijo
        $alumno->docente_id = $docente->id;
        $alumno->resolucion = $nroRes;
        if ($escuelaId) {
            $alumno->escuela_id = $escuelaId;
        }
        $alumno->save();

        if ($titular) {
            $titular->resolucion = $nroRes;
            $titular->save();
        }

        $accion = $request->input('accion_inmediata', 'en_camino');
        if ($accion === 'aprobar_ya') {
            $estadoInicial = 'aprobado';
            $vbAuditor = true;
            $vbJefe = true;
        } elseif ($accion === 'en_camino') {
            $estadoInicial = 'en_camino';
            $vbAuditor = false;
            $vbJefe = false;
        } else {
            $estadoInicial = 'pendiente';
            $vbAuditor = false;
            $vbJefe = false;
        }

        $exp = ExpedienteAlumno::updateOrCreate(
            [
                'empresa_id' => $empresaId,
                'alumno_id' => $alumno->id,
            ],
            [
                'docente_id' => $docente->id,
                'titular_id' => $titular ? $titular->id : $alumno->titular_id,
                'escuela_id' => $escuelaId,
                'origen_resolucion' => 'resolucion_apos',
                'nro_resolucion' => $nroRes,
                'diagnostico' => $alumno->diagnostico_detalle ?: 'CUD Activo',
                'horarios_atencion' => $request->horarios_atencion ?: "Lunes a Viernes de 08:00 a 11:00 hs ({$horasDiarias} hs/día)",
                'horas_mensuales_asignadas' => $horasMes,
                'estado_auditoria' => $estadoInicial,
                'visto_bueno_auditor' => $vbAuditor,
                'visto_bueno_jefe' => $vbJefe,
                'aprobado_auditor_at' => $vbAuditor ? now() : null,
                'aprobado_jefe_at' => $vbJefe ? now() : null,
                'motivo_rechazo' => null,
            ]
        );

        $titularTexto = $titular ? ($titular->nombre_completo . " (Af. {$titular->nro_afiliado})") : 'Titular APOS';

        NovedadAuditoria::create([
            'empresa_id' => $empresaId,
            'docente_id' => $docente->id,
            'expediente_id' => $exp->id,
            'tipo_novedad' => $estadoInicial === 'en_camino' ? 'resolucion_en_camino' : 'alta_resolucion_vinculo',
            'descripcion' => ($estadoInicial === 'en_camino' ? "📬 Resolución {$nroRes} emitida y enviada 'En Camino' a la docente {$docente->nombre_completo}" : "Se registró la Resolución {$nroRes} para la docente {$docente->nombre_completo}") . " • Alumno: {$alumno->nombre_completo} • Padre/Titular: {$titularTexto} ({$horasDiarias} hs/día).",
            'estado' => $estadoInicial === 'aprobado' ? 'aprobado' : 'pendiente',
        ]);

        if (Schema::hasTable('notificaciones_sistema')) {
            NotificacionSistema::create([
                'empresa_id' => $empresaId,
                'titulo' => "📬 Resolución {$nroRes} " . ($estadoInicial === 'en_camino' ? "Enviada en Camino a Docente" : "Vinculada"),
                'mensaje' => "Titular: {$titularTexto} • Alumno: {$alumno->nombre_completo} • Docente: {$docente->nombre_completo} ({$horasDiarias} hs/día).",
                'link' => route('auditor.docentes.legajos'),
                'leido' => false,
            ]);
        }

        return back()->with('success', "✅ Resolución {$nroRes} emitida para el Titular {$titularTexto} y su hijo/a {$alumno->nombre_completo} ({$horasDiarias} hs/día). Ya le aparece 'En Camino' a la Docente {$docente->nombre_completo} en su App en tiempo real.");
    }

    /**
     * Dar Visto Bueno del Auditor y/o del Jefe de Área al Expediente de Alumno
     */
    public function aprobarExpediente(Request $request, $id)
    {
        $this->ensureApprovalColumns();
        $exp = ExpedienteAlumno::with(['docente', 'alumno.titular', 'titular'])->findOrFail($id);

        if ($request->filled('nro_resolucion')) {
            $exp->nro_resolucion = trim($request->input('nro_resolucion'));
            $exp->origen_resolucion = 'resolucion_apos';
        } elseif (!$exp->nro_resolucion || str_contains($exp->nro_resolucion, 'TRÁMITE') || str_contains($exp->nro_resolucion, 'PENDIENTE')) {
            $exp->nro_resolucion = 'RES-APOS-' . date('Y') . '-' . str_pad((string)$exp->id, 4, '0', STR_PAD_LEFT);
            $exp->origen_resolucion = 'autorizacion_expresa_auditor';
        }

        $rolAprobacion = $request->input('rol_aprobacion', 'ambos');

        if ($rolAprobacion === 'auditor') {
            $exp->visto_bueno_auditor = true;
            $exp->aprobado_auditor_at = now();
        } elseif ($rolAprobacion === 'jefe') {
            $exp->visto_bueno_jefe = true;
            $exp->aprobado_jefe_at = now();
        } else {
            $exp->visto_bueno_auditor = true;
            $exp->visto_bueno_jefe = true;
            $exp->aprobado_auditor_at = $exp->aprobado_auditor_at ?: now();
            $exp->aprobado_jefe_at = now();
        }

        if ($exp->visto_bueno_auditor && $exp->visto_bueno_jefe) {
            $exp->estado_auditoria = 'aprobado';
            NovedadAuditoria::where('expediente_id', $exp->id)->update(['estado' => 'aprobado']);
        } else {
            $exp->estado_auditoria = 'pendiente';
        }

        $exp->motivo_rechazo = null;
        $exp->save();

        $alumnoNom = $exp->alumno ? $exp->alumno->nombre_completo : 'Alumno';
        $docenteNom = $exp->docente ? $exp->docente->nombre_completo : 'Docente';
        $titularNom = $exp->titular ? $exp->titular->nombre_completo : ($exp->alumno && $exp->alumno->titular ? $exp->alumno->titular->nombre_completo : 'Titular');

        $etiquetaAprob = $exp->estado_auditoria === 'aprobado'
            ? "Trámite Finalizado (Visto Bueno Auditor + Jefe de Área)"
            : ($exp->visto_bueno_auditor ? "Visto Bueno Auditor OK (Falta Jefe de Área)" : "Visto Bueno Jefe de Área OK (Falta Auditor)");

        if (Schema::hasTable('notificaciones_sistema')) {
            NotificacionSistema::create([
                'empresa_id' => $exp->empresa_id ?? 2,
                'titulo' => "✅ {$etiquetaAprob} ({$exp->nro_resolucion})",
                'mensaje' => "Docente: {$docenteNom} • Alumno: {$alumnoNom} (Titular: {$titularNom}).",
                'link' => route('auditor.docentes.legajos'),
                'leido' => false,
            ]);
        }

        return back()->with('success', "✅ {$etiquetaAprob} registrado en vivo para la Resolución {$exp->nro_resolucion} — Alumno: {$alumnoNom} | Docente: {$docenteNom}.");
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
