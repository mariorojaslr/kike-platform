@extends('layouts.app')

@section('title', 'Credencial Digital Oficial - Mutual INTEGRA')

@section('content')
<div class="container-fluid py-4" style="background-color: #0f172a; min-height: 100vh; color: white;">
    <div class="container" style="max-width: 500px;">
        
        <!-- Header PWA -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom border-secondary border-opacity-25">
            <div>
                <span class="badge bg-primary px-3 py-1 rounded-pill text-uppercase" style="font-size:0.7rem;">
                    <i class="fas fa-id-card me-1"></i> Credencial Digital Oficial
                </span>
                <h4 class="fw-bold mt-2 mb-0 text-white">Mutual OSP &bull; INTEGRA</h4>
            </div>
            <a href="{{ route('afiliado.turnos.demo') }}" class="btn btn-outline-info rounded-pill btn-sm fw-bold">
                <i class="fas fa-calendar-check me-1"></i> Cartilla y Turnos
            </a>
        </div>

        <!-- TARJETA CREDENCIAL METALIZADA DIGITAL -->
        <div class="card border-0 mb-4 shadow-lg text-white" style="border-radius: 20px; background: linear-gradient(135deg, #1e3a8a, #0f172a, #1d4ed8); border: 2px solid rgba(255,255,255,0.2) !important; position: relative; overflow: hidden;">
            
            <!-- Marca de Agua / Brillo -->
            <div style="position: absolute; right: -30px; bottom: -30px; opacity: 0.1; font-size: 10rem; pointer-events: none;">
                <i class="fas fa-shield-alt"></i>
            </div>

            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="badge bg-success bg-opacity-20 text-success border border-success px-3 py-1 rounded-pill fw-bold" style="font-size: 0.75rem;">
                            <i class="fas fa-check-circle me-1"></i> {{ $afiliado->estado }}
                        </span>
                        <h6 class="text-warning fw-bold mt-2 mb-0" style="letter-spacing: 1px;">{{ $afiliado->plan }}</h6>
                    </div>
                    <i class="fas fa-wifi text-white-50 fa-lg"></i>
                </div>

                <div class="mb-3">
                    <div class="text-white-50 small text-uppercase" style="font-size: 0.65rem;">Titular Habilitado</div>
                    <h4 class="fw-bold text-white mb-0" style="letter-spacing: 0.5px;">{{ $afiliado->nombre }}</h4>
                    <div class="small text-white-50">DNI: <strong class="text-white">{{ $afiliado->dni }}</strong></div>
                </div>

                <div class="d-flex justify-content-between align-items-end pt-3 border-top border-white border-opacity-10">
                    <div>
                        <div class="text-white-50 small text-uppercase" style="font-size: 0.65rem;">N° Afiliado Mutual</div>
                        <div class="font-monospace fw-bold fs-5 text-info">{{ $afiliado->nro_afiliado }}</div>
                        <small class="text-muted" style="font-size: 0.65rem;">Vence: {{ $afiliado->fecha_vencimiento }}</small>
                    </div>

                    <!-- Código QR Dinámico con Token de Seguridad -->
                    <div class="bg-white p-2 rounded-3 text-center shadow-sm">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=90x90&data={{ urlencode('MUTUAL_TOKEN_' . $afiliado->token_dinamico . '_DNI_' . $afiliado->dni) }}" alt="QR Afiliado" width="75" height="75">
                        <div class="font-monospace text-dark fw-bold mt-1" style="font-size: 0.6rem;">TOKEN: {{ $afiliado->token_dinamico }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Token de Seguridad Dinámico -->
        <div class="card border-0 bg-dark p-3 mb-4 rounded-3 text-center border border-secondary border-opacity-25 shadow-sm">
            <span class="text-muted small">Muestre este QR o Token en Clínicas, Farmacias y Sanatorios</span>
            <h4 class="font-monospace fw-bold text-warning mb-0 mt-1"><i class="fas fa-lock me-2"></i> {{ $afiliado->token_dinamico }}</h4>
            <small class="text-success" style="font-size: 0.7rem;"><i class="fas fa-sync fa-spin me-1"></i> Token válido por los próximos 15 minutos</small>
        </div>

        <!-- Grupo Familiar Adherente -->
        <h6 class="fw-bold text-white mb-3"><i class="fas fa-users text-info me-2"></i> Grupo Familiar Adherente</h6>

        @foreach($afiliado->grupo_familiar as $fam)
            <div class="card border-0 bg-secondary bg-opacity-10 p-3 mb-2 rounded-3 d-flex flex-row justify-content-between align-items-center" style="border: 1px solid rgba(255,255,255,0.05);">
                <div>
                    <strong class="text-white d-block"><i class="fas fa-user-circle me-1 text-primary"></i> {{ $fam->nombre }}</strong>
                    <small class="text-muted">DNI: {{ $fam->dni }} | {{ $fam->parentesco }}</small>
                </div>
                <span class="badge bg-success rounded-pill px-3 py-1"><i class="fas fa-check me-1"></i> ACTIVO</span>
            </div>
        @endforeach

    </div>
</div>
@endsection
