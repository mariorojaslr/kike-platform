@extends('layouts.app')

@section('title', 'Certificado Anual de Cobertura & Impuestos (ARCA) - Mutual INTEGRA')

@section('content')
<div class="container-fluid py-4" style="background-color: #0f172a; min-height: 100vh; color: white;">
    <div class="container" style="max-width: 680px;">
        
        <!-- Header Certificado -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary">
            <div>
                <span class="badge bg-success px-3 py-1 rounded-pill fw-bold text-uppercase" style="letter-spacing: 1px;">
                    <i class="fas fa-file-invoice me-1"></i> Certificado Anual ARCA / AFIP
                </span>
                <h3 class="fw-bold mt-2 mb-0 text-white">
                    <i class="fas fa-stamp text-warning me-2"></i> Deducción Impuesto a las Ganancias
                </h3>
                <small class="text-muted">Ejercicio Fiscal: <strong>{{ $anioFiscal }}</strong></small>
            </div>
            <div>
                <button type="button" class="btn btn-outline-light rounded-pill btn-sm fw-bold" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Imprimir Certificado
                </button>
            </div>
        </div>

        <!-- DOCUMENTO CERTIFICADO OFICIAL -->
        <div class="card border-0 bg-white text-dark shadow-lg p-4 rounded-4 position-relative overflow-hidden mb-4" id="printable-certificate" style="border: 2px solid #cbd5e1 !important;">
            
            <div class="d-flex justify-content-between align-items-start mb-4 border-bottom pb-3">
                <div>
                    <h4 class="fw-bold text-primary mb-1">OBRA SOCIAL & MUTUAL INTEGRA</h4>
                    <div class="small text-muted">Personería Jurídica N° 4812/98 &bull; CUIT 30-68912345-2</div>
                    <div class="small text-muted">Av. San Martín 450, Chilecito, La Rioja, Argentina</div>
                </div>
                <div class="text-end">
                    <span class="badge bg-dark text-white px-3 py-2 fs-6">AÑO {{ $anioFiscal }}</span>
                </div>
            </div>

            <div class="text-center my-3">
                <h5 class="fw-bold text-uppercase border-bottom border-top py-2" style="letter-spacing: 1px;">CERTIFICADO OFICIAL DE APORTES Y COBERTURA MUTUAL</h5>
            </div>

            <p class="lh-base" style="font-size: 0.95rem;">
                La **Obra Social / Mutual INTEGRA** CERTIFICA por la presente que el titular <strong>{{ $afiliado->nombre }}</strong>, DNI N° <strong>{{ $afiliado->dni }}</strong>, CUIT N° <strong>{{ $afiliado->cuit }}</strong>, posee cobertura médica y prestacional ininterrumpida bajo el número de afiliado <strong>{{ $afiliado->nro_afiliado }}</strong> en el plan <strong>{{ $afiliado->plan }}</strong>.
            </p>

            <div class="bg-light p-3 rounded-3 mb-4 border">
                <div class="row g-2 small">
                    <div class="col-6"><strong>Período Certificado:</strong> {{ $anioFiscal }} (12 Meses)</div>
                    <div class="col-6"><strong>Estado de Cuenta:</strong> <span class="text-success fw-bold">{{ $resumenAportes->estado_cuenta }}</span></div>
                    <div class="col-6"><strong>Integrantes Grupo Familiar:</strong> {{ $resumenAportes->grupo_familiar_count }} Personas</div>
                    <div class="col-6"><strong>Total Aportes Computables:</strong> <strong class="text-primary fs-6">${{ number_format($resumenAportes->total_aportes_periodo, 2, ',', '.') }}</strong></div>
                </div>
            </div>

            <p class="small text-muted mb-4">
                Se expide el presente certificado a pedido del interesado para ser presentado ante la **Agencia de Recaudación y Control Aduanero (ARCA / AFIP)** a los efectos de la deducción de cargas de familia y medicina prepaga / obra social en el Impuesto a las Ganancias.
            </p>

            <div class="d-flex justify-content-between align-items-end pt-3 border-top">
                <div>
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=90x90&data={{ urlencode('VALIDACION_ARCA_' . $resumenAportes->hash_certificado) }}" alt="QR Validación" width="70" height="70">
                    <div class="small font-monospace text-muted mt-1" style="font-size: 0.65rem;">MD5: {{ $resumenAportes->hash_certificado }}</div>
                </div>
                <div class="text-center" style="width: 200px;">
                    <div class="border-bottom border-dark mb-1" style="height: 40px;"></div>
                    <div class="small fw-bold">Firma & Sello Autoridad</div>
                    <div class="small text-muted" style="font-size: 0.65rem;">Gerencia General Mutual INTEGRA</div>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
