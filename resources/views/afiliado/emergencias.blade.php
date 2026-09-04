@extends('layouts.app')

@section('title', 'Central de Emergencias & Ambulancias SOS 24/7')

@section('content')
<div class="container-fluid py-4" style="background-color: #0f172a; min-height: 100vh; color: white;">
    <div class="container" style="max-width: 750px;">
        
        <!-- Header Emergencias -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary">
            <div>
                <span class="badge bg-danger text-white px-3 py-1 rounded-pill fw-bold text-uppercase shadow-sm" style="font-size: 0.75rem;">
                    <i class="fas fa-ambulance me-1"></i> Despacho Médico SOS 24/7
                </span>
                <h3 class="fw-bold mt-2 mb-0 text-white">
                    <i class="fas fa-heartbeat text-danger me-2"></i> Central de Emergencias Médicas
                </h3>
                <small class="text-white-50"><i class="fas fa-user me-1 text-info"></i> {{ $afiliado->nombre }} | {{ $afiliado->plan }}</small>
            </div>
            <div class="bg-danger bg-opacity-20 text-danger p-3 rounded-circle text-center" style="width: 55px; height: 55px;">
                <i class="fas fa-phone-alt fa-lg" style="line-height: 25px;"></i>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-danger border-0 shadow-lg rounded-3 mb-4 p-3 d-flex align-items-center gap-3">
                <i class="fas fa-ambulance fa-2x text-danger"></i>
                <div>
                    <h6 class="mb-0 fw-bold">{{ session('success') }}</h6>
                    <small>Mantenga la línea libre. El paramédico se contactará a su teléfono en breve.</small>
                </div>
            </div>
        @endif

        <!-- BOTÓN DE PÁNICO SOS MEDICO -->
        <div class="card border-0 bg-dark shadow-lg mb-4 text-center p-4" style="border-radius: 20px; border: 2px solid #ef4444 !important;">
            <h5 class="fw-bold text-white mb-2"><i class="fas fa-exclamation-triangle text-warning me-2"></i> ¿Tiene una Emergencia con Riesgo de Vida?</h5>
            <p class="small text-white-50 mb-4">Presione el botón de abajo para enviar sus coordenadas GPS en vivo a la Central de Ambulancias UMED.</p>
            
            <form action="{{ route('afiliado.emergencias.pedir') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger btn-lg rounded-circle shadow-lg p-4 fw-bold mx-auto d-flex align-items-center justify-content-center" style="width: 140px; height: 140px; border: 6px solid rgba(255,255,255,0.3); font-size: 1.3rem;">
                    <div>
                        <i class="fas fa-skull-crossbones fa-2x d-block mb-1"></i>
                        <span>SOS 24h</span>
                    </div>
                </button>
            </form>
        </div>

        <!-- RASTREO EN VIVO DE AMBULANCIA -->
        <div class="card border-0 bg-secondary bg-opacity-10 shadow-sm mb-4" style="border-radius: 16px; border: 1px solid rgba(255,255,255,0.1);">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-white mb-0"><i class="fas fa-route text-info me-2"></i> Estado de Unidad Asignada</h5>
                    <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold"><i class="fas fa-spinner fa-spin me-1"></i> UNIDAD EN CAMINO</span>
                </div>

                <div class="bg-dark p-3 rounded-3 mb-3 border border-secondary border-opacity-25">
                    <div class="row g-2">
                        <div class="col-6"><span class="text-white-50 small">Código:</span> <strong class="text-white d-block">{{ $emergenciaActiva->tipo }}</strong></div>
                        <div class="col-6"><span class="text-white-50 small">Tiempo Estimado (ETA):</span> <strong class="text-warning d-block fs-5"><i class="fas fa-clock me-1"></i> {{ $emergenciaActiva->eta_minutos }} min</strong></div>
                        <div class="col-6"><span class="text-white-50 small">Móvil:</span> <strong class="text-info d-block">{{ $emergenciaActiva->unidad }}</strong></div>
                        <div class="col-6"><span class="text-white-50 small">Médico a Cargo:</span> <strong class="text-white d-block">{{ $emergenciaActiva->chofer_medico }}</strong></div>
                    </div>
                </div>

                <!-- Mapa Simulado de Recorrido GPS -->
                <div class="position-relative rounded-3 overflow-hidden bg-dark text-center py-5 border border-secondary border-opacity-25" style="min-height: 180px; background: linear-gradient(135deg, #1e293b, #0f172a);">
                    <i class="fas fa-map-marked-alt fa-3x text-info opacity-50 mb-2"></i>
                    <h6 class="fw-bold text-white mb-1">Rastreo por Satélite GPS Activo</h6>
                    <small class="text-white-50">Ubicación solicitante: {{ $afiliado->direccion_registrada }}</small>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
