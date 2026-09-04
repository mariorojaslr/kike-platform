<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Expediente Oficial de Rendición - INTEGRA Mutual</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; color: #1e293b; background-color: #f8fafc; }
        .cert-container { background: #ffffff; padding: 40px; border-radius: 12px; border: 2px solid #cbd5e1; max-width: 900px; margin: 20px auto; box-shadow: 0 15px 35px rgba(0,0,0,0.1); }
        .stamp-box { border: 2px dashed #0284c7; background: #f0f9ff; padding: 15px; border-radius: 10px; margin-bottom: 20px; }
        .trazabilidad-table th { background: #0f172a; color: white; font-size: 0.8rem; text-transform: uppercase; }
        .qr-stamp { border: 2px solid #1e293b; padding: 10px; border-radius: 10px; text-align: center; }
        @media print {
            .no-print { display: none !important; }
            body { background: white; }
            .cert-container { border: none; box-shadow: none; padding: 0; }
        }
    </style>
</head>
<body>

<div class="container py-3 no-print text-end" style="max-width: 900px;">
    <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
        <i class="fas fa-print me-2"></i> Imprimir / Guardar en PDF
    </button>
</div>

<div class="cert-container">
    
    <!-- Encabezado Institucional -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-2 border-dark">
        <div>
            <span class="badge bg-dark px-3 py-1 mb-2 text-uppercase" style="letter-spacing: 1px;">Mutual OSP &bull; Sistema INTEGRA</span>
            <h3 class="fw-bold mb-0 text-primary">CERTIFICADO OFICIAL DE RENDICIÓN DE EXPEDIENTE</h3>
            <small class="text-muted">Servicios de Apoyo a la Integración Escolar y Discapacidad</small>
        </div>
        <div class="text-end">
            <span class="badge bg-success fs-6 px-3 py-2"><i class="fas fa-check-circle me-1"></i> RENDICIÓN APROBADA</span>
            <small class="d-block text-muted mt-1 font-monospace">Ref: EXP-2026-00{{ $expediente->id }}</small>
        </div>
    </div>

    <!-- Sello de Trazabilidad y HASH -->
    <div class="stamp-box">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <strong class="text-primary"><i class="fas fa-shield-alt me-1"></i> SELLO DE TRAZABILIDAD EXPRESA INTEGRAL</strong>
                <div class="small text-muted font-monospace mt-1">HASH DE SEGURIDAD: {{ $expediente->hash_trazabilidad }}</div>
                <small class="text-secondary d-block">Certificado emitido oficialmente el {{ $fechaEmision }} hs.</small>
            </div>
            <div class="qr-stamp bg-white">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=90x90&data={{ urlencode('https://integra.gentepiola.net/auditor/docentes-legajos?hash=' . $expediente->hash_trazabilidad) }}" alt="QR Trazabilidad" width="80">
                <div style="font-size: 0.6rem;" class="fw-bold mt-1">VERIFICAR VÁLIDO</div>
            </div>
        </div>
    </div>

    <!-- Resumen de Datos del Expediente -->
    <div class="row g-3 mb-4">
        <div class="col-6">
            <div class="p-3 bg-light rounded border">
                <h6 class="fw-bold text-dark border-bottom pb-1"><i class="fas fa-user-md text-primary me-1"></i> Datos del Docente / Terapeuta</h6>
                <div class="small"><strong>Nombre:</strong> {{ $expediente->docente_nombre }}</div>
                <div class="small"><strong>DNI:</strong> {{ $expediente->docente_dni }}</div>
                <div class="small"><strong>CUIL:</strong> {{ $expediente->docente_cuil }}</div>
                <div class="small"><strong>Comprobante:</strong> {{ $expediente->factura_arca }}</div>
            </div>
        </div>
        <div class="col-6">
            <div class="p-3 bg-light rounded border">
                <h6 class="fw-bold text-dark border-bottom pb-1"><i class="fas fa-child text-warning me-1"></i> Datos del Alumno y Titular</h6>
                <div class="small"><strong>Alumno Atendido:</strong> {{ $expediente->alumno_nombre }} (DNI: {{ $expediente->alumno_dni }})</div>
                <div class="small"><strong>Titular / Padre:</strong> {{ $expediente->titular_nombre }} (DNI: {{ $expediente->titular_dni }})</div>
                <div class="small"><strong>Institución Escolar:</strong> {{ $expediente->escuela_nombre }} (CUE: {{ $expediente->escuela_cue }})</div>
                <div class="small"><strong>Resolución OSP:</strong> {{ $expediente->nro_resolucion }}</div>
            </div>
        </div>
    </div>

    <!-- Tabla de Cadena de Avales y Responsabilidades (Sellos Digitales) -->
    <h6 class="fw-bold text-dark mb-2"><i class="fas fa-signature text-info me-1"></i> Cadena de Firma Digital y Responsabilidad Administrativa</h6>
    <table class="table table-bordered align-middle trazabilidad-table mb-4">
        <thead>
            <tr>
                <th>Rol / Eslabón</th>
                <th>Persona / Entidad</th>
                <th>Acción / Aval</th>
                <th>Fecha y Hora</th>
                <th class="text-center">Estado</th>
            </tr>
        </thead>
        <tbody class="small">
            <tr>
                <td><strong>1. Docente Prestador</strong></td>
                <td>{{ $expediente->docente_nombre }}</td>
                <td>Declaración de Asistencias e Ingreso de Factura ARCA</td>
                <td>{{ $fechaEmision }}</td>
                <td class="text-center text-success fw-bold"><i class="fas fa-check"></i> OK</td>
            </tr>
            <tr>
                <td><strong>2. Directora de Escuela</strong></td>
                <td>{{ $expediente->directora_aval }}</td>
                <td>Certificación Digital Oficial de Asistencia en Aula</td>
                <td>{{ $expediente->fecha_aval_directora }}</td>
                <td class="text-center text-success fw-bold"><i class="fas fa-check"></i> AVALADO</td>
            </tr>
            <tr>
                <td><strong>3. Padre / Titular</strong></td>
                <td>{{ $expediente->titular_nombre }}</td>
                <td>Conformidad Digital de Prestación de Servicio a su Hijo</td>
                <td>{{ $expediente->fecha_aval_padre }}</td>
                <td class="text-center text-success fw-bold"><i class="fas fa-check"></i> CONFORME</td>
            </tr>
            <tr>
                <td><strong>4. Auditoría Central</strong></td>
                <td>{{ $expediente->auditor_aprobacion }}</td>
                <td>Aprobación de Expediente y Orden de Liquidación</td>
                <td>{{ $fechaEmision }}</td>
                <td class="text-center text-success fw-bold"><i class="fas fa-check"></i> APROBADO</td>
            </tr>
        </tbody>
    </table>

    <!-- Resumen de Liquidación -->
    <div class="d-flex justify-content-between align-items-center p-3 bg-dark text-white rounded">
        <div>
            <div class="small text-muted">TOTAL LIQUIDADO Y AUTORIZADO:</div>
            <h4 class="fw-bold text-success mb-0">${{ number_format($expediente->monto_total, 2, ',', '.') }} ({{ $expediente->horas_mensuales }} hs mensuales)</h4>
        </div>
        <div class="text-end small text-muted">
            Documento firmado digitalmente en conformidad con la Ley N° 25.506 de Firma Digital.
        </div>
    </div>

</div>

</body>
</html>
