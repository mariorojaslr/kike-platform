@extends('layouts.app')

@section('title', 'Cartilla Médica y Turnos - Mutual INTEGRA')

@section('content')
<div class="container-fluid py-4" style="background-color: #0f172a; min-height: 100vh; color: white;">
    <div class="container" style="max-width: 550px;">
        
        <!-- Header Cartilla -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom border-secondary border-opacity-25">
            <div>
                <span class="badge bg-info text-dark px-3 py-1 rounded-pill fw-bold text-uppercase" style="font-size:0.7rem;">
                    <i class="fas fa-search-location me-1"></i> Cartilla Médica 130k Abonados
                </span>
                <h4 class="fw-bold mt-2 mb-0 text-white">Reserva de Turnos & Médicos</h4>
            </div>
            <a href="{{ route('afiliado.credencial.demo') }}" class="btn btn-outline-warning rounded-pill btn-sm fw-bold">
                <i class="fas fa-id-card me-1"></i> Ver Credencial
            </a>
        </div>

        <!-- Próximo Turno Reservado -->
        @if(count($turnosProximos) > 0)
            <div class="card border-0 mb-4 text-white shadow-lg" style="border-radius: 16px; background: linear-gradient(135deg, #059669, #047857); border-left: 5px solid #34d399 !important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-white text-success fw-bold"><i class="fas fa-calendar-check me-1"></i> PRÓXIMO TURNO CONFIRMADO</span>
                        <small class="text-white-50">Sanatorio Central</small>
                    </div>
                    @foreach($turnosProximos as $t)
                        <h6 class="fw-bold mb-1 text-white"><i class="fas fa-user-md me-1"></i> {{ $t->medico }}</h6>
                        <small class="d-block text-white-50"><i class="fas fa-clock me-1"></i> {{ $t->fecha_hora }}</small>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Buscador de Cartilla Médica -->
        <div class="input-group-voice mb-4">
            <input type="text" class="input-dark w-100 shadow-sm" placeholder="🔍 Buscar médico, clínica o especialidad...">
        </div>

        <h6 class="fw-bold text-white mb-3"><i class="fas fa-th-large text-primary me-2"></i> Especialidades de la Cartilla</h6>

        <div class="row g-2 mb-4">
            @foreach($especialidades as $esp)
                <div class="col-6">
                    <div class="card border-0 bg-secondary bg-opacity-10 p-3 h-100 rounded-3 shadow-sm d-flex flex-row align-items-center gap-3" style="border: 1px solid rgba(255,255,255,0.05); cursor: pointer;" onclick="alert('Mostrando médicos especialistas en {{ $esp->nombre }}...');">
                        <div class="bg-primary bg-opacity-20 text-primary p-3 rounded-circle" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas {{ $esp->icon }} fa-lg"></i>
                        </div>
                        <div>
                            <strong class="text-white d-block small">{{ $esp->nombre }}</strong>
                            <small class="text-muted" style="font-size: 0.65rem;">{{ $esp->prestadores_count }} Profesionales</small>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</div>
@endsection
