@extends('layouts.tenant')

@section('title', 'Requisitos Documentales - Archivos Obligatorios')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4 align-items-center">
        <div class="col-lg-8">
            <h2 class="text-primary fw-bold mb-1">
                <i class="fas fa-file-contract me-2"></i> Requisitos Documentales
            </h2>
            <p class="text-muted mb-0">Aquí defines qué documentación <b>obligatoria u opcional</b> se le exigirá a tus Docentes Profesionales y Pacientes/Alumnos. Esto será requerido tanto en su ingreso como en sus liquidaciones mensuales.</p>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <button type="button" class="btn btn-primary rounded-pill fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#createRequirementModal">
                <i class="fas fa-plus-circle me-1"></i> Añadir Nuevo Requisito
            </button>
        </div>
    </div>

    <!-- Filtros o Pestañas (Simuladas por tablas por ahora) -->
    <div class="row">
        
        <!-- Requisitos DOCENTES -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header bg-dark text-white border-0 py-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-user-md me-2"></i> Exigidos a Docentes / Terapeutas</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-secondary fw-bold">Documento</th>
                                    <th class="text-secondary fw-bold text-center">Tipo</th>
                                    <th class="text-secondary fw-bold text-center">Vencimiento</th>
                                    <th class="text-secondary fw-bold text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tipos->where('entidad_tipo', 'docente') as $tipo)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $tipo->nombre }}</div>
                                        @if($tipo->descripcion) <small class="text-muted">{{ $tipo->descripcion }}</small> @endif
                                    </td>
                                    <td class="text-center">
                                        @if($tipo->es_obligatorio)
                                            <span class="badge bg-danger rounded-pill"><i class="fas fa-asterisk me-1"></i> Obligatorio</span>
                                        @else
                                            <span class="badge bg-secondary rounded-pill">Opcional</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($tipo->vencimiento_dias)
                                            <span class="text-warning fw-bold"><i class="fas fa-history"></i> {{ $tipo->vencimiento_dias }} días</span>
                                        @else
                                            <span class="text-success"><i class="fas fa-infinity"></i> Fijo / Único</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-light text-primary" data-bs-toggle="tooltip" title="Editar"><i class="fas fa-edit"></i></button>
                                        <form action="{{ route('tenant.tipo_documentos.destroy', $tipo->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light text-danger" onclick="return confirm('¿Seguro quieres eliminar este requisito? No borrará archivos anteriores.')" data-bs-toggle="tooltip" title="Quitar Regla"><i class="fas fa-trash-alt"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Aún no has configurado ningún requisito para los Docentes.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Requisitos ALUMNOS -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header border-0 py-3" style="background-color: #0f172a; color: white;">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-child me-2"></i> Exigidos a Alumnos / Familiares</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-secondary fw-bold">Documento</th>
                                    <th class="text-secondary fw-bold text-center">Tipo</th>
                                    <th class="text-secondary fw-bold text-center">Vencimiento</th>
                                    <th class="text-secondary fw-bold text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tipos->where('entidad_tipo', 'alumno') as $tipo)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $tipo->nombre }}</div>
                                        @if($tipo->descripcion) <small class="text-muted">{{ $tipo->descripcion }}</small> @endif
                                    </td>
                                    <td class="text-center">
                                        @if($tipo->es_obligatorio)
                                            <span class="badge bg-danger rounded-pill"><i class="fas fa-asterisk me-1"></i> Obligatorio</span>
                                        @else
                                            <span class="badge bg-secondary rounded-pill">Opcional</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($tipo->vencimiento_dias)
                                            <span class="text-warning fw-bold"><i class="fas fa-history"></i> {{ $tipo->vencimiento_dias }} días</span>
                                        @else
                                            <span class="text-success"><i class="fas fa-infinity"></i> Único</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <form action="{{ route('tenant.tipo_documentos.destroy', $tipo->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light text-danger" onclick="return confirm('¿Seguro quieres eliminar este requisito?')" data-bs-toggle="tooltip" title="Quitar Regla"><i class="fas fa-trash-alt"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Aún no has configurado ningún requisito para los Alumnos.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal para Crear Requisito -->
<div class="modal fade" id="createRequirementModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
            <div class="modal-header border-bottom bg-light">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-plus-circle me-2 text-primary"></i>Nuevo Requisito</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('tenant.tipo_documentos.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Aplicar a Entidad:</label>
                        <select name="entidad_tipo" class="form-select border-0 bg-light" required>
                            <option value="docente">Docentes / Profesionales (Terapeutas)</option>
                            <option value="alumno">Alumnos / Pacientes (Familiares)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre del Documento</label>
                        <input type="text" name="nombre" class="form-control border-0 bg-light" placeholder="Ej: Certificado de Buena Conducta" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Descripción / Aclaración (Opcional)</label>
                        <input type="text" name="descripcion" class="form-control border-0 bg-light" placeholder="Ej: Se pide anualmente, subido por el profesional...">
                    </div>
                    
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">¿Vencimiento?</label>
                            <div class="input-group">
                                <input type="number" name="vencimiento_dias" class="form-control border-0 bg-light" placeholder="Días (Ej: 180)">
                                <span class="input-group-text border-0 bg-light text-muted">Días</span>
                            </div>
                            <small class="text-muted d-block mt-1">Dejar vacío si no vence</small>
                        </div>
                        <div class="col-6 mb-3 d-flex align-items-center mt-3">
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input" type="checkbox" name="es_obligatorio" id="es_obligatorio_chk" value="1" checked>
                                <label class="form-check-label fs-6 ms-2 mt-1" for="es_obligatorio_chk">Es Obligatorio</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 pe-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 text-secondary fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Guardar Regla</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
