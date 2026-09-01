@extends('layouts.tenant')

@section('title', 'Auditoría de Legajos de Docentes')

@section('content')
<div class="container-fluid py-3">

    <!-- Header y Titular -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="fas fa-id-card-alt text-primary me-2"></i> Auditoría de Legajos de Docentes</h3>
            <p class="text-muted small mb-0">Control de cumplimiento documental obligatorio (Título, Matrícula, DNI, Ética, Reenmarque Frente/Dorso).</p>
        </div>
        <a href="{{ route('auditor.novedades') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="fas fa-bell me-2"></i> Feed de Novedades en Tiempo Real
        </a>
    </div>

    <!-- KPIs de Estado de Legajos -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 border-start border-4 border-primary" style="border-radius: 12px;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small fw-bold mb-1">TOTAL DOCENTES</p>
                        <h3 class="fw-bold mb-0 text-dark">{{ $totalDocentes }}</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary">
                        <i class="fas fa-user-graduate fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 border-start border-4 border-success" style="border-radius: 12px;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small fw-bold mb-1">LEGAJOS AL DÍA (100%)</p>
                        <h3 class="fw-bold mb-0 text-success">{{ $alDia }}</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success">
                        <i class="fas fa-check-circle fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 border-start border-4 border-warning" style="border-radius: 12px;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small fw-bold mb-1">CON DOCS PENDIENTES</p>
                        <h3 class="fw-bold mb-0 text-warning">{{ $incompletos }}</h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 p-3 rounded-circle text-warning">
                        <i class="fas fa-exclamation-triangle fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 border-start border-4 border-danger" style="border-radius: 12px;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small fw-bold mb-1">SUSPENDIDOS / INHABILITADOS</p>
                        <h3 class="fw-bold mb-0 text-danger">{{ $suspendidos }}</h3>
                    </div>
                    <div class="bg-danger bg-opacity-10 p-3 rounded-circle text-danger">
                        <i class="fas fa-ban fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Docentes y Legajos -->
    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-header bg-white border-0 pt-4 pb-2 px-4">
            <h5 class="fw-bold mb-0"><i class="fas fa-list text-primary me-2"></i> Nómina de Docentes y Porcentaje de Cumplimiento</h5>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light small text-uppercase">
                        <tr>
                            <th>Docente / Terapeuta</th>
                            <th>DNI / Email</th>
                            <th>Formación</th>
                            <th>Progreso de Legajo</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($docentes as $doc)
                            @php
                                $pct = $doc->porcentaje_legajo ?? 0;
                                $docsCount = $doc->documentos->count();
                                $aprobadosCount = $doc->documentos->where('estado_auditoria', 'aprobado')->count();
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3 fw-bold text-center" style="width:40px;height:40px;line-height:24px;">
                                            {{ strtoupper(substr($doc->nombre, 0, 1)) }}
                                        </div>
                                        <div>
                                            <strong class="text-dark">{{ $doc->nombre }}</strong>
                                            <small class="text-muted d-block">{{ $doc->telefono ?? 'Sin Tel' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="font-monospace text-dark fw-bold">{{ $doc->dni ?? 'Sin DNI' }}</span>
                                    <small class="text-muted d-block">{{ $doc->email }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary bg-opacity-10 text-dark border px-2 py-1">
                                        {{ $doc->formacion->nombre ?? 'Maestra Integradora' }}
                                    </span>
                                </td>
                                <td style="min-width: 200px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 10px; border-radius: 10px;">
                                            <div class="progress-bar {{ $pct == 100 ? 'bg-success' : 'bg-warning' }}" role="progressbar" style="width: {{ $pct }}%;"></div>
                                        </div>
                                        <span class="small fw-bold {{ $pct == 100 ? 'text-success' : 'text-dark' }}">{{ $aprobadosCount }}/{{ max($docsCount, 1) }} ({{ $pct }}%)</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if($doc->estado_legajo === 'al_dia')
                                        <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>AL DÍA</span>
                                    @elseif($doc->estado_legajo === 'suspendido')
                                        <span class="badge bg-danger"><i class="fas fa-ban me-1"></i>SUSPENDIDO</span>
                                    @else
                                        <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>INCOMPLETO</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalLegajo{{ $doc->id }}">
                                        <i class="fas fa-folder-open me-1"></i> Ver Legajo
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No existen docentes registrados en la nómina.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODALES DE DETALLE DE LEGAJO POR DOCENTE -->
@foreach($docentes as $doc)
    <div class="modal fade" id="modalLegajo{{ $doc->id }}" tabindex="-1" aria-hidden="true" style="color: #0f172a;">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 18px;">
                <div class="modal-header bg-dark text-white border-0" style="border-radius: 18px 18px 0 0;">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-folder me-2 text-warning"></i> Legajo Profesional: {{ $doc->nombre }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3 bg-light p-3 rounded">
                        <div>
                            <strong class="text-dark">Porcentaje de Cumplimiento Documental:</strong>
                            <div class="progress mt-1" style="height: 8px; width: 250px;">
                                <div class="progress-bar bg-success" style="width: {{ $doc->porcentaje_legajo }}%;"></div>
                            </div>
                        </div>
                        <span class="badge bg-primary fs-6">{{ $doc->porcentaje_legajo }}% Aprobado</span>
                    </div>

                    <h6 class="fw-bold text-dark mb-3"><i class="fas fa-file-alt me-1 text-primary"></i> Documentos Presentados</h6>

                    <div class="list-group">
                        @forelse($doc->documentos as $documento)
                            <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center flex-wrap p-3 mb-2 border rounded">
                                <div>
                                    <h6 class="mb-1 fw-bold text-dark"><i class="fas fa-file-check me-2 text-primary"></i> {{ $documento->tipo_documento }}</h6>
                                    <small class="text-muted d-block">Subido el: {{ $documento->created_at->format('d/m/Y H:i') }}</small>
                                    
                                    @if($documento->es_frente_dorso)
                                        <span class="badge bg-info text-dark font-monospace" style="font-size:0.65rem;">📷 FRENTE Y DORSO</span>
                                    @endif

                                    @if($documento->motivo_rechazo)
                                        <div class="alert alert-danger p-2 py-1 small mt-2 mb-0">
                                            <i class="fas fa-exclamation-circle me-1"></i> Motivo de Rechazo: {{ $documento->motivo_rechazo }}
                                        </div>
                                    @endif
                                </div>
                                <div class="d-flex align-items-center gap-2 mt-2 mt-sm-0">
                                    @if($documento->ruta_archivo)
                                        <a href="{{ asset('storage/' . $documento->ruta_archivo) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Ver Frente / Archivo">
                                            <i class="fas fa-eye me-1"></i> Ver
                                        </a>
                                    @endif
                                    @if($documento->dorso_url)
                                        <a href="{{ asset('storage/' . $documento->dorso_url) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Ver Dorso">
                                            <i class="fas fa-images me-1"></i> Dorso
                                        </a>
                                    @endif

                                    @if($documento->estado_auditoria === 'aprobado')
                                        <span class="badge bg-success py-2 px-3"><i class="fas fa-check me-1"></i>Aprobado</span>
                                    @else
                                        <form action="{{ route('auditor.legajo_doc.aprobar', $documento->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success shadow-sm" title="Aprobar Documento">
                                                <i class="fas fa-check me-1"></i> Aprobar
                                            </button>
                                        </form>

                                        <button type="button" class="btn btn-sm btn-outline-danger shadow-sm" data-bs-toggle="collapse" data-bs-target="#collapseRechazoDoc{{ $documento->id }}">
                                            <i class="fas fa-times me-1"></i> Rechazar
                                        </button>
                                    @endif
                                </div>

                                <!-- Formulario de Rechazo con Argumento -->
                                <div class="collapse w-100 mt-3" id="collapseRechazoDoc{{ $documento->id }}">
                                    <form action="{{ route('auditor.legajo_doc.rechazar', $documento->id) }}" method="POST" class="bg-light p-3 rounded border border-danger">
                                        @csrf
                                        <label class="form-label fw-bold small text-danger">Motivo / Argumentación del Rechazo</label>
                                        <textarea name="motivo_rechazo" class="form-control mb-2" rows="2" placeholder="Ej: La imagen del reverso se encuentra borrosa o desactualizada." required></textarea>
                                        <div class="text-end">
                                            <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3">Enviar Rechazo al Docente</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted small">No se han subido documentos de legajo aún.</div>
                        @endforelse
                    </div>

                    <!-- Formulario de Carga Directa por Auditoría / Administración -->
                    <div class="mt-4 pt-3 border-top">
                        <button class="btn btn-sm btn-outline-primary fw-bold rounded-pill" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSubirAuditor{{ $doc->id }}">
                            <i class="fas fa-file-upload me-1"></i> 📤 Cargar Documento en Nombre del Docente (Vía Auditoría)
                        </button>

                        <div class="collapse mt-3" id="collapseSubirAuditor{{ $doc->id }}">
                            <form action="{{ route('auditor.legajo_doc.subir') }}" method="POST" enctype="multipart/form-data" class="bg-light p-3 rounded-3 border">
                                @csrf
                                <input type="hidden" name="docente_id" value="{{ $doc->id }}">
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted">Tipo de Documento *</label>
                                        <select name="tipo_documento" class="form-select form-select-sm" required>
                                            <option value="DNI (Frente y Dorso)">DNI (Frente y Dorso)</option>
                                            <option value="Certificado de Matrícula Profesional">Certificado de Matrícula Profesional</option>
                                            <option value="Certificado de Domicilio">Certificado de Domicilio</option>
                                            <option value="Certificado de Buena Conducta / Antecedentes">Certificado de Buena Conducta / Antecedentes</option>
                                            <option value="Título Profesional / Diploma">Título Profesional / Diploma</option>
                                            <option value="Seguro de Mala Praxis / RCP">Seguro de Mala Praxis / RCP</option>
                                            <option value="Otro Certificado u Hoja de Legajo">Otro Certificado / Documento</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted">Archivo PDF o Imagen *</label>
                                        <input type="file" name="documento" class="form-control form-control-sm" accept="image/*,.pdf" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted">Fecha de Vencimiento (Opcional)</label>
                                        <input type="date" name="fecha_vencimiento" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-6 d-flex align-items-end">
                                        <div class="form-check form-switch mb-1">
                                            <input class="form-check-input" type="checkbox" name="es_frente_dorso" value="1" id="chkFD{{ $doc->id }}">
                                            <label class="form-check-label small fw-bold" for="chkFD{{ $doc->id }}">Es Documento Frente y Dorso</label>
                                        </div>
                                    </div>
                                    <div class="col-12 text-end">
                                        <button type="submit" class="btn btn-sm btn-success rounded-pill px-4 fw-bold">
                                            <i class="fas fa-check-circle me-1"></i> Guardar y Aprobar Legajo
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endforeach
@endsection
