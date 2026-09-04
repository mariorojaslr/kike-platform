@extends('layouts.app')

@section('title', 'Telemedicina & Videoconsulta Médica en Vivo - Mutual INTEGRA')

@section('content')
<div class="container-fluid py-4" style="background-color: #0f172a; min-height: 100vh; color: white;">
    <div class="container" style="max-width: 1000px;">
        
        <!-- Header Telemedicina -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary">
            <div>
                <span class="badge bg-danger px-3 py-1 rounded-pill fw-bold text-uppercase" style="letter-spacing: 1px;">
                    <i class="fas fa-video me-1"></i> Videoconsulta Médica en Vivo (WebRTC)
                </span>
                <h3 class="fw-bold mt-2 mb-0 text-white">
                    <i class="fas fa-user-md text-info me-2"></i> {{ $medico->nombre }}
                </h3>
                <small class="text-muted"><i class="fas fa-certificate text-warning me-1"></i> {{ $medico->especialidad }} | {{ $medico->matricula }}</small>
            </div>
            <div>
                <a href="{{ route('afiliado.credencial.demo') }}" class="btn btn-outline-light rounded-pill btn-sm fw-bold">
                    <i class="fas fa-arrow-left me-1"></i> Volver a Credencial
                </a>
            </div>
        </div>

        <div class="row g-4">
            
            <!-- Video Conexión WebRTC / Sala Virtual -->
            <div class="col-lg-7">
                <div class="card border-0 bg-dark shadow-lg rounded-4 overflow-hidden position-relative" style="border: 1px solid rgba(255,255,255,0.15) !important;">
                    
                    <!-- Pantalla de Video Médica Simulada -->
                    <div style="height: 380px; background: linear-gradient(135deg, #1e293b, #0f172a); display: flex; flex-direction: column; align-items: center; justify-content: center; position: relative;">
                        
                        <!-- Badge de Conexión En Vivo -->
                        <span class="badge bg-success position-absolute top-0 start-0 m-3 px-3 py-2 rounded-pill shadow">
                            <span class="spinner-grow spinner-grow-sm me-1"></span> EN VIVO &bull; HD WebRTC Encintado
                        </span>

                        <!-- Avatar / Stream del Médico -->
                        <div class="text-center" id="doctor-video-placeholder">
                            <div class="avatar bg-primary text-white rounded-circle mx-auto mb-3 shadow-lg d-flex align-items-center justify-content-center" style="width: 100px; height: 100px; font-size: 2.5rem; background: linear-gradient(135deg, #2563eb, #7c3aed) !important;">
                                <i class="fas fa-user-md"></i>
                            </div>
                            <h5 class="fw-bold text-white mb-1">{{ $medico->nombre }}</h5>
                            <span class="text-success small fw-bold"><i class="fas fa-microphone me-1"></i> Audio HD Conectado &bull; 0.4 ms</span>
                        </div>

                        <!-- Mini Video del Paciente (PIP) -->
                        <div class="position-absolute bottom-0 end-0 m-3 bg-secondary rounded-3 border border-light shadow overflow-hidden" style="width: 110px; height: 80px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-user text-white-50 fa-2x"></i>
                            <span class="position-absolute bottom-0 start-0 m-1 badge bg-dark text-white" style="font-size: 0.55rem;">Tú (Afiliado)</span>
                        </div>
                    </div>

                    <!-- Barra de Controles de la Llamada -->
                    <div class="card-footer bg-secondary bg-opacity-20 border-top border-secondary p-3 d-flex justify-content-center align-items-center gap-3">
                        <button type="button" class="btn btn-dark rounded-circle p-3 text-white shadow-sm" id="btn-mic" onclick="toggleMic()" title="Silenciar Micrófono">
                            <i class="fas fa-microphone" id="icon-mic"></i>
                        </button>
                        <button type="button" class="btn btn-dark rounded-circle p-3 text-white shadow-sm" id="btn-cam" onclick="toggleCam()" title="Apagar Cámara">
                            <i class="fas fa-video" id="icon-cam"></i>
                        </button>
                        <button type="button" class="btn btn-success rounded-pill px-4 py-2 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalRecetaDigital">
                            <i class="fas fa-file-prescription me-1"></i> Emitir Receta Digital
                        </button>
                        <button type="button" class="btn btn-danger rounded-circle p-3 text-white shadow-sm" onclick="finalizarLlamada()" title="Finalizar Consulta">
                            <i class="fas fa-phone-slash"></i>
                        </button>
                    </div>

                </div>
            </div>

            <!-- Panel de Ficha Clínica & Historial de Recetas Emitidas -->
            <div class="col-lg-5">
                
                <!-- Datos del Afiliado -->
                <div class="card border-0 bg-secondary bg-opacity-10 p-3 mb-3 rounded-4" style="border: 1px solid rgba(255,255,255,0.08);">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold text-white mb-0"><i class="fas fa-user-circle text-primary me-2"></i> Paciente en Consulta</h6>
                        <span class="badge bg-primary rounded-pill">{{ $afiliado->nro_afiliado }}</span>
                    </div>
                    <strong class="text-white d-block fs-5">{{ $afiliado->nombre }}</strong>
                    <div class="small text-muted mb-1">DNI: {{ $afiliado->dni }} | {{ $afiliado->edad }} Años</div>
                    <div class="p-2 bg-dark rounded-3 small text-warning border border-warning border-opacity-25 mt-2">
                        <i class="fas fa-notes-medical me-1"></i> <strong>Antecedentes:</strong> {{ $afiliado->antecedentes }}
                    </div>
                </div>

                <!-- Recetas Emitidas en esta Consulta -->
                <h6 class="fw-bold text-white mb-3"><i class="fas fa-prescription-bottle-alt text-success me-2"></i> Recetas Digitales de esta Sesión</h6>

                <div id="recetas-container">
                    @foreach($recetasEmitidas as $rec)
                        <div class="card border-0 bg-dark p-3 mb-2 rounded-3 border-start border-4 border-success shadow-sm">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <strong class="text-success small fw-bold"><i class="fas fa-check-circle me-1"></i> {{ $rec->id }}</strong>
                                <small class="text-muted" style="font-size: 0.65rem;">{{ $rec->fecha }}</small>
                            </div>
                            <div class="fw-bold text-white">{{ $rec->medicamento }}</div>
                            <small class="text-muted d-block">{{ $rec->posologia }}</small>
                            <small class="text-info d-block mt-1">Dx: {{ $rec->diagnostico }}</small>
                        </div>
                    @endforeach
                </div>

            </div>

        </div>

    </div>
