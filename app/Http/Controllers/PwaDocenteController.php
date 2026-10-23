<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Docente;
use App\Models\Familiar;
use App\Models\Titular;
use App\Models\Escuela;
use App\Models\ExpedienteAlumno;
use App\Models\NovedadAuditoria;
use App\Models\NotificacionSistema;
use Illuminate\Support\Facades\Schema;

class PwaDocenteController extends Controller
{
    /**
     * Obtiene el docente activo para la sesión o demo de APOS La Rioja
     */
    private function resolveDocenteActivo(Request $request): Docente
    {
        $empresaIdDemo = 2; // APOS - Obra Social Provincial
        $docente = Docente::where('empresa_id', $empresaIdDemo)
            ->when($request->input('docente_id') ?? $request->query('docente_id'), fn($q, $id) => $q->where('id', $id))
            ->first();

        if (!$docente) {
            $docente = Docente::first();
        }

        return $docente;
    }

    private function ensureApprovalColumns(): void
    {
        ExpedienteAlumno::ensureWorkflowSchema();
    }

    /**
     * Vista de simulación del Ecosistema del Docente/Terapeuta
     */
    public function demo(Request $request)
    {
        $this->ensureApprovalColumns();
        $configAreas = ExpedienteAlumno::getConfigAreas();
        $empresaIdDemo = 2; // APOS - Obra Social Provincial
        $docenteReal = $this->resolveDocenteActivo($request);
        $docenteIdDemo = $docenteReal ? $docenteReal->id : 21;
        $docenteNombre = $docenteReal ? ('Prof. ' . $docenteReal->nombre_completo . ' (APOS La Rioja)') : 'Prof. María Eugenia López (APOS La Rioja)';

        // Lista de docentes de APOS para poder cambiar de docente en la demo si se desea
        $docentesApos = Docente::where('empresa_id', $empresaIdDemo)->orderBy('nombre')->get();

        // Pre-Expedientes iniciados en Mesa de Entrada APOS que vienen "En Camino" para que la docente los tome y complete Escuela, Horarios y CUD
        $resolucionesEnCamino = ExpedienteAlumno::with(['alumno.diagnostico', 'titular', 'escuela', 'docente'])
            ->where(function ($q) use ($docenteIdDemo) {
                $q->where('docente_id', $docenteIdDemo)
                  ->orWhere('estado_auditoria', 'en_camino');
            })
            ->whereIn('estado_auditoria', ['en_camino', 'pendiente'])
            ->orderByDesc('updated_at')
            ->limit(8)
            ->get();

        // Carga de Expedientes Reales de la Base de Datos para el Docente
        $expedientesReales = ExpedienteAlumno::with(['alumno', 'titular', 'escuela', 'facturas'])
            ->where('docente_id', $docenteIdDemo)
            ->orderByDesc('updated_at')
            ->get();

        $alumnosDemo = [];
        $montoCobrado = 0;
        $montoPretendido = 0;

        if ($expedientesReales->count() > 0) {
            foreach ($expedientesReales as $exp) {
                $vbAuditor = (bool)($exp->visto_bueno_auditor ?? ($exp->estado_auditoria === 'aprobado'));
                $okArea1 = (bool)($exp->ok_area_1 ?? ($exp->estado_auditoria === 'aprobado'));
                $okArea2 = (bool)($exp->ok_area_2 ?? ($exp->estado_auditoria === 'aprobado'));
                $vbJefe = (bool)($exp->visto_bueno_jefe ?? $okArea2);

                if ($exp->estado_auditoria === 'aprobado') {
                    $pct = 100;
                } elseif ($okArea1) {
                    $pct = 85;
                } elseif ($vbAuditor) {
                    $pct = 65;
                } elseif ($exp->estado_auditoria === 'en_camino') {
                    $pct = 20;
                } else {
                    $pct = 45;
                }

                $horasMesCalc = (int)($exp->horas_mensuales_asignadas ?? 60);
                if ($horasMesCalc > 0 && $horasMesCalc <= 3) {
                    $horasMesCalc = $horasMesCalc * 20; // 3 hs/día x 20 días hábiles = 60 hs/mes
                }
                $montoExp = $horasMesCalc * 6500;

                if ($exp->estado_auditoria === 'aprobado') {
                    $montoCobrado += $montoExp;
                    $estadoLabel = 'aprobado';
                } elseif ($exp->estado_auditoria === 'rechazado') {
                    $estadoLabel = 'rechazado';
                } else {
                    $montoPretendido += $montoExp;
                    $estadoLabel = $exp->estado_auditoria === 'en_camino' ? 'en_camino' : 'pendiente';
                }

                $nombreAlumno = $exp->alumno ? $exp->alumno->nombre_completo : 'Alumno #' . $exp->alumno_id;
                $nombreTitular = $exp->titular ? $exp->titular->nombre_completo : ($exp->alumno && $exp->alumno->titular ? $exp->alumno->titular->nombre_completo : 'Titular APOS');
                $nroPreExp = $exp->nro_pre_expediente ?: ('PRE-EXP-2026-' . str_pad((string)$exp->id, 4, '0', STR_PAD_LEFT));
                $nroResolucion = $exp->tieneResolucionOficial() ? $exp->nro_resolucion : $nroPreExp;

                if ($exp->estado_auditoria === 'aprobado') {
                    $pasoLabel = "✅ Resolución Emitida ({$exp->nro_resolucion}) • Cobro Habilitado";
                } elseif ($okArea1 && !$okArea2) {
                    $pasoLabel = "⏳ En " . ($configAreas['area_2'] ?? 'Área N° 2') . " (Auditoría y " . ($configAreas['area_1'] ?? 'Área N° 1') . " OK)";
                } elseif ($vbAuditor && !$okArea1) {
                    $pasoLabel = "⏳ En " . ($configAreas['area_1'] ?? 'Área N° 1') . " (Auditoría Docente OK)";
                } elseif ($exp->estado_auditoria === 'en_camino') {
                    $pasoLabel = "📥 Pre-Expediente {$nroPreExp} (Mesa de Entrada) • Tocar para completar";
                } else {
                    $pasoLabel = "🔍 En Auditoría Docente ({$nroPreExp})";
                }

                $alumnosDemo[] = (object)[
                    'id' => $exp->id,
                    'alumno_id' => $exp->alumno_id,
                    'nombre' => $nombreAlumno,
                    'titular_nombre' => $nombreTitular,
                    'nro_pre_expediente' => $nroPreExp,
                    'nro_resolucion' => $nroResolucion,
                    'curso' => ($exp->escuela->nombre_completo ?? 'Escuela Asignada') . " • " . ($exp->tieneResolucionOficial() ? "Res: {$exp->nro_resolucion}" : "Pre-Exp: {$nroPreExp}"),
                    'estado' => $estadoLabel,
                    'progreso' => $pct,
                    'visto_bueno_auditor' => $vbAuditor,
                    'visto_bueno_jefe' => $vbJefe,
                    'paso_label' => $pasoLabel
                ];
            }
        }

        // Asegurar que exista el requisito de Constancia de CBU Bancario en TipoDocumento
        $existeReqCbu = \App\Models\TipoDocumento::where('empresa_id', $empresaIdDemo)
            ->where('entidad_tipo', 'docente')
            ->where('nombre', 'LIKE', '%CBU%')
            ->exists();

        if (!$existeReqCbu) {
            \App\Models\TipoDocumento::create([
                'empresa_id' => $empresaIdDemo,
                'entidad_tipo' => 'docente',
                'nombre' => 'Constancia de CBU Bancario (22 dígitos habilitado para Pago)',
                'descripcion' => 'Requisito excluyente para operar y recibir transferencias de Contabilidad APOS / e-SIDIF',
                'es_obligatorio' => true,
                'vencimiento_dias' => null,
            ]);
        }

        $tiposDocumentos = \App\Models\TipoDocumento::where('empresa_id', $empresaIdDemo)
            ->where('entidad_tipo', 'docente')
            ->get();

        foreach ($tiposDocumentos as $tipo) {
            $subido = \App\Models\DocumentoSubido::where('tipo_documento_id', $tipo->id)
                ->where('entidad_id', $docenteIdDemo)
                ->where('entidad_tipo', 'docente')
                ->latest()
                ->first();

            if (!$subido && str_contains(strtoupper($tipo->nombre), 'CBU') && !empty($docenteReal->cbu)) {
                $tipo->estado_subida = 'aprobado';
                $tipo->comentarios = 'CBU Verificado: ' . $docenteReal->cbu;
            } else {
                $tipo->estado_subida = $subido ? $subido->estado : 'sin_entregar';
                $tipo->comentarios = $subido ? $subido->comentarios_auditor : null;
            }
        }

        return view('pwa.docente.dashboard', compact(
            'docenteReal',
            'docenteIdDemo',
            'docenteNombre',
            'docentesApos',
            'resolucionesEnCamino',
            'montoCobrado',
            'montoPretendido',
            'alumnosDemo',
            'tiposDocumentos'
        ));
    }

