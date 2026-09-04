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
                        <span class="badge bg-light text-dark fw-bold" style="font-size: 0.65rem;">Sanatorio Central</span>
                    </div>
                    @foreach($turnosProximos as $t)
                        <h6 class="fw-bold mb-1 text-white"><i class="fas fa-user-md me-1"></i> {{ $t->medico }}</h6>
                        <small class="d-block text-white-50"><i class="fas fa-clock me-1"></i> {{ $t->fecha_hora }}</small>
                        <div class="mt-2 text-end">
                            <a href="https://api.whatsapp.com/send?phone=5493825551234&text={{ urlencode('Hola, confirmo mi asistencia al turno con el ' . $t->medico . ' para el ' . $t->fecha_hora) }}" target="_blank" class="btn btn-sm btn-light rounded-pill fw-bold text-success" style="font-size: 0.75rem;">
                                <i class="fab fa-whatsapp me-1"></i> Notificar por WhatsApp
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Buscador de Cartilla Médica -->
        <div class="mb-4 position-relative">
            <input type="text" id="cartilla-search-input" onkeyup="filtrarCartilla()" class="form-control bg-dark text-white border-secondary rounded-pill px-4 py-2 shadow-sm" placeholder="🔍 Buscar médico, clínica o especialidad...">
        </div>

        <h6 class="fw-bold text-white mb-3"><i class="fas fa-th-large text-primary me-2"></i> Especialidades de la Cartilla</h6>

        <div class="row g-2 mb-4" id="especialidades-grid">
            @foreach($especialidades as $esp)
                <div class="col-6 esp-card" data-nombre="{{ strtolower($esp->nombre) }}">
                    <div class="card border-0 bg-secondary bg-opacity-10 p-3 h-100 rounded-3 shadow-sm d-flex flex-row align-items-center gap-3" style="border: 1px solid rgba(255,255,255,0.05); cursor: pointer;" onclick="reservarTurnoEspecialidad('{{ $esp->nombre }}')">
                        <div class="bg-primary bg-opacity-20 text-primary p-3 rounded-circle flex-shrink-0" style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas {{ $esp->icon }} fa-lg"></i>
                        </div>
                        <div>
                            <strong class="text-white d-block small lh-sm">{{ $esp->nombre }}</strong>
                            <small class="text-muted" style="font-size: 0.65rem;">{{ $esp->prestadores_count }} Profesionales</small>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</div>

<script>
function filtrarCartilla() {
    const input = document.getElementById('cartilla-search-input').value.toLowerCase();
    const cards = document.querySelectorAll('.esp-card');
    cards.forEach(card => {
        const nombre = card.getAttribute('data-nombre');
        if (nombre.includes(input)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

function reservarTurnoEspecialidad(nombreEspecialidad) {
    alert("🩺 Reserva de Turno en 1-Clic:\n\nHas seleccionado: " + nombreEspecialidad + ".\n\nSe ha reservado un turno prioritario para el próximo Martes a las 11:00 hs en Sanatorio Central Chilecito.\n\n¡Recibirás la confirmación por WhatsApp!");
}
</script>
@endsection
