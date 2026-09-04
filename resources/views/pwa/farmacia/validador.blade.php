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
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg">
                <div class="modal-header bg-success text-white border-0 rounded-top-4 py-3">
                    <h5 class="modal-title fw-bold"><i class="fas fa-check-circle me-2"></i>Receta Validada Exitosamente</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div class="mb-3">
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-bold">
                            AUTORIZACIÓN MUTUAL APROBADA
                        </span>
                    </div>

                    <h4 class="fw-bold text-dark mb-1" id="modal_nro_aut">AUT-FAR-000000</h4>
                    <p class="text-muted small mb-3" id="modal_fecha_hora">Fecha: --/--/---- --:--</p>

                    <div class="mb-3">
                        <img id="modal_qr" src="" alt="Código QR de Autorización" class="img-fluid border rounded-3 p-2 bg-white shadow-sm" style="max-width: 150px;">
                    </div>

                    <div class="bg-light p-3 rounded-3 text-start mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Paciente:</span>
                            <span class="fw-bold" id="modal_paciente">--</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Liquidado a Mutual:</span>
                            <span class="fw-bold text-success" id="modal_mutual_total">$0,00</span>
                        </div>
                        <div class="d-flex justify-content-between small">
                            <span class="text-muted">Copago Cobrado al Afiliado:</span>
                            <span class="fw-bold text-primary" id="modal_afiliado_total">$0,00</span>
                        </div>
                    </div>

                    <div class="alert alert-info py-2 small mb-0 text-start">
                        <i class="fas fa-info-circle me-1"></i> El importe de cobertura de la mutual se ha acreditado a la cuenta de la farmacia para la liquidación quincenal.
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-success rounded-pill px-4 fw-bold" onclick="window.print()">
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
                    document.getElementById('modal_nro_aut').innerText = data.nro_autorizacion;
                    document.getElementById('modal_fecha_hora').innerText = "Fecha: " + data.fecha_hora;
                    document.getElementById('modal_paciente').innerText = data.paciente_nombre;
                    document.getElementById('modal_mutual_total').innerText = "$" + data.total_mutual.toLocaleString('es-AR', {minimumFractionDigits: 2});
                    document.getElementById('modal_afiliado_total').innerText = "$" + data.total_afiliado.toLocaleString('es-AR', {minimumFractionDigits: 2});
                    
                    let qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" + encodeURIComponent("INTEGRA-MUTUAL-RECETA|" + data.nro_autorizacion + "|" + data.paciente_nombre);
                    document.getElementById('modal_qr').src = qrUrl;

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
</body>
</html>
