<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExpedienteAlumno;
use App\Models\ProcedimientoArea;
use App\Models\CumplimientoProcedimientoExpediente;
use App\Models\Familiar;
use App\Models\Docente;
use App\Models\Escuela;
use App\Models\NovedadAuditoria;
use App\Models\NotificacionSistema;
use Illuminate\Support\Facades\Schema;

class FlujoExpedienteController extends Controller
{
    /**
     * Muestra el Gestor Real del Circuito de Expedientes APOS con Tablas de Procedimientos por Área:
     * 1. Mesa de Entrada (Pre-Resolución sin docente o con docente)
     * 2. Área Docente (Auto-asignación por N° Pre-Resolución + CBU + Horario + Aval Directora)
     * 3. Auditoría Docente (Tabla de procedimientos + Tilde de validación)
     * 4. Área N° 1 (Tabla de procedimientos ["Procedimiento de validación"] + Sello y Tilde)
     * 5. Área N° 2 (Tabla de procedimientos ["Procedimiento de validación"] + Sello y Tilde -> Emite Resolución Secuenciada)
     * 6. Contabilidad (Valida Orden de Pago e-SIDIF / CBU Bancario Docente)
     */
    public function indexDemo(Request $request)
    {
        ExpedienteAlumno::ensureWorkflowSchema();
        $configAreas = ExpedienteAlumno::getConfigAreas();
        $filtroEtapa = $request->query('etapa', $request->route('etapa_fija', 'todas'));

        $procedimientosPorArea = ProcedimientoArea::where('activo', true)
            ->orderBy('orden')
            ->orderBy('id')
            ->get()
            ->groupBy('area_codigo');

        $etapasWorkflow = [
            (object)[
                'orden' => 1,
                'codigo' => 'mesa_entrada',
                'nombre' => '1. ' . ($configAreas['mesa_entrada'] ?? 'Mesa de Entrada'),
                'color' => 'info',
                'icono' => 'fas fa-inbox',
                'descripcion' => 'El padre pide el trámite. Se carga Padre + Hijo + Escuela y nace la Pre-Resolución (puede ir sin docente hasta que el padre hable con la maestra).',
                'procedimientos' => $procedimientosPorArea->get('mesa_entrada', collect()),
            ],
            (object)[
                'orden' => 2,
                'codigo' => 'docente_padre',
                'nombre' => '2. Área Docente y Directora',
                'color' => 'primary',
                'icono' => 'fas fa-chalkboard-teacher',
                'descripcion' => 'La docente busca el N° de Pre-Resolución, se lo asigna, registra su CBU, horario (máx. 3 hs/día) y la Directora valida asistencia.',
                'procedimientos' => $procedimientosPorArea->get('docente_padre', collect()),
            ],
            (object)[
                'orden' => 3,
                'codigo' => 'auditoria_docente',
                'nombre' => '3. Auditoría Docente',
                'color' => 'warning',
                'icono' => 'fas fa-user-check',
                'descripcion' => 'Verifica cumplimiento documental, tilda su tabla de procedimientos y pasa al Área N° 1.',
                'procedimientos' => $procedimientosPorArea->get('auditoria_docente', collect()),
            ],
            (object)[
                'orden' => 4,
                'codigo' => 'area_1',
                'nombre' => '4. ' . ($configAreas['area_1'] ?? 'Área N° 1'),
                'color' => 'secondary',
                'icono' => 'fas fa-clipboard-check',
                'descripcion' => 'Tabla de procedimientos del Área 1. Al poner el tilde de validación queda cerrado aquí y pasa al Área N° 2.',
                'procedimientos' => $procedimientosPorArea->get('area_1', collect()),
            ],
            (object)[
                'orden' => 5,
                'codigo' => 'area_2',
                'nombre' => '5. ' . ($configAreas['area_2'] ?? 'Área N° 2'),
                'color' => 'danger',
                'icono' => 'fas fa-stamp',
                'descripcion' => 'Tabla de procedimientos del Área 2. Al poner el tilde final emite la Resolución Secuenciada y pasa a Contabilidad.',
                'procedimientos' => $procedimientosPorArea->get('area_2', collect()),
            ],
            (object)[
                'orden' => 6,
                'codigo' => 'contabilidad',
                'nombre' => '6. ' . ($configAreas['contabilidad'] ?? 'Contabilidad (Orden de Pago)'),
                'color' => 'success',
                'icono' => 'fas fa-file-invoice-dollar',
                'descripcion' => 'Recibe el expediente completo con Resolución y CBU Docente, valida Orden de Pago y genera lote e-SIDIF.',
                'procedimientos' => $procedimientosPorArea->get('contabilidad', collect()),
            ],
        ];

        $expedientesReales = ExpedienteAlumno::with(['alumno.titular', 'titular', 'docente', 'escuela', 'cumplimientos.procedimiento'])
            ->orderByDesc('updated_at')
            ->get();

        foreach ($expedientesReales as $exp) {
            if (!$exp->nro_pre_expediente) {
                $exp->nro_pre_expediente = 'PRE-EXP-2026-' . str_pad((string)$exp->id, 4, '0', STR_PAD_LEFT);
                $exp->save();
            }
        }

        $alumnosApos = Familiar::with(['titular', 'escuela'])->orderBy('nombre')->get();
        $docentesApos = Docente::where('empresa_id', 2)->orderBy('nombre')->get();
        $escuelasApos = Escuela::orderBy('nombre')->get();

        return view('expedientes.flujo_workflow', compact(
            'etapasWorkflow',
            'expedientesReales',
            'procedimientosPorArea',
            'configAreas',
            'filtroEtapa',
            'alumnosApos',
            'docentesApos',
            'escuelasApos'
        ));
    }

