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
                <h3 class="mb-0 fw-bold">Auditoría Acelerada <i class="fas fa-bolt text-warning ms-2"></i></h3>
                <p class="text-secondary mb-0">Revisión de comprobantes y validación de sesiones.</p>
            </div>
            
            <div class="d-flex gap-2">
                 <button class="btn btn-primary"><i class="fas fa-filter"></i> Filtrar Pendientes</button>
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
                            <th class="ps-4">ID / Fecha</th>
                            <th>Terapeuta</th>
                            <th>Comprobante</th>
                            <th>Estado Actual</th>
                            <th class="text-end pe-4">Acción Inmediata</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($facturas as $factura)
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-bold">#{{ str_pad($factura->id, 5, '0', STR_PAD_LEFT) }}</span><br>
                                    <small class="text-muted"><i class="far fa-clock"></i> {{ $factura->created_at->format('d/m/Y H:i') }}</small>
                                </td>
                                
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-secondary bg-opacity-10 rounded-circle p-2 me-3">
                                            <i class="fas fa-user-md text-secondary"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold">{{ $factura->user->name ?? 'Usuario Eliminado' }}</h6>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <!-- Botón para abrir el visor de la foto -->
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalFactura{{ $factura->id }}">
                                        <i class="fas fa-camera"></i> Ver Imagen y QR
                                    </button>
                                </td>

                                <td>
                                    @if($factura->estado === 'pendiente')
                                        <span class="badge bg-warning text-dark"><i class="fas fa-hourglass-half"></i> Pendiente</span>
                                    @elseif($factura->estado === 'aprobada')
                                        <span class="badge bg-success"><i class="fas fa-check-circle"></i> Aprobada</span>
                                    @else
                                        <span class="badge bg-danger"><i class="fas fa-times-circle"></i> Rechazada</span>
                                    @endif
                                </td>

                                <td class="text-end pe-4">
                                    @if($factura->estado === 'pendiente')
                                        <div class="d-flex justify-content-end gap-2">
                                            <!-- Form Aprobar -->
                                            <form action="{{ route('auditor.facturas.status', $factura->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="estado" value="aprobada">
                                                <button type="submit" class="btn btn-sm btn-success" title="Aprobar Inmediatamente"><i class="fas fa-check"></i></button>
                                            </form>
                                            <!-- Form Rechazar -->
                                            <form action="{{ route('auditor.facturas.status', $factura->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="estado" value="rechazada">
                                                <button type="submit" class="btn btn-sm btn-danger" title="Rechazar Comprobante" onclick="return confirm('¿Rechazar esta factura?')"><i class="fas fa-times"></i></button>
                                            </form>
                                        </div>
                                    @else
                                        <small class="text-muted">Auditada</small>
                                    @endif
                                </td>
                            </tr>

                            <!-- MODAL VISOR PARA ESTA FACTURA -->
                            <div class="modal fade" id="modalFactura{{ $factura->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content border-0 shadow-lg">
                                        <div class="modal-header border-0 bg-light">
                                            <h5 class="modal-title fw-bold"><i class="fas fa-search"></i> Auditoría Detallada Factura #{{ $factura->id }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4 row">
                                            
                                            <!-- Columna Izquierda: La foto desde nuestro Staging Storage o Bunny -->
                                            <div class="col-md-7 text-center bg-light rounded-3 p-3">
                                                @if($factura->imagen_url)
                                                    <a href="{{ Storage::url($factura->imagen_url) }}" target="_blank" title="Abrir en pantalla completa">
                                                        <img src="{{ Storage::url($factura->imagen_url) }}" class="invoice-preview-img" alt="Foto del comprobante">
                                                    </a>
                                                    <div class="small text-muted mt-2"><i class="fas fa-hdd"></i> Almacenamiento: {{ strtoupper($factura->storage_disk) }}</div>
                                                @else
                                                    <div class="p-5">
                                                        <i class="fas fa-image fa-3x text-muted mb-3"></i>
                                                        <p class="text-muted">No se proveyó imagen física.</p>
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Columna Derecha: Metadatos y extracción OCR/QR -->
                                            <div class="col-md-5 d-flex flex-column justify-content-center px-4">
                                                <h6 class="fw-bold text-primary mb-3">Datos Extraídos del QR AFIP</h6>
                                                
                                                @if($factura->qr_data)
                                                    <p class="text-secondary small mb-1">El profesional escaneó el siguiente código desde su móvil:</p>
                                                    <div class="qr-data-box mb-4">
                                                        <!-- Si es un link real o json, lo mostramos crudo -->
                                                        {{ $factura->qr_data }}
                                                    </div>
                                                @else
                                                     <div class="alert alert-warning py-2 small">
                                                        <i class="fas fa-exclamation-triangle"></i> La factura fue capturada omitiendo el sistema de escaneo QR. Validar montos visualmente.
                                                     </div>
                                                @endif

                                                <hr>

                                                @if($factura->estado === 'pendiente')
                                                <div class="d-grid gap-2 mt-4">
                                                    <form action="{{ route('auditor.facturas.status', $factura->id) }}" method="POST" class="d-grid">
                                                        @csrf
                                                        <input type="hidden" name="estado" value="aprobada">
                                                        <button type="submit" class="btn btn-success btn-lg shadow-sm"><i class="fas fa-check-circle"></i> Aprobar Comprobante</button>
                                                    </form>
                                                    <button class="btn btn-outline-danger btn-lg mt-2" data-bs-dismiss="modal" onclick="document.getElementById('formRechazo{{$factura->id}}').submit();">
                                                        <i class="fas fa-times-circle"></i> Rechazar
                                                    </button>
                                                    
                                                    <form id="formRechazo{{$factura->id}}" action="{{ route('auditor.facturas.status', $factura->id) }}" method="POST" style="display: none;">
                                                        @csrf
                                                        <input type="hidden" name="estado" value="rechazada">
                                                    </form>
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
                                    <i class="fas fa-inbox fa-3x mb-3 text-secondary opacity-50"></i>
                                    <h5>No hay facturas pendientes</h5>
                                    <p>Todos los profesionales están al día.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Paginación -->
            @if(method_exists($facturas, 'links'))
            <div class="p-3 border-top">
                {{ $facturas->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
