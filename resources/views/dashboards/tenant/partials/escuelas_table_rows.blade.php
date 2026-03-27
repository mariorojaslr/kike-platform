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

@forelse($escuelas as $escuela)
<tr>
    <!-- Institución & Info Base -->
    <td>
        <div class="d-flex align-items-center">
            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm"
                 style="width: 45px; height: 45px; background: linear-gradient(135deg, var(--brand-secondary), var(--brand-primary)); color: white; flex-shrink: 0;">
                {{ mb_substr($escuela->nombre, 0, 1) }}
            </div>
            <div class="ms-3">
                <h6 class="mb-1 fw-bold text-dark">{!! highlightText($escuela->nombre, $search ?? '') !!}</h6>
                <div class="small text-muted d-flex align-items-center gap-2">
                    <span><i class="fas fa-barcode"></i> CUE: {!! highlightText($escuela->cue ?? 'Sin Registro', $search ?? '') !!}</span>
                </div>
                @if($escuela->direccion)
                <div class="small text-muted mt-1"><i class="fas fa-map-marker-alt"></i> {!! highlightText($escuela->direccion, $search ?? '') !!}</div>
                @endif
            </div>
        </div>
    </td>

    <!-- Contacto / Email -->
    <td>
        @if($escuela->contacto_principal)
            <div class="fw-bold text-dark small">{{ $escuela->contacto_principal }}</div>
        @else
            <div class="text-muted small fst-italic">Sin datos de Director/a</div>
        @endif

        @if($escuela->telefono)
            <div class="small mt-1 text-muted"><i class="fas fa-phone-alt bg-light p-1 rounded-circle" style="font-size:0.75rem;"></i> {{ $escuela->telefono }}</div>
        @endif
        @if($escuela->email)
            <div class="small mt-1 text-primary"><i class="fas fa-envelope bg-light p-1 rounded-circle border" style="font-size:0.75rem;"></i> {{ $escuela->email }}</div>
        @endif
    </td>

    <!-- Relaciones / Estado -->
    <td>
        @if($escuela->activo)
            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 shadow-sm py-2">
                <i class="fas fa-check-circle me-1"></i> Operativa
            </span>
        @else
            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 shadow-sm py-2">
                <i class="fas fa-times-circle me-1"></i> Inactiva
            </span>
        @endif
    </td>

    <!-- Acciones -->
    <td class="text-end">
        <div class="dropdown">
            <button class="btn btn-light rounded-pill btn-sm border shadow-sm px-3 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-cog text-secondary"></i> Opciones
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius: 12px; overflow: hidden;">
                <li>
                    <a class="dropdown-item py-2 fw-medium text-secondary hover-primary" href="#" data-bs-toggle="modal" data-bs-target="#editModal{{ $escuela->id }}">
                        <i class="fas fa-edit me-2" style="width: 20px;"></i> Modificar Institución
                    </a>
                </li>
                <li><hr class="dropdown-divider m-0"></li>
                <li>
                    <a class="dropdown-item py-2 fw-medium text-danger hover-danger" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $escuela->id }}">
                        <i class="fas fa-trash-alt me-2" style="width: 20px;"></i> Quitar Institución
                    </a>
                </li>
            </ul>
        </div>

        <!-- Modal Edit -->
        <div class="modal fade text-start" id="editModal{{ $escuela->id }}" tabindex="-1" aria-hidden="true" style="color: #0f172a;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                    <div class="modal-header bg-light border-0" style="border-radius: 15px 15px 0 0;">
                        <h5 class="modal-title fw-bold"><i class="fas fa-school text-primary me-2"></i> Actualizar Institución</h5>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('tenant.escuelas.update', $escuela->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body p-4">
                            
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label class="form-label fw-bold small text-muted">Nombre de la Institución <span class="text-danger">*</span></label>
                                    <input type="text" name="nombre" class="form-control bg-light border-0 py-2" value="{{ $escuela->nombre }}" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold small text-muted">CUE</label>
                                    <input type="text" name="cue" class="form-control bg-light border-0 py-2" value="{{ $escuela->cue }}">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label class="form-label fw-bold small text-muted">Teléfono Institucional</label>
                                    <input type="text" name="telefono" class="form-control bg-light border-0 py-2" value="{{ $escuela->telefono }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted">Correo Electrónico (Contacto Directo)</label>
                                    <input type="email" name="email" class="form-control bg-light border-0 py-2" value="{{ $escuela->email }}">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label class="form-label fw-bold small text-muted">Dirección o Ubicación</label>
                                    <input type="text" name="direccion" class="form-control bg-light border-0 py-2" value="{{ $escuela->direccion }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted">Nombre del Director/a o Nivel de Contacto</label>
                                    <input type="text" name="contacto_principal" class="form-control bg-light border-0 py-2" value="{{ $escuela->contacto_principal }}">
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer border-0 pb-4 pe-4">
                            <button type="button" class="btn btn-light rounded-pill px-4 fw-bold text-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn text-white rounded-pill px-4 fw-bold shadow-sm" style="background: var(--brand-primary); border: none;">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Delete -->
        <div class="modal fade text-start" id="deleteModal{{ $escuela->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                    <div class="modal-body p-5 text-center">
                        <div class="mb-4">
                            <i class="fas fa-exclamation-triangle text-danger" style="font-size: 4rem;"></i>
                        </div>
                        <h4 class="fw-bold mb-3">¿Remover esta Escuela del Sistema?</h4>
                        <p class="text-muted mb-4">Está a punto de borrar definitivamente la plataforma de la escuela <strong>{{ $escuela->nombre }}</strong>. Esta acción desvinculará a los posibles pacientes o terapeutas atados a su dependencia física. ¡Proceda con cautela!</p>
                        
                        <div class="d-flex justify-content-center gap-3">
                            <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                            <form action="{{ route('tenant.escuelas.destroy', $escuela->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm"><i class="fas fa-trash me-2"></i> Confirmar Borrado</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </td>
</tr>
@empty
<tr>
    <td colspan="4" class="text-center py-5">
        <div class="text-muted d-flex flex-column align-items-center justify-content-center">
            <i class="fas fa-school-flag mb-3" style="font-size: 3rem; color: #cbd5e1;"></i>
            <h5 class="fw-bold text-secondary mb-1">Sin Escuelas/Instituciones en esta Área</h5>
            <p class="small mb-0">Use el botón azul "Nueva Escuela" o "Importar" para confeccionar su base de datos.</p>
        </div>
    </td>
</tr>
@endforelse