    /**
     * Guardar el CBU Bancario (22 dígitos) de la Docente en su tabla de requerimientos
     */
    public function guardarCbuDocente(Request $request)
    {
        $this->ensureApprovalColumns();
        $docente = $this->resolveDocenteActivo($request);

        $cbu = preg_replace('/\D/', '', (string)$request->input('cbu', ''));
        if (strlen($cbu) !== 22) {
            return response()->json([
                'ok' => false,
                'message' => 'El CBU bancario debe contener exactamente 22 dígitos numéricos.'
            ], 422);
        }

        $docente->cbu = $cbu;
        if ($request->filled('banco')) {
            $docente->banco = trim((string)$request->input('banco'));
        }
        $docente->save();

        if (Schema::hasTable('notificaciones_sistema')) {
            NotificacionSistema::create([
                'empresa_id' => $docente->empresa_id ?? 2,
                'titulo' => '🏦 CBU Bancario Registrado por Docente',
                'mensaje' => "La docente {$docente->nombre_completo} registró su CBU ({$cbu}) para Contabilidad / Orden de Pago.",
                'link' => route('auditor.docentes.legajos'),
                'leido' => false,
            ]);
        }

        return response()->json([
            'ok' => true,
            'cbu' => $cbu,
            'banco' => $docente->banco ?? 'Banco Rioja S.A.U.',
            'message' => "✅ CBU Bancario ({$cbu}) registrado y verificado en tu tabla de requerimientos. Ya estás habilitada para operar y recibir Orden de Pago de Contabilidad."
        ]);
    }

