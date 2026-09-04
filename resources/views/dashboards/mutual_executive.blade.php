@extends('layouts.app')

@section('title', 'Cuadro de Mando Ejecutivo - Dirección General Mutual')

@section('content')
<div class="container-fluid py-4" style="background-color: #0b1329; min-height: 100vh; color: white;">
    <div class="container">
        
        <!-- Encabezado Ejecutivo del Dueño de la Mutual con Selector de Sucursales -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-3 border-bottom border-secondary border-opacity-25 gap-3">
            <div>
                <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold text-uppercase" style="letter-spacing: 1px;">
                    <i class="fas fa-crown me-1"></i> Dirección General & Consejo de Administración
                </span>
                <h2 class="fw-bold mt-2 mb-0 text-white">
                    <i class="fas fa-chart-line text-success me-2"></i> Cuadro de Mando Ejecutivo Mutual
                </h2>
                <small class="text-muted">
                    <i class="fas fa-users me-1 text-info"></i> Cobertura: <strong>{{ number_format($datosSedeActual->capitas, 0, ',', '.') }} Abonados Activos</strong> | Periodo: {{ $periodo }}
                </small>
            </div>

            <!-- Selector Interactivo de Sucursal / Sede -->
            <div class="d-flex align-items-center gap-2">
                <form action="{{ route('owner.mutual_dashboard') }}" method="GET" class="d-flex align-items-center gap-2">
                    <label class="form-label mb-0 text-muted small fw-bold text-nowrap"><i class="fas fa-map-marker-alt text-danger me-1"></i> Sucursal / Sede:</label>
                    <select name="sucursal" class="form-select bg-dark text-white border-secondary fw-bold rounded-pill" onchange="this.form.submit()" style="min-width: 260px;">
                        <option value="todas" {{ $sucursalSeleccionada == 'todas' ? 'selected' : '' }}>🌐 Todas las Sedes (Visión Global)</option>
                        <option value="chilecito" {{ $sucursalSeleccionada == 'chilecito' ? 'selected' : '' }}>🏔️ Sede Chilecito</option>
                        <option value="la_rioja" {{ $sucursalSeleccionada == 'la_rioja' ? 'selected' : '' }}>🏙️ Sede La Rioja Capital</option>
                        <option value="cordoba" {{ $sucursalSeleccionada == 'cordoba' ? 'selected' : '' }}>🎓 Sede Córdoba (Alta Complejidad)</option>
                        <option value="buenos_aires" {{ $sucursalSeleccionada == 'buenos_aires' ? 'selected' : '' }}>🏛️ Sede Buenos Aires (Derivaciones)</option>
                    </select>
                </form>

                <button class="btn btn-outline-light rounded-pill px-3 fw-bold shadow-sm" onclick="window.print();" title="Imprimir informe ejecutivo">
                    <i class="fas fa-print"></i>
                </button>
            </div>
        </div>

        <!-- Alerta de Filtro Activo por Sucursal -->
        @if($sucursalSeleccionada != 'todas')
            <div class="alert alert-info border-0 shadow-lg rounded-3 mb-4 p-3 d-flex align-items-center justify-content-between bg-primary bg-opacity-20 text-info">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-filter fa-lg"></i>
                    <div>
                        <strong class="text-white fs-6">Visualizando Métricas Específicas: {{ $datosSedeActual->nombre }}</strong>
                        <div class="small opacity-75">Filtrando cápitas ({{ number_format($datosSedeActual->capitas, 0, ',', '.') }}), prestadores y presupuesto auditado de la sede.</div>
                    </div>
                </div>
                <a href="{{ route('owner.mutual_dashboard') }}" class="btn btn-sm btn-light rounded-pill fw-bold px-3">
                    <i class="fas fa-times me-1"></i> Ver Todas las Sedes
                </a>
            </div>
        @endif

        <!-- KPI Cards de Alta Dirección (Billeteras y Cápitas) -->
        <div class="row g-3 mb-4">
            <!-- Recaudación Total -->
            <div class="col-md-3">
                <div class="card border-0 bg-dark p-4 h-100" style="border-radius: 18px; border-left: 5px solid #10b981 !important; box-shadow: 0 10px 25px rgba(0,0,0,0.3);">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-bold text-uppercase">RECAUDACIÓN POR CUOTAS</span>
                        <i class="fas fa-wallet fa-lg text-success"></i>
                    </div>
                    <h3 class="fw-bold text-success mb-0">${{ number_format($kpis->recaudacion_total, 0, ',', '.') }}</h3>
                    <small class="text-muted mt-2 d-block"><i class="fas fa-arrow-up me-1 text-success"></i>{{ number_format($datosSedeActual->capitas, 0, ',', '.') }} Cuotas Cobradas</small>
                </div>
            </div>

            <!-- Gasto Prestaciones Sanatoriales -->
            <div class="col-md-3">
                <div class="card border-0 bg-dark p-4 h-100" style="border-radius: 18px; border-left: 5px solid #3b82f6 !important; box-shadow: 0 10px 25px rgba(0,0,0,0.3);">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-bold text-uppercase">GASTO PRESTACIONES Y SANATORIOS</span>
                        <i class="fas fa-hospital-alt fa-lg text-primary"></i>
                    </div>
                    <h3 class="fw-bold text-primary mb-0">${{ number_format($kpis->gasto_prestaciones, 0, ',', '.') }}</h3>
                    <small class="text-muted mt-2 d-block">Siniestralidad Salud: <strong class="text-info">{{ $kpis->ratio_siniestralidad }}%</strong></small>
                </div>
            </div>

            <!-- Gasto Discapacidad y Docentes -->
            <div class="col-md-3">
                <div class="card border-0 bg-dark p-4 h-100" style="border-radius: 18px; border-left: 5px solid #f59e0b !important; box-shadow: 0 10px 25px rgba(0,0,0,0.3);">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-bold text-uppercase">GASTO DISCAPACIDAD Y DOCENTES</span>
                        <i class="fas fa-hands-helping fa-lg text-warning"></i>
                    </div>
                    <h3 class="fw-bold text-warning mb-0">${{ number_format($kpis->gasto_discapacidad, 0, ',', '.') }}</h3>
                    <small class="text-muted mt-2 d-block"><i class="fas fa-check-circle text-success me-1"></i>{{ $kpis->prestadores_activos }} Prestadores en Sede</small>
                </div>
            </div>

            <!-- Ahorro Generado por Auditoría e IA -->
            <div class="col-md-3">
                <div class="card border-0 bg-dark p-4 h-100" style="border-radius: 18px; border-left: 5px solid #8b5cf6 !important; box-shadow: 0 10px 25px rgba(0,0,0,0.3);">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-bold text-uppercase">AHORRO AUDITORÍA & IA</span>
                        <i class="fas fa-robot fa-lg text-info"></i>
                    </div>
                    <h3 class="fw-bold mb-0" style="color: #a78bfa;">${{ number_format($kpis->ahorro_auditoria_ia, 0, ',', '.') }}</h3>
                    <small class="text-muted mt-2 d-block"><i class="fas fa-shield-alt text-info me-1"></i>Evitado en Cobros Indebidos</small>
                </div>
            </div>
        </div>

        <!-- SECCIÓN: TOTALIZADORES POR SUCURSAL / DEPARTAMENTO GEOGRÁFICO -->
        <div class="card border-0 bg-dark p-4 mb-4" style="border-radius: 18px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-white mb-0">
                    <i class="fas fa-map-marked-alt text-danger me-2"></i> Totalizadores por Sucursal / Departamento Geográfico
                </h5>
                <span class="badge bg-secondary px-3 py-1 rounded-pill small">Control Descentalizado Auditado</span>
            </div>

            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0" style="background: transparent;">
                    <thead>
                        <tr class="text-muted small text-uppercase">
                            <th>Sucursal / Departamento</th>
                            <th>Cápitas Atendidas</th>
                            <th>Recaudación Local</th>
                            <th>Gasto Prestacional</th>
                            <th class="text-center">Siniestralidad</th>
                            <th>Principal Gasto / Prevalencia</th>
                            <th class="text-end">Acción Auditoría</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($totalizadoresSucursales as $suc)
                            <tr class="{{ $sucursalSeleccionada == $suc->codigo ? 'table-primary bg-primary bg-opacity-10' : '' }}">
                                <td>
                                    <div class="fw-bold text-white fs-6">{{ $suc->nombre }}</div>
                                    <small class="text-muted"><i class="fas fa-building me-1"></i> {{ $suc->prestador_top }}</small>
                                </td>
                                <td>
                                    <span class="badge px-3 py-1.5 rounded-pill fw-bold" style="background: rgba(14, 165, 233, 0.2); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.4); font-size: 0.8rem;">
                                        <i class="fas fa-users me-1"></i> {{ number_format($suc->capitas, 0, ',', '.') }} Cápitas
                                    </span>
                                </td>
                                <td class="fw-bold text-success">${{ number_format($suc->recaudacion, 0, ',', '.') }}</td>
                                <td class="fw-bold text-primary">${{ number_format($suc->gasto, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $suc->siniestralidad > 40 ? 'bg-danger' : ($suc->siniestralidad > 36 ? 'bg-warning text-dark' : 'bg-success') }} px-3 py-1.5 rounded-pill fw-bold">
                                        {{ $suc->siniestralidad }}%
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary text-light px-2 py-1">
                                        <i class="fas fa-stethoscope me-1"></i> {{ $suc->patologia_prevalente }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('owner.mutual_dashboard', ['sucursal' => $suc->codigo]) }}" class="btn btn-sm btn-outline-info rounded-pill px-3 fw-bold">
                                        <i class="fas fa-search me-1"></i> Auditar Sede
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- SECCIÓN: MATRIZ EPIDEMIOLÓGICA Y COSTOS POR ENFERMEDAD / PATOLOGÍA -->
        <div class="card border-0 bg-dark p-4 mb-4" style="border-radius: 18px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-white mb-0">
                    <i class="fas fa-notes-medical text-warning me-2"></i> Inteligencia Epidemiológica & Costos por Patología
                </h5>
                <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold">Clasificación Cie-10 & Vademécum</span>
            </div>

            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0" style="background: transparent;">
                    <thead>
                        <tr class="text-muted small text-uppercase">
                            <th>Enfermedad / Patología Prevalente</th>
                            <th>Categoría Prestacional</th>
                            <th class="text-center">Casos / Pacientes</th>
                            <th>Gasto Total Acumulado</th>
                            <th>Costo Promedio / Afiliado</th>
                            <th>Sede Mayor Concentración</th>
                            <th class="text-end">Tendencia</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($patologiasPrevalentes as $pat)
                            <tr>
                                <td>
                                    <div class="fw-bold text-white">{{ $pat->enfermedad }}</div>
                                </td>
                                <td>
                                    <span class="badge px-3 py-1.5 rounded-pill fw-bold" style="background: rgba(14, 165, 233, 0.2); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.4); font-size: 0.8rem;">
                                        {{ $pat->categoria }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary px-3 py-1 rounded-pill fw-bold">
                                        <i class="fas fa-user me-1"></i> {{ $pat->afiliados_casos }} Pacientes
                                    </span>
                                </td>
                                <td class="fw-bold text-warning">${{ number_format($pat->gasto_total, 0, ',', '.') }}</td>
                                <td class="fw-bold text-success">${{ number_format($pat->costo_promedio, 2, ',', '.') }}</td>
                                <td>
                                    <span class="badge bg-dark border border-secondary text-info px-2 py-1">
                                        <i class="fas fa-map-pin me-1"></i> {{ $pat->sede_concentracion }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold {{ str_contains($pat->tendencia, '+') ? 'text-warning' : 'text-success' }}">
                                        {{ $pat->tendencia }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <!-- Distribución del Gasto por Departamento -->
            <div class="col-lg-5">
                <div class="card border-0 bg-dark p-4 h-100" style="border-radius: 18px;">
                    <h5 class="fw-bold text-white mb-3"><i class="fas fa-pie-chart text-info me-2"></i> Distribución del Presupuesto por Sector</h5>
                    
                    @foreach($departamentosGasto as $dep)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small fw-bold text-white">{{ $dep->nombre }}</span>
                                <span class="small font-monospace text-info fw-bold" style="font-size: 0.85rem;">${{ number_format($dep->monto, 0, ',', '.') }} ({{ $dep->porcentaje }}%)</span>
                            </div>
                            <div style="height: 10px; background: #1e293b; border-radius: 6px; overflow: hidden; border: 1px solid rgba(255,255,255,0.1);">
                                <div style="height: 100%; width: {{ $dep->porcentaje }}%; background: linear-gradient(90deg, #3b82f6, #10b981); border-radius: 6px;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Sanatorios y Clínicas en Convenio (Top Rendimiento) -->
            <div class="col-lg-7">
                <div class="card border-0 bg-dark p-4 h-100" style="border-radius: 18px;">
                    <h5 class="fw-bold text-white mb-3"><i class="fas fa-hospital-user text-warning me-2"></i> Clínicas y Sanatorios con Convenio Activo</h5>
                    
                    <div class="table-responsive">
                        <table class="table table-dark table-hover align-middle mb-0" style="background: transparent;">
                            <thead>
                                <tr class="text-muted small text-uppercase">
                                    <th>Sanatorio / Centro Médico</th>
                                    <th class="text-center">Internaciones</th>
                                    <th>Facturación Mes</th>
                                    <th class="text-end">Calidad / Auditoría</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topSanatorios as $san)
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-white">{{ $san->nombre }}</div>
                                            <small class="text-muted">Convenio Vigente</small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary px-3 py-1.5 rounded-pill fw-bold">{{ $san->internaciones }} Camas</span>
                                        </td>
                                        <td class="fw-bold text-success">${{ number_format($san->monto, 0, ',', '.') }}</td>
                                        <td class="text-end">
                                            <span class="badge px-3 py-1.5 rounded-pill fw-bold" style="background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.4); font-size: 0.8rem;">
                                                <i class="fas fa-star text-warning me-1"></i> {{ $san->satisfaccion }}% Aprobado
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
