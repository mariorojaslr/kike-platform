@extends('layouts.app')

@section('title', 'Cobertura de Derivaciones & Viáticos - Mutual INTEGRA')

@section('content')
<div class="container-fluid py-4" style="background-color: #0f172a; min-height: 100vh; color: white;">
    <div class="container" style="max-width: 600px;">
        
        <!-- Header Derivaciones -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary">
            <div>
                <span class="badge bg-purple px-3 py-1 rounded-pill fw-bold text-uppercase" style="letter-spacing: 1px; background: #8b5cf6;">
                    <i class="fas fa-plane-departure me-1"></i> Alta Complejidad & Derivaciones
                </span>
                <h3 class="fw-bold mt-2 mb-0 text-white">
                    <i class="fas fa-hospital-alt text-info me-2"></i> Cobertura Fuera de Provincia
                </h3>
                <small class="text-muted">Red Sanatorial: Córdoba (Allende) & Buenos Aires (Italiano)</small>
            </div>
            <div>
                <button type="button" class="btn btn-outline-warning rounded-pill btn-sm fw-bold" onclick="generarVoucherPwa()">
                    <i class="fas fa-ticket-alt me-1"></i> Nueva Derivación
                </button>
            </div>
        </div>

        <!-- Tarjeta Informativa de Viáticos -->
        <div class="card border-0 bg-dark shadow-sm mb-4" style="border-left: 4px solid #8b5cf6 !important; border-radius: 14px;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <i class="fas fa-suitcase-rolling fa-2x text-purple" style="color: #a78bfa;"></i>
                    <div>
                        <h6 class="fw-bold mb-1 text-white">Módulo de Cobertura de Tránsito & Viáticos</h6>
                        <p class="small text-muted mb-0">
                            Los afiliados derivados por junta médica cuentan con <b>Credencial Provisoria de Tránsito QR</b>, alojamiento en hoteles en convenio y subsidio diario de viáticos acreditados en su cuenta.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Listado de Derivaciones -->
        <h5 class="fw-bold text-white mb-3"><i class="fas fa-route me-2 text-info"></i> Derivaciones y Vales de Viaje Activos</h5>

        @foreach($derivacionesActivas as $der)
            <div class="card border-0 bg-secondary bg-opacity-10 shadow-sm mb-3" style="border-radius: 16px; border: 1px solid rgba(255,255,255,0.1);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-primary px-3 py-1 rounded-pill"><i class="fas fa-map-marker-alt me-1"></i> {{ $der->destino }}</span>
                        <span class="badge bg-success px-3 py-1 rounded-pill"><i class="fas fa-check-circle me-1"></i> AUTORIZADO</span>
                    </div>

                    <h5 class="fw-bold text-white mb-1"><i class="fas fa-user-circle me-1 text-warning"></i> {{ $der->afiliado_nombre }}</h5>
                    <small class="text-muted d-block mb-3">Afiliado: {{ $der->nro_afiliado }} | DNI: {{ $der->afiliado_dni }}</small>

                    <div class="bg-dark p-3 rounded-3 mb-3 border border-secondary border-opacity-25" style="font-size: 0.8rem;">
                        <div class="mb-1 text-info"><strong>Centro Médico:</strong> {{ $der->centro_medico }}</div>
                        <div class="mb-1 text-white"><strong>Diagnóstico:</strong> {{ $der->diagnostico }}</div>
                        <div class="mb-1 text-success"><strong>Alojamiento:</strong> {{ $der->cobertura_alojamiento }}</div>
                        <div class="text-warning"><strong>Viáticos Acreditados:</strong> ${{ number_format($der->monto_viaticos, 2, ',', '.') }} (Fecha viaje: {{ $der->fecha_salida }})</div>
                    </div>

                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-info rounded-pill w-100 fw-bold" onclick="alert('Ver Credencial Provisoria de Tránsito con QR para presentar en {{ $der->centro_medico }}')">
                            <i class="fas fa-id-card me-1"></i> Credencial Tránsito QR
                        </button>
                        <button class="btn btn-sm btn-outline-light rounded-pill w-100 fw-bold" onclick="window.print()">
                            <i class="fas fa-print me-1"></i> Imprimir Vales
                        </button>
                    </div>
                </div>
            </div>
        @endforeach

    </div>
</div>

<script>
function generarVoucherPwa() {
    fetch("{{ route('derivaciones.emitir_voucher') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Accept": "application/json"
        },
        body: JSON.stringify({
            afiliado_nombre: "ABAYAY RAMON MARTIN",
            destino: "Sede Córdoba (Alta Complejidad)",
            dias_alojamiento: 5
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message + "\n\nCódigo de Voucher: " + data.voucher.codigo + "\nHash MD5: " + data.voucher.hash_md5);
            window.location.reload();
        }
    })
    .catch(err => alert("Error al emitir voucher de tránsito."));
}
</script>
@endsection