    /**
     * Endpoint en Tiempo Real para que la App de la Docente se refleje sola automáticamente
     * cuando Administración emite una "Resolución en Camino" o cuando el Auditor / Jefe de Área da el Visto Bueno.
     */
    public function liveStatus(Request $request)
    {
        $this->ensureApprovalColumns();
        $docente = $this->resolveDocenteActivo($request);
        $docenteId = $docente ? $docente->id : 1;

        $expedientes = ExpedienteAlumno::with(['alumno.diagnostico', 'titular', 'escuela'])
            ->where('docente_id', $docenteId)
            ->orderByDesc('updated_at')
            ->get();

        $montoCobrado = 0;
        $montoPretendido = 0;
        $enCaminoList = [];
        $aprobadosIds = [];

        foreach ($expedientes as $exp) {
            $horasMes = (int)($exp->horas_mensuales_asignadas ?? 60);
            if ($horasMes > 0 && $horasMes <= 3) $horasMes *= 20;
            $monto = $horasMes * 6500;

            if ($exp->estado_auditoria === 'aprobado') {
                $montoCobrado += $monto;
                $aprobadosIds[] = [
                    'id' => $exp->id,
                    'nro_resolucion' => $exp->nro_resolucion,
                    'alumno' => $exp->alumno ? $exp->alumno->nombre_completo : 'Alumno',
                    'titular' => $exp->titular ? $exp->titular->nombre_completo : 'Titular',
                ];
            } elseif ($exp->estado_auditoria !== 'rechazado') {
                $montoPretendido += $monto;
            }

            if ($exp->estado_auditoria === 'en_camino') {
                $enCaminoList[] = [
                    'expediente_id' => $exp->id,
                    'alumno_id' => $exp->alumno_id,
                    'titular_id' => $exp->titular_id,
                    'escuela_id' => $exp->escuela_id,
                    'nro_resolucion' => $exp->nro_resolucion,
                    'alumno_nombre' => $exp->alumno ? $exp->alumno->nombre_completo : 'Alumno APOS',
                    'alumno_dni' => $exp->alumno ? $exp->alumno->dni : '',
                    'titular_nombre' => $exp->titular ? $exp->titular->nombre_completo : ($exp->alumno && $exp->alumno->titular ? $exp->alumno->titular->nombre_completo : 'Titular APOS'),
                    'titular_afiliado' => $exp->titular ? $exp->titular->nro_afiliado : '',
                    'escuela_nombre' => $exp->escuela ? $exp->escuela->nombre_completo : 'Escuela Provincial',
                    'diagnostico' => $exp->diagnostico ?: ($exp->alumno && $exp->alumno->diagnostico ? $exp->alumno->diagnostico->nombre : 'CUD Activo'),
                    'horarios_atencion' => $exp->horarios_atencion ?: 'Lunes a Viernes de 08:00 a 11:00 hs (3 hs/día)',
                ];
            }
        }

        return response()->json([
            'ok' => true,
            'docente_id' => $docenteId,
            'monto_cobrado' => $montoCobrado,
            'monto_cobrado_fmt' => number_format($montoCobrado, 0, ',', '.'),
            'monto_pretendido' => $montoPretendido,
            'monto_pretendido_fmt' => number_format($montoPretendido, 0, ',', '.'),
            'en_camino_count' => count($enCaminoList),
            'en_camino' => $enCaminoList,
            'aprobados_count' => count($aprobadosIds),
            'aprobados' => $aprobadosIds,
            'updated_signature' => md5(json_encode([$montoCobrado, $montoPretendido, count($enCaminoList), count($aprobadosIds)])),
        ]);
    }

