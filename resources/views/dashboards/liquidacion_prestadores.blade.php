@extends('layouts.app')

@section('title', 'Tablero de Cierre de Liquidaciones & Billeteras - Mutual INTEGRA')

@section('content')
<div class="container-fluid py-4" style="background-color: #0b1329; min-height: 100vh; color: white;">
    <div class="container">
        
        <!-- Header del Tablero de Liquidaciones -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary border-opacity-25">
            <div>
                <span class="badge bg-success px-3 py-1 rounded-pill fw-bold text-uppercase" style="letter-spacing: 1px;">
                    <i class="fas fa-wallet me-1"></i> Tesorería & Liquidación Masiva CBU
                </span>
                <h2 class="fw-bold mt-2 mb-0 text-white">
                    <i class="fas fa-file-invoice-dollar text-success me-2"></i> Cierre de Liquidaciones a Prestadores
                </h2>
                <small class="text-muted"><i class="fas fa-calendar-alt me-1 text-info"></i> Período en Cierre: <strong>{{ $periodoActual }}</strong></small>
            </div>
            <button type="button" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm" onclick="ejecutarCierreMasivo()">
                <i class="fas fa-check-double me-1"></i> Aprobar & Creditar Lote Masivo
            </button>
        </div>

        <!-- KPIs de Liquidación -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 bg-secondary bg-opacity-10 p-3" style="border-radius: 14px; border-left: 4px solid #3b82f6 !important;">
                    <span class="text-muted small fw-bold text-uppercase">TOTAL EXPEDIENTES</span>
                    <h3 class="fw-bold text-primary mb-0 mt-1">${{ number_format($resumenGlobal->total_a_liquidar, 2, ',', '.') }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 bg-secondary bg-opacity-10 p-3" style="border-radius: 14px; border-left: 4px solid #10b981 !important;">
                    <span class="text-muted small fw-bold text-uppercase">APROBADO PARA TRANSF.</span>
                    <h3 class="fw-bold text-success mb-0 mt-1">${{ number_format($resumenGlobal->liquidado_aprobado, 2, ',', '.') }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 bg-secondary bg-opacity-10 p-3" style="border-radius: 14px; border-left: 4px solid #f59e0b !important;">
                    <span class="text-muted small fw-bold text-uppercase">RETENIDO EN AUDITORÍA</span>
                    <h3 class="fw-bold text-warning mb-0 mt-1">${{ number_format($resumenGlobal->en_auditoria, 2, ',', '.') }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 bg-secondary bg-opacity-10 p-3" style="border-radius: 14px; border-left: 4px solid #8b5cf6 !important;">
                    <span class="text-muted small fw-bold text-uppercase">PRESTADORES / LOTES</span>
                    <h3 class="fw-bold text-purple mb-0 mt-1" style="color: #a78bfa;">{{ $resumenGlobal->prestadores_count }} Prestadores</h3>
                </div>
            </div>
        </div>

        <!-- Tabla de Cierre de Liquidaciones -->
        <h5 class="fw-bold text-white mb-3"><i class="fas fa-list me-2 text-info"></i> Desglose de Lotes de Pago por Prestador / Colegio</h5>

        <div class="card border-0 bg-secondary bg-opacity-10 shadow-sm" style="border-radius: 16px; border: 1px solid rgba(255,255,255,0.1);">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle mb-0" style="background: transparent;">
                        <thead>
                            <tr class="text-muted small text-uppercase">
                                <th class="ps-4">Prestador / Entidad</th>
                                <th>CUIT & CBU / Alias Bancario</th>
                                <th>Bruto Facturado</th>
                                <th>Retenciones</th>
                                <th>Neto a Acreditar</th>
                                <th class="text-center">Estado Auditoría</th>
                                <th class="text-end pe-4">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($liquidacionesPrestadores as $liq)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-white"><i class="fas fa-building me-1 text-primary"></i> {{ $liq->prestador_nombre }}</div>
                                        <small class="text-muted">{{ $liq->tipo }}</small>
                                    </td>
                                    <td>
                                        <div class="small fw-bold text-info font-monospace">{{ $liq->cbu_alias }}</div>
                                        <small class="text-muted">CUIT: {{ $liq->cuit }} | {{ $liq->banco }}</small>
                                    </td>
                                    <td class="fw-bold text-white">${{ number_format($liq->monto_bruto, 2, ',', '.') }}</td>
                                    <td class="text-danger">-${{ number_format($liq->retenciones, 2, ',', '.') }}</td>
                                    <td class="fw-bold text-success fs-6">${{ number_format($liq->monto_neto, 2, ',', '.') }}</td>
                                    <td class="text-center">
                                        @if($liq->estado == 'aprobado_para_pago')
                                            <span class="badge bg-success px-3 py-1.5 rounded-pill"><i class="fas fa-check-circle me-1"></i> LISTO PARA TRANSFERIR</span>
                                        @else
                                            <span class="badge bg-warning text-dark px-3 py-1.5 rounded-pill fw-bold"><i class="fas fa-clock me-1"></i> EN AUDITORÍA FINAL</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <button class="btn btn-sm btn-outline-light rounded-pill px-3 fw-bold" onclick="alert('Generando archivo de transferencia bancaria CBU para {{ $liq->prestador_nombre }}...')">
                                            <i class="fas fa-download me-1 text-info"></i> TXT CBU
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

<script>
function ejecutarCierreMasivo() {
    if (!confirm("¿Deseas firmar y autorizar la transferencia masiva de $142.800.000,00 a las cuentas CBU auditadas?")) return;

    fetch("{{ route('liquidacion.procesar_cierre') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Accept": "application/json"
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message + "\n\nHash MD5 de Trazabilidad: " + data.hash_seguridad);
            window.location.reload();
        }
    })
    .catch(err => alert("Error al procesar el cierre masivo."));
}
</script>
@endsection
