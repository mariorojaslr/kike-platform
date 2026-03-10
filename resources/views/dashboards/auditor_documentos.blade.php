@extends('layouts.tenant')

@push('styles')
<style>
    .qr-data-box {
        font-family: monospace;
        font-size: 0.8rem;
        background-color: #f8f9fa;
        padding: 8px;
        border-radius: 4px;
        border: 1px dashed #ccc;
        word-break: break-all;
    }
    
    .invoice-preview-img {
        max-width: 100%;
        max-height: 400px;
        object-fit: contain;
        border-radius: 8px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        cursor: pointer;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h3 class="mb-0 fw-bold">Auditoría de Requisitos <i class="fas fa-file-signature text-primary ms-2"></i></h3>
                <p class="text-secondary mb-0">Revisión de la documentación exigida a docentes (Seguros, DNI, AFIP).</p>
            </div>
            
            <div class="d-flex gap-2">
                 <button class="btn btn-outline-primary"><i class="fas fa-filter"></i> Filtrar Pendientes</button>
            </div>
        </div>
    </div>

    <!-- MAIN TABLE -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Documento</th>
                            <th>Entidad / Titular</th>
                            <th>Fecha Recibido</th>
                            <th class="text-center">Estado Auditoría</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documentos as $doc)
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-bold">{{ $doc->tipoDocumento->nombre ?? 'Documento Desconocido' }}</span><br>
                                    <small class="text-muted"><i class="fas fa-hdd"></i> ID: #{{ $doc->id }}</small>
                                </td>
                                
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                            <i class="fas {{ $doc->entidad_tipo == 'docente' ? 'fa-user-md' : 'fa-child' }} text-primary"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold text-dark">{{ $doc->entidad_nombre }}</h6>
                                            <small class="text-muted text-uppercase" style="font-size: 0.7rem;">{{ $doc->entidad_tipo }}</small>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <span>{{ $doc->created_at->format('d/m/Y') }}</span><br>
                                    <small class="text-muted">{{ $doc->created_at->diffForHumans() }}</small>
                                </td>

                                <td class="text-center">
                                    @if($doc->estado === 'pendiente')
                                        <span class="badge bg-primary rounded-pill"><i class="fas fa-search"></i> A Revisar</span>
                                    @elseif($doc->estado === 'aprobado')
                                        <span class="badge bg-success rounded-pill"><i class="fas fa-check-double"></i> Válido</span>
                                    @elseif($doc->estado === 'observado')
                                        <span class="badge bg-warning text-dark rounded-pill"><i class="fas fa-exclamation-triangle"></i> Observado</span>
                                    @else
                                        <span class="badge bg-danger rounded-pill"><i class="fas fa-times-circle"></i> Rechazado</span>
                                    @endif
                                    
                                    @if($doc->comentarios_auditor)
                                        <i class="fas fa-comment-dots text-muted ms-1" data-bs-toggle="tooltip" title="{{ $doc->comentarios_auditor }}"></i>
                                    @endif
                                </td>

                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-dark" data-bs-toggle="modal" data-bs-target="#modalDoc{{ $doc->id }}">
                                        <i class="fas fa-eye me-1"></i> Abrir Expediente
                                    </button>
                                </td>
                            </tr>

                            <!-- MODAL VISOR PARA ESTE DOCUMENTO -->
                            <div class="modal fade" id="modalDoc{{ $doc->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content border-0 shadow-lg">
                                        <div class="modal-header border-bottom bg-dark text-white">
                                            <h5 class="modal-title fw-bold"><i class="fas fa-balance-scale"></i> Auditoría: {{ $doc->tipoDocumento->nombre ?? 'Documento' }}</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-0 row m-0">
                                            
                                            <!-- Columna Izquierda: La foto o PDF -->
                                            <div class="col-md-7 text-center bg-light p-4 border-end" style="min-height: 400px; display: flex; flex-direction: column; justify-content: center;">
                                                @if($doc->ruta_archivo)
                                                    @if(Str::endsWith(strtolower($doc->ruta_archivo), ['.pdf']))
                                                        <a href="{{ Storage::url($doc->ruta_archivo) }}" target="_blank" class="btn btn-outline-danger btn-lg mt-3">
                                                            <i class="fas fa-file-pdf"></i> Abrir PDF Adjunto
                                                        </a>
                                                    @else
                                                        <a href="{{ Storage::url($doc->ruta_archivo) }}" target="_blank" title="Abrir en pantalla completa">
                                                            <img src="{{ Storage::url($doc->ruta_archivo) }}" class="invoice-preview-img" alt="Foto del comprobante">
                                                        </a>
                                                    @endif
                                                @else
                                                    <div class="p-5">
                                                        <i class="fas fa-image fa-3x text-muted mb-3"></i>
                                                        <p class="text-muted">El archivo se corrompió o no fue subido.</p>
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Columna Derecha: Metadatos y extracción OCR/QR -->
                                            <div class="col-md-5 d-flex flex-column py-4 px-4">
                                                <h5 class="fw-bold text-dark mb-1">{{ $doc->entidad_nombre }}</h5>
                                                <p class="text-muted small mb-4">Entidad: {{ strtoupper($doc->entidad_tipo) }} | Fecha Subida: {{ $doc->created_at->format('d/m/Y H:i') }}</p>
                                                
                                                <h6 class="fw-bold text-primary mb-2">Dictamen del Auditor</h6>
                                                
                                                @if($doc->estado === 'aprobado')
                                                    <div class="alert alert-success mt-2 mb-0">
                                                        <i class="fas fa-check-circle me-1"></i> Este documento ya fue aprobado.
                                                        @if($doc->comentarios_auditor)
                                                            <hr><b>Nota:</b> {{ $doc->comentarios_auditor }}
                                                        @endif
                                                    </div>
                                                @else
                                                    <!-- Formulario de Evaluación -->
                                                    <form action="{{ route('auditor.documentos.status', $doc->id) }}" method="POST">
                                                        @csrf
                                                        <div class="mb-3">
                                                            <label class="form-label text-secondary small fw-bold">Estado Final:</label>
                                                            <select name="estado" class="form-select bg-light border-0" onchange="document.getElementById('notaRechazo_{{ $doc->id }}').style.display = (this.value === 'aprobado') ? 'none' : 'block';">
                                                                <option value="aprobado" {{ $doc->estado == 'aprobado' ? 'selected' : '' }}>🟢 Aprobar (Documento Válido)</option>
                                                                <option value="observado" {{ $doc->estado == 'observado' ? 'selected' : '' }}>🟡 Observar (Falta Firma o Ilegible)</option>
                                                                <option value="rechazado" {{ $doc->estado == 'rechazado' ? 'selected' : '' }}>🔴 Rechazar (Documento Inválido/Expirado)</option>
                                                            </select>
                                                        </div>

                                                        <div class="mb-3" id="notaRechazo_{{ $doc->id }}" style="display: none;">
                                                            <label class="form-label text-secondary small fw-bold">Aclaración para el Usuario:</label>
                                                            <textarea name="comentarios_auditor" class="form-control border-0 bg-light" rows="3" placeholder="Ej: La imagen está borrosa, por favor envíala de nuevo.">{{ $doc->comentarios_auditor }}</textarea>
                                                        </div>

                                                        <button type="submit" class="btn btn-primary w-100 shadow-sm rounded-pill fw-bold mt-2"><i class="fas fa-gavel me-2"></i> Emitir Fallo</button>
                                                    </form>
                                                    <hr>
                                                    <div class="alert alert-warning py-2 small border-0 mb-0">
                                                        <i class="fas fa-info-circle"></i> <b>Recuerde:</b> Aunque un documento figure como "Rechazado", el sistema bloqueará funciones críticas al usuario hasta su cumplimiento.
                                                    </div>
                                                @endif

                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- FIN MODAL -->

                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <div class="bg-light d-inline-block rounded-circle p-4 mb-3">
                                        <i class="fas fa-check-double fa-3x text-success opacity-75"></i>
                                    </div>
                                    <h5>No hay documentos pendientes de revisión</h5>
                                    <p>La bandeja de auditoría de requisitos está vacía.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if(method_exists($documentos, 'links'))
            <div class="p-3 border-top">
                {{ $documentos->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