    /**
     * VINCULACIÓN INMEDIATA: Cuando el docente señala un alumno y carga/toma una Resolución
     * Genera automáticamente el vínculo Alumno + Padre + Docente + Resolución, cambia el estado
     * del expediente a 'pendiente' para el Auditor y dispara la Notificación en Tiempo Real.
     */
    public function vincularAlumnoResolucion(Request $request)
    {
        $docente = $this->resolveDocenteActivo($request);
        $empresaId = $docente->empresa_id ?? 2;

        $alumno = null;
        if ($request->filled('alumno_id')) {
            $alumno = Familiar::with(['titular', 'escuela', 'diagnostico'])->find($request->input('alumno_id'));
        }
        if (!$alumno && $request->filled('alumno_dni')) {
            $alumno = Familiar::with(['titular', 'escuela', 'diagnostico'])
                ->where('dni', 'LIKE', '%' . trim($request->input('alumno_dni')) . '%')
                ->first();
        }
        if (!$alumno && $request->filled('alumno_nombre')) {
            $term = trim($request->input('alumno_nombre'));
            $alumno = Familiar::with(['titular', 'escuela', 'diagnostico'])
                ->where('nombre', 'LIKE', "%{$term}%")
                ->first();
        }

        if (!$alumno) {
            $alumno = Familiar::with(['titular', 'escuela', 'diagnostico'])->where('empresa_id', $empresaId)->first();
        }

        $titular = $alumno ? $alumno->titular : null;
        if (!$titular && $request->filled('titular_id')) {
            $titular = Titular::find($request->input('titular_id'));
        }

        $escuela = $alumno ? $alumno->escuela : null;
        if ($request->filled('escuela_id')) {
            $escuela = Escuela::find($request->input('escuela_id')) ?? $escuela;
        } elseif ($request->filled('escuela_nombre')) {
            $escuela = Escuela::where('nombre', 'LIKE', '%' . trim($request->input('escuela_nombre')) . '%')->first() ?? $escuela;
        }

        $nroInput = trim((string)$request->input('nro_resolucion', ''));
        $expExistente = ExpedienteAlumno::where('alumno_id', $alumno->id)->first();

        $nroPreExp = $expExistente ? $expExistente->nro_pre_expediente : null;
        if (!$nroPreExp) {
            if ($nroInput !== '' && !str_starts_with($nroInput, 'RES-')) {
                $nroPreExp = is_numeric($nroInput)
                    ? ('PRE-EXP-' . date('Y') . '-' . str_pad($nroInput, 4, '0', STR_PAD_LEFT))
                    : $nroInput;
            } else {
                $nroPreExp = ExpedienteAlumno::generarSiguientePreExpediente();
            }
        }

        $horarios = $request->input('horarios_atencion') ?: ($expExistente->horarios_atencion ?? 'Lunes a Viernes de 08:00 a 11:00 hs (3 hs/día)');
        $horasDiarias = min(3, max(1, (int)$request->input('horas_diarias', 3)));
        $horasMes = $horasDiarias * 20;
        $diagnosticoTexto = $request->input('diagnostico') ?: ($alumno->diagnostico_detalle ?? ($alumno->diagnostico->nombre ?? 'CUD Activo'));

        $this->ensureApprovalColumns();
        $configAreas = ExpedienteAlumno::getConfigAreas();

        // 1. Actualizar vínculo directo en la tabla familiares (Alumno -> Docente + Escuela + Pre-Expediente)
        $alumno->docente_id = $docente->id;
        if ($escuela) {
            $alumno->escuela_id = $escuela->id;
        }
        $alumno->resolucion = $nroPreExp;
        $alumno->save();

        // 2. Crear o actualizar el ExpedienteAlumno poniéndolo en Etapa 3 ('auditoria_docente')
        // La Resolución Oficial Secuenciada recién se emite cuando pase por Auditoría -> Área N° 1 -> Área N° 2
        $expediente = ExpedienteAlumno::updateOrCreate(
            [
                'empresa_id' => $empresaId,
                'alumno_id' => $alumno->id,
            ],
            [
                'docente_id' => $docente->id,
                'titular_id' => $titular ? $titular->id : $alumno->titular_id,
                'escuela_id' => $escuela ? $escuela->id : $alumno->escuela_id,
                'nro_pre_expediente' => $nroPreExp,
                'etapa_flujo' => 'auditoria_docente',
                'origen_resolucion' => 'pre_expediente_mesa_entrada',
                'nro_resolucion' => "EN TRÁMITE ({$nroPreExp})",
                'diagnostico' => $diagnosticoTexto,
                'horarios_atencion' => $horarios,
                'horas_mensuales_asignadas' => $horasMes,
                'estado_auditoria' => 'pendiente',
                'ok_mesa_entrada' => true,
                'ok_docente_padre' => true,
                'visto_bueno_auditor' => false,
                'ok_area_1' => false,
                'ok_area_2' => false,
                'visto_bueno_jefe' => false,
                'responsable_mesa_entrada' => $configAreas['mesa_entrada'] ?? 'Mesa de Entrada APOS',
                'motivo_rechazo' => null,
            ]
        );

        $docenteNom = $docente->nombre_completo;
        $expediente->marcarProcedimientosAreaCumplidos('mesa_entrada', $configAreas['mesa_entrada'] ?? 'Mesa de Entrada APOS');
        $expediente->marcarProcedimientosAreaCumplidos('docente_padre', $docenteNom);
        $alumnoNom = $alumno->nombre_completo . ($alumno->dni ? " (DNI {$alumno->dni})" : '');
        $titularNom = $titular ? ($titular->nombre_completo . " - Afiliado N° {$titular->nro_afiliado}") : 'Titular APOS';
        $escuelaNom = $escuela ? $escuela->nombre_completo : 'Escuela Provincial';
        $resTexto = "Pre-Expediente N° {$nroPreExp}";

        $descripcionEvento = "La docente {$docenteNom} completó los datos del {$resTexto} para el alumno {$alumnoNom} (Titular: {$titularNom}) en {$escuelaNom} ({$horarios}). Pasa a Auditoría Docente → " . ($configAreas['area_1'] ?? 'Área N° 1') . " → " . ($configAreas['area_2'] ?? 'Área N° 2') . ".";

        // 3. Crear registro en NovedadAuditoria (Bandeja del Auditor)
        $novedad = NovedadAuditoria::create([
            'empresa_id' => $empresaId,
            'docente_id' => $docente->id,
            'expediente_id' => $expediente->id,
            'tipo_novedad' => 'vinculacion_con_resolucion',
            'descripcion' => $descripcionEvento,
            'estado' => 'pendiente',
        ]);

        // 4. Disparar Notificación Inmediata en la Campanita y Popup del Auditor / Admin
        if (Schema::hasTable('notificaciones_sistema')) {
            NotificacionSistema::create([
                'empresa_id' => $empresaId,
                'titulo' => "🔔 Pre-Expediente {$nroPreExp} Completado por Docente",
                'mensaje' => "{$docenteNom} completó datos de {$alumno->nombre_completo} (Titular: " . ($titular->nombre_completo ?? 'APOS') . "). Pasa a Auditoría → " . ($configAreas['area_1'] ?? 'Área N° 1') . " → " . ($configAreas['area_2'] ?? 'Área N° 2') . ".",
                'link' => route('auditor.docentes.legajos'),
                'leido' => false,
            ]);
        }

        return response()->json([
            'ok' => true,
            'success' => true,
            'expediente_id' => $expediente->id,
            'novedad_id' => $novedad->id,
            'docente' => $docenteNom,
            'alumno' => $alumno->nombre_completo,
            'titular' => $titular ? $titular->nombre_completo : 'Titular APOS',
            'nro_afiliado' => $titular ? $titular->nro_afiliado : '',
            'escuela' => $escuelaNom,
            'nro_pre_expediente' => $nroPreExp,
            'nro_resolucion' => $nroPreExp,
            'estado_auditoria' => 'pendiente',
            'message' => "✅ ¡Pre-Expediente {$nroPreExp} completado por Docente y Padre! Ya ingresó a la bandeja de Auditoría Docente (luego pasará a " . ($configAreas['area_1'] ?? 'Área N° 1') . " y " . ($configAreas['area_2'] ?? 'Área N° 2') . " para otorgar la Resolución Secuenciada Oficial)."
        ]);
    }

