@extends('layouts.app')

@section('title', 'Liquidación Oficial e-SIDIF & Orden de Pago Docentes - APOS La Rioja')

@section('content')
<div class="container-fluid py-4" style="background-color: #0b1329; min-height: 100vh; color: white;">
    <div class="container-xl">

        <!-- Header Institucional APOS + e-SIDIF Argentina.gob.ar -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary border-opacity-25 gap-3">
            <div class="d-flex align-items-center gap-3">
                <img src="{{ asset('images/apos_logo.svg') }}" alt="APOS La Rioja" class="rounded-4 shadow" style="width: 68px; height: 68px; background: #fff; padding: 3px;">
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="badge bg-info text-dark px-3 py-1 rounded-pill fw-bold text-uppercase" style="letter-spacing: 0.5px;">
                            <i class="fas fa-landmark me-1"></i> e-SIDIF | Argentina.gob.ar (Secretaría de Hacienda)
                        </span>
                        <span class="badge bg-primary bg-opacity-25 text-info border border-info border-opacity-25 px-3 py-1 rounded-pill">
                            Sucursal La Rioja: Soluciones Digitales MCR &rarr; Cliente: APOS
                        </span>
                    </div>
                    <h2 class="fw-bold mt-2 mb-0 text-white" style="font-size: 1.65rem;">
                        <i class="fas fa-file-invoice-dollar text-success me-2"></i> Orden de Pago Individual y Colectiva &mdash; Docentes APOS
                    </h2>
                    <small class="text-white-50">
                        <i class="fas fa-calendar-check me-1 text-info"></i> Período Auditado: <strong class="text-white text-capitalize">{{ $periodoActual }}</strong>
                        &bull; Padrón verificado por Escuela, Auditoría y Jefatura de Área.
                    </small>
                </div>
            </div>

            <!-- Botonera Principal: 1) CSV e-SIDIF  2) Llave Pública/Privada WebService  3) Interbanking -->
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('liquidacion.exportar_esidif_csv') }}" class="btn btn-success rounded-pill px-4 py-2 fw-bold shadow">
                    <i class="fas fa-file-csv me-2 fa-lg"></i> 1. Descargar CSV Carga Masiva e-SIDIF
                </a>
                <button type="button" class="btn btn-warning text-dark rounded-pill px-4 py-2 fw-bold shadow" data-bs-toggle="modal" data-bs-target="#modalPkiEsidif">
                    <i class="fas fa-key me-2"></i> 2. Automatización Llave Pública / Privada (e-SIDIF)
                </button>
                <a href="{{ route('liquidacion.exportar_interbanking') }}" class="btn btn-outline-info rounded-pill px-3 py-2 fw-bold">
                    <i class="fas fa-university me-1"></i> TXT Interbanking
                </a>
                <a href="{{ route('tenant.dashboard') }}" class="btn btn-outline-light rounded-pill px-3 py-2">
                    <i class="fas fa-arrow-left me-1"></i> Volver a APOS
                </a>
            </div>
        </div>

        <!-- Panel de Integración e-SIDIF (CSV + Automatización PKI Llave Pública y Privada) -->
        <div class="card border-0 mb-4 shadow" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-radius: 16px; border: 1px solid rgba(56, 189, 248, 0.35) !important;">
            <div class="card-body p-4">
                <div class="row align-items-center g-3">
                    <div class="col-lg-8">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-success px-2.5 py-1 rounded-pill"><i class="fas fa-shield-halved me-1"></i> CONECTOR OFICIAL e-SIDIF ACTIVO</span>
                            <span class="text-info small font-monospace">{{ $pkiConfig['saf_codigo'] }} | CUIT: {{ $pkiConfig['cuit_organismo'] }}</span>
                        </div>
                        <h5 class="fw-bold text-white mb-1">
                            Pasarela de Liquidación Masiva de Docentes Integradores hacia <span class="text-info">e-SIDIF (Nación / Provincia)</span>
                        </h5>
                        <p class="text-white-50 small mb-2">
                            Podés descargar al instante el <strong>Archivo CSV Oficial de Carga Masiva</strong> con todos los docentes aprobados listos para subir a la plataforma e-SIDIF, o bien ejecutar el envío automatizado firmado con <strong>Llave Pública (`.crt`/`.pem`) y Llave Privada (`.key`)</strong>.
                        </p>
                        <div class="d-flex flex-wrap gap-3 small text-white-50">
                            <span><i class="fas fa-certificate text-warning me-1"></i> <strong>Llave Pública:</strong> {{ $pkiConfig['public_key_fingerprint'] }}</span>
                            <span><i class="fas fa-lock text-success me-1"></i> <strong>Llave Privada:</strong> {{ $pkiConfig['private_key_status'] }}</span>
                        </div>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <div class="d-grid gap-2">
                            <a href="{{ route('liquidacion.exportar_esidif_csv') }}" class="btn btn-success fw-bold rounded-3 py-2 shadow-sm">
                                <i class="fas fa-download me-2"></i> Descargar Archivo CSV para e-SIDIF ({{ count($planillaIndividualDocentes) }} Docentes)
                            </a>
                            <button type="button" class="btn btn-outline-warning fw-bold rounded-3 py-2" onclick="transmitirLoteAutomaticoEsidif()">
                                <i class="fas fa-satellite-dish me-2"></i> Transmitir Lote con Llave Pública/Privada
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPIs de Liquidación APOS -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 bg-secondary bg-opacity-10 p-3 h-100" style="border-radius: 14px; border-left: 4px solid #10b981 !important;">
                    <span class="text-white-50 small fw-bold text-uppercase">TOTAL ORDEN DE PAGO DOCENTES</span>
                    <h3 class="fw-bold text-success mb-0 mt-1">${{ number_format($resumenGlobal->liquidado_aprobado, 2, ',', '.') }}</h3>
                    <small class="text-white-50">100% Auditado con Resolución Activa</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 bg-secondary bg-opacity-10 p-3 h-100" style="border-radius: 14px; border-left: 4px solid #3b82f6 !important;">
                    <span class="text-white-50 small fw-bold text-uppercase">EXPEDIENTES / DOCENTES A COBRAR</span>
                    <h3 class="fw-bold text-info mb-0 mt-1">{{ $resumenGlobal->prestadores_count }} Liquidaciones</h3>
                    <small class="text-white-50">Incluidos en el CSV Masivo e-SIDIF</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 bg-secondary bg-opacity-10 p-3 h-100" style="border-radius: 14px; border-left: 4px solid #f59e0b !important;">
                    <span class="text-white-50 small fw-bold text-uppercase">HORAS CERTIFICADAS ESCUELAS</span>
                    <h3 class="fw-bold text-warning mb-0 mt-1">{{ number_format($resumenGlobal->total_horas_certificadas, 0, ',', '.') }} Hs.</h3>
                    <small class="text-white-50">Tope cumplido: Máx. 3 hs/día sin superposición</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 bg-secondary bg-opacity-10 p-3 h-100" style="border-radius: 14px; border-left: 4px solid #8b5cf6 !important;">
                    <span class="text-white-50 small fw-bold text-uppercase">VALOR HORA NOMENCLADOR APOS</span>
                    <h3 class="fw-bold mb-0 mt-1" style="color: #a78bfa;">${{ number_format($resumenGlobal->valor_hora_oficial, 2, ',', '.') }}</h3>
                    <small class="text-white-50">Educación Especial / Discapacidad CUD</small>
                </div>
            </div>
        </div>

        <!-- Resumen Colectivo de Lotes de Pago APOS -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold text-white mb-0">
                <i class="fas fa-layer-group me-2 text-info"></i> 1. Orden de Pago Colectiva por Sector (APOS La Rioja)
            </h5>
            <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-bold" onclick="ejecutarCierreMasivo()">
                <i class="fas fa-check-double me-1"></i> Firmar Acta Colectiva del Mes
            </button>
        </div>

        <div class="card border-0 bg-secondary bg-opacity-10 shadow-sm mb-5" style="border-radius: 16px; border: 1px solid rgba(255,255,255,0.1);">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle mb-0" style="background: transparent;">
                        <thead>
                            <tr class="text-white-50 small text-uppercase">
                                <th class="ps-4">Sector / Lote Colectivo APOS</th>
                                <th>Jurisdicción SAF & Alias</th>
                                <th>Bruto Liquidado</th>
                                <th>Retenciones</th>
                                <th>Neto a Transferir</th>
                                <th class="text-center">Estado Auditoría</th>
                                <th class="text-end pe-4">Exportación Oficial</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($liquidacionesPrestadores as $liq)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-white"><i class="fas fa-building-columns me-1 text-info"></i> {{ $liq->prestador_nombre }}</div>
                                        <small class="text-white-50">{{ $liq->tipo }}</small>
                                    </td>
                                    <td>
                                        <div class="small fw-bold text-info font-monospace">{{ $liq->cbu_alias }}</div>
                                        <small class="text-white-50">{{ $liq->cuit }} | {{ $liq->banco }}</small>
                                    </td>
                                    <td class="fw-bold text-white">${{ number_format($liq->monto_bruto, 2, ',', '.') }}</td>
                                    <td class="text-danger">-${{ number_format($liq->retenciones, 2, ',', '.') }}</td>
                                    <td class="fw-bold text-success fs-6">${{ number_format($liq->monto_neto, 2, ',', '.') }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-success text-white px-3 py-1 rounded-pill fw-bold shadow-sm">
                                            <i class="fas fa-check-circle me-1"></i> APROBADO P/ PAGO
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('liquidacion.exportar_esidif_csv') }}" class="btn btn-sm btn-success rounded-pill px-3 fw-bold">
                                                <i class="fas fa-file-csv me-1"></i> CSV e-SIDIF
                                            </a>
                                            <a href="{{ route('liquidacion.exportar_interbanking') }}" class="btn btn-sm btn-outline-info rounded-pill px-3 fw-bold">
                                                <i class="fas fa-download me-1"></i> TXT CBU
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Planilla Individual Detallada de Docentes Integradores para e-SIDIF -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
            <div>
                <h5 class="fw-bold text-white mb-0">
                    <i class="fas fa-chalkboard-teacher me-2 text-success"></i> 2. Orden de Pago Individual por Docente Integrador (Contenido del CSV e-SIDIF)
                </h5>
                <small class="text-white-50">Cada fila corresponde a un expediente auditado con Resolución de APOS o Autorización de Auditoría, horas avaladas por la Directora de Escuela y CBU del Docente.</small>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <div class="input-group input-group-sm" style="width: 300px;">
                    <span class="input-group-text bg-dark text-info border-secondary"><i class="fas fa-search"></i></span>
                    <input type="text" id="buscadorLiquidacionDocente" class="form-control bg-dark text-white border-secondary" placeholder="Buscar docente, alumno, resolución, escuela...">
                </div>
                <a href="{{ route('liquidacion.exportar_esidif_csv') }}" class="btn btn-sm btn-success rounded-pill px-3 fw-bold">
                    <i class="fas fa-file-csv me-1"></i> Descargar CSV Completo
                </a>
            </div>
        </div>

        <div class="card border-0 bg-secondary bg-opacity-10 shadow-sm" style="border-radius: 16px; border: 1px solid rgba(255,255,255,0.1);">
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 580px; overflow-y: auto;">
                    <table class="table table-dark table-hover align-middle mb-0" id="tablaLiquidacionIndividual" style="background: transparent;">
                        <thead style="position: sticky; top: 0; background-color: #0f172a; z-index: 5;">
                            <tr class="text-white-50 small text-uppercase">
                                <th class="ps-4">N° Resolución APOS</th>
                                <th>Docente Integrador (Beneficiario)</th>
                                <th>CUIT & CBU Acreditación</th>
                                <th>Alumno Afiliado & CUD</th>
                                <th>Escuela Certificadora</th>
                                <th class="text-center">Hs. Mes</th>
                                <th class="text-end pe-4">Neto a Pagar (e-SIDIF)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($planillaIndividualDocentes as $item)
                                <tr class="fila-liquidacion-docente">
                                    <td class="ps-4">
                                        <span class="badge bg-info bg-opacity-25 text-info border border-info border-opacity-25 font-monospace px-2 py-1">
                                            <i class="fas fa-file-signature me-1"></i>{{ $item->nro_resolucion }}
                                        </span>
                                        <div class="small text-white-50 mt-1">Exp. #{{ str_pad($item->expediente_id, 4, '0', STR_PAD_LEFT) }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-white">{{ $item->docente_nombre }}</div>
                                        <small class="text-white-50"><i class="fas fa-phone-alt me-1 text-success"></i>{{ $item->docente_telefono }}</small>
                                    </td>
                                    <td>
                                        <div class="small fw-bold text-info font-monospace">CUIT: {{ $item->docente_cuit }}</div>
                                        <small class="text-white-50 font-monospace">CBU: {{ $item->docente_cbu }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-white">{{ $item->alumno_nombre }}</div>
                                        <small class="text-warning">Afiliado: {{ $item->alumno_afiliado }} &bull; DNI: {{ $item->alumno_dni }}</small><br>
                                        <small class="text-white-50">{{ \Illuminate\Support\Str::limit($item->patologia_cud, 42) }}</small>
                                    </td>
                                    <td>
                                        <div class="small text-white">{{ $item->escuela_nombre }}</div>
                                        <small class="text-success"><i class="fas fa-check-double me-1"></i>Asistencia Certificada</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary bg-opacity-25 text-white border border-primary px-2.5 py-1 fw-bold">
                                            {{ $item->horas_certificadas }} hs
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="fw-bold text-success fs-6">${{ number_format($item->monto_neto, 2, ',', '.') }}</div>
                                        <small class="text-white-50">Incluido en CSV</small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-white-50">
                                        No hay expedientes cargados para liquidar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- MODAL: CONFIGURACIÓN CRIPTOGRÁFICA LLAVE PÚBLICA Y LLAVE PRIVADA (e-SIDIF WEBSERVICE) -->
<div class="modal fade" id="modalPkiEsidif" tabindex="-1" aria-hidden="true" style="color: #0f172a;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px;">
            <div class="modal-header text-white border-0" style="background: linear-gradient(135deg, #0f172a 0%, #0369a1 100%); border-radius: 18px 18px 0 0;">
                <div>
                    <span class="badge bg-warning text-dark fw-bold mb-1"><i class="fas fa-lock me-1"></i> SEGURIDAD CRIPTOGRÁFICA PKI / mTLS</span>
                    <h5 class="modal-title fw-bold mb-0"><i class="fas fa-key me-2"></i> Automatización e-SIDIF con Llave Pública y Llave Privada</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-info border-0 small mb-3">
                    <i class="fas fa-info-circle me-1"></i>
                    Además de la descarga inmediata en <strong>CSV para Carga Masiva</strong>, INTEGRA permite automatizar el envío directo de las órdenes de pago al WebService de <strong>e-SIDIF (Secretaría de Hacienda de la Nación)</strong> mediante autenticación mutua con <strong>Llave Pública (`.crt`/`.pem`)</strong> y firma digital con <strong>Llave Privada (`.key`)</strong>.
                </div>

                <form id="formPkiEsidif" onsubmit="guardarConfiguracionPki(event)">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Código SAF / Jurisdicción e-SIDIF</label>
                            <input type="text" name="saf_codigo" id="pkiSaf" class="form-control bg-light" value="{{ $pkiConfig['saf_codigo'] }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">CUIT Institucional (APOS La Rioja)</label>
                            <input type="text" name="cuit_organismo" id="pkiCuit" class="form-control bg-light" value="{{ $pkiConfig['cuit_organismo'] }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small text-muted">Endpoint WebService Oficial e-SIDIF</label>
                            <input type="url" name="endpoint_ws" id="pkiEndpoint" class="form-control bg-light font-monospace" value="{{ $pkiConfig['endpoint_ws'] }}" required>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 rounded-3 border bg-light h-100">
                                <label class="form-label fw-bold small text-primary mb-1">
                                    <i class="fas fa-certificate me-1"></i> 1. Llave Pública / Certificado X.509 (`.crt` / `.pem`)
                                </label>
                                <input type="file" class="form-control form-control-sm mb-2" accept=".crt,.pem,.cer">
                                <textarea class="form-control font-monospace" rows="4" style="font-size: 0.72rem;" placeholder="-----BEGIN CERTIFICATE-----">-----BEGIN CERTIFICATE-----
MIIFazCCA1OgAwIBAgIRAIIQz7DSQONZRGPgu2OCiwAwDQYJKoZIhvcNAQELBQAw
TzELMAkGA1UEBhMCQVAxFDASBgNVBAoTC0FQT1MgUklPSkExJDAiBgNVBAMTG2Vz
aWRpZi1hcG9zLmxhcmlvamEuZ29iLmFyMB4XDTI2MDEwMTAwMDAwMFoXDTI4MDEw
-----END CERTIFICATE-----</textarea>
                                <small class="text-success d-block mt-1"><i class="fas fa-check-circle me-1"></i> Llave Pública registrada ante e-SIDIF</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 rounded-3 border bg-light h-100">
                                <label class="form-label fw-bold small text-danger mb-1">
                                    <i class="fas fa-key me-1"></i> 2. Llave Privada de Firma Digital (`.key`)
                                </label>
                                <input type="file" class="form-control form-control-sm mb-2" accept=".key,.pem">
                                <textarea class="form-control font-monospace" rows="4" style="font-size: 0.72rem;" placeholder="-----BEGIN RSA PRIVATE KEY-----">-----BEGIN ENCRYPTED PRIVATE KEY-----
MIIFDjBABgkqhkiG9w0BBQ0wMzAbBgkqhkiG9w0BBQwwDgQIS29s481920192831
MBQGCCqGSIb3DQMHBAg819203948102938471029384710293847102938471029
**[RESGUARDADA EN BÓVEDA SERVIDOR INTEGRA]**
-----END ENCRYPTED PRIVATE KEY-----</textarea>
                                <small class="text-muted d-block mt-1"><i class="fas fa-shield-alt text-primary me-1"></i> Nunca sale del servidor; firma cada lote CSV/XML.</small>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 d-flex flex-wrap justify-content-between align-items-center gap-2 border-top pt-3">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cerrar</button>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                                <i class="fas fa-save me-1"></i> Guardar Par de Llaves (Pública/Privada)
                            </button>
                            <button type="button" class="btn btn-success rounded-pill px-4 fw-bold" onclick="transmitirLoteAutomaticoEsidif()">
                                <i class="fas fa-paper-plane me-1"></i> Firmar y Transmitir Ahora a e-SIDIF
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Buscador en tiempo real en la planilla individual de docentes
document.addEventListener('DOMContentLoaded', function() {
    const inputBusqueda = document.getElementById('buscadorLiquidacionDocente');
    if (inputBusqueda) {
        inputBusqueda.addEventListener('input', function() {
            const q = this.value.toLowerCase().trim();
            document.querySelectorAll('.fila-liquidacion-docente').forEach(fila => {
                const texto = fila.innerText.toLowerCase();
                fila.style.display = texto.includes(q) ? '' : 'none';
            });
        });
    }
});

function guardarConfiguracionPki(e) {
    e.preventDefault();
    const saf = document.getElementById('pkiSaf').value;
    const cuit = document.getElementById('pkiCuit').value;
    const endpoint = document.getElementById('pkiEndpoint').value;

    fetch("{{ route('liquidacion.esidif_pki_guardar') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Accept": "application/json"
        },
        body: JSON.stringify({ saf_codigo: saf, cuit_organismo: cuit, endpoint_ws: endpoint })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message + "\n\nHuella Digital: " + data.fingerprint);
            window.location.reload();
        }
    });
}

function transmitirLoteAutomaticoEsidif() {
    if (!confirm("¿Deseás firmar digitalmente con la Llave Privada de APOS el lote completo de Docentes Integradores y transmitirlo al WebService de e-SIDIF Nación?")) return;

    fetch("{{ route('liquidacion.esidif_transmitir_ws') }}", {
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
            alert(
                data.message +
                "\n\n• N° Lote Oficial: " + data.lote_id +
                "\n• Docentes Incluidos: " + data.expedientes_transmitidos +
                "\n• Firma Digital RSA-SHA256: " + data.firma_digital_rsa
            );
        }
    })
    .catch(() => alert("Error de conexión con el servicio de transmisión."));
}

function ejecutarCierreMasivo() {
    if (!confirm("¿Deseás firmar el Acta Colectiva e Individual del período actual para APOS La Rioja?")) return;

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
            alert(data.message + "\n\nHash de Auditoría: " + data.hash_seguridad);
            window.location.reload();
        }
    })
    .catch(() => alert("Error al procesar el cierre masivo."));
}
</script>
@endsection
