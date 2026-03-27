@php
    if (!function_exists('highlightText')) {
        function highlightText($text, $search) {
            if (!$text) return '';
            $text = (string)$text;
            if (!$search) return htmlspecialchars($text);
            $pattern = '/(' . preg_quote($search, '/') . ')/ui';
            return preg_replace($pattern, '<mark class="bg-warning text-dark px-1 rounded shadow-sm fw-bold">$1</mark>', htmlspecialchars($text));
        }
    }
@endphp

@forelse($formaciones as $formacion)
    <tr>
        <td class="align-middle fw-bold">
            <span class="badge bg-secondary px-3 py-2 text-white shadow-sm"><i class="fas fa-fingerprint me-1"></i>#{!! highlightText($formacion->id, $search ?? '') !!}</span>
        </td>
        <td class="align-middle fs-6">
            {!! highlightText($formacion->nombre, $search ?? '') !!}
        </td>
        <td class="align-middle">
            <span class="badge bg-success-subtle text-success border border-success px-2 py-1"><i class="fas fa-check-circle me-1"></i>Habilitada para Terapeutas</span>
        </td>
        <td class="align-middle text-end">
            <div class="d-flex justify-content-end gap-2">
                @if(is_null($formacion->empresa_id))
                    <span class="badge bg-light text-muted border px-2 py-1" data-bs-toggle="tooltip" title="Rol Oficial del Sistema. Imposible de modificar."><i class="fas fa-lock me-1"></i> Protegido</span>
                @else
                    <button class="btn btn-sm btn-outline-primary shadow-sm rounded-circle" style="width: 32px; height: 32px;" data-bs-toggle="modal" data-bs-target="#editFormacion{{ $formacion->id }}" title="Editar Título">
                        <i class="fas fa-pencil-alt"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger shadow-sm rounded-circle" style="width: 32px; height: 32px;" data-bs-toggle="modal" data-bs-target="#deleteFormacion{{ $formacion->id }}" title="Borrar Título">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                @endif
            </div>
            
            @if(!is_null($formacion->empresa_id))
            <!-- Modal Edit -->
            <div class="modal fade" id="editFormacion{{ $formacion->id }}" tabindex="-1" aria-hidden="true" style="text-align: left; color: #0f172a;">
                <div class="modal-dialog">
                    <div class="modal-content border-0" style="border-radius: 15px;">
                        <div class="modal-header bg-light border-0" style="border-radius: 15px 15px 0 0;">
                            <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2 text-primary"></i>Editar Especialidad</h5>
                        </div>
                        <form action="{{ route('tenant.formaciones.update', $formacion->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-body p-4">
                                <label class="form-label fw-bold small text-muted">Nombre</label>
                                <input type="text" name="nombre" class="form-control bg-white" value="{{ $formacion->nombre }}" required>
                            </div>
                            <div class="modal-footer border-0 pb-4 pe-4">
                                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Actualizar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Delete -->
            <div class="modal fade" id="deleteFormacion{{ $formacion->id }}" tabindex="-1" aria-hidden="true" style="text-align: left; color: #0f172a;">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0" style="border-radius: 15px;">
                        <div class="modal-header bg-danger text-white border-0" style="border-radius: 15px 15px 0 0;">
                            <h5 class="modal-title fw-bold"><i class="fas fa-exclamation-triangle me-2"></i>Eliminar Especialidad</h5>
                        </div>
                        <form action="{{ route('tenant.formaciones.destroy', $formacion->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <div class="modal-body p-4 text-center">
                                <p>¿Seguro que deseas eliminar <strong>{{ $formacion->nombre }}</strong>?</p>
                                <p class="text-muted small">Esta acción no se puede deshacer.</p>
                            </div>
                            <div class="modal-footer border-0 pb-4 justify-content-center">
                                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Mantener</button>
                                <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Eliminar Definitivamente</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="4" class="text-center py-5 text-muted">
            <i class="fas fa-search-minus fa-3x mb-3 text-light"></i><br>
            <h5 class="fw-bold">No hay Títulos disponibles en el Catálogo</h5>
        </td>
    </tr>
@endforelse