    /**
     * Procesador de Archivos Docentes (Vía Móvil) -> También notifica en vivo al Auditor
     */
    public function uploadDocument(Request $request)
    {
        $request->validate([
            'documento' => 'required|file|max:15240',
            'tipo_documento' => 'required|string',
            'alumno_nombre' => 'nullable|string'
        ]);

        try {
            $docente = $this->resolveDocenteActivo($request);
            $empresaId = $docente->empresa_id ?? 2;
            $file = $request->file('documento');
            $path = $file->store('documentos_docentes', 'public');

            $tipoDoc = \App\Models\TipoDocumento::where('nombre', $request->tipo_documento)->first();

            if ($tipoDoc) {
                \App\Models\DocumentoSubido::create([
                    'empresa_id' => $empresaId,
                    'tipo_documento_id' => $tipoDoc->id,
                    'entidad_tipo' => 'docente',
                    'entidad_id' => $docente->id,
                    'ruta_archivo' => $path,
                    'estado' => 'pendiente',
                    'fecha_vencimiento' => $tipoDoc->vencimiento_dias ? \Carbon\Carbon::now()->addDays($tipoDoc->vencimiento_dias) : null
                ]);
            }

            // Crear también en DocenteDocumento para el legajo del auditor
            if (Schema::hasTable('docente_documentos')) {
                \App\Models\DocenteDocumento::create([
                    'docente_id' => $docente->id,
                    'tipo_documento' => $request->tipo_documento,
                    'ruta_archivo' => $path,
                    'estado_auditoria' => 'pendiente',
                    'comentarios' => $request->alumno_nombre ? "Vinculado a alumno: {$request->alumno_nombre}" : 'Subido desde App Móvil Docente',
                ]);
            }

            $alumnoCtx = $request->alumno_nombre && $request->alumno_nombre !== 'Paciente Desconocido'
                ? " para el alumno {$request->alumno_nombre}"
                : "";

            NovedadAuditoria::create([
                'empresa_id' => $empresaId,
                'docente_id' => $docente->id,
                'tipo_novedad' => 'documento_subido_docente',
                'descripcion' => "La docente {$docente->nombre_completo} subió '{$request->tipo_documento}'{$alumnoCtx}. Pendiente de visado por Auditoría.",
                'estado' => 'pendiente',
            ]);

            if (Schema::hasTable('notificaciones_sistema')) {
                NotificacionSistema::create([
                    'empresa_id' => $empresaId,
                    'titulo' => '📄 Nuevo Documento Subido por Docente',
                    'mensaje' => "{$docente->nombre_completo} adjuntó {$request->tipo_documento}{$alumnoCtx}.",
                    'link' => route('auditor.docentes.legajos'),
                    'leido' => false,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'El archivo fue recibido y el Auditor ya fue notificado en tiempo real.',
                'path' => $path
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al subir el archivo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Búsqueda en Vivo (AJAX) para PWA sobre el Padrón Real de APOS La Rioja
     * Soporta buscar por: N° de Resolución, Nombre/Apellido del Padre o Hijo, DNI o N° de Afiliado
     */
    public function search(Request $request)
    {
        $term = trim($request->query('q', ''));
        $type = $request->query('type');

        if (!$term && $type !== 'alumnos_by_titular') {
            return response()->json([]);
        }

        if ($type === 'titulares') {
            $titulares = Titular::with(['familiares.escuela', 'familiares.diagnostico'])
                ->where(function ($q) use ($term) {
                    $q->where('nombre', 'LIKE', "%{$term}%")
                      ->orWhere('dni', 'LIKE', "%{$term}%")
                      ->orWhere('n_afiliado', 'LIKE', "%{$term}%")
                      ->orWhereHas('familiares', function ($fq) use ($term) {
                          $fq->where('nombre', 'LIKE', "%{$term}%")
                             ->orWhere('dni', 'LIKE', "%{$term}%");
                      });
                })
                ->take(20)
                ->get()
                ->map(function ($t) {
                    $t->nombre = $t->nombre_completo;
                    $t->nro_afiliado = $t->n_afiliado ?: $t->dni;
                    foreach ($t->familiares as $f) {
                        $f->nombre = $f->nombre_completo;
                        $exp = ExpedienteAlumno::where('alumno_id', $f->id)->first();
                        $f->nro_resolucion = $exp ? $exp->nro_resolucion : ($f->resolucion ?: null);
                        $f->horarios_atencion = $exp ? $exp->horarios_atencion : null;
                    }
                    return $t;
                });

            return response()->json($titulares);
        }

        if ($type === 'alumnos' || $type === 'resolucion') {
            $this->ensureApprovalColumns();
            $alumnoIdsPorResolucion = ExpedienteAlumno::where(function ($q) use ($term) {
                    $q->where('nro_resolucion', 'LIKE', "%{$term}%")
                      ->orWhere('nro_pre_expediente', 'LIKE', "%{$term}%");
                })
                ->pluck('alumno_id')
                ->toArray();

            $query = Familiar::with(['escuela', 'titular', 'diagnostico', 'docente']);

            if ($term) {
                $query->where(function ($q) use ($term, $alumnoIdsPorResolucion) {
                    $q->where('nombre', 'LIKE', "%{$term}%")
                      ->orWhere('dni', 'LIKE', "%{$term}%")
                      ->orWhere('resolucion', 'LIKE', "%{$term}%")
                      ->orWhere('diagnostico_detalle', 'LIKE', "%{$term}%");

                    if (!empty($alumnoIdsPorResolucion)) {
                        $q->orWhereIn('id', $alumnoIdsPorResolucion);
                    }

                    $q->orWhereHas('titular', function ($tq) use ($term) {
                        $tq->where('nombre', 'LIKE', "%{$term}%")
                           ->orWhere('dni', 'LIKE', "%{$term}%")
                           ->orWhere('n_afiliado', 'LIKE', "%{$term}%");
                    });
                });
            }

            if ($request->filled('titular_id')) {
                $query->where('titular_id', $request->query('titular_id'));
            }

            $alumnos = $query->take(25)->get()->map(function ($a) {
                $a->nombre = $a->nombre_completo;
                if ($a->titular) {
                    $a->titular->nombre = $a->titular->nombre_completo;
                    $a->titular->nro_afiliado = $a->titular->n_afiliado ?: $a->titular->dni;
                }
                if ($a->escuela) {
                    $a->escuela->nombre = $a->escuela->nombre_completo;
                }
                $exp = ExpedienteAlumno::where('alumno_id', $a->id)->first();
                $a->nro_pre_expediente = $exp && $exp->nro_pre_expediente ? $exp->nro_pre_expediente : ('PRE-EXP-2026-' . str_pad((string)$a->id, 4, '0', STR_PAD_LEFT));
                $a->nro_resolucion = $exp && $exp->tieneResolucionOficial() ? $exp->nro_resolucion : $a->nro_pre_expediente;
                $a->estado_expediente = $exp ? $exp->estado_auditoria : 'disponible';
                $a->horarios_atencion = $exp ? $exp->horarios_atencion : 'Lunes a Viernes de 08:00 a 11:00 hs (3 hs/día)';
                $a->horas_mensuales_asignadas = 60;
                return $a;
            });

            return response()->json($alumnos);
        }

        if ($type === 'alumnos_by_titular') {
            $alumnos = Familiar::with(['escuela', 'titular', 'diagnostico'])
                ->where('titular_id', $request->query('titular_id'))
                ->take(15)
                ->get()
                ->map(function ($a) {
                    $a->nombre = $a->nombre_completo;
                    $exp = ExpedienteAlumno::where('alumno_id', $a->id)->first();
                    $a->nro_resolucion = $exp ? $exp->nro_resolucion : ($a->resolucion ?: null);
                    $a->horarios_atencion = $exp ? $exp->horarios_atencion : 'Lunes a Viernes (Máx. 3 hs/día)';
                    return $a;
                });

            return response()->json($alumnos);
        }

        if ($type === 'escuelas') {
            $escuelas = Escuela::where(function ($q) use ($term) {
                    $q->where('nombre', 'LIKE', "%{$term}%");
                    if (Schema::hasColumn('escuelas', 'cue')) {
                        $q->orWhere('cue', 'LIKE', "%{$term}%");
                    }
                    if (Schema::hasColumn('escuelas', 'direccion')) {
                        $q->orWhere('direccion', 'LIKE', "%{$term}%");
                    }
                })
                ->take(20)
                ->get()
                ->map(function ($e) {
                    $e->nombre = $e->nombre_completo;
                    return $e;
                });

            return response()->json($escuelas);
        }

        return response()->json([]);
    }
}