</div>

<!-- Modal para Emitir Receta Digital durante la videollamada -->
<div class="modal fade" id="modalRecetaDigital" tabindex="-1" aria-hidden="true" style="color: #0f172a;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px;">
            <div class="modal-header bg-dark text-white border-0" style="border-radius: 18px 18px 0 0;">
                <h5 class="modal-title fw-bold"><i class="fas fa-file-prescription text-success me-2"></i> Emitir Receta Electrónica Firmada</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-receta-digital">
                @csrf
                <div class="modal-body p-4 text-dark">
                    <div class="alert alert-success p-3 small mb-3">
                        <i class="fas fa-shield-alt me-1"></i> La receta se firmará digitalmente con el certificado del <strong>{{ $medico->nombre }}</strong> ({{ $medico->matricula }}) y quedará disponible en el perfil del afiliado.
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Medicamento & Presentación *</label>
                        <input type="text" id="rec-medicamento" class="form-control" placeholder="Ej: Enalapril 10mg (Comprimidos x 30)" required value="Losartán 50mg (Comprimidos x 30)">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Posología & Indicaciones de Toma *</label>
                        <input type="text" id="rec-posologia" class="form-control" placeholder="Ej: 1 comprimido cada 12 hs" required value="1 comprimido cada 24 hs (por la mañana)">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Diagnóstico CIE-10 *</label>
                        <input type="text" id="rec-diagnostico" class="form-control" placeholder="Ej: I10 - Hipertensión Esencial" required value="I10 - Hipertensión Esencial (Primaria)">
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 pe-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 text-secondary fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" onclick="firmarYGuardarReceta()" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                        <i class="fas fa-signature me-1"></i> Firmar y Emitir Receta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let micActive = true;
let camActive = true;

function toggleMic() {
    micActive = !micActive;
    const icon = document.getElementById("icon-mic");
    const btn = document.getElementById("btn-mic");
    if (micActive) {
        icon.className = "fas fa-microphone";
        btn.classList.replace("btn-danger", "btn-dark");
    } else {
        icon.className = "fas fa-microphone-slash";
        btn.classList.replace("btn-dark", "btn-danger");
    }
}

function toggleCam() {
    camActive = !camActive;
    const icon = document.getElementById("icon-cam");
    const btn = document.getElementById("btn-cam");
    if (camActive) {
        icon.className = "fas fa-video";
        btn.classList.replace("btn-danger", "btn-dark");
    } else {
        icon.className = "fas fa-video-slash";
        btn.classList.replace("btn-dark", "btn-danger");
    }
}

function finalizarLlamada() {
    if (confirm("¿Deseas finalizar la consulta médica por video?")) {
        alert("📞 Videoconsulta finalizada. El informe de atención y las recetas digitales se han archivado en la historia clínica del afiliado.");
        window.location.href = "{{ route('afiliado.credencial.demo') }}";
    }
}

function firmarYGuardarReceta() {
    const med = document.getElementById("rec-medicamento").value;
    const pos = document.getElementById("rec-posologia").value;
    const diag = document.getElementById("rec-diagnostico").value;

    if (!med || !pos || !diag) {
        alert("Por favor completa todos los campos requeridos.");
        return;
    }

    fetch("{{ route('telemedicina.receta.emitir') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Accept": "application/json"
        },
        body: JSON.stringify({
            medicamento: med,
            posologia: pos,
            diagnostico: diag
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const html = `
                <div class="card border-0 bg-dark p-3 mb-2 rounded-3 border-start border-4 border-success shadow-sm">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <strong class="text-success small fw-bold"><i class="fas fa-check-circle me-1"></i> ${data.receta.nro_receta}</strong>
                        <small class="text-muted" style="font-size: 0.65rem;">${data.receta.fecha}</small>
                    </div>
                    <div class="fw-bold text-white">${data.receta.medicamento}</div>
                    <small class="text-muted d-block">${data.receta.posologia}</small>
                    <small class="text-info d-block mt-1">Dx: ${data.receta.diagnostico}</small>
                </div>
            `;
            document.getElementById("recetas-container").insertAdjacentHTML('afterbegin', html);
            
            // Cerrar Modal
            const modalEl = document.getElementById("modalRecetaDigital");
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();

            alert("✅ Receta Digital Firmada e Integrada al expediente con Hash MD5: " + data.receta.hash_md5);
        }
    })
    .catch(err => alert("Error al emitir la receta digital."));
}
</script>
@endsection
