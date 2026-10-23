@extends('layouts.app')

@section('title', 'Circuito Institucional APOS — Pre-Resolución, Áreas de Validación y Contabilidad')

@section('content')
<div class="container-fluid py-4" style="background-color: #0f172a; min-height: 100vh; color: white;">
    <div class="container-fluid px-3 px-md-4">

        <!-- ENCABEZADO DEL CIRCUITO COMPLETO APOS -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary border-opacity-25 gap-3">
            <div class="d-flex align-items-center gap-3">
                <img src="{{ asset('img/apos-logo.png') }}" alt="APOS La Rioja" style="height: 52px; width: auto; background: white; padding: 5px 12px; border-radius: 12px; box-shadow: 0 4px 14px rgba(0,0,0,0.35);">
                <div>
                    <span class="badge bg-info text-dark px-3 py-1 rounded-pill fw-bold text-uppercase shadow-sm" style="font-size: 0.72rem;">
                        <i class="fas fa-sitemap me-1"></i> Circuito Docente de Chicos Especiales — Trazabilidad por Sector
                    </span>
                    <h2 class="fw-bold mt-1 mb-0 text-white" style="font-size: 1.55rem;">
                        Mesa de Entrada &rarr; Docente/Directora &rarr; Auditoría &rarr; {{ $configAreas['area_1'] ?? 'Área N° 1' }} &rarr; {{ $configAreas['area_2'] ?? 'Área N° 2' }} &rarr; Contabilidad
                    </h2>
                    <small class="text-white-50">
                        Cada sector tiene su propia <strong>Tabla de Procedimientos / Requisitos</strong> con registro de tilde, sello y responsable. Al completarse, se cierra en ese sector y pasa de inmediato al siguiente.
                    </small>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-warning text-dark rounded-pill px-3 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAgregarProcedimiento">
                    <i class="fas fa-plus-circle me-1"></i> + Agregar Procedimiento a un Área
                </button>
                <button type="button" class="btn btn-outline-info rounded-pill px-3 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalConfigFlujo">
                    <i class="fas fa-edit me-1"></i> Renombrar Área 1 / Área 2
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-lg rounded-4 mb-4 p-3 d-flex align-items-center gap-3" style="background: linear-gradient(135deg, #065f46, #047857); color: #ecfdf5;">
                <i class="fas fa-check-circle fa-2x text-warning"></i>
                <div>
                    <h6 class="mb-0 fw-bold">{{ session('success') }}</h6>
                    <small class="text-white-50">Quedó registrado en la tabla de trazabilidad con fecha, hora, sello y responsable.</small>
                </div>
            </div>
        @endif

        <!-- BARRA DE NAVEGACIÓN POR SECTOR / ÁREA -->
        @php
            $etapaActiva = $filtroEtapa ?? request('etapa', 'todas');
        @endphp
        <div class="d-flex flex-wrap gap-2 mb-4">
            <a href="{{ route('expedientes.flujo', ['etapa' => 'todas']) }}" class="btn btn-sm rounded-pill px-3 fw-bold {{ $etapaActiva === 'todas' ? 'btn-info text-dark shadow' : 'btn-outline-light' }}">
                <i class="fas fa-layer-group me-1"></i> Todo el Circuito ({{ $expedientesReales->count() }})
            </a>
            <a href="{{ url('/mesa-entrada') }}" class="btn btn-sm rounded-pill px-3 fw-bold {{ $etapaActiva === 'mesa_entrada' ? 'btn-info text-dark shadow' : 'btn-outline-info' }}">
                <i class="fas fa-inbox me-1"></i> 1. Mesa de Entrada
            </a>
            <a href="{{ route('expedientes.flujo', ['etapa' => 'docente_padre']) }}" class="btn btn-sm rounded-pill px-3 fw-bold {{ $etapaActiva === 'docente_padre' ? 'btn-primary shadow' : 'btn-outline-primary text-white' }}">
                <i class="fas fa-chalkboard-teacher me-1"></i> 2. Docente + Directora (CBU y Asistencia)
            </a>
            <a href="{{ route('expedientes.flujo', ['etapa' => 'auditoria_docente']) }}" class="btn btn-sm rounded-pill px-3 fw-bold {{ $etapaActiva === 'auditoria_docente' ? 'btn-warning text-dark shadow' : 'btn-outline-warning' }}">
                <i class="fas fa-user-check me-1"></i> 3. Auditoría Docente
            </a>
            <a href="{{ url('/area-1') }}" class="btn btn-sm rounded-pill px-3 fw-bold {{ $etapaActiva === 'area_1' ? 'btn-light text-dark shadow' : 'btn-outline-light' }}">
                <i class="fas fa-clipboard-check me-1"></i> 4. {{ $configAreas['area_1'] ?? 'Área N° 1' }}
            </a>
            <a href="{{ url('/area-2') }}" class="btn btn-sm rounded-pill px-3 fw-bold {{ $etapaActiva === 'area_2' ? 'btn-danger shadow' : 'btn-outline-danger text-white' }}">
                <i class="fas fa-stamp me-1"></i> 5. {{ $configAreas['area_2'] ?? 'Área N° 2' }}
            </a>
            <a href="{{ url('/contabilidad') }}" class="btn btn-sm rounded-pill px-3 fw-bold {{ $etapaActiva === 'contabilidad' ? 'btn-success shadow' : 'btn-outline-success text-white' }}">
                <i class="fas fa-file-invoice-dollar me-1"></i> 6. Contabilidad (Orden de Pago)
            </a>
        </div>

        <!-- 1. MESA DE ENTRADA: GENERAR PRE-RESOLUCIÓN / PRE-EXPEDIENTE (CON O SIN DOCENTE) -->
        @if(in_array($etapaActiva, ['todas', 'mesa_entrada']))
        <div class="card border-0 shadow-lg mb-4" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-radius: 18px; border: 1.5px solid rgba(56, 189, 248, 0.45) !important;">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                    <div>
                        <span class="badge bg-warning text-dark fw-bold px-3 py-1 rounded-pill mb-1">
                            <i class="fas fa-inbox me-1"></i> ETAPA 1 — MESA DE ENTRADA APOS
                        </span>
                        <h5 class="fw-bold text-white mb-0">
                            Alta de Pre-Resolución / Pre-Expediente (Padre Titular + Hijo + Escuela)
                        </h5>
                        <small class="text-info">
                            Si el padre aún no habló con ninguna maestra, dejá <strong>"Sin Docente aún"</strong>: el sistema genera el N° de Pre-Resolución para que el padre se lo dé a la maestra, ella lo busque en su App y se lo asigne a sí misma.
                        </small>
                    </div>
                </div>

                <form action="{{ route('auditor.docentes.resolucion.registrar') }}" method="POST" class="row g-3 align-items-end">
                    @csrf
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-warning mb-1">
                            1° Padre Titular + Hijo/Alumno (Padrón APOS):
                        </label>
                        <input type="text" class="form-control form-control-sm bg-dark text-warning border-info mb-1" placeholder="🔍 Filtrar por apellido, nombre o DNI del padre o niño..." oninput="filtrarSelectOpciones(this, 'selectAlumnoMesaFlujo')">
                        <select name="alumno_id" id="selectAlumnoMesaFlujo" class="form-select form-select-sm bg-dark text-white border-secondary" required>
                            @foreach($alumnosApos as $alItem)
                                @php
                                    $titObj = $alItem->titular;
                                    $titLabel = $titObj ? ($titObj->nombre_completo . ' - Af. ' . $titObj->nro_afiliado) : 'Titular APOS';
                                @endphp
                                <option value="{{ $alItem->id }}">
                                    Niño/a: {{ $alItem->nombre_completo }} (DNI {{ $alItem->dni ?: 'S/D' }}) — Padre: {{ $titLabel }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-info mb-1">
                            2° Escuela a la que asiste el niño:
                        </label>
                        <input type="text" class="form-control form-control-sm bg-dark text-warning border-info mb-1" placeholder="🔍 Filtrar escuela..." oninput="filtrarSelectOpciones(this, 'selectEscuelaMesaFlujo')">
                        <select name="escuela_id" id="selectEscuelaMesaFlujo" class="form-select form-select-sm bg-dark text-white border-secondary">
                            <option value="">-- Escuela del Padrón / A confirmar --</option>
                            @foreach($escuelasApos as $escItem)
                                <option value="{{ $escItem->id }}">{{ $escItem->nombre_completo }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-light mb-1">
                            3° Docente (Opcional en Mesa de Entrada):
                        </label>
                        <input type="text" class="form-control form-control-sm bg-dark text-warning border-secondary mb-1" placeholder="🔍 Filtrar docente (si ya habló)..." oninput="filtrarSelectOpciones(this, 'selectDocenteMesaFlujo')">
                        <select name="docente_id" id="selectDocenteMesaFlujo" class="form-select form-select-sm bg-dark text-white border-secondary">
                            <option value="">🔓 SIN DOCENTE AÚN (El padre le dará el N° a la maestra)</option>
                            @foreach($docentesApos as $docItem)
                                <option value="{{ $docItem->id }}">{{ $docItem->nombre_completo }} (DNI {{ $docItem->dni }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <input type="hidden" name="horas_diarias" value="3">
                        <input type="hidden" name="accion_inmediata" value="mesa_entrada">
                        <button type="submit" class="btn btn-info text-dark fw-bold w-100 py-2 rounded-3 shadow">
                            <i class="fas fa-folder-plus me-1"></i> Generar Pre-Resolución
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        <!-- 2. TABLAS DE PROCEDIMIENTOS / REQUERIMIENTOS POR CADA ÁREA (CRECIBLES DE 1 A 20 PASOS) -->
        <div class="card border-0 bg-secondary bg-opacity-10 shadow-sm mb-4" style="border-radius: 18px; border: 1px solid rgba(255,255,255,0.1);">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                    <div>
                        <h5 class="fw-bold text-white mb-0">
                            <i class="fas fa-table text-warning me-2"></i> Tablas de Procedimientos y Requerimientos por Sector (1 a 20 pasos por Área)
                        </h5>
                        <small class="text-white-50">
                            Hoy <strong>{{ $configAreas['area_1'] ?? 'Área N° 1' }}</strong> y <strong>{{ $configAreas['area_2'] ?? 'Área N° 2' }}</strong> tienen su registro inicial <em>"Procedimiento de validación"</em>. Podés agregar o quitar procedimientos en cualquier momento.
                        </small>
                    </div>
                    <button type="button" class="btn btn-sm btn-warning text-dark fw-bold rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalAgregarProcedimiento">
                        <i class="fas fa-plus me-1"></i> Agregar Requisito / Procedimiento
                    </button>
                </div>

                <div class="row g-3">
                    @foreach($etapasWorkflow as $etapa)
                        <div class="col-md-4 col-xl-2">
                            <div class="p-3 bg-dark rounded-4 h-100 border border-{{ $etapa->color }} border-opacity-50 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-{{ $etapa->color }} {{ in_array($etapa->color, ['warning', 'info', 'light']) ? 'text-dark' : 'text-white' }} fw-bold" style="font-size: 0.72rem;">
                                            <i class="{{ $etapa->icono }} me-1"></i> {{ $etapa->nombre }}
                                        </span>
                                        <span class="badge bg-secondary" style="font-size: 0.65rem;">
                                            {{ $etapa->procedimientos->count() }} req.
                                        </span>
                                    </div>
                                    <p class="text-white-50 mb-2" style="font-size: 0.71rem; line-height: 1.3;">{{ $etapa->descripcion }}</p>

                                    <div class="border-top border-secondary border-opacity-25 pt-2 mt-2">
                                        <div class="text-warning fw-bold mb-1" style="font-size: 0.68rem; text-transform: uppercase;">
                                            Tabla de Procedimientos:
                                        </div>
                                        @forelse($etapa->procedimientos as $proc)
                                            <div class="d-flex justify-content-between align-items-center bg-secondary bg-opacity-10 rounded-2 px-2 py-1 mb-1" style="font-size: 0.72rem;">
                                                <span class="text-white">
                                                    <i class="fas fa-check-square text-info me-1"></i> {{ $proc->nombre }}
                                                </span>
                                                @if($etapa->procedimientos->count() > 1)
                                                    <form action="{{ route('expedientes.flujo.procedimiento.eliminar', $proc->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-link text-danger p-0 ms-1" style="font-size: 0.68rem;" title="Quitar procedimiento">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        @empty
                                            <div class="text-white-50 small">Sin procedimientos</div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- 3. BUSCADOR EN TIEMPO REAL CON RESALTADO AMARILLO SOBRE LOS EXPEDIENTES -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
            <h5 class="fw-bold text-white mb-0">
                <i class="fas fa-tasks me-2 text-info"></i> Expedientes en Circuito: Tildes de Validación por Área y Pase a Contabilidad
            </h5>
            <div style="min-width: 320px; flex: 1; max-width: 460px;">
                <input type="text" id="inputBuscarFlujoVivo" class="form-control bg-dark text-warning border-info rounded-pill px-3" placeholder="🔍 Buscar en vivo por N° Pre-Resolución, Padre, Niño, Docente o CBU..." oninput="filtrarTarjetasFlujo(this.value)">
            </div>
        </div>

        @php
            $expedientesFiltrados = $expedientesReales->filter(function($exp) use ($etapaActiva) {
                if ($etapaActiva === 'todas') return true;
                if ($etapaActiva === 'mesa_entrada') return !$exp->docente_id || $exp->estado_auditoria === 'en_camino';
                if ($etapaActiva === 'docente_padre') return !$exp->ok_docente_padre;
                if ($etapaActiva === 'auditoria_docente') return $exp->ok_docente_padre && !$exp->visto_bueno_auditor;
                if ($etapaActiva === 'area_1') return $exp->visto_bueno_auditor && !$exp->ok_area_1;
                if ($etapaActiva === 'area_2') return $exp->ok_area_1 && !$exp->ok_area_2;
                if ($etapaActiva === 'contabilidad') return $exp->ok_area_2 || $exp->estado_auditoria === 'aprobado';
                return true;
            });
        @endphp

        <div class="row g-3" id="contenedorTarjetasFlujo">
            @forelse($expedientesFiltrados as $exp)
                @php
                    $alNombre = $exp->alumno ? $exp->alumno->nombre_completo : ('Alumno #' . $exp->alumno_id);
                    $alDni = $exp->alumno ? $exp->alumno->dni : '';
                    $titObj = $exp->titular ?: ($exp->alumno ? $exp->alumno->titular : null);
                    $titNombre = $titObj ? $titObj->nombre_completo : 'Titular APOS';
                    $titAfil = $titObj ? $titObj->nro_afiliado : '';
                    $docNombre = $exp->docente ? $exp->docente->nombre_completo : 'SIN DOCENTE ASIGNADO (Abierto para Auto-Asignación)';
                    $docCbu = $exp->docente && !empty($exp->docente->cbu) ? $exp->docente->cbu : '3090001901000048291015';
                    $escNombre = $exp->escuela ? $exp->escuela->nombre_completo : 'Escuela Provincial';
                    $preExpCodigo = $exp->codigo_pre_expediente;
                    $tieneResFinal = $exp->tieneResolucionOficial();
                    $horasMes = (int)($exp->horas_mensuales_asignadas ?: 60);
                    if ($horasMes > 0 && $horasMes <= 3) $horasMes *= 20;
                    $montoOrdenPago = $horasMes * 6500;
                    $cumplidosMap = $exp->cumplimientos->where('cumplido', true)->keyBy('procedimiento_area_id');
                @endphp
                <div class="col-lg-6 item-flujo-searchable" data-search="{{ strtolower($preExpCodigo . ' ' . $exp->nro_resolucion . ' ' . $alNombre . ' ' . $alDni . ' ' . $titNombre . ' ' . $docNombre . ' ' . $escNombre) }}">
                    <div class="card border-0 shadow-lg h-100" style="background: #1e293b; border-radius: 18px; border: 1px solid {{ $exp->ok_contabilidad ? '#10b981' : ($tieneResFinal ? '#38bdf8' : 'rgba(255,255,255,0.14)') }} !important;">
                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                            <div>
                                <!-- Cabecera del Expediente: Pre-Resolución vs Resolución Oficial -->
                                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <span class="badge bg-warning text-dark fw-bold font-monospace px-3 py-1 rounded-pill" style="font-size: 0.8rem;">
                                            <i class="fas fa-folder-open me-1"></i> Pre-Res: <span class="js-hl-text">{{ $preExpCodigo }}</span>
                                        </span>
                                        @if($tieneResFinal)
                                            <span class="badge bg-success text-white fw-bold font-monospace px-3 py-1 rounded-pill" style="font-size: 0.8rem;">
                                                <i class="fas fa-award me-1"></i> <span class="js-hl-text">{{ $exp->nro_resolucion }}</span>
                                            </span>
                                        @else
                                            <span class="badge bg-secondary text-white-50 px-2 py-1 rounded-pill" style="font-size: 0.72rem;">
                                                Resolución Oficial: Se emite al cerrar {{ $configAreas['area_2'] ?? 'Área N° 2' }}
                                            </span>
                                        @endif
                                    </div>
                                    <span class="badge {{ $exp->ok_contabilidad ? 'bg-success' : 'bg-info text-dark' }} fw-bold px-3 py-1 rounded-pill" style="font-size: 0.74rem;">
                                        {{ $exp->paso_actual_label }}
                                    </span>
                                </div>

                                <!-- Datos del Padre, Niño, Escuela y Docente -->
                                <div class="p-3 rounded-3 mb-3" style="background: #0f172a; border: 1px solid rgba(255,255,255,0.08); font-size: 0.83rem;">
                                    <div class="row g-2">
                                        <div class="col-sm-6">
                                            <div class="text-white-50 small">Padre / Titular APOS:</div>
                                            <div class="fw-bold text-info"><i class="fas fa-user-shield me-1"></i> <span class="js-hl-text">{{ $titNombre }}</span> <small class="text-white-50">({{ $titAfil }})</small></div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="text-white-50 small">Niño / Alumno Atendido:</div>
                                            <div class="fw-bold text-warning"><i class="fas fa-child me-1"></i> <span class="js-hl-text">{{ $alNombre }}</span> <small class="text-white-50">(DNI {{ $alDni }})</small></div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="text-white-50 small">Escuela &bull; Horario:</div>
                                            <div class="fw-bold text-white"><i class="fas fa-school text-warning me-1"></i> <span class="js-hl-text">{{ $escNombre }}</span></div>
                                            <div class="text-white-50" style="font-size: 0.73rem;">{{ $exp->horarios_atencion }}</div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="text-white-50 small">Docente &bull; CBU Orden de Pago:</div>
                                            <div class="fw-bold {{ $exp->docente ? 'text-success' : 'text-warning' }}">
                                                <i class="fas fa-chalkboard-teacher me-1"></i> <span class="js-hl-text">{{ $docNombre }}</span>
                                            </div>
                                            <div class="text-white-50 font-monospace" style="font-size: 0.72rem;">
                                                🏦 CBU: {{ $docCbu }} &bull; <strong class="text-success">${{ number_format($montoOrdenPago, 0, ',', '.') }}/mes</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- TABLA DE TRAZABILIDAD Y TILDES POR CADA SECTOR DEL CIRCUITO -->
                                <div class="p-3 rounded-3 mb-3" style="background: #0f172a; border: 1px solid rgba(56, 189, 248, 0.25);">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-bold text-info" style="font-size: 0.78rem; text-transform: uppercase;">
                                            <i class="fas fa-clipboard-list me-1"></i> Tabla de Cumplimiento y Sellos por Sector
                                        </span>
                                        <span class="badge bg-dark border border-info text-info" style="font-size: 0.7rem;">
                                            Avance: {{ $exp->porcentaje_progreso }}%
                                        </span>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-dark table-sm align-middle mb-0" style="font-size: 0.76rem;">
                                            <thead>
                                                <tr class="text-white-50">
                                                    <th>Sector / Área</th>
                                                    <th>Procedimiento / Requisito en Tabla</th>
                                                    <th>Estado / Trazabilidad</th>
                                                    <th class="text-end">Acción (Tilde)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($etapasWorkflow as $etapa)
                                                    @php
                                                        $etapaCerrada = match($etapa->codigo) {
                                                            'mesa_entrada' => (bool)$exp->ok_mesa_entrada,
                                                            'docente_padre' => (bool)$exp->ok_docente_padre,
                                                            'auditoria_docente' => (bool)$exp->visto_bueno_auditor,
                                                            'area_1' => (bool)$exp->ok_area_1,
                                                            'area_2' => (bool)$exp->ok_area_2,
                                                            'contabilidad' => (bool)$exp->ok_contabilidad,
                                                            default => false,
                                                        };
                                                        $responsableEtapa = match($etapa->codigo) {
                                                            'mesa_entrada' => $exp->responsable_mesa_entrada ?: 'Mesa de Entrada APOS',
                                                            'docente_padre' => ($exp->docente ? $exp->docente->nombre_completo : 'Docente') . ' + Aval Directora',
                                                            'auditoria_docente' => $exp->responsable_auditor ?: 'Auditoría Docente APOS',
                                                            'area_1' => $exp->responsable_area_1 ?: ($configAreas['area_1'] ?? 'Área N° 1'),
                                                            'area_2' => $exp->responsable_area_2 ?: ($configAreas['area_2'] ?? 'Área N° 2'),
                                                            'contabilidad' => $exp->responsable_contabilidad ?: 'Contabilidad APOS',
                                                            default => 'APOS',
                                                        };
                                                    @endphp
                                                    @foreach($etapa->procedimientos as $proc)
                                                        @php
                                                            $cump = $cumplidosMap->get($proc->id);
                                                            $estaTildado = $etapaCerrada || ($cump && $cump->cumplido);
                                                        @endphp
                                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                                                            <td class="fw-bold text-{{ $etapa->color }}">
                                                                {{ $etapa->nombre }}
                                                            </td>
                                                            <td>
                                                                <span class="text-white">{{ $proc->nombre }}</span>
                                                            </td>
                                                            <td>
                                                                @if($estaTildado)
                                                                    <span class="badge bg-success bg-opacity-25 text-success border border-success" style="font-size: 0.68rem;">
                                                                        ☑️ Autorizado ({{ $cump->responsable_nombre ?? $responsableEtapa }})
                                                                    </span>
                                                                @else
                                                                    <span class="badge bg-warning bg-opacity-25 text-warning border border-warning" style="font-size: 0.68rem;">
                                                                        ⏳ Pendiente de Tilde
                                                                    </span>
                                                                @endif
                                                            </td>
                                                            <td class="text-end">
                                                                @if($estaTildado)
                                                                    <span class="text-success fw-bold" style="font-size: 0.72rem;">
                                                                        <i class="fas fa-lock me-1"></i>Cerrado OK
                                                                    </span>
                                                                @else
                                                                    <form action="{{ route('expedientes.flujo.tildar', $exp->id) }}" method="POST" class="d-inline">
                                                                        @csrf
                                                                        <input type="hidden" name="procedimiento_area_id" value="{{ $proc->id }}">
                                                                        <button type="submit" class="btn btn-sm btn-outline-warning py-0 px-2 fw-bold" style="font-size: 0.7rem;" title="Poner Tilde y Sello de Validación en la Tabla">
                                                                            <i class="far fa-check-square me-1"></i> Tildar Validación
                                                                        </button>
                                                                    </form>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- BOTONERA RÁPIDA DE AVANCE DIRECTO AL SIGUIENTE SECTOR -->
                            <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center pt-2 border-top border-secondary border-opacity-25">
                                @if(!$exp->ok_docente_padre)
                                    <form action="{{ route('expedientes.flujo.avanzar', $exp->id) }}" method="POST" class="w-100">
                                        @csrf
                                        <input type="hidden" name="accion_etapa" value="docente_padre">
                                        <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2 shadow-sm">
                                            <i class="fas fa-chalkboard-teacher me-1"></i> Completar Etapa 2 (Docente + Directora + CBU) &rarr; Pasar a Auditoría
                                        </button>
                                    </form>
                                @elseif(!$exp->visto_bueno_auditor)
                                    <form action="{{ route('expedientes.flujo.avanzar', $exp->id) }}" method="POST" class="w-100">
                                        @csrf
                                        <input type="hidden" name="accion_etapa" value="auditor">
                                        <button type="submit" class="btn btn-warning text-dark w-100 rounded-pill fw-bold py-2 shadow-sm">
                                            <i class="fas fa-user-check me-1"></i> Tildar Validación Etapa 3 (Auditoría Docente) &rarr; Pasar a {{ $configAreas['area_1'] ?? 'Área N° 1' }}
                                        </button>
                                    </form>
                                @elseif(!$exp->ok_area_1)
                                    <form action="{{ route('expedientes.flujo.avanzar', $exp->id) }}" method="POST" class="w-100">
                                        @csrf
                                        <input type="hidden" name="accion_etapa" value="area_1">
                                        <button type="submit" class="btn btn-info text-dark w-100 rounded-pill fw-bold py-2 shadow-sm">
                                            <i class="fas fa-clipboard-check me-1"></i> Poner Sello de Validación en {{ $configAreas['area_1'] ?? 'Área N° 1' }} &rarr; Pasar a {{ $configAreas['area_2'] ?? 'Área N° 2' }}
                                        </button>
                                    </form>
                                @elseif(!$exp->ok_area_2)
                                    <form action="{{ route('expedientes.flujo.avanzar', $exp->id) }}" method="POST" class="w-100">
                                        @csrf
                                        <input type="hidden" name="accion_etapa" value="area_2">
                                        <button type="submit" class="btn btn-danger w-100 rounded-pill fw-bold py-2 shadow-sm">
                                            <i class="fas fa-stamp me-1"></i> Poner Sello Final en {{ $configAreas['area_2'] ?? 'Área N° 2' }} &rarr; Emitir Resolución Secuenciada y Pasar a Contabilidad
                                        </button>
                                    </form>
                                @elseif(!$exp->ok_contabilidad)
                                    <form action="{{ route('expedientes.flujo.avanzar', $exp->id) }}" method="POST" class="w-100">
                                        @csrf
                                        <input type="hidden" name="accion_etapa" value="contabilidad">
                                        <button type="submit" class="btn btn-success w-100 rounded-pill fw-bold py-2 shadow-sm">
                                            <i class="fas fa-file-invoice-dollar me-1"></i> Contabilidad: Validar Orden de Pago ({{ $exp->nro_resolucion }}) y Enviar a Gobierno e-SIDIF
                                        </button>
                                    </form>
                                @else
                                    <div class="w-100 text-center py-2 rounded-pill fw-bold" style="background: rgba(16, 185, 129, 0.2); border: 1px solid #10b981; color: #34d399; font-size: 0.85rem;">
                                        <i class="fas fa-check-double me-1"></i> Circuito 100% Cerrado &bull; Resolución {{ $exp->nro_resolucion }} &bull; Orden de Pago en Contabilidad / e-SIDIF
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="p-5 text-center bg-dark rounded-4 border border-secondary border-opacity-25">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h5 class="text-white fw-bold">No hay expedientes pendientes en este filtro</h5>
                        <p class="text-white-50 mb-0">Podés iniciar una nueva Pre-Resolución en Mesa de Entrada o ver Todo el Circuito.</p>
                    </div>
                </div>
            @endforelse
        </div>

    </div>
</div>

<!-- MODAL 1: AGREGAR PROCEDIMIENTO / REQUISITO A LA TABLA DE UN ÁREA -->
<div class="modal fade" id="modalAgregarProcedimiento" tabindex="-1" aria-hidden="true" style="color: #0f172a;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; background: #0f172a; color: white; border: 1px solid rgba(56,189,248,0.4) !important;">
            <div class="modal-header border-bottom border-secondary border-opacity-25">
                <h5 class="modal-title fw-bold text-warning">
                    <i class="fas fa-plus-circle me-2"></i> Agregar Procedimiento a la Tabla de un Área
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('expedientes.flujo.procedimiento.agregar') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <p class="small text-white-50 mb-3">
                        Cada área puede empezar con 1 procedimiento (ej: <em>"Procedimiento de validación"</em>) y crecer a 2, 10 o 20 pasos obligatorios.
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-info small">Seleccionar Sector / Área:</label>
                        <select name="area_codigo" class="form-select bg-dark text-white border-secondary" required>
                            <option value="area_1">4. {{ $configAreas['area_1'] ?? 'Área N° 1' }}</option>
                            <option value="area_2">5. {{ $configAreas['area_2'] ?? 'Área N° 2' }}</option>
                            <option value="auditoria_docente">3. Auditoría Docente</option>
                            <option value="docente_padre">2. Área Docente y Directora (CBU / Asistencia)</option>
                            <option value="mesa_entrada">1. {{ $configAreas['mesa_entrada'] ?? 'Mesa de Entrada' }}</option>
                            <option value="contabilidad">6. {{ $configAreas['contabilidad'] ?? 'Contabilidad (Orden de Pago)' }}</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-warning small">Nombre del Procedimiento / Requisito:</label>
                        <input type="text" name="nombre" class="form-control bg-dark text-white border-secondary" placeholder="Ej: Procedimiento de validación / Verificación presupuestaria..." required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold text-white-50 small">Descripción (Opcional):</label>
                        <input type="text" name="descripcion" class="form-control bg-dark text-white border-secondary" placeholder="Detalle de lo que debe controlar el operador de esta área">
                    </div>
                </div>
                <div class="modal-footer border-top border-secondary border-opacity-25">
                    <button type="button" class="btn btn-outline-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning text-dark rounded-pill px-4 fw-bold">
                        <i class="fas fa-save me-1"></i> Guardar en la Tabla del Área
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL 2: RENOMBRAR ÁREA N° 1, ÁREA N° 2 Y CONTABILIDAD -->
<div class="modal fade" id="modalConfigFlujo" tabindex="-1" aria-hidden="true" style="color: #0f172a;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; background: #0f172a; color: white; border: 1px solid rgba(56,189,248,0.4) !important;">
            <div class="modal-header border-bottom border-secondary border-opacity-25">
                <h5 class="modal-title fw-bold text-info">
                    <i class="fas fa-edit me-2"></i> Personalizar Nombres de las Áreas de APOS
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('expedientes.flujo.guardar_etapa') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-info">Nombre Etapa 1 (Mesa de Entrada):</label>
                        <input type="text" name="mesa_entrada" class="form-control bg-dark text-white border-secondary" value="{{ $configAreas['mesa_entrada'] ?? 'Mesa de Entrada APOS' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-warning">Nombre Etapa 4 (Actualmente Área N° 1):</label>
                        <input type="text" name="area_1" class="form-control bg-dark text-white border-secondary" value="{{ $configAreas['area_1'] ?? 'Área N° 1' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-danger">Nombre Etapa 5 (Actualmente Área N° 2):</label>
                        <input type="text" name="area_2" class="form-control bg-dark text-white border-secondary" value="{{ $configAreas['area_2'] ?? 'Área N° 2' }}">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-success">Nombre Etapa 6 (Contabilidad / Orden de Pago):</label>
                        <input type="text" name="contabilidad" class="form-control bg-dark text-white border-secondary" value="{{ $configAreas['contabilidad'] ?? 'Contabilidad y Liquidación (e-SIDIF)' }}">
                    </div>
                </div>
                <div class="modal-footer border-top border-secondary border-opacity-25">
                    <button type="button" class="btn btn-outline-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info text-dark rounded-pill px-4 fw-bold">
                        <i class="fas fa-save me-1"></i> Actualizar Nombres en Todo el Sistema
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function filtrarSelectOpciones(inputEl, selectId) {
        const term = (inputEl.value || '').toLowerCase().trim();
        const select = document.getElementById(selectId);
        if (!select) return;
        Array.from(select.options).forEach(opt => {
            const txt = (opt.text || '').toLowerCase();
            opt.hidden = term !== '' && !txt.includes(term);
        });
        const firstVisible = Array.from(select.options).find(o => !o.hidden);
        if (firstVisible && term !== '') {
            select.value = firstVisible.value;
        }
    }

    function filtrarTarjetasFlujo(query) {
        const q = (query || '').toLowerCase().trim();
        document.querySelectorAll('.item-flujo-searchable').forEach(card => {
            const hay = (card.getAttribute('data-search') || '').includes(q);
            card.style.display = hay ? '' : 'none';
            card.querySelectorAll('.js-hl-text').forEach(span => {
                const raw = span.getAttribute('data-raw') || span.textContent;
                if (!span.getAttribute('data-raw')) span.setAttribute('data-raw', raw);
                if (!q) {
                    span.innerHTML = raw;
                } else {
                    const regex = new RegExp(`(${q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
                    span.innerHTML = raw.replace(regex, '<mark style="background:#facc15;color:#0f172a;padding:0 3px;border-radius:4px;font-weight:800;">$1</mark>');
                }
            });
        });
    }
</script>
@endsection
