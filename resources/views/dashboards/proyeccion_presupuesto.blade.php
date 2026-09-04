@extends('layouts.app')

@section('title', 'Proyección Presupuestaria & BI Epidemiológico - Mutual INTEGRA')

@section('content')
<div class="container-fluid py-4" style="background-color: #0b1329; min-height: 100vh; color: white;">
    <div class="container">
        
        <!-- Header BI Proyección -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary border-opacity-25">
            <div>
                <span class="badge bg-purple px-3 py-1 rounded-pill fw-bold text-uppercase" style="letter-spacing: 1px; background: #8b5cf6;">
                    <i class="fas fa-brain me-1"></i> Inteligencia de Negocio & Proyección Presupuestaria IA
                </span>
                <h2 class="fw-bold mt-2 mb-0 text-white">
                    <i class="fas fa-chart-area text-info me-2"></i> Modelado Financiero & Epidemiológico (12 Meses)
                </h2>
                <small class="text-muted">Período de Proyección: <strong>{{ $periodoProyeccion }}</strong> | Cobertura: 130.000 Cápitas</small>
            </div>
            <div>
                <button type="button" class="btn btn-outline-light rounded-pill btn-sm fw-bold" onclick="window.print()">
                    <i class="fas fa-file-pdf me-1"></i> Exportar Informe BI
                </button>
            </div>
        </div>

        <!-- Alerta de Inteligencia Predictiva -->
        <div class="card border-0 bg-dark shadow-sm mb-4" style="border-left: 4px solid #8b5cf6 !important; border-radius: 14px;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <i class="fas fa-project-diagram fa-2x text-purple" style="color: #a78bfa;"></i>
                    <div>
                        <h6 class="fw-bold mb-1 text-white">Modelado Predictivo de Siniestralidad Sanitaria</h6>
                        <p class="small text-muted mb-0">
                            La Inteligencia Artificial analiza la curva histórica de consumo por patología, el ingreso de nuevas cápitas y el índice de inflación sanitaria para proyectar el presupuesto óptimo de reserva técnica.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Proyección por Matriz Epidemiológica -->
        <h5 class="fw-bold text-white mb-3"><i class="fas fa-chart-line me-2 text-info"></i> Matriz de Variación Presupuestaria por Patología</h5>

        <div class="card border-0 bg-secondary bg-opacity-10 shadow-sm mb-4" style="border-radius: 16px; border: 1px solid rgba(255,255,255,0.1);">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle mb-0" style="background: transparent;">
                        <thead>
                            <tr class="text-muted small text-uppercase">
                                <th class="ps-4">Patología / Categoría Sanitaria</th>
                                <th class="text-center">Casos Actuales ➔ Proyectados</th>
                                <th>Gasto Anual Actual</th>
                                <th>Gasto Anual Proyectado</th>
                                <th class="text-center">Variación %</th>
                                <th class="text-end pe-4">Nivel de Riesgo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($proyeccionEpidemiologica as $proy)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-white"><i class="fas fa-microscope me-1 text-primary"></i> {{ $proy->patologia }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-dark border border-secondary px-3 py-1 fw-bold text-info">
                                            {{ $proy->casos_actuales }} ➔ {{ $proy->casos_proyectados }} casos
                                        </span>
                                    </td>
                                    <td class="fw-bold text-muted">${{ number_format($proy->gasto_anual_actual, 2, ',', '.') }}</td>
                                    <td class="fw-bold text-success fs-6">${{ number_format($proy->gasto_anual_proyectado, 2, ',', '.') }}</td>
                                    <td class="text-center fw-bold text-warning">{{ $proy->variacion }}</td>
                                    <td class="text-end pe-4">
                                        @if($proy->nivel_riesgo == 'ALTO')
                                            <span class="badge bg-danger px-3 py-1.5 rounded-pill"><i class="fas fa-exclamation-triangle me-1"></i> RIESGO ALTO</span>
                                        @elseif($proy->nivel_riesgo == 'MEDIO')
                                            <span class="badge bg-warning text-dark px-3 py-1.5 rounded-pill fw-bold"><i class="fas fa-info-circle me-1"></i> RIESGO MEDIO</span>
                                        @else
                                            <span class="badge bg-success px-3 py-1.5 rounded-pill"><i class="fas fa-check-circle me-1"></i> ESTABLE</span>
                                        @endif
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
@endsection
