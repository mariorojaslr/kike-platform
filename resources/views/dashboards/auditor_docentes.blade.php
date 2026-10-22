@extends('layouts.tenant')

@section('title', 'Panel Central del Auditor - Vinculaciones, Resoluciones y Legajos APOS')

@section('content')
<div class="container-fluid py-2">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-4 mb-3" role="alert">
            <i class="fas fa-check-circle me-2 fa-lg"></i> <strong>{{ session('success') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Alerta Dinámica de Nuevas Novedades Detectadas en Vivo (sin recargar) -->
    <div id="liveAuditorAlertBanner" class="alert border-0 shadow-lg rounded-4 mb-4 p-3 text-white" style="display: none; background: linear-gradient(135deg, #b45309 0%, #d97706 100%);">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-white text-warning rounded-circle d-flex align-items-center justify-content-center shadow" style="width: 48px; height: 48px; font-size: 1.4rem; flex-shrink: 0;">
                    <i class="fas fa-bell fa-shake"></i>
                </div>
                <div>
                    <span class="badge bg-dark text-warning fw-bold mb-1">¡NUEVA INTERVENCIÓN / VINCULACIÓN EN TIEMPO REAL!</span>
                    <h6 class="fw-bold mb-0 text-white" id="liveAuditorAlertText">Un docente acaba de señalar un alumno / cargar una Resolución.</h6>
                </div>
            </div>
            <button type="button" class="btn btn-light fw-bold rounded-pill px-4 shadow-sm text-dark" onclick="window.location.reload()">
                <i class="fas fa-sync-alt me-1"></i> Actualizar Bandeja Ahora
            </button>
        </div>
    </div>

    <!-- Header del Auditor con Logo Oficial APOS y Accesos Directos -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div class="d-flex align-items-center gap-3">
            <img src="{{ asset('images/apos_logo.svg') }}" alt="APOS La Rioja" class="rounded-3 shadow-sm border" style="height: 58px; width: auto; background: #fff; padding: 4px 8px;">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                    <span class="badge bg-warning text-dark fw-bold rounded-pill px-3 py-1">
                        <i class="fas fa-bolt me-1"></i> MONITOREO EN TIEMPO REAL ACTIVO
                    </span>
                    <span class="badge bg-primary text-white rounded-pill px-3 py-1">
                        APOS La Rioja &bull; Área Docentes Integradores
                    </span>
                </div>
                <h3 class="fw-bold mb-1"><i class="fas fa-user-check text-primary me-2"></i> Panel del Auditor: Resoluciones, Vínculos y Legajos</h3>
                <p class="text-muted small mb-0">
                    Cada vez que un docente señala a un alumno o carga una Resolución, impacta aquí en forma inmediata vinculando <strong>Alumno + Padre Titular + Docente</strong> para tu visado.
                </p>
            </div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAltaResolucionAuditor">
                <i class="fas fa-file-signature me-1"></i> + Alta / Vincular Resolución a Alumno
            </button>
            <a href="{{ url('/app-docente/demo') }}" target="_blank" class="btn btn-outline-primary rounded-pill px-3 fw-bold">
                <i class="fas fa-mobile-screen-button me-1"></i> Probar desde App Docente
            </a>
            <a href="{{ route('auditor.novedades') }}" class="btn btn-dark rounded-pill px-3 fw-bold">
                <i class="fas fa-stream me-1 text-warning"></i> Historial Completo ({{ count($novedades ?? []) }})
            </a>
        </div>
    </div>

    <!-- ======================================================================================== -->
    <!-- SECCIÓN 1 (AL ALCANCE DE LA MANO): BANDEJA INMEDIATA DE VINCULACIONES Y RESOLUCIONES     -->
    <!-- ======================================================================================== -->
    <div class="row g-4 mb-4">
        <!-- Columna Izquierda: Expedientes / Vinculaciones Docente-Alumno-Padre para Visar en 1 Clic -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; border-top: 4px solid #f59e0b !important;">
                <div class="card-header bg-white border-0 pt-3 pb-2 px-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="fas fa-bell text-warning me-2"></i> Bandeja Inmediata del Auditor (Vínculos Alumno – Padre – Docente)
                        </h5>
                        <small class="text-muted">Al alcance de un clic: validá la Resolución tomada por el docente o asignale autorización expresa.</small>
                    </div>
                    <span class="badge {{ count($expedientesPendientes ?? []) > 0 ? 'bg-danger' : 'bg-success' }} rounded-pill px-3 py-2">
                        {{ count($expedientesPendientes ?? []) }} Pendiente(s) de Visado
                    </span>
                </div>
                <div class="card-body px-4 pt-2 pb-3" style="max-height: 560px; overflow-y: auto;">
                    <div class="input-group input-group-sm mb-3">
                        <span class="input-group-text bg-warning text-dark border-warning fw-bold"><i class="fas fa-search me-1"></i> Filtrar Vínculos</span>
                        <input type="text" id="buscadorBandejaResoluciones" class="form-control border-warning fw-semibold" placeholder="Buscar en tiempo real por Alumno, Padre, Docente, DNI o N° de Resolución (marca en amarillo)...">
                    </div>
                    @if(isset($expedientesPendientes) && count($expedientesPendientes) > 0)
                        @foreach($expedientesPendientes as $exp)
                            @php
                                $titularObj = $exp->titular ?? ($exp->alumno ? $exp->alumno->titular : null);
                            @endphp
                            <div class="p-3 mb-3 rounded-4 border shadow-sm item-vinculo-auditor" style="background: #fffbeb; border-left: 5px solid #f59e0b !important;">
                                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-2">
                                    <div>
                                        <span class="badge bg-warning text-dark fw-bold mb-1">
                                            <i class="fas fa-clock me-1"></i> ESTADO: PENDIENTE DE AUDITORÍA (NUEVA VINCULACIÓN)
                                        </span>
                                        <h6 class="fw-bold text-dark mb-0" style="font-size: 1rem;">
                                            <i class="fas fa-chalkboard-teacher text-primary me-1"></i> Docente: <span class="text-primary">{{ $exp->docente->nombre_completo ?? 'Docente APOS' }}</span>
                                        </h6>
                                    </div>
                                    <small class="text-muted font-monospace">
                                        <i class="fas fa-bolt text-warning me-1"></i>{{ $exp->updated_at ? $exp->updated_at->diffForHumans() : 'Reciente' }}
                                    </small>
                                </div>

                                <!-- Triple Vínculo: Alumno + Padre Titular + Escuela/Resolución -->
                                <div class="row g-2 p-2 rounded-3 bg-white border mb-3 small">
                                    <div class="col-md-6">
                                        <div class="text-muted fw-bold" style="font-size: 0.7rem;">ALUMNO / HIJO SEÑALADO:</div>
                                        <div class="fw-bold text-dark">
                                            <i class="fas fa-child text-success me-1"></i> {{ $exp->alumno->nombre_completo ?? 'Alumno' }}
                                            <span class="text-muted">(DNI: {{ $exp->alumno->dni ?? 'S/D' }})</span>
                                        </div>
                                        <div class="text-secondary" style="font-size: 0.75rem;">
                                            CUD: {{ $exp->diagnostico ?: ($exp->alumno->diagnostico_detalle ?? 'CUD Activo') }}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="text-muted fw-bold" style="font-size: 0.7rem;">PADRE / TITULAR VINCULADO:</div>
                                        <div class="fw-bold text-dark">
                                            <i class="fas fa-user-shield text-info me-1"></i> {{ $titularObj ? $titularObj->nombre_completo : 'Titular APOS' }}
                                        </div>
                                        <div class="text-secondary" style="font-size: 0.75rem;">
                                            Afiliado APOS N°: <strong>{{ $titularObj->nro_afiliado ?? 'S/D' }}/{{ $exp->alumno->extension_afiliado ?? '01' }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-12 border-top pt-1 mt-1 d-flex flex-wrap justify-content-between">
                                        <span><i class="fas fa-school text-warning me-1"></i> <strong>Escuela:</strong> {{ $exp->escuela->nombre_completo ?? 'Escuela Provincial' }}</span>
                                        <span><i class="fas fa-calendar-day text-primary me-1"></i> <strong>Horario:</strong> {{ $exp->horarios_atencion ?? '3 hs/día' }} ({{ $exp->horas_mensuales_asignadas ?? 60 }} hs/mes)</span>
                                    </div>
                                </div>

                                <!-- Acción Inmediata del Auditor: Confirmar/Asignar N° de Resolución y Aprobar en 1 Clic -->
                                <form action="{{ route('auditor.expedientes.aprobar', $exp->id) }}" method="POST" class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                    @csrf
                                    <div class="input-group input-group-sm" style="max-width: 310px;">
                                        <span class="input-group-text bg-dark text-warning fw-bold"><i class="fas fa-file-signature me-1"></i> N° Resolución:</span>
                                        <input type="text" name="nro_resolucion" class="form-control fw-bold" value="{{ $exp->nro_resolucion }}" placeholder="Ej: 505/25 o Autorización">
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 fw-bold shadow-sm">
                                            <i class="fas fa-check-double me-1"></i> Aprobar Vínculo y Resolución (1-Clic)
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" data-bs-toggle="collapse" data-bs-target="#rechazarExpTop{{ $exp->id }}">
                                            <i class="fas fa-times me-1"></i> Observar
                                        </button>
                                    </div>
                                </form>

                                <div class="collapse mt-2" id="rechazarExpTop{{ $exp->id }}">
                                    <form action="{{ route('auditor.expedientes.rechazar', $exp->id) }}" method="POST" class="p-2 bg-white rounded border border-danger">
                                        @csrf
                                        <div class="input-group input-group-sm">
                                            <input type="text" name="motivo_rechazo" class="form-control" placeholder="Motivo de observación para la docente..." required>
                                            <button type="submit" class="btn btn-danger fw-bold">Notificar Observación</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-success border-0 rounded-4 py-2 px-3 mb-3 small">
                            <i class="fas fa-check-circle me-1"></i> Todos los vínculos actuales están visados. Debajo se muestran las últimas vinculaciones activas (Resolución + Alumno + Padre + Docente):
                        </div>
                        @foreach(($expedientesRecientes ?? [])->take(5) as $exp)
                            @php
                                $titularObj = $exp->titular ?? ($exp->alumno ? $exp->alumno->titular : null);
                            @endphp
                            <div class="p-2.5 mb-2 rounded-3 border bg-light d-flex flex-wrap justify-content-between align-items-center gap-2 small">
                                <div>
                                    <span class="badge bg-success me-1"><i class="fas fa-link me-1"></i>Res. {{ $exp->nro_resolucion ?: 'Activa' }}</span>
                                    <strong>Docente:</strong> {{ $exp->docente->nombre_completo ?? 'Docente' }}
                                    &rarr; <strong>Alumno:</strong> {{ $exp->alumno->nombre_completo ?? 'Alumno' }}
                                    <span class="text-muted">(Padre: {{ $titularObj->nombre_completo ?? 'Titular' }})</span>
                                </div>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success">Visado OK</span>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Feed de Eventos y Novedades en Tiempo Real -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; border-top: 4px solid #0284c7 !important;">
                <div class="card-header bg-white border-0 pt-3 pb-2 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="fas fa-satellite-dish text-primary me-2"></i> Bitácora Instantánea de Novedades
                        </h5>
                        <small class="text-muted">Registro automático de cada intervención docente o alta de resolución.</small>
                    </div>
                    <span class="badge bg-primary rounded-pill">{{ count($novedades ?? []) }}</span>
                </div>
                <div class="card-body p-0" style="max-height: 520px; overflow-y: auto;">
                    <div class="list-group list-group-flush">
                        @forelse($novedades ?? [] as $nov)
                            <div class="list-group-item px-4 py-3 border-bottom {{ $nov->estado === 'pendiente' ? 'bg-warning bg-opacity-10' : '' }}">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-dark small">
                                        <i class="fas fa-user-graduate text-primary me-1"></i> {{ $nov->docente->nombre_completo ?? 'Docente APOS' }}
                                    </span>
                                    <small class="text-muted font-monospace" style="font-size: 0.72rem;">
                                        {{ $nov->created_at ? $nov->created_at->diffForHumans() : 'Ahora' }}
                                    </small>
                                </div>
                                <p class="mb-2 small text-dark" style="line-height: 1.45;">{{ $nov->descripcion }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge {{ $nov->estado === 'pendiente' ? 'bg-warning text-dark' : ($nov->estado === 'aprobado' ? 'bg-success' : 'bg-danger') }}" style="font-size: 0.68rem;">
                                        {{ strtoupper($nov->estado ?? 'PENDIENTE') }}
                                    </span>
                                    @if($nov->expediente && $nov->expediente->estado_auditoria === 'pendiente')
                                        <form action="{{ route('auditor.expedientes.aprobar', $nov->expediente->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success rounded-pill py-0 px-3 fw-bold" style="font-size: 0.72rem;">
                                                <i class="fas fa-check me-1"></i> Aprobar Ahora
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center text-muted small">
                                Todavía no hay eventos en la bitácora. Cuando un docente seleccione un alumno o cargue una resolución en su App, aparecerá aquí al instante.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ======================================================================================== -->
    <!-- SECCIÓN 2: KPIs Y TABLA DE CUMPLIMIENTO DE LEGAJOS DE LOS 125 DOCENTES DE APOS           -->
    <!-- ======================================================================================== -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 border-start border-4 border-primary" style="border-radius: 12px;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small fw-bold mb-1">TOTAL DOCENTES APOS</p>
                        <h3 class="fw-bold mb-0 text-dark">{{ $totalDocentes }}</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary">
                        <i class="fas fa-user-graduate fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 border-start border-4 border-success" style="border-radius: 12px;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small fw-bold mb-1">LEGAJOS AL DÍA (100%)</p>
                        <h3 class="fw-bold mb-0 text-success">{{ $alDia }}</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success">
                        <i class="fas fa-check-circle fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 border-start border-4 border-warning" style="border-radius: 12px;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small fw-bold mb-1">CON DOCS PENDIENTES</p>
                        <h3 class="fw-bold mb-0 text-warning">{{ $incompletos }}</h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 p-3 rounded-circle text-warning">
                        <i class="fas fa-exclamation-triangle fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 border-start border-4 border-danger" style="border-radius: 12px;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small fw-bold mb-1">OBSERVADOS / INHABILITADOS</p>
                        <h3 class="fw-bold mb-0 text-danger">{{ $suspendidos }}</h3>
                    </div>
                    <div class="bg-danger bg-opacity-10 p-3 rounded-circle text-danger">
                        <i class="fas fa-ban fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Docentes y Legajos -->
    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-header bg-white border-0 pt-4 pb-2 px-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="fw-bold mb-0"><i class="fas fa-list text-primary me-2"></i> Nómina de Docentes Integradores y Cumplimiento de Legajo</h5>
            <div class="input-group input-group-sm" style="max-width: 380px;">
                <span class="input-group-text bg-warning text-dark border-warning fw-bold"><i class="fas fa-search"></i></span>
                <input type="text" id="buscadorNominaDocentes" class="form-control border-warning fw-semibold" placeholder="Buscar Docente, DNI, Teléfono o Especialidad...">
            </div>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="tablaNominaDocentesAuditor">
                    <thead class="bg-light small text-uppercase">
                        <tr>
                            <th>Docente / Terapeuta</th>
                            <th>DNI / Contacto</th>
                            <th>Especialidad</th>
                            <th>Progreso de Legajo</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($docentes as $doc)
                            @php
                                $pct = $doc->porcentaje_legajo ?? 0;
                                $docsCount = $doc->documentos->count();
                                $aprobadosCount = $doc->documentos->where('estado_auditoria', 'aprobado')->count();
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3 fw-bold text-center" style="width:40px;height:40px;line-height:24px;">
                                            {{ strtoupper(substr($doc->nombre_completo, 0, 1)) }}
                                        </div>
                                        <div>
                                            <strong class="text-dark">{{ $doc->nombre_completo }}</strong>
                                            <small class="text-muted d-block">{{ $doc->telefono ?? 'Sin Tel' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="font-monospace text-dark fw-bold">{{ $doc->dni ?? 'Sin DNI' }}</span>
                                    <small class="text-muted d-block">{{ $doc->email }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary bg-opacity-10 text-dark border px-2 py-1">
                                        {{ $doc->formacion->nombre ?? 'Maestra Integradora' }}
                                    </span>
                                </td>
                                <td style="min-width: 200px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 10px; border-radius: 10px;">
                                            <div class="progress-bar {{ $pct == 100 ? 'bg-success' : 'bg-warning' }}" role="progressbar" style="width: {{ $pct }}%;"></div>
                                        </div>
                                        <span class="small fw-bold {{ $pct == 100 ? 'text-success' : 'text-dark' }}">{{ $aprobadosCount }}/{{ max($docsCount, 1) }} ({{ $pct }}%)</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if($doc->estado_legajo === 'al_dia')
                                        <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>AL DÍA</span>
                                    @elseif($doc->estado_legajo === 'suspendido')
                                        <span class="badge bg-danger"><i class="fas fa-ban me-1"></i>SUSPENDIDO</span>
                                    @else
                                        <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>INCOMPLETO</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalLegajo{{ $doc->id }}">
                                        <i class="fas fa-folder-open me-1"></i> Ver Legajo
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No existen docentes registrados en la nómina.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: ALTA / VINCULACIÓN DE RESOLUCIÓN + ALUMNO + PADRE + DOCENTE -->
<div class="modal fade" id="modalAltaResolucionAuditor" tabindex="-1" aria-hidden="true" style="color: #0f172a;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px;">
            <div class="modal-header bg-primary text-white border-0" style="border-radius: 18px 18px 0 0;">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-file-signature me-2"></i> Vincular Resolución APOS: Alumno + Padre Titular + Docente
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('auditor.resolucion.vincular') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 small mb-3">
                        <i class="fas fa-bolt me-1"></i> Al guardar, el sistema vincula inmediatamente al <strong>Alumno</strong> y a su <strong>Padre Titular</strong> con la <strong>Docente</strong> bajo el N° de Resolución indicado y dispara la alerta de auditoría.
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">1. Seleccionar Alumno (y Padre Titular)</label>
                            <select name="alumno_id" class="form-select bg-light" required>
                                <option value="">-- Elegir Alumno Afiliado APOS --</option>
                                @foreach($alumnosApos ?? [] as $al)
                                    <option value="{{ $al->id }}">
                                        {{ $al->nombre_completo }} (DNI: {{ $al->dni ?? 'S/D' }}) — Padre: {{ $al->titular->nombre_completo ?? 'Titular' }} (Af. {{ $al->titular->nro_afiliado ?? '' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">2. Docente Integrador Asignado</label>
                            <select name="docente_id" class="form-select bg-light" required>
                                <option value="">-- Elegir Docente Integrador --</option>
                                @foreach($docentes ?? [] as $d)
                                    <option value="{{ $d->id }}">{{ $d->nombre_completo }} (DNI: {{ $d->dni ?? 'S/D' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">3. N° de Resolución APOS (o dejar vacío p/ Autorización Auditor)</label>
                            <input type="text" name="nro_resolucion" class="form-control bg-light fw-bold" placeholder="Ej: 1584/25 o RES-APOS-2026-089">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">4. Escuela / Establecimiento</label>
                            <select name="escuela_id" class="form-select bg-light">
                                <option value="">-- Conservar escuela del alumno --</option>
                                @foreach($escuelasApos ?? [] as $esc)
                                    <option value="{{ $esc->id }}">{{ $esc->nombre_completo }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-bold small text-muted">Días y Horarios de Atención (Regla Máx. 3 hs/día)</label>
                            <input type="text" name="horarios_atencion" class="form-control bg-light" value="Lunes a Viernes de 08:00 a 11:00 hs (3 hs/día)">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Estado de Auditoría</label>
                            <select name="accion_inmediata" class="form-select bg-light fw-bold">
                                <option value="pendiente">Dejar Pendiente en Bandeja</option>
                                <option value="aprobar_ya">Aprobar Directamente</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 pe-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">
                        <i class="fas fa-link me-1"></i> Registrar Vínculo y Notificar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODALES DE DETALLE DE LEGAJO POR DOCENTE -->
@foreach($docentes as $doc)
    <div class="modal fade" id="modalLegajo{{ $doc->id }}" tabindex="-1" aria-hidden="true" style="color: #0f172a;">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 18px;">
                <div class="modal-header bg-dark text-white border-0" style="border-radius: 18px 18px 0 0;">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-folder me-2 text-warning"></i> Legajo Profesional: {{ $doc->nombre_completo }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3 bg-light p-3 rounded">
                        <div>
                            <strong class="text-dark">Porcentaje de Cumplimiento Documental:</strong>
                            <div class="progress mt-1" style="height: 8px; width: 250px;">
                                <div class="progress-bar bg-success" style="width: {{ $doc->porcentaje_legajo }}%;"></div>
                            </div>
                        </div>
                        <span class="badge bg-primary fs-6">{{ $doc->porcentaje_legajo }}% Aprobado</span>
                    </div>

                    <h6 class="fw-bold text-dark mb-3"><i class="fas fa-file-alt me-1 text-primary"></i> Documentos Presentados</h6>

                    <div class="list-group">
                        @forelse($doc->documentos as $documento)
                            <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center flex-wrap p-3 mb-2 border rounded">
                                <div>
                                    <h6 class="mb-1 fw-bold text-dark"><i class="fas fa-file-check me-2 text-primary"></i> {{ $documento->tipo_documento }}</h6>
                                    <small class="text-muted d-block">Subido el: {{ $documento->created_at->format('d/m/Y H:i') }}</small>
                                    @if($documento->comentarios)
                                        <small class="text-primary d-block">{{ $documento->comentarios }}</small>
                                    @endif
                                    @if($documento->motivo_rechazo)
                                        <div class="alert alert-danger p-2 py-1 small mt-2 mb-0">
                                            <i class="fas fa-exclamation-circle me-1"></i> Motivo de Rechazo: {{ $documento->motivo_rechazo }}
                                        </div>
                                    @endif
                                </div>
                                <div class="d-flex align-items-center gap-2 mt-2 mt-sm-0">
                                    @if($documento->ruta_archivo)
                                        <a href="{{ asset('storage/' . $documento->ruta_archivo) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-eye me-1"></i> Ver
                                        </a>
                                    @endif

                                    @if($documento->estado_auditoria === 'aprobado')
                                        <span class="badge bg-success py-2 px-3"><i class="fas fa-check me-1"></i>Aprobado</span>
                                    @else
                                        <form action="{{ route('auditor.legajo_doc.aprobar', $documento->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success shadow-sm">
                                                <i class="fas fa-check me-1"></i> Aprobar
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-outline-danger shadow-sm" data-bs-toggle="collapse" data-bs-target="#collapseRechazoDoc{{ $documento->id }}">
                                            <i class="fas fa-times me-1"></i> Rechazar
                                        </button>
                                    @endif
                                </div>

                                <div class="collapse w-100 mt-3" id="collapseRechazoDoc{{ $documento->id }}">
                                    <form action="{{ route('auditor.legajo_doc.rechazar', $documento->id) }}" method="POST" class="bg-light p-3 rounded border border-danger">
                                        @csrf
                                        <label class="form-label fw-bold small text-danger">Motivo del Rechazo</label>
                                        <textarea name="motivo_rechazo" class="form-control mb-2" rows="2" required></textarea>
                                        <div class="text-end">
                                            <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3">Enviar Rechazo al Docente</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted small">No se han subido documentos de legajo aún.</div>
                        @endforelse
                    </div>

                    <div class="mt-4 pt-3 border-top">
                        <button class="btn btn-sm btn-outline-primary fw-bold rounded-pill" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSubirAuditor{{ $doc->id }}">
                            <i class="fas fa-file-upload me-1"></i> 📤 Cargar Documento en Nombre del Docente (Vía Auditoría)
                        </button>

                        <div class="collapse mt-3" id="collapseSubirAuditor{{ $doc->id }}">
                            <form action="{{ route('auditor.legajo_doc.subir') }}" method="POST" enctype="multipart/form-data" class="bg-light p-3 rounded-3 border">
                                @csrf
                                <input type="hidden" name="docente_id" value="{{ $doc->id }}">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted">Tipo de Documento *</label>
                                        <select name="tipo_documento" class="form-select form-select-sm" required>
                                            <option value="DNI (Frente y Dorso)">DNI (Frente y Dorso)</option>
                                            <option value="Certificado de Matrícula Profesional">Certificado de Matrícula Profesional</option>
                                            <option value="Certificado de Domicilio">Certificado de Domicilio</option>
                                            <option value="Certificado de Buena Conducta / Antecedentes">Certificado de Buena Conducta / Antecedentes</option>
                                            <option value="Título Profesional / Diploma">Título Profesional / Diploma</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted">Archivo PDF o Imagen *</label>
                                        <input type="file" name="documento" class="form-control form-control-sm" accept="image/*,.pdf" required>
                                    </div>
                                    <div class="col-12 text-end">
                                        <button type="submit" class="btn btn-sm btn-success rounded-pill px-4 fw-bold">
                                            <i class="fas fa-check-circle me-1"></i> Guardar y Aprobar Legajo
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach

<script>
// Detector en vivo para la pantalla del Auditor: si una docente vincula un alumno desde su celular, avisa al instante
let auditorUltimoIdNovedad = {{ isset($novedades) && count($novedades) > 0 ? $novedades->first()->id : 0 }};

setInterval(function() {
    fetch("{{ route('api.auditor.novedades_live') }}")
        .then(res => res.json())
        .then(data => {
            if (data.latest_id && data.latest_id > auditorUltimoIdNovedad) {
                auditorUltimoIdNovedad = data.latest_id;
                const banner = document.getElementById('liveAuditorAlertBanner');
                const txt = document.getElementById('liveAuditorAlertText');
                if (banner && txt) {
                    txt.innerText = data.latest_descripcion || 'Nueva vinculación de alumno y resolución recibida.';
                    banner.style.display = 'block';
                }
                if (typeof integraSonarCampana === 'function') {
                    integraSonarCampana();
                }
            }
        })
        .catch(() => {});
}, 5000);
</script>
@endsection
