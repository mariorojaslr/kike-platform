@extends('layouts.app')

@section('title', 'Portal de Prestadores Médicos y Clínicas - Mutual INTEGRA')

@section('content')
<div class="container-fluid py-4" style="background-color: #0f172a; min-height: 100vh; color: white;">
    <div class="container">
        
        <!-- Header del Portal de Prestadores Médicos -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary">
            <div>
                <span class="badge bg-primary px-3 py-1 rounded-pill fw-bold text-uppercase" style="letter-spacing: 1px;">
                    <i class="fas fa-hospital me-1"></i> Red de Prestadores Médicos & Sanatorios
                </span>
                <h2 class="fw-bold mt-2 mb-0 text-white">
                    <i class="fas fa-stethoscope text-info me-2"></i> {{ $prestadorNombre }}
                </h2>
                <small class="text-muted"><i class="fas fa-users me-1 text-success"></i> Cobertura Activa: <strong>{{ $cápitasActivas }}</strong></small>
            </div>
            <button type="button" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNuevaAutorizacion">
                <i class="fas fa-plus-circle me-1"></i> Solicitar Autorización Médica
            </button>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-lg rounded-3 mb-4 p-3 d-flex align-items-center gap-3">
                <i class="fas fa-check-circle fa-2x text-success"></i>
                <div>
                    <h6 class="mb-0 fw-bold">{{ session('success') }}</h6>
                    <small>Se emitió el bono digital de autorización con sello de auditoría médica.</small>
                </div>
            </div>
        @endif

        <!-- Tarjetas KPI del Prestador -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 bg-secondary bg-opacity-10 p-3" style="border-radius: 14px; border-left: 4px solid #3b82f6 !important;">
                    <span class="text-muted small fw-bold">PRÁCTICAS Y CONSULTAS MES</span>
                    <h3 class="fw-bold text-primary mb-0 mt-1">142 Prácticas</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 bg-secondary bg-opacity-10 p-3" style="border-radius: 14px; border-left: 4px solid #10b981 !important;">
                    <span class="text-muted small fw-bold">LIQUIDADO AUTORIZADO</span>
                    <h3 class="fw-bold text-success mb-0 mt-1">$1.845.000</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 bg-secondary bg-opacity-10 p-3" style="border-radius: 14px; border-left: 4px solid #f59e0b !important;">
                    <span class="text-muted small fw-bold">EN AUDITORÍA MÉRICA</span>
                    <h3 class="fw-bold text-warning mb-0 mt-1">2 Solicitudes</h3>
                </div>
            </div>
        </div>

        <!-- Solicitudes de Autorizaciones de Prácticas e Internaciones -->
        <h5 class="fw-bold text-white mb-3"><i class="fas fa-file-medical me-2 text-info"></i> Órdenes Médicas e Internaciones Solicitadas</h5>

        <div class="card border-0 bg-secondary bg-opacity-10 shadow-sm" style="border-radius: 16px; border: 1px solid rgba(255,255,255,0.1);">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle mb-0" style="background: transparent;">
                        <thead>
                            <tr class="text-muted small text-uppercase">
                                <th class="ps-4">Afiliado Mutual</th>
                                <th>Tipo de Prestación</th>
                                <th>Código Nomenclador / Práctica</th>
                                <th>Importe</th>
                                <th class="text-center">Estado Auditoría</th>
                                <th class="text-end pe-4">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ordenesSolicitadas as $ord)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-white"><i class="fas fa-user me-1 text-primary"></i> {{ $ord->afiliado_nombre }}</div>
                                        <small class="text-muted font-monospace">{{ $ord->afiliado_nro }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info bg-opacity-20 text-info border border-info px-2 py-1">
                                            {{ $ord->tipo }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="small fw-bold text-white">{{ $ord->practica_codigo }}</div>
                                        <small class="text-muted">Solicitado: {{ $ord->fecha_solicitud }}</small>
                                    </td>
                                    <td class="fw-bold text-success">${{ number_format($ord->monto, 2, ',', '.') }}</td>
                                    <td class="text-center">
                                        @if($ord->estado == 'aprobado')
                                            <span class="badge bg-success px-3 py-1"><i class="fas fa-check-circle me-1"></i> AUTORIZADO</span>
                                        @else
                                            <span class="badge bg-warning text-dark px-3 py-1"><i class="fas fa-hourglass-half me-1"></i> EN AUDITORÍA MÉRICA</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <button class="btn btn-sm btn-outline-light rounded-pill px-3" onclick="alert('Imprimiendo Bono Digital Autorizado N° {{ $ord->id }}...');">
                                            <i class="fas fa-print me-1"></i> Imprimir Bono
                                        </button>
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

<!-- Modal Nueva Autorización Médica / Internación -->
<div class="modal fade" id="modalNuevaAutorizacion" tabindex="-1" aria-hidden="true" style="color: #0f172a;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header bg-dark text-white border-0" style="border-radius: 16px 16px 0 0;">
                <h5 class="modal-title fw-bold"><i class="fas fa-notes-medical text-success me-2"></i> Nueva Solicitud de Autorización Médica / Internación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('prestadores.demo') }}" method="GET">
                <div class="modal-body p-4 text-dark">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Número de N° Afiliado / DNI *</label>
                            <input type="text" class="form-control" placeholder="Ej: MUT-32456789/00 o DNI 32456789" value="MUT-32456789/00 - Abayay Ramón Martín" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tipo de Solicitud *</label>
                            <select class="form-select" required>
                                <option value="ambulatoria">Consulta / Práctica Ambulatoria</option>
                                <option value="internacion">Internación Sanatorial / Días de Cama</option>
                                <option value="cirugia">Cirugía Programada / Quirófano</option>
                                <option value="estudio">Resonancia / Tomografía / Alta Calidad</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Código Nomenclador / Práctica *</label>
                            <input type="text" class="form-control" placeholder="Ej: 42.01.01 Resonancia Magnética" value="42.01.01 - Resonancia Magnética Nuclear" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Diagnóstico Presuntivo (CIE-10)</label>
                            <input type="text" class="form-control" placeholder="Ej: G44.2 Cefalea tensional" value="G44.2 - Cefalea tensional crónica">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Adjuntar Orden Médica Escaneada (Opcional)</label>
                            <input type="file" class="form-control" accept="image/*,.pdf">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 pe-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 text-secondary fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm" onclick="alert('✅ Solicitud enviada a Auditoría Médica de la Mutual en tiempo real.');">
                        <i class="fas fa-paper-plane me-1"></i> Enviar a Auditoría Médica
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
