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

    /**
     * Vista de simulación del Ecosistema del Docente/Terapeuta
     */
    public function demo(Request $request)
    {
        $empresaIdDemo = 2; // APOS - Obra Social Provincial
        $docenteReal = $this->resolveDocenteActivo($request);
        $docenteIdDemo = $docenteReal ? $docenteReal->id : 21;
        $docenteNombre = $docenteReal ? ('Prof. ' . $docenteReal->nombre_completo . ' (APOS La Rioja)') : 'Prof. María Eugenia López (APOS La Rioja)';

        // Lista de docentes de APOS para poder cambiar de docente en la demo si se desea
        $docentesApos = Docente::where('empresa_id', $empresaIdDemo)->orderBy('nombre')->get();

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
                $pct = $exp->estado_auditoria === 'aprobado' ? 100 : 50;
                $montoExp = ($exp->horas_mensuales_asignadas ?? 60) * 6500;

                if ($exp->estado_auditoria === 'aprobado') {
                    $montoCobrado += $montoExp;
                    $estadoLabel = 'aprobado';
                } elseif ($exp->estado_auditoria === 'rechazado') {
                    $estadoLabel = 'rechazado';
                } else {
                    $montoPretendido += $montoExp;
                    $estadoLabel = 'pendiente';
                }

                $nombreAlumno = $exp->alumno ? $exp->alumno->nombre_completo : 'Alumno #' . $exp->alumno_id;
                $nombreTitular = $exp->titular ? $exp->titular->nombre_completo : ($exp->alumno && $exp->alumno->titular ? $exp->alumno->titular->nombre_completo : 'Titular APOS');
                $nroResolucion = $exp->nro_resolucion ?: 'Pendiente Auditoría';

                $alumnosDemo[] = (object)[
                    'id' => $exp->id,
                    'alumno_id' => $exp->alumno_id,
                    'nombre' => $nombreAlumno,
                    'titular_nombre' => $nombreTitular,
                    'nro_resolucion' => $nroResolucion,
                    'curso' => ($exp->escuela->nombre_completo ?? 'Escuela Asignada') . " • Res: {$nroResolucion}",
                    'estado' => $estadoLabel,
                    'progreso' => $pct,
                    'paso_label' => $exp->estado_auditoria === 'aprobado' ? 'Aprobado por Auditoría APOS' : 'En Bandeja del Auditor (Pendiente Visado)'
                ];
            }
        }

        $tiposDocumentos = \App\Models\TipoDocumento::where('empresa_id', $empresaIdDemo)
            ->where('entidad_tipo', 'docente')
            ->get();

        if ($tiposDocumentos->count() === 0) {
            $defaultReqs = [
                ['nombre' => 'DNI (Frente y Dorso)', 'descripcion' => 'Fotocopia o foto nítida de frente y dorso de DNI', 'es_obligatorio' => true, 'vencimiento_dias' => null],
                ['nombre' => 'Certificado de Matrícula / Título Profesional', 'descripcion' => 'Matrícula o título habilitante vigente', 'es_obligatorio' => true, 'vencimiento_dias' => 365],
                ['nombre' => 'Certificado de Domicilio', 'descripcion' => 'Comprobante de domicilio reciente (Policía o Servicio)', 'es_obligatorio' => true, 'vencimiento_dias' => 365],
                ['nombre' => 'Certificado de Buena Conducta / Antecedentes', 'descripcion' => 'Certificado de antecedentes provincial/nacional', 'es_obligatorio' => true, 'vencimiento_dias' => 365],
            ];

            foreach ($defaultReqs as $reqData) {
                \App\Models\TipoDocumento::create(array_merge($reqData, [
                    'empresa_id' => $empresaIdDemo,
                    'entidad_tipo' => 'docente'
                ]));
            }

            $tiposDocumentos = \App\Models\TipoDocumento::where('empresa_id', $empresaIdDemo)
                ->where('entidad_tipo', 'docente')
                ->get();
        }

        foreach ($tiposDocumentos as $tipo) {
            $subido = \App\Models\DocumentoSubido::where('tipo_documento_id', $tipo->id)
                ->where('entidad_id', $docenteIdDemo)
                ->where('entidad_tipo', 'docente')
                ->latest()
                ->first();

            $tipo->estado_subida = $subido ? $subido->estado : 'sin_entregar';
            $tipo->comentarios = $subido ? $subido->comentarios_auditor : null;
        }

        return view('pwa.docente.dashboard', compact(
            'docenteReal',
            'docenteIdDemo',
            'docenteNombre',
            'docentesApos',
            'montoCobrado',
            'montoPretendido',
            'alumnosDemo',
            'tiposDocumentos'
        ));
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

        $nroResolucion = trim((string)$request->input('nro_resolucion', ''));
        $tieneResolucion = $nroResolucion !== '';

        // Si el alumno ya tenía un expediente con resolución en APOS y el docente no tipeó otra, usamos esa o marcamos pendiente
        $expExistente = ExpedienteAlumno::where('alumno_id', $alumno->id)->first();
        if (!$tieneResolucion && $expExistente && $expExistente->nro_resolucion) {
            $nroResolucion = $expExistente->nro_resolucion;
            $tieneResolucion = true;
        }

        $horarios = $request->input('horarios_atencion') ?: ($expExistente->horarios_atencion ?? 'Lunes a Viernes de 08:00 a 11:00 hs (3 hs/día)');
        $horasMes = (int)($request->input('horas_mensuales_asignadas') ?: ($expExistente->horas_mensuales_asignadas ?? 60));
        $diagnosticoTexto = $request->input('diagnostico') ?: ($alumno->diagnostico_detalle ?? ($alumno->diagnostico->nombre ?? 'CUD Activo'));

        // 1. Actualizar vínculo directo en la tabla familiares (Alumno -> Docente + Escuela)
        $alumno->docente_id = $docente->id;
        if ($escuela) {
            $alumno->escuela_id = $escuela->id;
        }
        $alumno->save();

        // 2. Crear o actualizar el ExpedienteAlumno poniéndolo en estado 'pendiente' de Auditoría
        $expediente = ExpedienteAlumno::updateOrCreate(
            [
                'empresa_id' => $empresaId,
                'alumno_id' => $alumno->id,
            ],
            [
                'docente_id' => $docente->id,
                'titular_id' => $titular ? $titular->id : $alumno->titular_id,
                'escuela_id' => $escuela ? $escuela->id : $alumno->escuela_id,
                'origen_resolucion' => $tieneResolucion ? 'resolucion_apos' : 'pendiente_auditoria',
                'nro_resolucion' => $tieneResolucion ? $nroResolucion : 'EN TRÁMITE (SOLICITA AUDITORÍA)',
                'diagnostico' => $diagnosticoTexto,
                'horarios_atencion' => $horarios,
                'horas_mensuales_asignadas' => $horasMes,
                'estado_auditoria' => 'pendiente',
                'motivo_rechazo' => null,
            ]
        );

        $docenteNom = $docente->nombre_completo;
        $alumnoNom = $alumno->nombre_completo . ($alumno->dni ? " (DNI {$alumno->dni})" : '');
        $titularNom = $titular ? ($titular->nombre_completo . " - Afiliado N° {$titular->nro_afiliado}") : 'Titular APOS';
        $escuelaNom = $escuela ? $escuela->nombre_completo : 'Escuela Provincial';
        $resTexto = $tieneResolucion ? "Resolución N° {$nroResolucion}" : "SIN RESOLUCIÓN INICIAL (Requiere Autorización de Auditoría)";

        $descripcionEvento = "La docente {$docenteNom} señaló y vinculó al alumno {$alumnoNom}, hijo del titular {$titularNom}, bajo {$resTexto} en {$escuelaNom} ({$horarios}).";

        // 3. Crear registro en NovedadAuditoria (Bandeja del Auditor)
        $novedad = NovedadAuditoria::create([
            'empresa_id' => $empresaId,
            'docente_id' => $docente->id,
            'expediente_id' => $expediente->id,
            'tipo_novedad' => $tieneResolucion ? 'vinculacion_con_resolucion' : 'vinculacion_sin_resolucion',
            'descripcion' => $descripcionEvento,
            'estado' => 'pendiente',
        ]);

        // 4. Disparar Notificación Inmediata en la Campanita y Popup del Auditor / Admin
        if (Schema::hasTable('notificaciones_sistema')) {
            NotificacionSistema::create([
                'empresa_id' => $empresaId,
                'titulo' => '🔔 Nueva Vinculación Docente-Alumno (' . ($tieneResolucion ? "Res. {$nroResolucion}" : 'Sin Res.') . ')',
                'mensaje' => "{$docenteNom} tomó al alumno {$alumno->nombre_completo} (Titular: " . ($titular->nombre_completo ?? 'APOS') . "). Click para auditar.",
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
            'nro_resolucion' => $expediente->nro_resolucion,
            'estado_auditoria' => 'pendiente',
            'message' => "✅ ¡Vínculo registrado y notificado en vivo al Auditor! Alumno: {$alumno->nombre_completo} | Titular: " . ($titular->nombre_completo ?? 'APOS') . " | Docente: {$docenteNom} | {$resTexto}."
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
            $alumnoIdsPorResolucion = ExpedienteAlumno::where('nro_resolucion', 'LIKE', "%{$term}%")
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
                $a->nro_resolucion = $exp ? $exp->nro_resolucion : ($a->resolucion ?: null);
                $a->horarios_atencion = $exp ? $exp->horarios_atencion : 'Lunes a Viernes (Máx. 3 hs/día)';
                $a->horas_mensuales_asignadas = $exp ? $exp->horas_mensuales_asignadas : 60;
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
