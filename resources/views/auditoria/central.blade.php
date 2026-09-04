@extends('layouts.app')

@section('title', 'Hub de Auditoría Médica Central & Trazabilidad MD5 - Mutual INTEGRA')

@section('content')
<div class="container-fluid py-4" style="background-color: #0b1329; min-height: 100vh; color: white;">
    <div class="container">
        
        <!-- Header Auditoría Central -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary border-opacity-25">
            <div>
                <span class="badge bg-danger px-3 py-1 rounded-pill fw-bold text-uppercase" style="letter-spacing: 1px;">
                    <i class="fas fa-user-md me-1"></i> Auditoría Médica Central & Junta Evaluadora
                </span>
                <h2 class="fw-bold mt-2 mb-0 text-white">
                    <i class="fas fa-shield-alt text-warning me-2"></i> {{ $auditorActual->nombre }}
                </h2>
                <small class="text-muted"><i class="fas fa-certificate me-1 text-info"></i> {{ $auditorActual->cargo }} | {{ $auditorActual->matricula }}</small>
            </div>
            <div>
                <span class="badge bg-success bg-opacity-20 text-success border border-success px-3 py-2 rounded-pill">
                    <i class="fas fa-lock me-1"></i> Criptografía Hash MD5 Activa
                </span>
            </div>
        </div>

        <!-- Alertas de Auditoría Prioritaria -->
        <div class="card border-0 bg-dark shadow-sm mb-4" style="border-left: 4px solid #ef4444 !important; border-radius: 14px;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <i class="fas fa-stethoscope fa-2x text-danger"></i>
                    <div>
                        <h6 class="fw-bold mb-1 text-white">Módulo de Aprobación de Prácticas Quirúrgicas & Próthesis</h6>
                        <p class="small text-muted mb-0">
                            Todas las autorizaciones aprobadas generan automáticamente sello digital con <b>Hash MD5 inalterable</b> para rendición oficial ante el Ministerio de Salud y la Obra Social Provincia.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Solicitudes de Alta Complejidad -->
        <h5 class="fw-bold text-white mb-3"><i class="fas fa-notes-medical me-2 text-info"></i> Solicitudes Sanatoriales Pendientes de Dictamen</h5>

        <div class="card border-0 bg-secondary bg-opacity-10 shadow-sm" style="border-radius: 16px; border: 1px solid rgba(255,255,255,0.1);">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle mb-0" style="background: transparent;">
                        <thead>
                            <tr class="text-muted small text-uppercase">
                                <th class="ps-4">Código Solicitud & Fecha</th>
                                <th>Afiliado Titular / Beneficiario</th>
                                <th>Sanatorio Solicante</th>
                                <th>Práctica / Próthesis & CIE-10</th>
                                <th>Importe Estimado</th>
                                <th class="text-center">Estado Auditoría</th>
                                <th class="text-end pe-4">Dictamen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($solicitudesAuditoria as $sol)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-info font-monospace">{{ $sol->id }}</div>
                                        <small class="text-muted">{{ $sol->fecha_solicitud }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-white"><i class="fas fa-user me-1 text-primary"></i> {{ $sol->afiliado_nombre }}</div>
                                        <small class="text-muted">DNI: {{ $sol->afiliado_dni }} | {{ $sol->nro_afiliado }}</small>
                                    </td>
                                    <td>
                                        <div class="small fw-bold text-warning"><i class="fas fa-hospital me-1"></i> {{ $sol->prestador }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-white">{{ $sol->practica }}</div>
                                        <small class="text-info">CIE-10: {{ $sol->codigo_cie10 }}</small>
                                    </td>
                                    <td class="fw-bold text-success fs-6">${{ number_format($sol->monto_estimado, 2, ',', '.') }}</td>
                                    <td class="text-center">
                                        @if($sol->estado == 'aprobado')
                                            <span class="badge bg-success px-3 py-1.5 rounded-pill"><i class="fas fa-check-circle me-1"></i> APROBADO CON HASH MD5</span>
                                        @else
                                            <span class="badge bg-warning text-dark px-3 py-1.5 rounded-pill fw-bold"><i class="fas fa-hourglass-half me-1"></i> PENDIENTE REVISIÓN</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <button class="btn btn-sm btn-success rounded-pill px-3 fw-bold shadow-sm" onclick="abrirDictamen('{{ $sol->id }}', '{{ $sol->afiliado_nombre }}', '{{ $sol->practica }}')">
                                            <i class="fas fa-pen-nib me-1"></i> Dictaminar
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
function abrirDictamen(solicitudId, afiliado, practica) {
    const dictamen = prompt("Escribe el dictamen para " + afiliado + " (" + practica + "):\n\nEscribe 'APROBADO', 'RECHAZADO' u 'OBSERVADO'", "APROBADO");
    if (!dictamen) return;

    fetch("{{ route('auditoria.central.procesar') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Accept": "application/json"
        },
        body: JSON.stringify({
            solicitud_id: solicitudId,
            dictamen: dictamen.toLowerCase(),
            observaciones: "Dictamen emitido en junta de auditoría médica central."
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.reload();
        }
    })
    .catch(err => alert("Error al procesar dictamen."));
}
</script>
@endsection
