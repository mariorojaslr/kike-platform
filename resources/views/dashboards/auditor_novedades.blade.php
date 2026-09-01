@extends('layouts.tenant')

@section('title', 'Novedades y Expedientes en Tiempo Real')

@section('content')
<div class="container-fluid py-3">

    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="fas fa-bell text-warning me-2"></i> Feed de Novedades y Expedientes en Tiempo Real</h3>
            <p class="text-muted small mb-0">Auditoría ágil e inmediata de Resoluciones en Papel, Certificados Médicos y Facturas ARCA/AFIP.</p>
        </div>
        <a href="{{ route('auditor.docentes.legajos') }}" class="btn btn-outline-primary rounded-pill px-4 shadow-sm">
            <i class="fas fa-id-card-alt me-2"></i> Ver Legajos de Docentes
        </a>
    </div>

    <!-- Feed de Novedades -->
    <div class="row mb-5">
        <div class="col-lg-6 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-header bg-dark text-white fw-bold py-3 d-flex justify-content-between align-items-center" style="border-radius: 15px 15px 0 0;">
                    <span><i class="fas fa-stream me-2 text-warning"></i> Novedades en Tiempo Real</span>
                    <span class="badge bg-warning text-dark">{{ count($novedades) }} Eventos</span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" style="max-height: 550px; overflow-y: auto;">
                        @forelse($novedades as $nov)
                            <div class="list-group-item p-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <strong class="text-dark"><i class="fas fa-user-graduate me-1 text-primary"></i> {{ $nov->docente->nombre ?? 'Docente' }}</strong>
                                    <small class="text-muted font-monospace">{{ $nov->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1 text-secondary small">{{ $nov->descripcion }}</p>
                                <span class="badge bg-info bg-opacity-10 text-info border px-2 py-1 font-monospace" style="font-size:0.65rem;">
                                    {{ strtoupper(str_replace('_', ' ', $nov->tipo_novedad)) }}
                                </span>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted small">No hay novedades registradas recientemente.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Expedientes y Facturas Pendientes de Aprobar en 1-Clic -->
        <div class="col-lg-6 mb-3">
            <!-- Facturas ARCA Pendientes -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                <div class="card-header bg-dark text-white fw-bold py-3 d-flex justify-content-between align-items-center" style="border-radius: 15px 15px 0 0;">
                    <span><i class="fas fa-file-invoice-dollar me-2 text-success"></i> Facturas ARCA / AFIP por Aprobar</span>
                    <span class="badge bg-success">{{ count($facturasPendientes) }} Pendientes</span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($facturasPendientes as $fac)
                            <div class="list-group-item p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-receipt me-1 text-success"></i> Factura N° {{ $fac->nro_factura ?? 'Sin N°' }}</h6>
                                        <small class="text-muted d-block">Docente: {{ $fac->docente->nombre ?? 'N/A' }} | CUIT: {{ $fac->cuit_emisor ?? 'N/A' }}</small>
                                        <small class="text-muted d-block font-monospace">CAE: {{ $fac->cae ?? 'N/A' }} (Venc: {{ $fac->vencimiento_cae }})</small>
                                    </div>
                                    <h5 class="mb-0 fw-bold text-success">${{ number_format($fac->monto_total, 2, ',', '.') }}</h5>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    @if($fac->comprobante_url)
                                        <a href="{{ asset('storage/' . $fac->comprobante_url) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-eye me-1"></i> Ver Factura PDF/Foto
                                        </a>
                                    @endif

                                    <div class="d-flex gap-2">
                                        <form action="{{ route('auditor.facturas_docente.aprobar', $fac->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm">
                                                <i class="fas fa-check me-1"></i> Aprobar 1-Clic
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill" data-bs-toggle="collapse" data-bs-target="#collapseRechazoFac{{ $fac->id }}">
                                            Rechazar
                                        </button>
                                    </div>
                                </div>

                                <div class="collapse w-100 mt-3" id="collapseRechazoFac{{ $fac->id }}">
                                    <form action="{{ route('auditor.facturas_docente.rechazar', $fac->id) }}" method="POST" class="bg-light p-3 rounded border border-danger">
                                        @csrf
                                        <label class="form-label fw-bold small text-danger">Motivo de Rechazo (se notifica a la docente)</label>
                                        <textarea name="motivo_rechazo" class="form-control mb-2" rows="2" placeholder="Ej: El importe supera el tope de 3 horas autorizadas." required></textarea>
                                        <div class="text-end">
                                            <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3">Notificar Rechazo</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted small">No hay facturas ARCA pendientes de auditoría.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Expedientes Alumno Pendientes -->
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-header bg-dark text-white fw-bold py-3 d-flex justify-content-between align-items-center" style="border-radius: 15px 15px 0 0;">
                    <span><i class="fas fa-folder me-2 text-info"></i> Expedientes de Alumnos por Validar</span>
                    <span class="badge bg-info text-dark">{{ count($expedientesPendientes) }} Pendientes</span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($expedientesPendientes as $exp)
                            <div class="list-group-item p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-child me-1 text-info"></i> Alumno: {{ $exp->alumno->nombre ?? 'Alumno #' . $exp->alumno_id }}</h6>
                                        <small class="text-muted d-block">Docente: {{ $exp->docente->nombre ?? 'Docente' }} | Escuela: {{ $exp->escuela->nombre ?? 'Escuela' }}</small>
                                        <small class="text-muted d-block font-monospace">Resolución: {{ $exp->nro_resolucion ?? 'En trámite' }} (Máx. {{ $exp->horas_mensuales_asignadas }}hs/mes)</small>
                                    </div>
                                    <span class="badge bg-warning text-dark">Pendiente</span>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div class="d-flex gap-1">
                                        @if($exp->resolucion_url)
                                            <a href="{{ asset('storage/' . $exp->resolucion_url) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="Ver Resolución">
                                                <i class="fas fa-file-contract me-1"></i> Resolución
                                            </a>
                                        @endif
                                        @if($exp->certificado_medico_url)
                                            <a href="{{ asset('storage/' . $exp->certificado_medico_url) }}" target="_blank" class="btn btn-sm btn-outline-info" title="Ver Certificado Médico">
                                                <i class="fas fa-notes-medical me-1"></i> Certificado
                                            </a>
                                        @endif
                                    </div>

                                    <div class="d-flex gap-2">
                                        <form action="{{ route('auditor.expedientes.aprobar', $exp->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm">
                                                <i class="fas fa-check me-1"></i> Aprobar 1-Clic
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill" data-bs-toggle="collapse" data-bs-target="#collapseRechazoExp{{ $exp->id }}">
                                            Rechazar
                                        </button>
                                    </div>
                                </div>

                                <div class="collapse w-100 mt-3" id="collapseRechazoExp{{ $exp->id }}">
                                    <form action="{{ route('auditor.expedientes.rechazar', $exp->id) }}" method="POST" class="bg-light p-3 rounded border border-danger">
                                        @csrf
                                        <label class="form-label fw-bold small text-danger">Motivo de Rechazo del Expediente</label>
                                        <textarea name="motivo_rechazo" class="form-control mb-2" rows="2" placeholder="Ej: La foto de la resolución es ilegible o falta el diagnóstico firmado." required></textarea>
                                        <div class="text-end">
                                            <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3">Notificar Rechazo</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted small">No hay expedientes pendientes de validación.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
