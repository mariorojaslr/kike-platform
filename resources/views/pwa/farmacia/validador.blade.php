<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INTEGRA | Validador Online de Farmacias Convenidas</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            min-height: 100vh;
        }

        .header-pharmacy {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: white;
            padding: 24px 0;
            margin-bottom: 24px;
            box-shadow: 0 4px 20px rgba(5, 150, 105, 0.2);
        }

        .card-custom {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            background: #ffffff;
            margin-bottom: 24px;
        }

        .badge-status-active {
            background-color: #dcfce7;
            color: #15803d;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 20px;
        }

        .vademecum-item {
            transition: all 0.2s ease;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px;
            margin-bottom: 12px;
        }

        .vademecum-item:hover {
            border-color: #10b981;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.1);
        }

        .btn-add-med {
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .ticket-box {
            background: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 16px;
            padding: 20px;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="header-pharmacy">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <div class="bg-white text-success rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; font-size: 1.5rem;">
                    <i class="fas fa-pills"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0">INTEGRA | Portal Farmacias Convenidas</h4>
                    <small class="opacity-75">Validador Online en Vivo de Vademécum Mutual (130.000 Abonados)</small>
                </div>
            </div>
            <div>
                <a href="{{ url('/demo') }}" class="btn btn-sm btn-light rounded-pill fw-bold px-3">
                    <i class="fas fa-arrow-left me-1"></i> Hub Demo
                </a>
            </div>
        </div>
    </header>

    <div class="container pb-5">
        <div class="row">
            
            <!-- Columna Izquierda: Afiliado & Vademécum -->
            <div class="col-lg-7">
                
                <!-- Búsqueda / Verificación Afiliado -->
                <div class="card card-custom p-4">
                    <h6 class="fw-bold text-muted mb-3 text-uppercase fs-7"><i class="fas fa-id-card text-success me-2"></i>1. Afiliado en Mostrador</h6>
                    <div class="input-group mb-3">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" class="form-field form-control border-start-0" id="search_dni" value="28.452.109" placeholder="Buscar por DNI o Escanear QR...">
                        <button class="btn btn-success fw-bold px-4" type="button"><i class="fas fa-check-circle me-1"></i> Validar</button>
                    </div>

                    <div class="p-3 bg-light rounded-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="avatar bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 44px; height: 44px; font-weight:700;">
                                AR
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0" id="afiliado_nombre">{{ $afiliadoDefault['nombre'] }}</h6>
                                <small class="text-muted d-block">DNI: {{ $afiliadoDefault['dni'] }} &bull; {{ $afiliadoDefault['nro_afiliado'] }}</small>
                                <small class="text-success fw-semibold"><i class="fas fa-shield-alt me-1"></i> {{ $afiliadoDefault['plan'] }}</small>
                            </div>
                        </div>
                        <span class="badge badge-status-active"><i class="fas fa-user-check me-1"></i> {{ $afiliadoDefault['estado'] }}</span>
                    </div>

                    <div class="mt-3">
                        <label class="form-label small fw-bold text-muted">Seleccionar Paciente / Integrante:</label>
                        <select class="form-select border-1" id="select_paciente" onchange="actualizarPaciente()">
                            @foreach($afiliadoDefault['grupo_familiar'] as $miembro)
                                <option value="{{ $miembro['nombre'] }}" data-relacion="{{ $miembro['relacion'] }}" data-dni="{{ $miembro['dni'] }}" {{ $miembro['relacion'] == 'HIJO (DISCAPACIDAD)' ? 'selected' : '' }}>
                                    {{ $miembro['nombre'] }} ({{ $miembro['relacion'] }}) - DNI {{ $miembro['dni'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Catálogo de Vademécum Mutual -->
                <div class="card card-custom p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-muted mb-0 text-uppercase fs-7"><i class="fas fa-prescription-bottle-alt text-success me-2"></i>2. Vademécum Mutual en Vivo</h6>
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3">Actualizado hoy</span>
                    </div>

                    <div class="mb-3">
                        <input type="text" class="form-control" id="filter_vademecum" placeholder="Filtrar por Droga o Laboratorio..." onkeyup="filtrarVademecum()">
                    </div>

                    <div id="vademecum_container">
                        @foreach($vademecum as $med)
                            <div class="vademecum-item vademecum-card d-flex align-items-center justify-content-between" data-search="{{ strtolower($med['droga'] . ' ' . $med['laboratorio'] . ' ' . $med['categoria']) }}">
                                <div>
                                    <div class="fw-bold text-dark mb-0">{{ $med['droga'] }}</div>
                                    <div class="small text-muted mb-1">Lab: {{ $med['laboratorio'] }} &bull; <span class="badge bg-light text-secondary border">{{ $med['categoria'] }}</span></div>
                                    <div class="small fw-bold text-success">
                                        PVP: ${{ number_format($med['pvp'], 2, ',', '.') }} 
                                        <span class="ms-2 text-primary">&bull; Cobertura Mutual: {{ $med['cobertura_pct'] }}%</span>
                                    </div>
                                </div>
                                <button class="btn btn-outline-success btn-add-med px-3" onclick="agregarMedicamento({{ json_encode($med) }})">
                                    <i class="fas fa-plus me-1"></i> Agregar
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            <!-- Columna Derecha: Resumen de Receta & Liquidación -->
            <div class="col-lg-5">
                
                <div class="card card-custom p-4">
                    <h6 class="fw-bold text-muted mb-3 text-uppercase fs-7"><i class="fas fa-file-invoice-dollar text-success me-2"></i>3. Resumen de Dispensa</h6>
                    
                    <div id="carrito_vacio" class="text-center py-4 text-muted">
                        <i class="fas fa-prescription-bottle text-black-50 mb-2" style="font-size: 2.5rem;"></i>
                        <p class="small mb-0">No se han agregado medicamentos a la receta.</p>
                    </div>

                    <div id="carrito_tabla_wrapper" style="display: none;">
                        <div class="table-responsive mb-3">
                            <table class="table table-sm text-center align-middle" style="font-size: 0.85rem;">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-start">Medicamento</th>
                                        <th>PVP</th>
                                        <th>Cob.</th>
                                        <th>Abonado</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="carrito_items">
                                </tbody>
                            </table>
                        </div>

                        <!-- Cobertura Especial por CUD -->
                        <div class="form-check form-switch mb-3 p-3 bg-light rounded-3">
                            <input class="form-check-input ms-0 me-2" type="checkbox" id="check_cud" checked onchange="recalcularCarrito()">
                            <label class="form-check-label fw-bold text-dark small" for="check_cud">
                                <i class="fas fa-certificate text-warning me-1"></i> Aplicar Cobertura 100% CUD Discapacidad
                            </label>
                            <small class="d-block text-muted" style="font-size: 0.75rem;">Aplica cobertura total del estado por Resolución de Apoyo Escolar</small>
                        </div>

                        <!-- Totales -->
                        <div class="ticket-box mb-3">
                            <div class="d-flex justify-content-between mb-1 text-muted small">
                                <span>Total PVP Medicamentos:</span>
                                <span class="fw-bold" id="total_pvp">$0,00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1 text-success small">
                                <span>Aporte Cobertura Mutual:</span>
                                <span class="fw-bold" id="total_mutual">-$0,00</span>
                            </div>
                            <hr class="my-2 border-secondary opacity-25">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-dark">Copago a Cobrar al Afiliado:</span>
                                <span class="fw-bold text-primary fs-5" id="total_afiliado">$0,00</span>
                            </div>
                        </div>

                        <button class="btn btn-success btn-lg w-100 fw-bold py-3 shadow-sm rounded-3" onclick="validarRecetaOnline()">
                            <i class="fas fa-bolt me-2"></i> Validar Receta y Emitir Bono Digital
                        </button>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- Modal Resultado de Validacion -->
    <div class="modal fade" id="modalResultado" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg">
                <div class="modal-header bg-success text-white border-0 rounded-top-4 py-3">
                    <h5 class="modal-title fw-bold"><i class="fas fa-check-circle me-2"></i>Receta Validada Exitosamente</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center bg-white">
                    
                    <!-- Selector de Formato Imprimible -->
                    <div class="d-flex flex-wrap justify-content-center align-items-center gap-2 mb-4 p-2 bg-light rounded-4 border no-print print-options-bar">
                        <span class="small fw-bold text-muted me-2"><i class="fas fa-print me-1"></i> Formato de Salida:</span>
                        <button type="button" class="btn btn-success rounded-pill px-3 py-1 btn-sm fw-bold shadow-sm" id="btn_far_fmt_a4" onclick="cambiarFormatoFarmacia('a4')">
                            <i class="fas fa-file-invoice me-1"></i> Medio A4 / Troquelado (Original y Copia)
                        </button>
                        <button type="button" class="btn btn-outline-success rounded-pill px-3 py-1 btn-sm fw-bold shadow-sm" id="btn_far_fmt_80mm" onclick="cambiarFormatoFarmacia('80mm')">
                            <i class="fas fa-receipt me-1"></i> Ticket Térmico (80mm POS)
                        </button>
                    </div>

                    <div id="bonoPrintArea">
                        
                        <!-- VISTA A4 / TROQUELADO -->
                        <div id="far_vista_a4">
                            <!-- ORIGINAL FARMACIA -->
                            <div class="border rounded-4 p-4 position-relative mb-3 text-start bg-white" style="border: 2px dashed #cbd5e1 !important;">
                                <span class="position-absolute top-0 end-0 bg-success text-white font-monospace px-3 py-1 rounded-bottom-start small fw-bold">ORIGINAL FARMACIA</span>
                                
                                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                    <div>
                                        <span class="badge bg-success px-3 py-1 rounded-pill mb-1 fw-bold">MUTUAL INTEGRA | FARMACIA CONVENIDA</span>
                                        <h5 class="fw-bold text-dark mb-0">COMPROBANTE DE DISPENSA FARMACÉUTICA</h5>
                                    </div>
                                    <div class="text-end me-4">
                                        <h6 class="fw-bold text-success mb-0" id="far_val_nro_aut">AUT-FAR-000000</h6>
                                        <small class="text-muted d-block" id="far_val_fecha">Fecha: --/--/---- --:--</small>
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded-3 border">
                                            <small class="text-muted text-uppercase fw-bold d-block mb-1" style="font-size: 0.75rem;">Paciente / Afiliado:</small>
                                            <h6 class="fw-bold text-dark mb-0" id="far_val_paciente">--</h6>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded-3 border">
                                            <small class="text-muted text-uppercase fw-bold d-block mb-1" style="font-size: 0.75rem;">Resumen de Cobertura:</small>
                                            <div class="d-flex justify-content-between small"><span class="text-muted">Abonado por Mutual:</span> <strong class="text-success" id="far_val_mutual">$0,00</strong></div>
                                            <div class="d-flex justify-content-between small"><span class="text-muted">Copago Afiliado:</span> <strong class="text-primary" id="far_val_afiliado">$0,00</strong></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row align-items-center">
                                    <div class="col-md-3 text-center mb-2 mb-md-0">
                                        <img class="img-fluid border rounded-3 p-2 bg-white shadow-sm far_val_qr" src="" alt="QR" style="max-width: 110px;">
                                        <small class="d-block text-muted mt-1" style="font-size: 0.65rem;">Validado en mostrador</small>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="p-3 bg-light rounded-3 border">
                                            <small class="fw-bold text-muted text-uppercase d-block mb-1" style="font-size: 0.7rem;"><i class="fas fa-shield-check text-success me-1"></i> Trazabilidad Vademécum Mutual:</small>
                                            <p class="small text-dark mb-1" style="font-size: 0.8rem;">Descuento aplicado según convenio oficial de medicamentos. Facturación acreditada a la farmacia.</p>
                                            <small class="text-muted d-block font-monospace" style="font-size: 0.65rem;">HASH VALIDACIÓN MD5: f89a31c498cb38d5f260853678922e09</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- LÍNEA DE TROQUEL -->
                            <div class="my-3 text-center text-muted small position-relative" style="border-bottom: 2px dashed #94a3b8;">
                                <span style="position: absolute; top: -11px; left: 50%; transform: translateX(-50%); background: #ffffff; padding: 0 15px; font-weight: 600; color: #64748b; font-size: 0.75rem;">
                                    <i class="fas fa-scissors me-1"></i> TROQUEL / CORTAR AQUÍ (ORIGINAL FARMACIA / COPIA AFILIADO) <i class="fas fa-scissors ms-1"></i>
                                </span>
                            </div>

                            <!-- COPIA AFILIADO -->
                            <div class="border rounded-4 p-4 position-relative text-start bg-white" style="border: 2px dashed #cbd5e1 !important;">
                                <span class="position-absolute top-0 end-0 bg-secondary text-white font-monospace px-3 py-1 rounded-bottom-start small fw-bold">COPIA AFILIADO</span>
                                
                                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                    <div>
                                        <span class="badge bg-secondary px-3 py-1 rounded-pill mb-1 fw-bold">MUTUAL INTEGRA | COMPROBANTE PACIENTE</span>
                                        <h5 class="fw-bold text-dark mb-0">COMPROBANTE DE DISPENSA FARMACÉUTICA</h5>
                                    </div>
                                    <div class="text-end me-4">
                                        <h6 class="fw-bold text-dark mb-0" id="far_val_nro_aut_copia">AUT-FAR-000000</h6>
                                        <small class="text-muted d-block" id="far_val_fecha_copia">Fecha: --/--/---- --:--</small>
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded-3 border">
                                            <small class="text-muted text-uppercase fw-bold d-block mb-1" style="font-size: 0.75rem;">Paciente / Afiliado:</small>
                                            <h6 class="fw-bold text-dark mb-0" id="far_val_paciente_copia">--</h6>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded-3 border">
                                            <small class="text-muted text-uppercase fw-bold d-block mb-1" style="font-size: 0.75rem;">Resumen de Cobertura:</small>
                                            <div class="d-flex justify-content-between small"><span class="text-muted">Abonado por Mutual:</span> <strong class="text-success" id="far_val_mutual_copia">$0,00</strong></div>
                                            <div class="d-flex justify-content-between small"><span class="text-muted">Copago Afiliado:</span> <strong class="text-primary" id="far_val_afiliado_copia">$0,00</strong></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row align-items-center">
                                    <div class="col-md-3 text-center mb-2 mb-md-0">
                                        <img class="img-fluid border rounded-3 p-2 bg-white shadow-sm far_val_qr" src="" alt="QR" style="max-width: 110px;">
                                        <small class="d-block text-muted mt-1" style="font-size: 0.65rem;">Validado en mostrador</small>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="p-3 bg-light rounded-3 border">
                                            <small class="fw-bold text-muted text-uppercase d-block mb-1" style="font-size: 0.7rem;"><i class="fas fa-shield-check text-success me-1"></i> Trazabilidad Vademécum Mutual:</small>
                                            <p class="small text-dark mb-1" style="font-size: 0.8rem;">Conserve esta copia como comprobante oficial de retiro de medicamentos en farmacia.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- VISTA 80mm TICKET POS -->
                        <div id="far_vista_80mm" style="display: none; max-width: 320px; margin: 0 auto; background: #ffffff; padding: 15px; border: 1px solid #e2e8f0; font-family: 'Courier New', Courier, monospace; color: #000000; text-align: left;">
                            <div class="text-center mb-2">
                                <h6 class="fw-bold mb-0 text-uppercase" style="font-size: 14px;">FARMACIA CONVENIDA</h6>
                                <p class="mb-0 small fw-bold">MUTUAL INTEGRA - RECETA DISPENSADA</p>
                                <div class="my-1">================================</div>
                            </div>

                            <div style="font-size: 11px; line-height: 1.4;">
                                <div class="d-flex justify-content-between">
                                    <span>N° AUT:</span>
                                    <strong id="far_tk_aut">AUT-FAR-000000</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>FECHA:</span>
                                    <span id="far_tk_fecha">--/--/---- --:--</span>
                                </div>
                                <div class="my-1">--------------------------------</div>
                                <div class="mb-1">
                                    <div>PACIENTE:</div>
                                    <strong id="far_tk_paciente" class="text-uppercase">--</strong>
                                </div>
                                <div class="my-1">--------------------------------</div>
                                <div class="d-flex justify-content-between">
                                    <span>LIQUIDADO MUTUAL:</span>
                                    <strong class="text-success" id="far_tk_mutual">$0,00</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>COPAGO AFILIADO:</span>
                                    <strong id="far_tk_afiliado">$0,00</strong>
                                </div>
                                <div class="my-1">--------------------------------</div>
                                <div class="text-center my-2">
                                    <img class="img-fluid p-1 bg-white border far_val_qr" src="" alt="QR" style="max-width: 120px;">
                                    <small class="d-block mt-1 font-monospace" style="font-size: 8px;">MD5: f89a31c498cb38d5f260853678922e09</small>
                                </div>
                                <div class="my-1 text-center">================================</div>
                                <div class="text-center small">* COMPROBANTE OFICIAL DE DISPENSA *</div>
                            </div>
                        </div>

                    </div>

                </div>
                <div class="modal-footer border-0 pb-4 px-4 no-print">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm" onclick="window.print()">
                        <i class="fas fa-print me-1"></i> Imprimir Comprobante
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let carrito = [];

        function agregarMedicamento(med) {
            let existe = carrito.find(item => item.id === med.id);
            if (existe) {
                existe.cantidad++;
            } else {
                carrito.push({
                    id: med.id,
                    droga: med.droga,
                    pvp: parseFloat(med.pvp),
                    cobertura_pct: parseFloat(med.cobertura_pct),
                    cantidad: 1
                });
            }
            renderizarCarrito();
        }

        function removerItem(index) {
            carrito.splice(index, 1);
            renderizarCarrito();
        }

        function renderizarCarrito() {
            const wrapper = document.getElementById('carrito_tabla_wrapper');
            const vacio = document.getElementById('carrito_vacio');
            const tbody = document.getElementById('carrito_items');

            if (carrito.length === 0) {
                wrapper.style.display = 'none';
                vacio.style.display = 'block';
                return;
            }

            wrapper.style.display = 'block';
            vacio.style.display = 'none';

            let html = '';
            let esCud = document.getElementById('check_cud').checked;

            let totalPvp = 0;
            let totalMutual = 0;
            let totalAfiliado = 0;

            carrito.forEach((item, index) => {
                let pct = esCud ? 100 : item.cobertura_pct;
                let montoMutual = (item.pvp * (pct / 100)) * item.cantidad;
                let montoAfiliado = (item.pvp * item.cantidad) - montoMutual;

                totalPvp += item.pvp * item.cantidad;
                totalMutual += montoMutual;
                totalAfiliado += montoAfiliado;

                html += `
                    <tr>
                        <td class="text-start fw-semibold">${item.droga}</td>
                        <td>$${(item.pvp).toLocaleString('es-AR')}</td>
                        <td><span class="badge bg-primary-subtle text-primary">${pct}%</span></td>
                        <td class="fw-bold text-dark">$${montoAfiliado.toLocaleString('es-AR')}</td>
                        <td>
                            <button class="btn btn-sm btn-link text-danger p-0" onclick="removerItem(${index})">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });

            tbody.innerHTML = html;
            document.getElementById('total_pvp').innerText = '$' + totalPvp.toLocaleString('es-AR', {minimumFractionDigits: 2});
            document.getElementById('total_mutual').innerText = '-$' + totalMutual.toLocaleString('es-AR', {minimumFractionDigits: 2});
            document.getElementById('total_afiliado').innerText = '$' + totalAfiliado.toLocaleString('es-AR', {minimumFractionDigits: 2});
        }

        function recalcularCarrito() {
            renderizarCarrito();
        }

        function filtrarVademecum() {
            let filter = document.getElementById('filter_vademecum').value.toLowerCase();
            let items = document.querySelectorAll('.vademecum-card');

            items.forEach(item => {
                let searchData = item.getAttribute('data-search');
                if (searchData.includes(filter)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        function actualizarPaciente() {
            // Callback al cambiar de integrante
        }

        function cambiarFormatoFarmacia(formato) {
            const vistaA4 = document.getElementById('far_vista_a4');
            const vista80mm = document.getElementById('far_vista_80mm');
            const btnA4 = document.getElementById('btn_far_fmt_a4');
            const btn80mm = document.getElementById('btn_far_fmt_80mm');

            if (formato === '80mm') {
                vistaA4.style.display = 'none';
                vista80mm.style.display = 'block';
                btnA4.classList.remove('btn-success');
                btnA4.classList.add('btn-outline-success');
                btn80mm.classList.remove('btn-outline-success');
                btn80mm.classList.add('btn-success');
                document.body.classList.add('print-mode-80mm');
                document.body.classList.remove('print-mode-a4');
            } else {
                vistaA4.style.display = 'block';
                vista80mm.style.display = 'none';
                btnA4.classList.remove('btn-outline-success');
                btnA4.classList.add('btn-success');
                btn80mm.classList.remove('btn-success');
                btn80mm.classList.add('btn-outline-success');
                document.body.classList.add('print-mode-a4');
                document.body.classList.remove('print-mode-80mm');
            }
        }

        function validarRecetaOnline() {
            let select = document.getElementById('select_paciente');
            let pacienteNombre = select.options[select.selectedIndex].value;
            let esCud = document.getElementById('check_cud').checked ? 1 : 0;

            fetch("{{ route('farmacia.validar') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    afiliado_dni: "28.452.109",
                    paciente_nombre: pacienteNombre,
                    es_discapacidad: esCud,
                    items: carrito
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const aut = data.nro_autorizacion;
                    const fecha = data.fecha_hora;
                    const pac = data.paciente_nombre;
                    const mutStr = "$" + data.total_mutual.toLocaleString('es-AR', {minimumFractionDigits: 2});
                    const afilStr = "$" + data.total_afiliado.toLocaleString('es-AR', {minimumFractionDigits: 2});
                    
                    // A4 Original & Copia
                    if (document.getElementById('far_val_nro_aut')) document.getElementById('far_val_nro_aut').innerText = aut;
                    if (document.getElementById('far_val_nro_aut_copia')) document.getElementById('far_val_nro_aut_copia').innerText = aut;
                    if (document.getElementById('far_val_fecha')) document.getElementById('far_val_fecha').innerText = "Fecha: " + fecha;
                    if (document.getElementById('far_val_fecha_copia')) document.getElementById('far_val_fecha_copia').innerText = "Fecha: " + fecha;
                    if (document.getElementById('far_val_paciente')) document.getElementById('far_val_paciente').innerText = pac;
                    if (document.getElementById('far_val_paciente_copia')) document.getElementById('far_val_paciente_copia').innerText = pac;
                    if (document.getElementById('far_val_mutual')) document.getElementById('far_val_mutual').innerText = mutStr;
                    if (document.getElementById('far_val_mutual_copia')) document.getElementById('far_val_mutual_copia').innerText = mutStr;
                    if (document.getElementById('far_val_afiliado')) document.getElementById('far_val_afiliado').innerText = afilStr;
                    if (document.getElementById('far_val_afiliado_copia')) document.getElementById('far_val_afiliado_copia').innerText = afilStr;

                    // Ticket 80mm
                    if (document.getElementById('far_tk_aut')) document.getElementById('far_tk_aut').innerText = aut;
                    if (document.getElementById('far_tk_fecha')) document.getElementById('far_tk_fecha').innerText = fecha;
                    if (document.getElementById('far_tk_paciente')) document.getElementById('far_tk_paciente').innerText = pac;
                    if (document.getElementById('far_tk_mutual')) document.getElementById('far_tk_mutual').innerText = mutStr;
                    if (document.getElementById('far_tk_afiliado')) document.getElementById('far_tk_afiliado').innerText = afilStr;
                    
                    let qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" + encodeURIComponent("INTEGRA-MUTUAL-RECETA|" + aut + "|" + pac);
                    document.querySelectorAll('.far_val_qr').forEach(img => img.src = qrUrl);

                    cambiarFormatoFarmacia('a4');

                    let modal = new bootstrap.Modal(document.getElementById('modalResultado'));
                    modal.show();

                    carrito = [];
                    renderizarCarrito();
                }
            })
            .catch(err => {
                alert("Error al conectar con la central de validación.");
            });
        }
    </script>
    @include('partials.ai_assistant_widget')
</body>
</html>
