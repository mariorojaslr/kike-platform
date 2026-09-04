@extends('layouts.app')

@section('title', 'Portal del Padre / Titular - Reintegros y Avales')

@section('content')
<div class="container-fluid py-4" style="background-color: #0f172a; min-height: 100vh; color: white;">
    <div class="container" style="max-width: 600px;">
        
        <!-- Header PWA del Padre -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary">
            <div>
                <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold text-uppercase" style="font-size: 0.75rem;">
                    <i class="fas fa-hand-holding-usd me-1"></i> Módulo de Reintegros
                </span>
                <h3 class="fw-bold mt-2 mb-0 text-white">
                    <i class="fas fa-user-shield text-primary me-2"></i> {{ $padreNombre }}
                </h3>
                <small class="text-white-50"><i class="fas fa-child me-1 text-warning"></i> Atendido: <strong class="text-white">{{ $hijoNombre }}</strong></small>
            </div>
            <div class="bg-primary bg-opacity-20 text-primary p-3 rounded-circle text-center" style="width: 50px; height: 50px;">
                <i class="fas fa-user-tie fa-lg" style="line-height: 20px;"></i>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-lg rounded-3 mb-4 p-3 d-flex align-items-center gap-3">
                <i class="fas fa-check-circle fa-2x text-success"></i>
                <div>
                    <h6 class="mb-0 fw-bold">{{ session('success') }}</h6>
                    <small>Se guardó la trazabilidad inalterable en Auditoría.</small>
                </div>
            </div>
        @endif

        <!-- Card Explicativa de Reintegro -->
        <div class="card border-0 bg-dark shadow-sm mb-4" style="border-left: 4px solid #3b82f6 !important; border-radius: 14px;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <i class="fas fa-info-circle fa-2x text-primary"></i>
                    <div>
                        <h6 class="fw-bold mb-1 text-white">¿Cómo funciona la Modalidad Reintegro?</h6>
                        <p class="small text-light mb-0">
                            El Titular/Padre paga el servicio prestado por el docente y solicita la devolución a la Mutual. 
                            Usted puede <b>subir la Resolución</b> y <b>confirmar la asistencia del docente</b> para agilizar la liquidación.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botón para Cargar Resolución directamente el Padre -->
        <button type="button" class="btn btn-outline-warning w-100 rounded-pill fw-bold py-3 mb-4 d-flex align-items-center justify-content-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalSubirResolucionPadre">
            <i class="fas fa-file-upload fa-lg"></i> Cargar Resolución del OSP (Vía Padre)
        </button>

        <!-- Histórico de Reintegros y Estados -->
        <h5 class="fw-bold text-white mb-3"><i class="fas fa-list-alt me-2 text-info"></i> Mis Solicitudes de Reintegro</h5>

        @foreach($reintegros as $r)
            <div class="card border-0 bg-secondary bg-opacity-10 shadow-sm mb-3" style="border-radius: 16px; border: 1px solid rgba(255,255,255,0.1);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="fw-bold text-white mb-0"><i class="fas fa-calendar-alt text-warning me-2"></i> {{ $r->periodo }}</h5>
                        @if($r->estado_reintegro == 'aprobado_para_pago')
                            <span class="badge bg-success text-white rounded-pill px-3 py-1 fw-bold shadow-sm"><i class="fas fa-check-circle me-1"></i> LISTO PARA REINTEGRO</span>
                        @else
                            <span class="badge bg-warning text-dark rounded-pill px-3 py-1 fw-bold shadow-sm"><i class="fas fa-hourglass-half me-1"></i> EN AUDITORÍA</span>
                        @endif
                    </div>

                    <p class="small text-white-50 mb-3"><i class="fas fa-user-md me-1 text-info"></i> Docente: <strong class="text-white">{{ $r->docente_nombre }}</strong> | Monto: <strong class="text-success">${{ number_format($r->monto_facturado, 0, ',', '.') }}</strong></p>

                    <!-- Triple Trazabilidad (Docente, Directora, Padre) -->
                    <div class="bg-dark p-3 rounded-3 mb-3 border border-secondary border-opacity-25" style="font-size: 0.8rem;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span><i class="fas fa-file-contract text-info me-2"></i> Resolución OSP:</span>
                            <span class="badge bg-success"><i class="fas fa-check me-1"></i> {{ $r->nro_resolucion }}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span><i class="fas fa-school text-primary me-2"></i> Aval Directora Escuela:</span>
                            @if($r->aval_directora)
                                <span class="badge bg-success"><i class="fas fa-check me-1"></i> Aprobado</span>
                            @else
                                <span class="badge bg-warning text-dark">Pendiente</span>
                            @endif
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <span><i class="fas fa-user-shield text-warning me-2"></i> Confirmación del Padre:</span>
                            @if($r->aval_padre)
                                <span class="badge bg-success" title="{{ $r->fecha_aval_padre }}"><i class="fas fa-check me-1"></i> Avalado</span>
                            @else
                                <span class="badge bg-warning text-dark">Pendiente</span>
                            @endif
                        </div>
                    </div>

                    @if(!$r->aval_padre)
                        <form action="{{ route('padre.asistencia.confirmar') }}" method="POST">
                            @csrf
                            <input type="hidden" name="reintegro_id" value="{{ $r->id }}">
                            <input type="hidden" name="padre_nombre" value="{{ $padreNombre }}">
                            <button type="submit" class="btn btn-success w-100 rounded-pill fw-bold py-2 shadow-sm">
                                <i class="fas fa-check-circle me-1"></i> Confirmar Asistencia del Docente a mi Hijo
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach

    </div>
</div>

<!-- Modal Cargar Resolución Padre -->
<div class="modal fade" id="modalSubirResolucionPadre" tabindex="-1" aria-hidden="true" style="color: #0f172a;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header bg-dark text-white border-0" style="border-radius: 16px 16px 0 0;">
                <h5 class="modal-title fw-bold"><i class="fas fa-file-upload text-warning me-2"></i> Cargar Resolución de OSP</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('pwa.expediente.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4 text-dark">
                    <p class="small text-muted mb-3">Si usted posee la Resolución en papel o PDF, puede adjuntarla aquí para que la Mutual la asigne automáticamente a la docente.</p>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Adjuntar Imagen o PDF de la Resolución *</label>
                        <input type="file" name="resolucion_file" class="form-control" accept="image/*,.pdf" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 pe-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 text-secondary fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold shadow-sm">
                        <i class="fas fa-cloud-upload-alt me-1"></i> Subir y Notificar a Mutual
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
