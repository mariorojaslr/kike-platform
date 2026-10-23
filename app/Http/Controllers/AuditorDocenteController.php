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
        ExpedienteAlumno::ensureWorkflowSchema();
    }

    /**
     * Guardar nombres personalizables de Área N° 1 y Área N° 2 (para actualizar en 1 clic con el cliente)
     */
    public function guardarConfigAreas(Request $request)
    {
        ExpedienteAlumno::saveConfigAreas($request->only([
            'mesa_entrada',
            'area_1',
            'area_1_req',
            'area_2',
            'area_2_req',
        ]));

        return back()->with('success', '✅ Nombres y requisitos de las Áreas del Circuito APOS actualizados en todo el sistema.');
    }

    /**
     * Dashboard de Auditoría de Docentes + Bandeja Inmediata en Vivo al alcance de la mano
     */
    public function indexLegajos()
    {
        $this->ensureApprovalColumns();
        $configAreas = ExpedienteAlumno::getConfigAreas();
        $docentes = Docente::with('documentos')->orderBy('nombre')->get();

        $totalDocentes = $docentes->count();
        $alDia = $docentes->where('estado_legajo', 'al_dia')->count();
        $incompletos = $docentes->where('estado_legajo', 'incompleto')->count();
        $suspendidos = $docentes->where('estado_legajo', 'suspendido')->count();

        // Novedades en tiempo real (vinculaciones de alumno, pre-expedientes, resoluciones emitidas, documentos)
        $novedades = NovedadAuditoria::with(['docente', 'expediente.alumno.titular', 'expediente.escuela', 'factura'])
            ->orderByDesc('created_at')
            ->limit(25)
            ->get();

        // Expedientes pendientes de visado por Auditoría / Área N° 1 / Área N° 2
        $expedientesPendientes = ExpedienteAlumno::with(['docente', 'alumno.titular', 'titular', 'escuela', 'facturas', 'cumplimientos.procedimiento'])
            ->whereIn('estado_auditoria', ['pendiente', 'en_camino'])
            ->orderByDesc('updated_at')
            ->get();

        // Últimos expedientes activos/aprobados vinculados
        $expedientesRecientes = ExpedienteAlumno::with(['docente', 'alumno.titular', 'titular', 'escuela', 'cumplimientos.procedimiento'])
            ->orderByDesc('updated_at')
            ->limit(15)
            ->get();

        // Listas para alta rápida de Pre-Expediente en Mesa de Entrada
        $alumnosApos = Familiar::with(['titular', 'escuela'])->orderBy('nombre')->get();
        $escuelasApos = Escuela::orderBy('nombre')->get();
        $procedimientosPorArea = \App\Models\ProcedimientoArea::where('activo', true)->orderBy('orden')->get()->groupBy('area_codigo');

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
            'escuelasApos',
            'configAreas',
            'procedimientosPorArea'
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
     * Etapa 1 — Mesa de Entrada APOS: Alta de Pre-Expediente (Padre Titular + Hijo Paciente)
     * Inicia el trámite con un N° de Pre-Expediente interno (ej. PRE-EXP-2026-0001).
     * La Resolución Oficial Secuenciada recién se otorga al finalizar todas las áreas (Auditoría -> Área N° 1 -> Área N° 2).
     */
    public function registrarResolucionVinculo(Request $request)
    {
        $this->ensureApprovalColumns();
        $configAreas = ExpedienteAlumno::getConfigAreas();

        $request->validate([
            'alumno_id' => 'required|exists:familiares,id',
            'docente_id' => 'nullable|exists:docentes,id',
            'nro_pre_expediente' => 'nullable|string|max:120',
            'nro_resolucion' => 'nullable|string|max:120',
            'escuela_id' => 'nullable|exists:escuelas,id',
            'horarios_atencion' => 'nullable|string|max:255',
            'horas_diarias' => 'nullable|integer|min:1|max:3',
            'horas_mensuales_asignadas' => 'nullable|integer|min:1|max:60',
        ]);

        $alumno = Familiar::with(['titular', 'escuela'])->findOrFail($request->alumno_id);
        $docente = $request->filled('docente_id') ? Docente::find($request->docente_id) : null;
        $titular = $alumno->titular;
        $escuelaId = $request->escuela_id ?: $alumno->escuela_id;
        $empresaId = $docente ? ($docente->empresa_id ?? 2) : 2;

        $nroPreExp = trim((string)($request->nro_pre_expediente ?: $request->nro_resolucion));
        if ($nroPreExp === '' || str_starts_with($nroPreExp, 'RES-')) {
            $nroPreExp = ExpedienteAlumno::generarSiguientePreExpediente();
        } elseif (is_numeric($nroPreExp)) {
            $nroPreExp = 'PRE-EXP-' . date('Y') . '-' . str_pad($nroPreExp, 4, '0', STR_PAD_LEFT);
        }

        $horasDiarias = min(3, max(1, (int)$request->input('horas_diarias', 3)));
        $horasMes = $horasDiarias * 20;

        $accion = $request->input('accion_inmediata', 'mesa_entrada');

        if ($accion === 'aprobar_ya') {
            $nroResFinal = ExpedienteAlumno::generarSiguienteResolucionSecuenciada();
            $estadoInicial = 'aprobado';
            $etapaFlujo = 'resolucion_emitida';
            $okDocentePadre = true;
            $vbAuditor = true;
            $okArea1 = true;
            $okArea2 = true;
            $vbJefe = true;
        } elseif ($docente) {
            // Si en Mesa de Entrada ya indicaron quién es la docente
            $nroResFinal = 'EN TRÁMITE (' . $nroPreExp . ')';
            $estadoInicial = 'pendiente';
            $etapaFlujo = 'docente_padre';
            $okDocentePadre = true;
            $vbAuditor = false;
            $okArea1 = false;
            $okArea2 = false;
            $vbJefe = false;
        } else {
            // Mesa de Entrada: Nace SIN DUEÑO (docente_id = null). El padre le dará este N° a la docente para que lo busque y se lo asigne.
            $nroResFinal = 'EN TRÁMITE (' . $nroPreExp . ')';
            $estadoInicial = 'en_camino';
            $etapaFlujo = 'mesa_entrada';
            $okDocentePadre = false;
            $vbAuditor = false;
            $okArea1 = false;
            $okArea2 = false;
            $vbJefe = false;
        }

        // Vincular en tabla familiares y titulares para que al buscar por N° de Pre-Resolución traiga Padre + Hijo + Escuela
        if ($docente) {
            $alumno->docente_id = $docente->id;
        }
        $alumno->resolucion = $estadoInicial === 'aprobado' ? $nroResFinal : $nroPreExp;
        if ($escuelaId) {
            $alumno->escuela_id = $escuelaId;
        }
        $alumno->save();

        if ($titular) {
            $titular->resolucion = $estadoInicial === 'aprobado' ? $nroResFinal : $nroPreExp;
            $titular->save();
        }

        $exp = ExpedienteAlumno::updateOrCreate(
            [
                'empresa_id' => $empresaId,
                'alumno_id' => $alumno->id,
            ],
            [
                'docente_id' => $docente ? $docente->id : null,
                'titular_id' => $titular ? $titular->id : $alumno->titular_id,
                'escuela_id' => $escuelaId,
                'nro_pre_expediente' => $nroPreExp,
                'etapa_flujo' => $etapaFlujo,
                'origen_resolucion' => $estadoInicial === 'aprobado' ? 'resolucion_apos' : 'mesa_entrada_pre_expediente',
                'nro_resolucion' => $nroResFinal,
                'diagnostico' => $alumno->diagnostico_detalle ?: 'CUD Activo',
                'horarios_atencion' => $request->horarios_atencion ?: "Lunes a Viernes de 08:00 a 11:00 hs ({$horasDiarias} hs/día)",
                'horas_mensuales_asignadas' => $horasMes,
                'estado_auditoria' => $estadoInicial,
                'ok_mesa_entrada' => true,
                'ok_docente_padre' => $okDocentePadre,
                'visto_bueno_auditor' => $vbAuditor,
                'ok_area_1' => $okArea1,
                'ok_area_2' => $okArea2,
                'visto_bueno_jefe' => $vbJefe,
                'responsable_mesa_entrada' => $configAreas['mesa_entrada'] ?? 'Mesa de Entrada APOS',
                'responsable_auditor' => $vbAuditor ? 'Auditoría Docente APOS' : null,
                'responsable_area_1' => $okArea1 ? ($configAreas['area_1'] ?? 'Área N° 1') : null,
                'responsable_area_2' => $okArea2 ? ($configAreas['area_2'] ?? 'Área N° 2') : null,
                'aprobado_auditor_at' => $vbAuditor ? now() : null,
                'fecha_area_1' => $okArea1 ? now() : null,
                'fecha_area_2' => $okArea2 ? now() : null,
                'aprobado_jefe_at' => $vbJefe ? now() : null,
                'motivo_rechazo' => null,
            ]
        );

        // Registrar tilde en tabla de cumplimientos de Mesa de Entrada
        $exp->marcarProcedimientosAreaCumplidos('mesa_entrada', $configAreas['mesa_entrada'] ?? 'Mesa de Entrada APOS');
        if ($okDocentePadre && $docente) {
            $exp->marcarProcedimientosAreaCumplidos('docente_padre', $docente->nombre_completo);
        }
        if ($vbAuditor) {
            $exp->marcarProcedimientosAreaCumplidos('auditoria_docente', 'Auditoría Docente APOS');
        }
        if ($okArea1) {
            $exp->marcarProcedimientosAreaCumplidos('area_1', $configAreas['area_1'] ?? 'Área N° 1');
        }
        if ($okArea2) {
            $exp->marcarProcedimientosAreaCumplidos('area_2', $configAreas['area_2'] ?? 'Área N° 2');
        }

        $titularTexto = $titular ? ($titular->nombre_completo . " (Af. {$titular->nro_afiliado})") : 'Titular APOS';
        $docenteTexto = $docente ? $docente->nombre_completo : 'SIN DOCENTE ASIGNADO (El padre le dará el N° ' . $nroPreExp . ' al docente para que lo busque y se lo asigne)';

        if ($docente) {
            NovedadAuditoria::create([
                'empresa_id' => $empresaId,
                'docente_id' => $docente->id,
                'expediente_id' => $exp->id,
                'tipo_novedad' => 'alta_resolucion_vinculo',
                'descripcion' => "📥 Mesa de Entrada registró el Pre-Expediente {$nroPreExp} (Padre/Titular: {$titularTexto} • Hijo/Alumno: {$alumno->nombre_completo}) con Docente {$docente->nombre_completo}.",
                'estado' => $estadoInicial === 'aprobado' ? 'aprobado' : 'pendiente',
            ]);
        }

        if (Schema::hasTable('notificaciones_sistema')) {
            NotificacionSistema::create([
                'empresa_id' => $empresaId,
                'titulo' => "📥 Pre-Resolución / Pre-Expediente {$nroPreExp} creado en Mesa de Entrada",
                'mensaje' => "Titular: {$titularTexto} • Hijo: {$alumno->nombre_completo} • Docente: {$docenteTexto}.",
                'link' => route('auditor.docentes.legajos'),
                'leido' => false,
            ]);
        }

        return back()->with('success', "✅ Pre-Resolución / Pre-Expediente {$nroPreExp} generado en Mesa de Entrada para el Padre {$titularTexto} y su hijo/a {$alumno->nombre_completo}. " . ($docente ? "Asignado a {$docente->nombre_completo}." : "Quedó disponible sin docente para que cuando el padre hable con la maestra, ella busque el N° {$nroPreExp} en su App y se lo asigne a sí misma."));
    }

    /**
     * Visado secuencial por Etapa del Circuito APOS:
     * - 'auditor' -> Aprueba Etapa 3 (Auditoría Docente) y pasa el Pre-Expediente a Área N° 1
     * - 'area_1'  -> Aprueba Etapa 4 (Área N° 1) y pasa el Pre-Expediente a Área N° 2
     * - 'area_2' / 'jefe' / 'ambos' -> Aprueba Etapa 5 (Área N° 2), finaliza el circuito y EMITE AUTOMÁTICAMENTE LA RESOLUCIÓN SECUENCIADA OFICIAL (RES-APOS-2026-XXXX) habilitando el pago docente.
     */
    public function aprobarExpediente(Request $request, $id)
    {
        $this->ensureApprovalColumns();
        $configAreas = ExpedienteAlumno::getConfigAreas();
        $exp = ExpedienteAlumno::with(['docente', 'alumno.titular', 'titular'])->findOrFail($id);

        if (!$exp->nro_pre_expediente) {
            $exp->nro_pre_expediente = ExpedienteAlumno::generarSiguientePreExpediente();
        }

        $rolAprobacion = $request->input('rol_aprobacion', 'ambos');
        $responsableInput = trim((string)$request->input('responsable_nombre', ''));

        $exp->ok_mesa_entrada = true;
        $exp->ok_docente_padre = true;
        $exp->marcarProcedimientosAreaCumplidos('mesa_entrada', $exp->responsable_mesa_entrada ?: ($configAreas['mesa_entrada'] ?? 'Mesa de Entrada APOS'));
        $exp->marcarProcedimientosAreaCumplidos('docente_padre', $exp->docente ? $exp->docente->nombre_completo : 'Docente APOS');

        if ($rolAprobacion === 'auditor') {
            $exp->visto_bueno_auditor = true;
            $exp->aprobado_auditor_at = now();
            $exp->responsable_auditor = $responsableInput ?: 'Auditoría Docente APOS';
            $exp->etapa_flujo = 'area_1';
            $exp->estado_auditoria = 'pendiente';
            if (!$exp->nro_resolucion || str_starts_with($exp->nro_resolucion, 'RES-')) {
                $exp->nro_resolucion = 'EN TRÁMITE (' . $exp->nro_pre_expediente . ')';
            }
            $exp->marcarProcedimientosAreaCumplidos('auditoria_docente', $exp->responsable_auditor);
            $etiquetaAprob = "Etapa 3: Visto Bueno Auditoría Docente OK (Tilde y Sello registrados) → Derivado a " . ($configAreas['area_1'] ?? 'Área N° 1');
        } elseif ($rolAprobacion === 'area_1') {
            $exp->visto_bueno_auditor = true;
            $exp->aprobado_auditor_at = $exp->aprobado_auditor_at ?: now();
            $exp->responsable_auditor = $exp->responsable_auditor ?: 'Auditoría Docente APOS';
            $exp->ok_area_1 = true;
            $exp->fecha_area_1 = now();
            $exp->responsable_area_1 = $responsableInput ?: ($configAreas['area_1'] ?? 'Área N° 1');
            $exp->etapa_flujo = 'area_2';
            $exp->estado_auditoria = 'pendiente';
            if (!$exp->nro_resolucion || str_starts_with($exp->nro_resolucion, 'RES-')) {
                $exp->nro_resolucion = 'EN TRÁMITE (' . $exp->nro_pre_expediente . ')';
            }
            $exp->marcarProcedimientosAreaCumplidos('auditoria_docente', $exp->responsable_auditor);
            $exp->marcarProcedimientosAreaCumplidos('area_1', $exp->responsable_area_1);
            $etiquetaAprob = "Etapa 4: Sello de Validación " . ($configAreas['area_1'] ?? 'Área N° 1') . " OK → Derivado a " . ($configAreas['area_2'] ?? 'Área N° 2');
        } elseif ($rolAprobacion === 'contabilidad') {
            $exp->visto_bueno_auditor = true;
            $exp->ok_area_1 = true;
            $exp->ok_area_2 = true;
            $exp->visto_bueno_jefe = true;
            $exp->ok_contabilidad = true;
            $exp->fecha_contabilidad = now();
            $exp->responsable_contabilidad = $responsableInput ?: ($configAreas['contabilidad'] ?? 'Contabilidad APOS');
            $exp->etapa_flujo = 'contabilidad';
            $exp->estado_auditoria = 'aprobado';
            if (!$exp->tieneResolucionOficial()) {
                $exp->nro_resolucion = ExpedienteAlumno::generarSiguienteResolucionSecuenciada();
            }
            $exp->marcarProcedimientosAreaCumplidos('auditoria_docente', $exp->responsable_auditor ?: 'Auditoría Docente APOS');
            $exp->marcarProcedimientosAreaCumplidos('area_1', $exp->responsable_area_1 ?: ($configAreas['area_1'] ?? 'Área N° 1'));
            $exp->marcarProcedimientosAreaCumplidos('area_2', $exp->responsable_area_2 ?: ($configAreas['area_2'] ?? 'Área N° 2'));
            $exp->marcarProcedimientosAreaCumplidos('contabilidad', $exp->responsable_contabilidad);
            $etiquetaAprob = "💳 Contabilidad Validada ({$exp->responsable_contabilidad}) → Expediente {$exp->nro_resolucion} incluido en lote de pago e-SIDIF / CBU Banco Rioja";
        } else {
            // 'area_2', 'jefe', o 'ambos' -> Completa todas las áreas previas y emite la RESOLUCIÓN OFICIAL SECUENCIADA
            $exp->visto_bueno_auditor = true;
            $exp->aprobado_auditor_at = $exp->aprobado_auditor_at ?: now();
            $exp->responsable_auditor = $exp->responsable_auditor ?: 'Auditoría Docente APOS';

            $exp->ok_area_1 = true;
            $exp->fecha_area_1 = $exp->fecha_area_1 ?: now();
            $exp->responsable_area_1 = $exp->responsable_area_1 ?: ($configAreas['area_1'] ?? 'Área N° 1');

            $exp->ok_area_2 = true;
            $exp->fecha_area_2 = now();
            $exp->responsable_area_2 = $responsableInput ?: ($configAreas['area_2'] ?? 'Área N° 2');

            $exp->visto_bueno_jefe = true;
            $exp->aprobado_jefe_at = now();
            $exp->etapa_flujo = 'resolucion_emitida';
            $exp->estado_auditoria = 'aprobado';

            if ($request->filled('nro_resolucion') && str_starts_with(trim($request->input('nro_resolucion')), 'RES-')) {
                $exp->nro_resolucion = trim($request->input('nro_resolucion'));
            } elseif (!$exp->tieneResolucionOficial()) {
                $exp->nro_resolucion = ExpedienteAlumno::generarSiguienteResolucionSecuenciada();
            }
            $exp->origen_resolucion = 'resolucion_apos';

            // Actualizar resolución definitiva en el alumno y titular
            if ($exp->alumno) {
                $exp->alumno->resolucion = $exp->nro_resolucion;
                $exp->alumno->save();
            }
            if ($exp->titular) {
                $exp->titular->resolucion = $exp->nro_resolucion;
                $exp->titular->save();
            }

            $exp->marcarProcedimientosAreaCumplidos('auditoria_docente', $exp->responsable_auditor);
            $exp->marcarProcedimientosAreaCumplidos('area_1', $exp->responsable_area_1);
            $exp->marcarProcedimientosAreaCumplidos('area_2', $exp->responsable_area_2);

            NovedadAuditoria::where('expediente_id', $exp->id)->update(['estado' => 'aprobado']);
            $etiquetaAprob = "🏛️ Expediente Completo (" . ($configAreas['area_2'] ?? 'Área N° 2') . " OK) → Emitida Resolución Secuenciada {$exp->nro_resolucion} y enviado a Contabilidad para Pago Docente";
        }

        $exp->motivo_rechazo = null;
        $exp->save();

        $alumnoNom = $exp->alumno ? $exp->alumno->nombre_completo : 'Alumno';
        $docenteNom = $exp->docente ? $exp->docente->nombre_completo : 'Docente';
        $titularNom = $exp->titular ? $exp->titular->nombre_completo : ($exp->alumno && $exp->alumno->titular ? $exp->alumno->titular->nombre_completo : 'Titular');

        if (Schema::hasTable('notificaciones_sistema')) {
            NotificacionSistema::create([
                'empresa_id' => $exp->empresa_id ?? 2,
                'titulo' => "✅ {$etiquetaAprob}",
                'mensaje' => "Pre-Exp: {$exp->nro_pre_expediente} • Docente: {$docenteNom} • Alumno: {$alumnoNom} (Titular: {$titularNom}).",
                'link' => route('auditor.docentes.legajos'),
                'leido' => false,
            ]);
        }

        return back()->with('success', "✅ {$etiquetaAprob} — Pre-Expediente {$exp->nro_pre_expediente} | Alumno: {$alumnoNom} | Docente: {$docenteNom}.");
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
