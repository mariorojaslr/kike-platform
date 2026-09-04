@extends('layouts.tenant')

@section('title', 'Auditoría Médica Central - Mutual INTEGRA')

@section('content')
<div class="container-fluid py-3">

    <!-- Header Auditoría Médica -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="fas fa-user-md text-primary me-2"></i> Panel de Auditoría Médica Central</h3>
            <p class="text-muted small mb-0">{{ $auditorMedico }} | Gestión de Prácticas, Internaciones y Quirófano para 130.000 Abonados.</p>
        </div>
        <a href="{{ route('auditor.novedades') }}" class="btn btn-outline-warning rounded-pill px-4 shadow-sm">
            <i class="fas fa-bell me-2"></i> Feed Novedades Discapacidad
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 p-3 d-flex align-items-center gap-3">
            <i class="fas fa-check-circle fa-2x text-success"></i>
            <div>
                <h6 class="mb-0 fw-bold">{{ session('success') }}</h6>
                <small>Se notificó en tiempo real al prestador y se emitió la orden autorizada.</small>
            </div>
        </div>
    @endif

    <!-- Solicitudes Médicas Pendientes de Auditoría -->
    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-header bg-dark text-white fw-bold py-3 d-flex justify-content-between align-items-center" style="border-radius: 15px 15px 0 0;">
            <span><i class="fas fa-stethoscope me-2 text-warning"></i> Solicitudes Médicas e Internaciones en Espera</span>
            <span class="badge bg-warning text-dark">{{ count($solicitudesAuditoria) }} Pendientes</span>
        </div>
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                @forelse($solicitudesAuditoria as $sol)
                    <div class="list-group-item p-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h5 class="mb-1 fw-bold text-dark">
                                    <i class="fas fa-hospital-alt me-1 text-primary"></i> {{ $sol->prestador }}
                                </h5>
                                <small class="text-muted d-block">Afiliado: <strong>{{ $sol->afiliado_nombre }}</strong> ({{ $sol->afiliado_nro }})</small>
                                <small class="text-info d-block font-monospace mt-1">CIE-10 Diagnóstico: {{ $sol->diagnostico_cie10 }}</small>
                            </div>
                            <h4 class="fw-bold text-success mb-0">${{ number_format($sol->monto_presupuestado, 2, ',', '.') }}</h4>
                        </div>

                        <div class="p-3 bg-light rounded-3 my-3 border border-secondary border-opacity-25">
                            <strong class="text-dark d-block mb-1"><i class="fas fa-file-medical-alt me-1 text-info"></i> Práctica / Internación Solicitada:</strong>
                            <div class="text-secondary small">{{ $sol->practica }}</div>
                            <small class="text-muted d-block mt-2">Solicitado el: {{ $sol->fecha }} hs</small>
                        </div>

                        <div class="d-flex justify-content-end align-items-center gap-2">
                            <form action="{{ route('auditor.practica.autorizar', $sol->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                                    <i class="fas fa-check-circle me-1"></i> Autorizar Práctica (1-Clic)
                                </button>
                            </form>
                            <button type="button" class="btn btn-outline-danger rounded-pill px-3" onclick="alert('Solicitando mayores elementos / auditoría en terreno...');">
                                Observar / Auditoría en Terreno
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted small">No hay solicitudes médicas pendientes de auditoría.</div>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection
