@extends('layouts.tenant')

@section('title', 'Inicio')

@section('content')
    @if(($empresa->tipo_entidad ?? '') === 'subagencia')
        <!-- VISTA NIVEL 2: PANEL DE SUCURSAL / MARCA BLANCA PROVINCIAL (EJ. SOLUCIONES DIGITALES MCR) -->
        <div class="content-card mb-4 border-0 text-white shadow" style="background: linear-gradient(135deg, #1e1b4b 0%, #3730a3 60%, #4f46e5 100%); border-radius: 18px;">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ asset('images/mcr_logo.svg') }}" alt="{{ $empresa->nombre }}" class="rounded-4 shadow" style="width: 72px; height: 72px; background: #fff; padding: 3px;">
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                            <span class="badge bg-warning text-dark fw-bold px-3 py-1 rounded-pill" style="font-size: 0.72rem;">
                                <i class="fas fa-network-wired me-1"></i>NIVEL 2 &bull; SUCURSAL / MARCA BLANCA PROVINCIAL ({{ strtoupper($empresa->provincia->nombre ?? 'LA RIOJA') }})
                            </span>
                            <span class="badge bg-light bg-opacity-25 text-white border border-light border-opacity-50 px-3 py-1 rounded-pill" style="font-size: 0.72rem;">
                                <i class="fas fa-layer-group me-1"></i>Plataforma Madre: INTEGRA
                            </span>
                        </div>
                        <h4 class="fw-bold mb-1">{{ $empresa->nombre }} &mdash; Cartera de Clientes en {{ $empresa->provincia->nombre ?? 'La Rioja' }}</h4>
                        <p class="mb-0 small text-white-50">
                            Desde esta Sucursal Provincial administrás las Obras Sociales y clientes asignados a tu provincia.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cartera de Clientes (Obras Sociales) dentro de esta Sucursal Provincial -->
        <div class="content-card mb-4 border-0 shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                <h5 class="fw-bold text-primary mb-0">
                    <i class="fas fa-hospital-user me-2"></i> Clientes / Obras Sociales de {{ $empresa->nombre }} (Nivel 3)
                </h5>
                <span class="badge bg-primary rounded-pill">{{ isset($clientesHijos) ? $clientesHijos->count() : 0 }} Obra(s) Social(es) Activa(s)</span>
            </div>
            <div class="row g-3">
                @forelse($clientesHijos ?? [] as $clienteHijo)
                    @php
                        $adminHijo = \App\Models\User::where('empresa_id', $clienteHijo->id)->first();
                    @endphp
                    <div class="col-md-12">
                        <div class="p-3 rounded-4 border d-flex flex-wrap align-items-center justify-content-between gap-3" style="background-color: #f8fafc; border-left: 5px solid #0284c7 !important;">
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ asset('images/apos_logo.svg') }}" alt="{{ $clienteHijo->nombre }}" class="rounded-3 shadow-sm" style="width: 58px; height: 58px;">
                                <div>
                                    <span class="badge bg-info text-dark fw-bold mb-1" style="font-size: 0.68rem;">CLIENTE NIVEL 3 &bull; OBRA SOCIAL PROVINCIAL</span>
                                    <h5 class="fw-bold mb-0 text-dark">{{ $clienteHijo->nombre }}</h5>
                                    <small class="text-muted">
                                        Cobertura: <strong>{{ number_format($clienteHijo->capitas_afiliados ?: 160000, 0, ',', '.') }} Afiliados</strong> &bull;
                                        Comisión Sucursal: <strong>{{ number_format($clienteHijo->comision_subagencia_pct ?: 15, 0) }}%</strong>
                                    </small>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                @if($adminHijo)
                                    <a href="{{ route('impersonate.enter', $adminHijo->id) }}" class="btn btn-primary fw-bold rounded-pill px-4 shadow-sm">
                                        <i class="fas fa-sign-in-alt me-1"></i> Entrar a Administrar {{ $clienteHijo->nombre }}
                                    </a>
                                @endif
                                <a href="{{ route('owner.liquidaciones') }}" class="btn btn-outline-success fw-bold rounded-pill px-3">
                                    <i class="fas fa-file-csv me-1"></i> Liquidación e-SIDIF
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-4 text-muted">
                        No hay clientes cargados aún dentro de esta sucursal provincial.
                    </div>
                @endforelse
            </div>
        </div>
    @else
        <!-- VISTA NIVEL 3: CABECERA INSTITUCIONAL DEL CLIENTE (APOS LA RIOJA) DENTRO DE SU SUCURSAL (SOLUCIONES DIGITALES MCR) -->
        <div class="content-card mb-4 border-0 text-white shadow" style="background: linear-gradient(135deg, #0f172a 0%, #0369a1 60%, #0284c7 100%); border-radius: 18px;">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ asset('images/apos_logo.svg') }}" alt="APOS La Rioja" class="rounded-4 shadow" style="width: 72px; height: 72px; background: #fff; padding: 3px;">
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                            <span class="badge bg-info text-dark fw-bold px-3 py-1 rounded-pill" style="font-size: 0.72rem;">
                                <i class="fas fa-landmark me-1"></i>NIVEL 3 &bull; OBRA SOCIAL PROVINCIAL - LA RIOJA
                            </span>
                            <span class="badge bg-light bg-opacity-25 text-white border border-light border-opacity-50 px-3 py-1 rounded-pill" style="font-size: 0.72rem;">
                                <i class="fas fa-sitemap me-1"></i>Sucursal Provincial: {{ $empresa->parent->nombre ?? $empresa->subagencia_nombre ?? 'Soluciones Digitales MCR' }}
                            </span>
                        </div>
                        <h4 class="fw-bold mb-1">APOS &mdash; Área de Docentes Integradores y Educación Especial</h4>
                        <p class="mb-0 small text-white-50">
                            Cobertura Provincial: <strong class="text-white">160.000 Afiliados (Titulares y Adicionales)</strong> &bull; Padrón Oficial Activo de Titulares, Alumnos con CUD, Docentes y Escuelas de La Rioja.
                        </p>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ url('/app-docente') }}" target="_blank" class="btn btn-light fw-bold rounded-pill px-3 shadow-sm text-primary">
                        <i class="fas fa-mobile-screen-button me-1"></i> App Docente
                    </a>
                    <a href="{{ route('auditor.docentes.legajos') }}" class="btn btn-warning fw-bold rounded-pill px-3 shadow-sm text-dark">
                        <i class="fas fa-user-check me-1"></i> Panel Auditor
                    </a>
                    <a href="{{ url('/app-directora') }}" target="_blank" class="btn btn-info fw-bold rounded-pill px-3 shadow-sm text-white">
                        <i class="fas fa-school me-1"></i> App Directora
                    </a>
                    <a href="{{ route('owner.liquidaciones') }}" class="btn btn-success fw-bold rounded-pill px-3 shadow-sm text-white">
                        <i class="fas fa-file-csv me-1"></i> Liquidación e-SIDIF
                    </a>
                    <a href="{{ route('panel.accesos') }}" class="btn btn-outline-light fw-bold rounded-pill px-3">
                        <i class="fas fa-share-nodes me-1"></i> Links por Rol
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- Ecosistema Metrics (Clickables) -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <a href="{{ route('tenant.titulares.index') }}" class="text-decoration-none">
                <div class="content-card mb-0 h-100 d-flex align-items-center justify-content-between hover-shadow">
                    <div>
                        <p class="text-muted small fw-bold text-uppercase mb-1">Padres Titulares (Área)</p>
                        <h3 class="fw-bold mb-0 text-primary">{{ $totalAfiliados ?? 0 }}</h3>
                        <small class="text-muted">De 160.000 cápitas APOS</small>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary" style="width: 54px; height: 54px; font-size: 1.5rem;">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('tenant.familiares.index') }}" class="text-decoration-none">
                <div class="content-card mb-0 h-100 d-flex align-items-center justify-content-between hover-shadow">
                    <div>
                        <p class="text-muted small fw-bold text-uppercase mb-1">Chicos / Alumnos CUD</p>
                        <h3 class="fw-bold mb-0 text-success">{{ $totalFamiliares ?? 0 }}</h3>
                        <small class="text-muted">Con patología y resolución</small>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success" style="width: 54px; height: 54px; font-size: 1.5rem;">
                        <i class="fas fa-child"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('tenant.docentes.index') }}" class="text-decoration-none">
                <div class="content-card mb-0 h-100 d-flex align-items-center justify-content-between hover-shadow">
                    <div>
                        <p class="text-muted small fw-bold text-uppercase mb-1">Docentes / Terapeutas</p>
                        <h3 class="fw-bold mb-0 text-info">{{ $totalDocentes ?? 0 }}</h3>
                        <small class="text-muted">Padrón La Rioja activo</small>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-info bg-opacity-10 text-info" style="width: 54px; height: 54px; font-size: 1.5rem;">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('tenant.escuelas.index') }}" class="text-decoration-none">
                <div class="content-card mb-0 h-100 d-flex align-items-center justify-content-between hover-shadow">
                    <div>
                        <p class="text-muted small fw-bold text-uppercase mb-1">Escuelas Vinculadas</p>
                        <h3 class="fw-bold mb-0 text-warning">{{ $totalEscuelas ?? 0 }}</h3>
                        <small class="text-muted">Provincia de La Rioja</small>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning" style="width: 54px; height: 54px; font-size: 1.5rem;">
                        <i class="fas fa-school"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- ============================================================================================ -->
    <!-- CENTRO EN VIVO APOS: MESA DE ENTRADA (PRE-RESOLUCIÓN) Y CIRCUITO AUDITORÍA -> ÁREA 1 -> ÁREA 2 -> CONTABILIDAD -->
    <!-- ============================================================================================ -->
    <div class="row g-4 mb-4">
        <!-- Columna Izquierda: Mesa de Entrada (Generar Pre-Resolución con o sin Docente) -->
        <div class="col-lg-5">
            <div class="content-card h-100 border-0 shadow-sm" style="border-top: 4px solid #0284c7 !important;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="fas fa-inbox text-primary me-2"></i> 1. Mesa de Entrada: Generar Pre-Resolución
                    </h5>
                    <span class="badge bg-info text-dark fw-bold rounded-pill">Etapa 1 del Circuito</span>
                </div>
                <p class="text-muted small mb-3">
                    El padre solicita el trámite para su hijo/a. Si aún no habló con ningún docente, dejá <strong>"Sin Docente aún"</strong>: cuando el padre hable con la maestra y le pase el N° de Pre-Resolución, ella lo busca en su App y se lo asigna a sí misma.
                </p>

                <form action="{{ route('auditor.resolucion.vincular') }}" method="POST">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label fw-bold small text-muted mb-1">1° Padre Titular y Niño / Alumno (Padrón APOS) *</label>
                        <select name="alumno_id" class="form-select form-select-sm bg-light fw-semibold" required>
                            <option value="">-- Seleccionar Titular y Alumno Afiliado APOS --</option>
                            @foreach($alumnosApos ?? [] as $al)
                                <option value="{{ $al->id }}">
                                    {{ $al->nombre_completo }} (DNI {{ $al->dni ?? 'S/D' }}) — Padre: {{ $al->titular->nombre_completo ?? 'Titular' }} (Af. {{ $al->titular->nro_afiliado ?? '' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold small text-muted mb-1">2° Docente Integradora (Opcional en Mesa de Entrada)</label>
                        <select name="docente_id" class="form-select form-select-sm bg-light fw-semibold">
                            <option value="">🔓 SIN DOCENTE AÚN (El padre le dará el N° a la maestra para que se lo asigne)</option>
                            @foreach($docentesApos ?? [] as $docItem)
                                <option value="{{ $docItem->id }}">
                                    {{ $docItem->nombre_completo }} (DNI {{ $docItem->dni ?? 'S/D' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-7">
                            <label class="form-label fw-bold small text-muted mb-1">N° Pre-Resolución / Pre-Expediente</label>
                            <input type="text" name="nro_pre_expediente" class="form-control form-control-sm fw-bold border-warning" placeholder="Automático (Ej: PRE-EXP-2026-0001)">
                        </div>
                        <div class="col-5">
                            <label class="form-label fw-bold small text-warning mb-1">Horas por Día (Máx. 3) *</label>
                            <input type="number" name="horas_diarias" min="1" max="12" value="3" class="form-control form-control-sm fw-bold text-center text-warning border-warning js-input-horas-diarias" required>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-7">
                            <label class="form-label fw-bold small text-muted mb-1">Escuela del Niño</label>
                            <select name="escuela_id" class="form-select form-select-sm bg-light">
                                <option value="">-- Conservar escuela del alumno --</option>
                                @foreach($escuelasApos ?? [] as $escItem)
                                    <option value="{{ $escItem->id }}">{{ $escItem->nombre_completo }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-5">
                            <label class="form-label fw-bold small text-muted mb-1">Etapa Inicial</label>
                            <select name="accion_inmediata" class="form-select form-select-sm bg-light fw-bold">
                                <option value="mesa_entrada">📥 Mesa de Entrada</option>
                                <option value="pendiente">⏳ En Auditoría</option>
                                <option value="aprobar_ya">✅ Emitir Resolución</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold shadow-sm">
                        <i class="fas fa-folder-plus me-1"></i> Generar Pre-Resolución en Mesa de Entrada
                    </button>
                </form>
            </div>
        </div>

        <!-- Columna Derecha: Tablero en Tiempo Real del Circuito Completo (Auditoría -> Área 1 -> Área 2 -> Contabilidad) -->
        <div class="col-lg-7">
            <div class="content-card h-100 border-0 shadow-sm" style="border-top: 4px solid #10b981 !important;">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="fas fa-sitemap text-success me-2"></i> 2. Circuito: Mesa de Entrada &rarr; Auditoría &rarr; Área 1 &rarr; Área 2 &rarr; Contabilidad
                        </h5>
                        <small class="text-muted">Cada sector tilda su tabla de procedimientos ("Procedimiento de validación"). Al cerrarse Área 2 se emite la Resolución Secuenciada y pasa a Contabilidad.</small>
                    </div>
                    <div class="d-flex gap-1">
                        <a href="{{ route('expedientes.flujo') }}" class="btn btn-sm btn-warning text-dark rounded-pill fw-bold">
                            <i class="fas fa-table me-1"></i> Tablas por Área
                        </a>
                        <a href="{{ url('/app-docente') }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill fw-bold">
                            <i class="fas fa-mobile-screen-button me-1"></i> App Docente
                        </a>
                    </div>
                </div>

                <div class="input-group input-group-sm mb-3">
                    <span class="input-group-text bg-warning text-dark border-warning fw-bold"><i class="fas fa-search me-1"></i> Buscar en Vivo</span>
                    <input type="text" id="buscadorCircuitoTenant" class="form-control border-warning fw-semibold" placeholder="Filtrar por N° Pre-Resolución, Padre Titular, Alumno o Docente (resalta en amarillo)...">
                </div>

                <div style="max-height: 360px; overflow-y: auto;">
                    <table class="table table-sm table-hover align-middle mb-0" id="tablaCircuitoTenant">
                        <thead class="table-light small text-uppercase">
                            <tr>
                                <th>Pre-Resolución / Niño / Padre</th>
                                <th>Docente / Escuela</th>
                                <th class="text-center">Estado por Sector</th>
                                <th class="text-end">Tilde de Validación</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($expedientesEnVivo ?? [] as $expItem)
                                @php
                                    $titObj = $expItem->titular ?? ($expItem->alumno ? $expItem->alumno->titular : null);
                                    $vbAud = (bool)($expItem->visto_bueno_auditor ?? false);
                                    $okA1 = (bool)($expItem->ok_area_1 ?? false);
                                    $okA2 = (bool)($expItem->ok_area_2 ?? ($expItem->estado_auditoria === 'aprobado'));
                                    $okCont = (bool)($expItem->ok_contabilidad ?? false);
                                    $preCod = $expItem->codigo_pre_expediente;
                                @endphp
                                <tr>
                                    <td>
                                        <span class="badge bg-dark text-warning fw-bold font-monospace mb-1">
                                            <i class="fas fa-folder-open me-1"></i>{{ $preCod }}
                                        </span>
                                        @if($expItem->tieneResolucionOficial())
                                            <span class="badge bg-success fw-bold font-monospace mb-1">
                                                {{ $expItem->nro_resolucion }}
                                            </span>
                                        @endif
                                        <div class="fw-bold text-dark small">{{ $expItem->alumno->nombre_completo ?? 'Alumno' }}</div>
                                        <div class="text-muted" style="font-size: 0.72rem;">
                                            Padre: <strong>{{ $titObj->nombre_completo ?? 'Titular APOS' }}</strong> (Af. {{ $titObj->nro_afiliado ?? 'S/D' }})
                                        </div>
                                    </td>
                                    <td class="small">
                                        <div class="fw-bold {{ $expItem->docente ? 'text-primary' : 'text-danger' }}">
                                            {{ $expItem->docente ? $expItem->docente->nombre_completo : 'Sin Docente (Disponible por N°)' }}
                                        </div>
                                        <div class="text-muted" style="font-size: 0.72rem;">{{ $expItem->escuela->nombre_completo ?? 'Escuela Provincial' }}</div>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex flex-wrap gap-1 justify-content-center">
                                            <span class="badge {{ $vbAud ? 'bg-success' : 'bg-secondary bg-opacity-25 text-dark' }}" style="font-size: 0.64rem;">
                                                Aud: {{ $vbAud ? '✓' : '⏳' }}
                                            </span>
                                            <span class="badge {{ $okA1 ? 'bg-success' : 'bg-secondary bg-opacity-25 text-dark' }}" style="font-size: 0.64rem;">
                                                Área 1: {{ $okA1 ? '✓' : '⏳' }}
                                            </span>
                                            <span class="badge {{ $okA2 ? 'bg-success' : 'bg-secondary bg-opacity-25 text-dark' }}" style="font-size: 0.64rem;">
                                                Área 2: {{ $okA2 ? '✓' : '⏳' }}
                                            </span>
                                            <span class="badge {{ $okCont ? 'bg-success' : 'bg-secondary bg-opacity-25 text-dark' }}" style="font-size: 0.64rem;">
                                                Contab: {{ $okCont ? '✓' : '⏳' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <form action="{{ route('auditor.expedientes.aprobar', $expItem->id) }}" method="POST" class="d-inline-flex gap-1 flex-wrap justify-content-end">
                                            @csrf
                                            @if(!$vbAud)
                                                <button type="submit" name="rol_aprobacion" value="auditor" class="btn btn-sm btn-warning text-dark py-0 px-2 fw-bold" style="font-size: 0.68rem;">
                                                    ☑️ Tildar Auditoría
                                                </button>
                                            @elseif(!$okA1)
                                                <button type="submit" name="rol_aprobacion" value="area_1" class="btn btn-sm btn-info text-dark py-0 px-2 fw-bold" style="font-size: 0.68rem;">
                                                    ☑️ Tildar Área 1
                                                </button>
                                            @elseif(!$okA2)
                                                <button type="submit" name="rol_aprobacion" value="area_2" class="btn btn-sm btn-danger py-0 px-2 fw-bold" style="font-size: 0.68rem;">
                                                    ☑️ Tildar Área 2 (Emitir Res.)
                                                </button>
                                            @elseif(!$okCont)
                                                <button type="submit" name="rol_aprobacion" value="contabilidad" class="btn btn-sm btn-success py-0 px-2 fw-bold" style="font-size: 0.68rem;">
                                                    💳 Tildar Contabilidad (Orden Pago)
                                                </button>
                                            @else
                                                <span class="text-success fw-bold small"><i class="fas fa-check-double me-1"></i>Orden de Pago OK</span>
                                            @endif
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3 small">No hay pre-resoluciones registradas aún.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Sincronización automática en tiempo real en el Panel de Administración (/tenant)
    let tenantUltimoIdNovedad = {{ $ultimaNovedadId ?? 0 }};
    setInterval(function() {
        fetch("{{ route('api.auditor.novedades_live') }}")
            .then(r => r.json())
            .then(data => {
                if (data && data.latest_id && data.latest_id > tenantUltimoIdNovedad) {
                    tenantUltimoIdNovedad = data.latest_id;
                    if (typeof window.integraModalElegante === 'function') {
                        window.integraModalElegante({
                            tipo: 'success',
                            badge: 'REFLEJO AUTOMÁTICO EN VIVO',
                            titulo: 'Movimiento Detectado en Tiempo Real',
                            mensaje: (data.latest_descripcion || 'La docente o el auditor acaba de actualizar un trámite.') + ' Actualizando tablero automáticamente...',
                            autoCerrarMs: 3200
                        });
                    }
                    setTimeout(function() { window.location.reload(); }, 3200);
                }
            })
            .catch(() => {});
    }, 4000);
    </script>

    <!-- Banner Reportar Pago por Transferencia sin Comisiones -->
    <div class="row mt-4 mb-4">
        <div class="col-12">
            <div class="content-card d-flex align-items-center justify-content-between flex-wrap gap-3" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(168, 85, 247, 0.1)); border: 1px solid rgba(59, 130, 246, 0.2);">
                <div>
                    <h5 class="fw-bold mb-1"><i class="fas fa-file-invoice-dollar text-primary me-2"></i> Reportar Pago de Abono (Transferencia Bancaria)</h5>
                    <p class="mb-0 text-muted small">Transfiere directamente a nuestras cuentas (Santander, Banco Provincia, ARQ/DollarApp) y sube tu comprobante para lectura automática con IA.</p>
                </div>
                <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#reportarPagoModal">
                    <i class="fas fa-upload me-2"></i> Subir Comprobante
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Reportar Pago -->
    <div class="modal fade" id="reportarPagoModal" tabindex="-1" aria-hidden="true" style="color: #0f172a;">
        <div class="modal-dialog">
            <div class="modal-content border-0" style="border-radius: 15px;">
                <div class="modal-header bg-primary text-white border-0" style="border-radius: 15px 15px 0 0;">
                    <h5 class="modal-title fw-bold"><i class="fas fa-receipt me-2"></i> Subir Comprobante de Pago</h5>
                    <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('pagos.reportar') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="empresa_id" value="{{ Auth::user()->empresa_id ?? session('impersonated_tenant_id') ?? 1 }}">
                    <div class="modal-body p-4">
                        <div class="alert alert-info py-2 small mb-3">
                            <i class="fas fa-robot text-primary me-1"></i> Nuestra IA (Gemini Vision) extraerá automáticamente el N° de Operación, Monto y Banco de la captura.
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Foto / Captura del Comprobante (JPG, PNG, PDF)</label>
                            <input type="file" name="comprobante" class="form-control" accept="image/*,application/pdf" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pb-4 pe-4">
                        <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Procesar y Enviar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Setup Marca Blanca -->
    <div class="modal fade" id="setupModal" tabindex="-1" aria-hidden="true" style="color: #0f172a;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0" style="border-radius: 15px;">
                <div class="modal-header bg-light border-0" style="border-radius: 15px 15px 0 0;">
                    <h5 class="modal-title fw-bold"><i class="fas fa-paint-roller text-primary me-2"></i> Setup de Marca (Whitelabel)</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <!-- IMPORTANTE: El backend confía que Empresa existe mediante $empresa pasada por el Controlador TenantDashboardController -->
                <form action="{{ route('tenant.setup.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label fw-bold small text-muted">Nombre Público de la Institución</label>
                                <input type="text" name="nombre" class="form-control bg-light border-0" value="{{ $empresa->nombre ?? '' }}" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label class="form-label fw-bold small text-muted">A qué Provincia pertenece esta institución</label>
                                <select name="provincia_id" id="setupProvincia" class="form-select bg-light border-0">
                                    <option value="">- Seleccionar Provincia -</option>
                                    @foreach($provincias as $prov)
                                        <option value="{{ $prov->id }}" {{ ($empresa->provincia_id == $prov->id) ? 'selected' : '' }}>{{ $prov->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Localidad Principal</label>
                                <select name="localidad_id" id="setupLocalidad" class="form-select bg-light border-0" {{ $empresa->provincia_id ? '' : 'disabled' }}>
                                    <option value="">- Elija Provincia primero -</option>
                                    @if(isset($localidadesEmpresa) && count($localidadesEmpresa)>0)
                                        @foreach($localidadesEmpresa as $loc)
                                            <option value="{{ $loc->id }}" {{ ($empresa->localidad_id == $loc->id) ? 'selected' : '' }}>{{ $loc->nombre }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Color Primario</label>
                                <input type="color" name="color_primario" class="form-control form-control-color w-100 border-0" value="{{ $empresa->color_primario ?? '#3b82f6' }}">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Color Secundario (Menú)</label>
                                <input type="color" name="color_secundario" class="form-control form-control-color w-100 border-0" value="{{ $empresa->color_secundario ?? '#1e293b' }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Subir Logotipo Vectorial</label>
                            <input type="file" name="logo" class="form-control bg-light border-0" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer border-0 pb-4 pe-4">
                        <button type="submit" class="btn btn-primary rounded-pill px-4" style="background-color: var(--brand-primary); border: none;">Guardar Apariencia</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Lógica JS dependiente para Combos Provincia -> Localidad en Tenant
        const selProvincia = document.getElementById('setupProvincia');
        const selLocalidad = document.getElementById('setupLocalidad');

        if(selProvincia) {
            selProvincia.addEventListener('change', async function() {
                const provId = this.value;
                selLocalidad.innerHTML = '<option value="">- Cargando... -</option>';
                selLocalidad.disabled = true;

                if(!provId) {
                    selLocalidad.innerHTML = '<option value="">- Elija Provincia primero -</option>';
                    return;
                }

                try {
                    // Llamamos a la API interna
                    const r = await fetch('/api/localidades/' + provId);
                    const data = await r.json();
                    
                    selLocalidad.innerHTML = '<option value="">- Seleccione una Localidad -</option>';
                    data.forEach(l => { 
                        selLocalidad.innerHTML += `<option value="${l.id}">${l.nombre}</option>`; 
                    });
                    selLocalidad.disabled = false;
                } catch (e) {
                    console.error("Error obteniendo localidades", e);
                    selLocalidad.innerHTML = '<option value="">Error de conexión</option>';
                }
            });
        }
    });
</script>
@endpush
