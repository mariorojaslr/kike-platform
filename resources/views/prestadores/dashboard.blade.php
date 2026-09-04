@extends('layouts.app')

@section('title', 'Portal de Prestadores Médicos y Clínicas - Mutual INTEGRA')

@section('content')
<div class="container-fluid py-4" style="background-color: #0f172a; min-height: 100vh; color: white;">
    <div class="container">
        
        <!-- Header del Portal de Prestadores Médicos -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary">
            <div>
                <span class="badge bg-primary px-3 py-1 rounded-pill fw-bold text-uppercase" style="letter-spacing: 1px;">
                    <i class="fas fa-hospital me-1"></i> Red de Prestadores Médicos & Sanatorios
                </span>
                <h2 class="fw-bold mt-2 mb-0 text-white">
                    <i class="fas fa-stethoscope text-info me-2"></i> {{ $prestadorNombre }}
                </h2>
                <small class="text-muted"><i class="fas fa-users me-1 text-success"></i> Cobertura Activa: <strong>{{ $cápitasActivas }}</strong></small>
            </div>
            <button type="button" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNuevaAutorizacion">
                <i class="fas fa-plus-circle me-1"></i> Solicitar Autorización Médica
            </button>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-lg rounded-3 mb-4 p-3 d-flex align-items-center gap-3">
                <i class="fas fa-check-circle fa-2x text-success"></i>
                <div>
                    <h6 class="mb-0 fw-bold">{{ session('success') }}</h6>
                    <small>Se emitió el bono digital de autorización con sello de auditoría médica.</small>
                </div>
            </div>
        @endif

        <!-- Tarjetas KPI del Prestador -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 bg-secondary bg-opacity-10 p-3" style="border-radius: 14px; border-left: 4px solid #3b82f6 !important;">
                    <span class="text-muted small fw-bold">PRÁCTICAS Y CONSULTAS MES</span>
                    <h3 class="fw-bold text-primary mb-0 mt-1">142 Prácticas</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 bg-secondary bg-opacity-10 p-3" style="border-radius: 14px; border-left: 4px solid #10b981 !important;">
                    <span class="text-muted small fw-bold">LIQUIDADO AUTORIZADO</span>
                    <h3 class="fw-bold text-success mb-0 mt-1">$1.845.000</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 bg-secondary bg-opacity-10 p-3" style="border-radius: 14px; border-left: 4px solid #f59e0b !important;">
                    <span class="text-muted small fw-bold">EN AUDITORÍA MÉDICA</span>
                    <h3 class="fw-bold text-warning mb-0 mt-1">2 Solicitudes</h3>
                </div>
            </div>
        </div>

        <!-- Solicitudes de Autorizaciones de Prácticas e Internaciones -->
        <h5 class="fw-bold text-white mb-3"><i class="fas fa-file-medical me-2 text-info"></i> Órdenes Médicas e Internaciones Solicitadas</h5>

        <div class="card border-0 bg-secondary bg-opacity-10 shadow-sm" style="border-radius: 16px; border: 1px solid rgba(255,255,255,0.1);">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle mb-0" style="background: transparent;">
                        <thead>
                            <tr class="text-muted small text-uppercase">
                                <th class="ps-4">Afiliado Mutual</th>
                                <th>Tipo de Prestación</th>
                                <th>Código Nomenclador / Práctica</th>
                                <th>Importe</th>
                                <th class="text-center">Estado Auditoría</th>
                                <th class="text-end pe-4">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ordenesSolicitadas as $ord)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-white"><i class="fas fa-user me-1 text-primary"></i> {{ $ord->afiliado_nombre }}</div>
                                        <small class="font-monospace" style="color: #94a3b8;">{{ $ord->afiliado_nro }}</small>
                                    </td>
                                    <td>
                                        <span class="badge px-3 py-1.5 rounded-pill fw-bold" style="background: rgba(14, 165, 233, 0.2); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.4); font-size: 0.8rem;">
                                            <i class="fas fa-notes-medical me-1"></i> {{ $ord->tipo }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="small fw-bold text-white">{{ $ord->practica_codigo }}</div>
                                        <small style="color: #94a3b8;">Solicitado: {{ $ord->fecha_solicitud }}</small>
                                    </td>
                                    <td class="fw-bold text-success">${{ number_format($ord->monto, 2, ',', '.') }}</td>
                                    <td class="text-center">
                                        @if($ord->estado == 'aprobado')
                                            <span class="badge bg-success px-3 py-1.5 rounded-pill"><i class="fas fa-check-circle me-1"></i> AUTORIZADO</span>
                                        @else
                                            <span class="badge bg-warning text-dark px-3 py-1.5 rounded-pill fw-bold"><i class="fas fa-hourglass-half me-1"></i> EN AUDITORÍA MÉDICA</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <button class="btn btn-sm btn-outline-light rounded-pill px-3 fw-semibold" onclick="verBonoDigital({{ json_encode($ord) }})">
                                            <i class="fas fa-print me-1 text-info"></i> Imprimir Bono
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

<!-- Modal Visualizador / Impresor del Bono Digital Oficial -->
<div class="modal fade" id="modalBonoDigital" tabindex="-1" aria-hidden="true" style="color: #0f172a;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header bg-primary text-white border-0 py-3" style="border-radius: 20px 20px 0 0;">
                <div class="d-flex align-items-center">
                    <i class="fas fa-file-medical-alt fa-2x me-3"></i>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">MUTUAL INTEGRA | Bono Digital de Autorización</h5>
                        <small class="opacity-75">Documento Oficial de Cobertura Sanatorial & Auditoría Médica</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body p-4 bg-white" id="bonoPrintArea">
                <div class="border rounded-4 p-4 position-relative" style="background: #fafafa; border: 2px dashed #cbd5e1 !important;">
                    
                    <!-- Encabezado de Impresión -->
                    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                        <div>
                            <span class="badge bg-primary px-3 py-1 rounded-pill mb-2 fw-bold">OBRA SOCIAL & MUTUAL INTEGRA</span>
                            <h4 class="fw-bold text-dark mb-0">BONO DE PRESTACIÓN MÉDICA</h4>
                            <small class="text-muted">Sistema Centralizado de Auditoría en Tiempo Real</small>
                        </div>
                        <div class="text-end">
                            <h6 class="fw-bold text-primary mb-1" id="bono_nro_aut">AUT-2026-000000</h6>
                            <small class="text-muted d-block" id="bono_fecha">Fecha: --/--/----</small>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill fw-bold mt-1" id="bono_estado_badge">
                                AUTORIZADO 100%
                            </span>
                        </div>
                    </div>

                    <!-- Datos del Paciente / Afiliado -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-white rounded-3 border">
                                <small class="text-muted text-uppercase fw-bold d-block mb-1" style="font-size: 0.75rem;">Afiliado / Paciente:</small>
                                <h6 class="fw-bold text-dark mb-1" id="bono_paciente">--</h6>
                                <small class="text-primary fw-semibold" id="bono_nro_afiliado">MUT-0000000/00</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-white rounded-3 border">
                                <small class="text-muted text-uppercase fw-bold d-block mb-1" style="font-size: 0.75rem;">Prestador Efector / Sanatorio:</small>
                                <h6 class="fw-bold text-dark mb-1">{{ $prestadorNombre }}</h6>
                                <small class="text-muted"><i class="fas fa-hospital me-1"></i> Cobertura en Convenio (130.000 Abonados)</small>
                            </div>
                        </div>
                    </div>

                    <!-- Detalle de la Práctica Autorizada -->
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered align-middle text-center mb-0" style="font-size: 0.9rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-start">Código Nomenclador / Práctica Médica</th>
                                    <th>Tipo de Prestación</th>
                                    <th>Cobertura Mutual</th>
                                    <th>Importe Liquidado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-start fw-bold text-dark" id="bono_practica">--</td>
                                    <td><span class="badge bg-info-subtle text-info border px-2 py-1" id="bono_tipo">Ambulatoria</span></td>
                                    <td><span class="badge bg-success text-white px-2 py-1">100% Sin Copago</span></td>
                                    <td class="fw-bold text-success fs-6" id="bono_monto">$0,00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Trazabilidad Digital, Firma de Auditoría & QR -->
                    <div class="row align-items-center pt-2">
                        <div class="col-md-3 text-center mb-3 mb-md-0">
                            <img id="bono_qr" src="" alt="Código QR de Validación" class="img-fluid border rounded-3 p-2 bg-white shadow-sm" style="max-width: 130px;">
                            <small class="d-block text-muted mt-1" style="font-size: 0.7rem;">Escaneable en mostrador</small>
                        </div>
                        <div class="col-md-9">
                            <div class="p-3 bg-white rounded-3 border">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="fw-bold text-muted text-uppercase" style="font-size: 0.75rem;"><i class="fas fa-signature text-success me-1"></i> Sello y Firma Auditoría Médica Central:</small>
                                    <small class="text-success fw-bold"><i class="fas fa-shield-alt me-1"></i> Firma Digital Verificada</small>
                                </div>
                                <p class="small text-dark mb-1"><strong>Dr. Roberto E. Ferrero</strong> &bull; M.P. 48920 &bull; Jefe de Auditoría Médica Mutual INTEGRA</p>
                                <small class="text-muted d-block font-monospace" style="font-size: 0.7rem;" id="bono_hash">HASH HASH MD5: e99a18c428cb38d5f260853678922e03</small>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="modal-footer border-0 pb-4 pe-4">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" onclick="imprimirBonoContenido()">
                    <i class="fas fa-print me-1"></i> Imprimir / Guardar en PDF
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nueva Autorización Médica / Internación -->
<div class="modal fade" id="modalNuevaAutorizacion" tabindex="-1" aria-hidden="true" style="color: #0f172a;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header bg-dark text-white border-0" style="border-radius: 16px 16px 0 0;">
                <h5 class="modal-title fw-bold"><i class="fas fa-notes-medical text-success me-2"></i> Nueva Solicitud de Autorización Médica / Internación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('prestadores.demo') }}" method="GET">
                <div class="modal-body p-4 text-dark">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Número de N° Afiliado / DNI *</label>
                            <input type="text" class="form-control" placeholder="Ej: MUT-32456789/00 o DNI 32456789" value="MUT-32456789/00 - Abayay Ramón Martín" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tipo de Solicitud *</label>
                            <select class="form-select" required>
                                <option value="ambulatoria">Consulta / Práctica Ambulatoria</option>
                                <option value="internacion">Internación Sanatorial / Días de Cama</option>
                                <option value="cirugia">Cirugía Programada / Quirófano</option>
                                <option value="estudio">Resonancia / Tomografía / Alta Calidad</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Código Nomenclador / Práctica *</label>
                            <input type="text" class="form-control" placeholder="Ej: 42.01.01 Resonancia Magnética" value="42.01.01 - Resonancia Magnética Nuclear" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Diagnóstico Presuntivo (CIE-10)</label>
                            <input type="text" class="form-control" placeholder="Ej: G44.2 Cefalea tensional" value="G44.2 - Cefalea tensional crónica">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Adjuntar Orden Médica Escaneada (Opcional)</label>
                            <input type="file" class="form-control" accept="image/*,.pdf">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 pe-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 text-secondary fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm" onclick="alert('✅ Solicitud enviada a Auditoría Médica de la Mutual en tiempo real.');">
                        <i class="fas fa-paper-plane me-1"></i> Enviar a Auditoría Médica
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function verBonoDigital(ord) {
        document.getElementById('bono_nro_aut').innerText = "AUT-2026-" + ord.id;
        document.getElementById('bono_fecha').innerText = "Fecha: " + ord.fecha_solicitud;
        document.getElementById('bono_paciente').innerText = ord.afiliado_nombre;
        document.getElementById('bono_nro_afiliado').innerText = ord.afiliado_nro;
        document.getElementById('bono_practica').innerText = ord.practica_codigo;
        document.getElementById('bono_tipo').innerText = ord.tipo;
        document.getElementById('bono_monto').innerText = "$" + ord.monto.toLocaleString('es-AR', {minimumFractionDigits: 2});
        
        let badgeEstado = document.getElementById('bono_estado_badge');
        if (ord.estado === 'aprobado') {
            badgeEstado.className = "badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill fw-bold mt-1";
            badgeEstado.innerText = "AUTORIZADO Y LIQUIDADO";
        } else {
            badgeEstado.className = "badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-1 rounded-pill fw-bold mt-1";
            badgeEstado.innerText = "EN AUDITORÍA MÉDICA";
        }

        let qrData = "MUTUAL-INTEGRA-BONO|AUT-2026-" + ord.id + "|" + ord.afiliado_nombre + "|" + ord.monto;
        document.getElementById('bono_qr').src = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" + encodeURIComponent(qrData);

        let modal = new bootstrap.Modal(document.getElementById('modalBonoDigital'));
        modal.show();
    }

    function imprimirBonoContenido() {
        window.print();
    }
</script>
@endsection
