@extends('layouts.app')

@section('title', 'Billetera Virtual Mutual & Préstamos de Salud')

@section('content')
<div class="container-fluid py-4" style="background-color: #0f172a; min-height: 100vh; color: white;">
    <div class="container" style="max-width: 700px;">
        
        <!-- Header Billetera -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary">
            <div>
                <span class="badge bg-success text-white px-3 py-1 rounded-pill fw-bold text-uppercase shadow-sm" style="font-size: 0.75rem;">
                    <i class="fas fa-wallet me-1"></i> Finanzas de Salud Mutual
                </span>
                <h3 class="fw-bold mt-2 mb-0 text-white">
                    <i class="fas fa-credit-card text-success me-2"></i> Billetera Virtual INTEGRA
                </h3>
                <small class="text-white-50"><i class="fas fa-user me-1 text-info"></i> {{ $billetera->titular }} | {{ $billetera->cbu_alias }}</small>
            </div>
            <div class="bg-success bg-opacity-20 text-success p-3 rounded-circle text-center" style="width: 55px; height: 55px;">
                <i class="fas fa-coins fa-lg" style="line-height: 25px;"></i>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-lg rounded-3 mb-4 p-3 d-flex align-items-center gap-3">
                <i class="fas fa-check-circle fa-2x text-success"></i>
                <div>
                    <h6 class="mb-0 fw-bold">{{ session('success') }}</h6>
                    <small>Acreditación bancaria verificada en tiempo real.</small>
                </div>
            </div>
        @endif

        <!-- Card Saldo y Crédito -->
        <div class="card border-0 text-white shadow-lg mb-4 p-4" style="background: linear-gradient(135deg, #059669, #047857); border-radius: 20px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-white-50 small fw-bold">SALDO EN CUENTA PARA COPAGOS Y FARMACIA</span>
                <i class="fas fa-wifi text-white-50"></i>
            </div>
            <h1 class="fw-bold mb-3 display-5">${{ number_format($billetera->saldo_disponible, 0, ',', '.') }}</h1>
            
            <div class="pt-3 border-top border-white border-opacity-25 d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-white-50 d-block">Línea de Crédito Salud Preaprobada:</small>
                    <strong class="fs-5">${{ number_format($billetera->credito_preaprobado, 0, ',', '.') }} (hasta 12 cuotas)</strong>
                </div>
                <button type="button" class="btn btn-light rounded-pill fw-bold text-success px-3 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalPedirCredito">
                    <i class="fas fa-hand-holding-usd me-1"></i> Pedir Crédito
                </button>
            </div>
        </div>

        <!-- Movimientos -->
        <h5 class="fw-bold text-white mb-3"><i class="fas fa-history me-2 text-info"></i> Historial de Movimientos</h5>

        <div class="card border-0 bg-secondary bg-opacity-10 shadow-sm" style="border-radius: 16px; border: 1px solid rgba(255,255,255,0.1);">
            <div class="card-body p-0">
                <div class="list-group list-group-flush bg-transparent">
                    @foreach($movimientos as $m)
                        <div class="list-group-item bg-transparent text-white p-3 border-secondary border-opacity-25 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-bold mb-1">
                                    @if($m->monto > 0)
                                        <i class="fas fa-arrow-down text-success me-2"></i>
                                    @else
                                        <i class="fas fa-arrow-up text-danger me-2"></i>
                                    @endif
                                    {{ $m->concepto }}
                                </h6>
                                <small class="text-white-50">{{ $m->fecha }} | ID: {{ $m->id }}</small>
                            </div>
                            <div class="text-end">
                                <span class="fw-bold fs-5 {{ $m->monto > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $m->monto > 0 ? '+' : '' }}${{ number_format($m->monto, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal Pedir Crédito -->
<div class="modal fade" id="modalPedirCredito" tabindex="-1" aria-hidden="true" style="color: #0f172a;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header bg-dark text-white border-0" style="border-radius: 16px 16px 0 0;">
                <h5 class="modal-title fw-bold"><i class="fas fa-hand-holding-usd text-success me-2"></i> Solicitar Microcrédito de Salud</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('afiliado.billetera.credito') }}" method="POST">
                @csrf
                <div class="modal-body p-4 text-dark">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Monto a Solicitar ($)</label>
                        <select name="monto" class="form-select fw-bold text-success">
                            <option value="50000">$50.000 en 6 cuotas de $9.800</option>
                            <option value="100000" selected>$100.000 en 12 cuotas de $10.500</option>
                            <option value="200000">$200.000 en 12 cuotas de $21.000</option>
                        </select>
                    </div>
                    <small class="text-muted">El crédito se descuenta automáticamente de su recibo de sueldo / débito directo.</small>
                </div>
                <div class="modal-footer border-0 pb-4 pe-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 text-secondary fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                        <i class="fas fa-bolt me-1"></i> Acreditar al Instante
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
