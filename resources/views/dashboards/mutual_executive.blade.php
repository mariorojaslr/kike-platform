@extends('layouts.app')

@section('title', 'Cuadro de Mando Ejecutivo - Dirección General Mutual')

@section('content')
<div class="container-fluid py-4" style="background-color: #0b1329; min-height: 100vh; color: white;">
    <div class="container">
        
        <!-- Encabezado Ejecutivo del Dueño de la Mutual -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary border-opacity-25">
            <div>
                <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold text-uppercase" style="letter-spacing: 1px;">
                    <i class="fas fa-crown me-1"></i> Dirección General & Consejo de Administración
                </span>
                <h2 class="fw-bold mt-2 mb-0 text-white">
                    <i class="fas fa-chart-line text-success me-2"></i> Cuadro de Mando Ejecutivo Mutual
                </h2>
                <small class="text-muted"><i class="fas fa-users me-1 text-info"></i> Cobertura Provincial: <strong>{{ number_format($capitasTotal, 0, ',', '.') }} Abonados Activos</strong> | Periodo: {{ $periodo }}</small>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-light rounded-pill px-4 fw-bold shadow-sm" onclick="window.print();">
                    <i class="fas fa-print me-1"></i> Imprimir Informe Ejecutivo
                </button>
                <button class="btn btn-success rounded-pill px-4 fw-bold shadow-sm" onclick="alert('Generando reporte consolidado de auditoría e impuestos...');">
                    <i class="fas fa-file-excel me-1"></i> Exportar Rendición
                </button>
            </div>
        </div>

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
                    <small class="text-muted mt-2 d-block"><i class="fas fa-arrow-up me-1 text-success"></i>130.000 Cuotas Cobradas</small>
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
                    <small class="text-muted mt-2 d-block"><i class="fas fa-check-circle text-success me-1"></i>840 Docentes / Terapeutas</small>
                </div>
            </div>

            <!-- Ahorro Generado por Auditoría e IA -->
            <div class="col-md-3">
                <div class="card border-0 bg-dark p-4 h-100" style="border-radius: 18px; border-left: 5px solid #8b5cf6 !important; box-shadow: 0 10px 25px rgba(0,0,0,0.3);">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-bold text-uppercase">AHORRO AUDITORÍA & IA</span>
                        <i class="fas fa-robot fa-lg style='color: #8b5cf6;'"></i>
                    </div>
                    <h3 class="fw-bold mb-0" style="color: #a78bfa;">${{ number_format($kpis->ahorro_auditoria_ia, 0, ',', '.') }}</h3>
                    <small class="text-muted mt-2 d-block"><i class="fas fa-shield-alt text-info me-1"></i>Evitado en Cobros Indebidos</small>
                </div>
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
                                <span class="small font-monospace text-muted">${{ number_format($dep->monto, 0, ',', '.') }} ({{ $dep->porcentaje }}%)</span>
                            </div>
                            <div style="height: 8px; background: #1e293b; border-radius: 6px; overflow: hidden;">
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
                                            <span class="badge bg-primary px-3 py-1">{{ $san->internaciones }} Camas</span>
                                        </td>
                                        <td class="fw-bold text-success">${{ number_format($san->monto, 0, ',', '.') }}</td>
                                        <td class="text-end">
                                            <span class="badge bg-success bg-opacity-20 text-success border border-success px-2 py-1">
                                                <i class="fas fa-star me-1"></i> {{ $san->satisfaccion }}% Aprobado
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
