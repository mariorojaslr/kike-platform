@php
    if (!function_exists('highlightText')) {
        function highlightText($text, $search) {
            if (!$text) return '';
            $text = (string)$text;
            $search = trim((string)$search);
            
            // Fix encoding issues from CSV imports ensuring it does not return blank string
            $safeText = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            
            if (!$search) return $safeText;
            
            $pattern = '/(' . preg_quote($search, '/') . ')/ui';
            $replaced = preg_replace($pattern, '<mark class="bg-warning text-dark px-1 rounded shadow-sm fw-bold"></mark>', $safeText);
            
            return $replaced !== null ? $replaced : $safeText;
        }
    }
@endphp

@forelse($titulares as $titular)
    <tr>
        <td class="align-middle">
            <div class="d-flex align-items-center gap-3">
                <div class="position-relative">
                    @if($titular->foto_perfil)
                        <img src="{{ Storage::disk('public')->url($titular->foto_perfil) }}" alt="Avatar" class="rounded-circle shadow-sm" style="width: 55px; height: 55px; object-fit: cover; border: 2px solid var(--brand-primary);">
                    @else
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 55px; height: 55px; background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary)); font-size: 1.2rem; border: 2px solid #fff;">
                            {{ substr($titular->nombre, 0, 1) }}
                        </div>
                    @endif
                    <!-- Indicador de conexión falso (Decorativo moderno) -->
                    <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-light rounded-circle" style="width: 12px; height: 12px; transform: translate(-10%, -10%);"></span>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold" style="color: var(--text-main);">{!! highlightText($titular->nombre, $search ?? '') !!}</h6>
                    <small class="text-muted"><i class="fas fa-user-shield fa-xs me-1"></i>Titular / Responsable</small>
                </div>
            </div>
        </td>
        <td class="align-middle">
            <div class="d-flex flex-column gap-1">
                <span class="badge rounded-pill bg-light text-dark shadow-sm border" style="font-size: 0.8rem; width: fit-content;"><i class="fas fa-id-card me-1 text-muted"></i> DNI: {!! highlightText($titular->dni, $search ?? '') !!}</span>
                @if($titular->cuil) <span class="badge rounded-pill bg-light text-dark shadow-sm border" style="font-size: 0.8rem; width: fit-content;"><i class="fas fa-briefcase me-1 text-muted"></i> CUIL: {!! highlightText($titular->cuil, $search ?? '') !!}</span> @endif
            </div>
        </td>
        <td class="align-middle text-muted" style="font-size: 0.85rem;">
            @if($titular->n_afiliado) <div class="mb-1"><strong class="text-dark"><i class="fas fa-hashtag text-primary"></i> Afil:</strong> <span class="badge bg-primary rounded-pill">{!! highlightText($titular->n_afiliado, $search ?? '') !!}</span></div> @endif
            @if($titular->resolucion) <div><strong class="text-dark"><i class="fas fa-file-signature text-warning"></i> Res:</strong> {!! highlightText($titular->resolucion, $search ?? '') !!}</div> @endif
        </td>
        <td class="align-middle text-end">
            <!-- Botón Ver Familiares/Alumnos -->
            <button class="btn btn-sm btn-outline-info rounded-circle me-1" title="Ver Grupo Familiar/Alumnos" data-bs-toggle="modal" data-bs-target="#hijosModal{{ $titular->id }}">
                <i class="fas fa-users"></i>
                @if($titular->familiares->count() > 0)
                    <span class="position-absolute translate-middle p-1 bg-success border border-light rounded-circle" style="margin-top: 5px;"></span>
                @endif
            </button>

            <!-- Modal Ver Familiares -->
            <div class="modal fade text-start" id="hijosModal{{ $titular->id }}" tabindex="-1" aria-hidden="true" style="color: #0f172a;">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                        <div class="modal-header bg-info bg-opacity-10 border-0" style="border-radius: 15px 15px 0 0;">
                            <h5 class="modal-title fw-bold text-info-emphasis"><i class="fas fa-child me-2"></i> Grupo Familiar / Alumnos a Cargo</h5>
                            <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4 bg-light">
                            <h6 class="fw-bold mb-3">Titular: <span class="text-primary">{{ $titular->nombre }}</span></h6>
                            @if($titular->familiares && $titular->familiares->count() > 0)
                                <div class="table-responsive rounded shadow-sm border bg-white">
                                    <table class="table table-hover mb-0 align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Nombre Alumno</th>
                                                <th>N° Afiliado</th>
                                                <th>Diagnóstico</th>
                                                <th>Escuela</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($titular->familiares as $hijo)
                                                <tr>
                                                    <td class="fw-bold">{{ $hijo->nombre }}</td>
                                                    <td><span class="badge bg-secondary">{{ $hijo->n_afiliado ?? 'S/D' }}</span></td>
                                                    <td>
                                                        @if($hijo->tiene_patologia && $hijo->diagnostico)
                                                            <span class="badge bg-warning text-dark text-wrap text-start" style="max-width: 150px;">{{ $hijo->diagnostico->nombre ?? 'N/A' }}</span>
                                                        @else
                                                            <span class="text-muted small">S/D</span>
                                                        @endif
                                                    </td>
                                                    <td class="small">
                                                        @if($hijo->escuela)
                                                            <i class="fas fa-school text-primary"></i> {{ $hijo->escuela->nombre }}<br>
                                                            <span class="text-muted" style="font-size: 0.70rem;">{{ $hijo->grado_division ?? 'Sin Grado' }} | {{ $hijo->turno ?? 'Sin Turno' }}</span>
                                                        @else
                                                            <span class="text-muted small">Sin Escuela</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center">
                                    <i class="fas fa-exclamation-triangle fa-2x me-3 text-warning"></i>
                                    <div>
                                        <h6 class="fw-bold mb-1">Sin Pacientes / Alumnos</h6>
                                        <p class="mb-0 small text-muted">Este titular aún no tiene ningún familiar vinculado a su cargo.</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer border-0 pb-4 pe-4">
                            <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold shadow-sm" data-bs-dismiss="modal">Cerrar Detalle</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Botón Editar -->
            <button class="btn btn-sm btn-outline-primary rounded-circle me-1" title="Editar Referente" data-bs-toggle="modal" data-bs-target="#editModal{{ $titular->id }}">
                <i class="fas fa-edit"></i>
            </button>

            <!-- Modal Editar -->
            <div class="modal fade text-start" id="editModal{{ $titular->id }}" tabindex="-1" aria-hidden="true" style="color: #0f172a;">
                <div class="modal-dialog">
                    <div class="modal-content border-0" style="border-radius: 15px;">
                        <div class="modal-header bg-light border-0" style="border-radius: 15px 15px 0 0;">
                            <h5 class="modal-title fw-bold">Editar Titular</h5>
                            <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('tenant.titulares.update', $titular->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="modal-body p-4">
                                <div class="text-center mb-4">
                                    @if($titular->foto_perfil)
                                        <img src="{{ Storage::disk('public')->url($titular->foto_perfil) }}" class="rounded-circle shadow-sm mb-2" style="width: 80px; height: 80px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle d-inline-flex border shadow-sm align-items-center justify-content-center bg-light text-muted mb-2" style="width: 80px; height: 80px;">
                                            <i class="fas fa-camera fa-2x"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <label class="btn btn-sm btn-outline-secondary rounded-pill cursor-pointer">
                                            <i class="fas fa-upload me-1"></i> Cambiar Foto
                                            <input type="file" name="foto_perfil" class="d-none" accept=".jpg,.jpeg,.png">
                                        </label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-muted">Nombre Completo</label>
                                    <input type="text" name="nombre" class="form-control bg-light border-0" value="{{ $titular->nombre }}" required>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <label class="form-label fw-bold small text-muted">DNI</label>
                                        <input type="text" name="dni" class="form-control bg-light border-0" value="{{ $titular->dni }}" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label fw-bold small text-muted">CUIL</label>
                                        <input type="text" name="cuil" class="form-control bg-light border-0" value="{{ $titular->cuil }}">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <label class="form-label fw-bold small text-muted">N° Afiliado</label>
                                        <input type="text" name="n_afiliado" class="form-control bg-light border-0" value="{{ $titular->n_afiliado }}">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label fw-bold small text-muted">Resolución</label>
                                        <input type="text" name="resolucion" class="form-control bg-light border-0" value="{{ $titular->resolucion }}">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer border-0 pb-4 pe-4">
                                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary rounded-pill px-4" style="background-color: var(--brand-primary); border: none;">Guardar Cambios</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Botón Eliminar -->
            <button class="btn btn-sm btn-outline-danger rounded-circle" title="Eliminar Referente" onclick="if(confirm('¿Eliminar definitivamente este referente y todo su grupo familiar vinculado?')) { document.getElementById('delForm{{$titular->id}}').submit(); }">
                <i class="fas fa-trash-alt"></i>
            </button>
            <form id="delForm{{$titular->id}}" action="{{ route('tenant.titulares.destroy', $titular->id) }}" method="POST" class="d-none">
                @csrf
                @method('DELETE')
            </form>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="4" class="text-center py-4 text-muted">
            <i class="fas fa-search fa-2x mb-3 text-light"></i><br>
            No se encontraron titulares o referencias con ese parámetro.
        </td>
    </tr>
@endforelse