    /**
     * Registra un Tilde individual o grupal en la Tabla de Procedimientos de un Área
     * y si todos los procedimientos del área quedaron tildados, cierra el sector como Autorizado y avanza al siguiente!
     */
    public function tildarProcedimiento(Request $request, $expedienteId)
    {
        ExpedienteAlumno::ensureWorkflowSchema();
        $configAreas = ExpedienteAlumno::getConfigAreas();
        $exp = ExpedienteAlumno::with(['alumno.titular', 'titular', 'docente', 'escuela'])->findOrFail($expedienteId);

        $procId = (int)$request->input('procedimiento_area_id');
        $proc = ProcedimientoArea::findOrFail($procId);
        $areaCodigo = $proc->area_codigo;
        $responsable = trim((string)$request->input('responsable_nombre', '')) ?: match ($areaCodigo) {
            'mesa_entrada' => $configAreas['mesa_entrada'] ?? 'Mesa de Entrada APOS',
            'docente_padre' => $exp->docente ? $exp->docente->nombre_completo : 'Docente / Directora',
            'auditoria_docente' => 'Auditoría Docente APOS',
            'area_1' => $configAreas['area_1'] ?? 'Área N° 1',
            'area_2' => $configAreas['area_2'] ?? 'Área N° 2',
            'contabilidad' => $configAreas['contabilidad'] ?? 'Contabilidad APOS',
            default => 'Validador APOS',
        };

        $sello = 'SELLO-' . strtoupper(substr(md5($exp->id . '-' . $proc->id . '-' . now()->timestamp), 0, 8));

        CumplimientoProcedimientoExpediente::updateOrCreate(
            [
                'expediente_id' => $exp->id,
                'procedimiento_area_id' => $proc->id,
            ],
            [
                'area_codigo' => $areaCodigo,
                'cumplido' => true,
                'responsable_nombre' => $responsable,
                'sello_validacion' => $sello,
                'fecha_cumplimiento' => now(),
            ]
        );

        // Verificar si TODOS los procedimientos activos de esta área ya están tildados para este expediente
        $totalActivosArea = ProcedimientoArea::where('area_codigo', $areaCodigo)->where('activo', true)->pluck('id');
        $cumplidosArea = CumplimientoProcedimientoExpediente::where('expediente_id', $exp->id)
            ->whereIn('procedimiento_area_id', $totalActivosArea)
            ->where('cumplido', true)
            ->count();

        $areaCompleta = ($cumplidosArea >= $totalActivosArea->count());

        if ($areaCompleta) {
            // Reutilizamos la lógica de cierre de etapa y pase automático a la siguiente área
            $request->merge([
                'accion_etapa' => $areaCodigo,
                'responsable_nombre' => $responsable,
            ]);
            return $this->avanzarEstado($request, $exp->id);
        }

        return redirect()->back()->with('success', "☑️ Tilde registrado en '{$proc->nombre}' por {$responsable} (Sello {$sello}). Completados {$cumplidosArea}/{$totalActivosArea->count()} requisitos del sector.");
    }

    /**
     * Agrega un nuevo procedimiento/requisito a la tabla de cualquier área (para crecer de 1 a 10 o 20 procedimientos)
     */
    public function agregarProcedimientoArea(Request $request)
    {
        ExpedienteAlumno::ensureWorkflowSchema();

        $request->validate([
            'area_codigo' => 'required|string|max:50',
            'nombre' => 'required|string|max:180',
            'descripcion' => 'nullable|string|max:255',
        ]);

        $maxOrden = (int)ProcedimientoArea::where('area_codigo', $request->area_codigo)->max('orden');

        ProcedimientoArea::create([
            'empresa_id' => 2,
            'area_codigo' => $request->area_codigo,
            'nombre' => trim($request->nombre),
            'descripcion' => trim((string)$request->descripcion) ?: 'Procedimiento obligatorio del sector exigido para cierre de etapa.',
            'orden' => $maxOrden + 1,
            'es_obligatorio' => true,
            'activo' => true,
        ]);

        return redirect()->back()->with('success', "➕ Nuevo procedimiento '{$request->nombre}' agregado a la tabla del sector.");
    }

