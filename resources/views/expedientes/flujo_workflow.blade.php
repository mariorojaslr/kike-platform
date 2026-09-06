@extends('layouts.app')

@section('title', 'Gestor del Flujo de Expedientes (Workflow & Pipeline de Estados)')

@section('content')
<div class="container-fluid py-4" style="background-color: #0f172a; min-height: 100vh; color: white;">
    <div class="container-fluid px-4">
        
        <!-- Header Flujo de Expedientes -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary border-opacity-25">
            <div>
                <span class="badge bg-primary text-white px-3 py-1 rounded-pill fw-bold text-uppercase shadow-sm" style="font-size: 0.75rem;">
                    <i class="fas fa-project-diagram me-1"></i> Workflow & Pipeline de Trazabilidad 360°
                </span>
                <h2 class="fw-bold mt-2 mb-0 text-white">
                    <i class="fas fa-tasks text-info me-2"></i> Configuración y Flujo de Expedientes
                </h2>
                <small class="text-white-50"><i class="fas fa-shield-alt me-1 text-success"></i> Control en Tiempo Real de Etapas, Avales y Auditoría Médica</small>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-info rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalConfigFlujo">
                    <i class="fas fa-cog me-1"></i> Configurar Reglas de Etapas
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-lg rounded-3 mb-4 p-3 d-flex align-items-center gap-3">
                <i class="fas fa-check-circle fa-2x text-success"></i>
                <div>
                    <h6 class="mb-0 fw-bold">{{ session('success') }}</h6>
                    <small>Se registró el movimiento en el historial inalterable de auditoría.</small>
                </div>
            </div>
        @endif

        <!-- MAPA VISUAL DEL PIPELINE (7 ETAPAS) -->
        <div class="card border-0 bg-secondary bg-opacity-10 shadow-sm mb-4" style="border-radius: 18px; border: 1px solid rgba(255,255,255,0.1);">
            <div class="card-body p-4">
                <h5 class="fw-bold text-white mb-3"><i class="fas fa-stream text-warning me-2"></i> Etapas del Flujo de Procesamiento</h5>
                <div class="row g-2 text-center">
                    @foreach($etapasWorkflow as $etapa)
                        <div class="col-md">
                            <div class="p-3 bg-dark rounded-3 h-100 border border-secondary border-opacity-25 d-flex flex-column justify-content-between position-relative">
                                <div>
                                    <div class="badge bg-{{ $etapa->color }} rounded-circle p-2 mb-2" style="width: 38px; height: 38px;">
                                        <i class="{{ $etapa->icono }} fs-6"></i>
                                    </div>
                                    <h6 class="fw-bold text-white mb-1" style="font-size: 0.85rem;">{{ $etapa->nombre }}</h6>
                                    <p class="small text-white-50 mb-2" style="font-size: 0.72rem;">{{ $etapa->descripcion }}</p>
                                </div>
                                <div class="pt-2 border-top border-secondary border-opacity-25">
                                    <span class="badge bg-secondary text-white-50" style="font-size: 0.65rem;">Req: {{ implode(', ', $etapa->requiere) }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- PIPELINE TIPO KANBAN DE EXPEDIENTES EN CURSO -->
        <h5 class="fw-bold text-white mb-3"><i class="fas fa-columns me-2 text-info"></i> Estado de Expedientes Activos en el Pipeline</h5>

        <div class="row g-3">
            @foreach($expedientesEnFlujo as $exp)
                <div class="col-md-4">
                    <div class="card border-0 bg-secondary bg-opacity-10 shadow-sm h-100" style="border-radius: 16px; border: 1px solid rgba(255,255,255,0.12);">
                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="badge bg-info text-dark fw-bold font-monospace px-3 py-1 rounded-pill">{{ $exp->id }}</span>
                                    <span class="badge bg-warning text-dark fw-bold px-3 py-1 rounded-pill"><i class="fas fa-clock me-1"></i> {{ $exp->etapa_nombre }}</span>
                                </div>

                                <h5 class="fw-bold text-white mb-1"><i class="fas fa-user-graduate text-warning me-2"></i> {{ $exp->alumno }}</h5>
                                <p class="small text-info mb-3"><i class="fas fa-user-md me-1"></i> {{ $exp->docente }}</p>

                                <div class="bg-dark p-3 rounded-3 mb-3 border border-secondary border-opacity-25" style="font-size: 0.8rem;">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-white-50">Escuela:</span>
                                        <span class="text-white fw-bold">{{ $exp->escuela }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-white-50">Resolución OSP:</span>
                                        <span class="text-info fw-bold">{{ $exp->resolucion }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-white-50">Horas Mes:</span>
                                        <span class="badge bg-primary">{{ $exp->horas_mes }} hs</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-white-50">Monto Facturado:</span>
                                        <span class="text-success fw-bold fs-6">${{ number_format($exp->monto, 0, ',', '.') }}</span>
                                    </div>
                                </div>

                                <!-- CHECKLIST DE TRIPLE TRAZABILIDAD -->
                                <div class="p-3 bg-dark rounded-3 mb-3 border border-secondary border-opacity-25">
                                    <h6 class="fw-bold text-white-50 small mb-2"><i class="fas fa-tasks me-1 text-warning"></i> Checklist de Requisitos:</h6>
                                    <div class="d-flex flex-column gap-1 small">
                                        <div>
                                            @if($exp->aval_directora)
                                                <i class="fas fa-check-circle text-success me-2"></i> <span class="text-white">Aval Directora Escuela</span>
                                            @else
                                                <i class="fas fa-times-circle text-warning me-2"></i> <span class="text-white-50">Aval Directora Escuela (Pendiente)</span>
                                            @endif
                                        </div>
                                        <div>
                                            @if($exp->aval_padre)
                                                <i class="fas fa-check-circle text-success me-2"></i> <span class="text-white">Conformidad del Padre</span>
                                            @else
                                                <i class="fas fa-times-circle text-warning me-2"></i> <span class="text-white-50">Conformidad del Padre (Pendiente)</span>
                                            @endif
                                        </div>
                                        <div>
                                            @if($exp->factura_arca)
                                                <i class="fas fa-check-circle text-success me-2"></i> <span class="text-white">Factura ARCA OCR Auditada</span>
                                            @else
                                                <i class="fas fa-times-circle text-warning me-2"></i> <span class="text-white-50">Factura ARCA (Pendiente)</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <form action="{{ route('expedientes.flujo.avanzar', $exp->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="nuevo_estado" value="Avanzar Siguiente Etapa">
                                    <button type="submit" class="btn btn-success w-100 rounded-pill fw-bold py-2 shadow-sm">
                                        <i class="fas fa-arrow-right me-1"></i> Avanzar a Siguiente Etapa
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</div>

<!-- Modal Configurar Reglas de Etapas -->
<div class="modal fade" id="modalConfigFlujo" tabindex="-1" aria-hidden="true" style="color: #0f172a;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header bg-dark text-white border-0" style="border-radius: 16px 16px 0 0;">
                <h5 class="modal-title fw-bold"><i class="fas fa-cogs text-info me-2"></i> Configuración de Reglas del Flujo de Expedientes</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('expedientes.flujo.guardar_etapa') }}" method="POST">
                @csrf
                <div class="modal-body p-4 text-dark">
                    <div class="alert alert-info p-3 small mb-3">
                        <i class="fas fa-info-circle me-1"></i> Defina qué validaciones y firmas son estrictamente requeridas antes de que un expediente pase a Tesorería.
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="chkReqDirectora" checked>
                            <label class="form-check-label fw-bold" for="chkReqDirectora">Exigir Firma Digital de Directora de Escuela (Límite 3hs/día)</label>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="chkReqPadre" checked>
                            <label class="form-check-label fw-bold" for="chkReqPadre">Exigir Conformidad de Titular/Padre vía App o WhatsApp</label>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="chkReqMD5" checked>
                            <label class="form-check-label fw-bold" for="chkReqMD5">Exigir Sello Criptográfico Hash MD5 de Junta de Auditoría Médica</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 pe-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 text-secondary fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info rounded-pill px-4 fw-bold text-white shadow-sm">
                        <i class="fas fa-save me-1"></i> Guardar Reglas de Workflow
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