    /**
     * Elimina o desactiva un procedimiento de un área
     */
    public function eliminarProcedimientoArea($id)
    {
        ExpedienteAlumno::ensureWorkflowSchema();
        $proc = ProcedimientoArea::findOrFail($id);
        $nombre = $proc->nombre;
        $proc->delete();

        return redirect()->back()->with('success', "🗑️ Procedimiento '{$nombre}' quitado de la tabla del sector.");
    }

    /**
     * Avanza o valida una etapa completa de un expediente real en la BD, registrando todos sus tildes
     */
    public function avanzarEstado(Request $request, $id)
    {
        ExpedienteAlumno::ensureWorkflowSchema();
        $configAreas = ExpedienteAlumno::getConfigAreas();
        $exp = ExpedienteAlumno::with(['alumno.titular', 'titular', 'docente', 'escuela'])->findOrFail($id);

        if (!$exp->nro_pre_expediente) {
            $exp->nro_pre_expediente = ExpedienteAlumno::generarSiguientePreExpediente();
        }

        $accionEtapa = $request->input('accion_etapa', 'siguiente');
        $responsable = trim((string)$request->input('responsable_nombre', ''));

        if ($accionEtapa === 'docente_padre' || ($accionEtapa === 'siguiente' && $exp->estado_auditoria === 'en_camino')) {
            if (!$exp->docente_id && $request->filled('docente_id')) {
                $exp->docente_id = (int)$request->input('docente_id');
            } elseif (!$exp->docente_id) {
                $exp->docente_id = Docente::where('empresa_id', 2)->value('id');
            }
            $exp->ok_mesa_entrada = true;
            $exp->ok_docente_padre = true;
            $exp->etapa_flujo = 'auditoria_docente';
            $exp->estado_auditoria = 'pendiente';
            $exp->marcarProcedimientosAreaCumplidos('mesa_entrada', $configAreas['mesa_entrada'] ?? 'Mesa de Entrada APOS');
            $exp->marcarProcedimientosAreaCumplidos('docente_padre', $responsable ?: ($exp->docente ? $exp->docente->nombre_completo : 'Docente y Directora'));
            $msg = "✅ Etapa 2 (Docente + Directora + Padre) cerrada y autorizada para {$exp->nro_pre_expediente}. Pasa inmediatamente a Etapa 3: Auditoría Docente.";
        } elseif ($accionEtapa === 'auditor' || $accionEtapa === 'auditoria_docente' || ($accionEtapa === 'siguiente' && !$exp->visto_bueno_auditor)) {
            $exp->ok_mesa_entrada = true;
            $exp->ok_docente_padre = true;
            $exp->visto_bueno_auditor = true;
            $exp->aprobado_auditor_at = now();
            $exp->responsable_auditor = $responsable ?: 'Auditoría Docente APOS';
            $exp->etapa_flujo = 'area_1';
            $exp->estado_auditoria = 'pendiente';
            $exp->marcarProcedimientosAreaCumplidos('mesa_entrada', $configAreas['mesa_entrada'] ?? 'Mesa de Entrada APOS');
            $exp->marcarProcedimientosAreaCumplidos('docente_padre', $exp->docente ? $exp->docente->nombre_completo : 'Docente APOS');
            $exp->marcarProcedimientosAreaCumplidos('auditoria_docente', $exp->responsable_auditor);
            $msg = "✅ Etapa 3 (Auditoría Docente) cerrada con Sello de Validación por {$exp->responsable_auditor}. Pasa inmediatamente a Etapa 4: " . ($configAreas['area_1'] ?? 'Área N° 1') . ".";
        } elseif ($accionEtapa === 'area_1' || ($accionEtapa === 'siguiente' && $exp->visto_bueno_auditor && !$exp->ok_area_1)) {
            $exp->ok_mesa_entrada = true;
            $exp->ok_docente_padre = true;
            $exp->visto_bueno_auditor = true;
            $exp->aprobado_auditor_at = $exp->aprobado_auditor_at ?: now();
            $exp->responsable_auditor = $exp->responsable_auditor ?: 'Auditoría Docente APOS';
            $exp->ok_area_1 = true;
            $exp->fecha_area_1 = now();
            $exp->responsable_area_1 = $responsable ?: ($configAreas['area_1'] ?? 'Área N° 1');
            $exp->etapa_flujo = 'area_2';
            $exp->estado_auditoria = 'pendiente';
            $exp->marcarProcedimientosAreaCumplidos('auditoria_docente', $exp->responsable_auditor);
            $exp->marcarProcedimientosAreaCumplidos('area_1', $exp->responsable_area_1);
            $msg = "✅ Etapa 4 (" . ($configAreas['area_1'] ?? 'Área N° 1') . ") cerrada con Tilde en 'Procedimiento de validación' por {$exp->responsable_area_1}. Pasa inmediatamente a Etapa 5: " . ($configAreas['area_2'] ?? 'Área N° 2') . ".";
        } elseif ($accionEtapa === 'contabilidad' || ($accionEtapa === 'siguiente' && $exp->ok_area_2 && !$exp->ok_contabilidad)) {
            $exp->ok_mesa_entrada = true;
            $exp->ok_docente_padre = true;
            $exp->visto_bueno_auditor = true;
            $exp->ok_area_1 = true;
            $exp->ok_area_2 = true;
            $exp->visto_bueno_jefe = true;
            $exp->ok_contabilidad = true;
            $exp->fecha_contabilidad = now();
            $exp->responsable_contabilidad = $responsable ?: ($configAreas['contabilidad'] ?? 'Contabilidad APOS');
            $exp->etapa_flujo = 'contabilidad';
            $exp->estado_auditoria = 'aprobado';
            if (!$exp->tieneResolucionOficial()) {
                $exp->nro_resolucion = ExpedienteAlumno::generarSiguienteResolucionSecuenciada();
            }
            $exp->marcarProcedimientosAreaCumplidos('area_2', $exp->responsable_area_2 ?: ($configAreas['area_2'] ?? 'Área N° 2'));
            $exp->marcarProcedimientosAreaCumplidos('contabilidad', $exp->responsable_contabilidad);
            $msg = "💳 Etapa 6 (Contabilidad) cerrada por {$exp->responsable_contabilidad}: Orden de Pago validada para Resolución {$exp->nro_resolucion} y enviada al lote de Gobierno (e-SIDIF / CBU Banco Rioja).";
        } else {
            // Etapa 5 completada: Área N° 2 -> Emite RESOLUCIÓN OFICIAL SECUENCIADA y pasa a Contabilidad (Etapa 6)
            $exp->ok_mesa_entrada = true;
            $exp->ok_docente_padre = true;
            $exp->visto_bueno_auditor = true;
            $exp->aprobado_auditor_at = $exp->aprobado_auditor_at ?: now();
            $exp->responsable_auditor = $exp->responsable_auditor ?: 'Auditoría Docente APOS';
            $exp->ok_area_1 = true;
            $exp->fecha_area_1 = $exp->fecha_area_1 ?: now();
            $exp->responsable_area_1 = $exp->responsable_area_1 ?: ($configAreas['area_1'] ?? 'Área N° 1');
            $exp->ok_area_2 = true;
            $exp->fecha_area_2 = now();
            $exp->responsable_area_2 = $responsable ?: ($configAreas['area_2'] ?? 'Área N° 2');
            $exp->visto_bueno_jefe = true;
            $exp->aprobado_jefe_at = now();
            $exp->etapa_flujo = 'resolucion_emitida';
            $exp->estado_auditoria = 'aprobado';

            if (!$exp->tieneResolucionOficial()) {
                $exp->nro_resolucion = ExpedienteAlumno::generarSiguienteResolucionSecuenciada();
            }
            $exp->origen_resolucion = 'resolucion_apos';

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
            $msg = "🏛️ Etapa 5 (" . ($configAreas['area_2'] ?? 'Área N° 2') . ") cerrada con Tilde en 'Procedimiento de validación' por {$exp->responsable_area_2}. Se emitió la Resolución Secuenciada {$exp->nro_resolucion} y pasó inmediatamente a Contabilidad para Orden de Pago.";
        }

        $exp->save();

        if (Schema::hasTable('notificaciones_sistema')) {
            NotificacionSistema::create([
                'empresa_id' => $exp->empresa_id ?? 2,
                'titulo' => $exp->estado_auditoria === 'aprobado'
                    ? "🏛️ Resolución {$exp->nro_resolucion} → Pasa a Contabilidad"
                    : "🔄 Avance de Pre-Expediente {$exp->nro_pre_expediente}",
                'mensaje' => $msg,
                'link' => route('expedientes.flujo'),
                'leido' => false,
            ]);
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Guarda los nombres personalizables de Mesa de Entrada, Área N° 1, Área N° 2 y Contabilidad
     */
    public function guardarEtapaConfig(Request $request)
    {
        ExpedienteAlumno::saveConfigAreas($request->only([
            'mesa_entrada',
            'area_1',
            'area_1_req',
            'area_2',
            'area_2_req',
            'contabilidad',
        ]));

        return redirect()->back()->with('success', "⚙️ Nombres de las Áreas del Circuito APOS actualizados en todo el sistema.");
    }
}
