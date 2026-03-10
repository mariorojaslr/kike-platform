@forelse($familiares as $familiar)
    <tr>
        <td class="align-middle">
            <div class="d-flex align-items-center gap-3">
                <div class="position-relative">
                    @if($familiar->foto_perfil)
                        <img src="{{ Storage::disk('public')->url($familiar->foto_perfil) }}" alt="Avatar Alumno" class="rounded-circle shadow-sm" style="width: 55px; height: 55px; object-fit: cover; border: 2px solid var(--brand-secondary);">
                    @else
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 55px; height: 55px; background: linear-gradient(135deg, #10b981, #059669); font-size: 1.2rem; border: 2px solid #fff;">
                            {{ substr($familiar->nombre, 0, 1) }}
                        </div>
                    @endif
                </div>
                <div>
                    <h6 class="mb-0 fw-bold" style="color: var(--text-main);">{{ $familiar->nombre }}</h6>
                    <span class="badge rounded-pill bg-light text-dark shadow-sm border mt-1" style="font-size: 0.75rem;"><i class="fas fa-id-card me-1 text-muted"></i> DNI: {{ $familiar->dni ?? 'No especificado' }}</span>
                </div>
            </div>
        </td>
        <td class="align-middle text-center">
            <!-- Nro de Afiliado Generado Dinámicamente Obra Social -->
            <span class="badge px-3 py-2 fs-6 shadow-sm" style="background-color: rgba(59, 130, 246, 0.1); color: #2563eb; border: 1px solid rgba(59, 130, 246, 0.3);">
                <i class="fas fa-barcode me-2 text-primary"></i>{{ $familiar->numero_afiliado }}
            </span>
        </td>
        <td class="align-middle">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 35px; height: 35px; background: #64748b;">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div>
                    <span class="fw-bold text-dark d-block">{{ $familiar->titular->nombre ?? 'Sin Titular' }}</span>
                    <span class="small text-muted">Vínculo: {{ $familiar->parentesco ?? 'Desconocido' }}</span>
                </div>
            </div>
        </td>
        <td class="align-middle">
            @if($familiar->tiene_patologia && $familiar->diagnostico)
                <span class="badge bg-warning text-dark"><i class="fas fa-notes-medical me-1"></i>{{ $familiar->diagnostico->nombre }}</span>
                <br><small class="text-muted">Cod: {{ $familiar->diagnostico->codigo }}</small>
            @else
                <span class="badge bg-light text-muted border">Paciente regular</span>
            @endif
        </td>
        <td class="align-middle text-end">
            <!-- Botón Editar -->
            <button class="btn btn-sm btn-outline-primary rounded-circle me-1" title="Editar Expediente" data-bs-toggle="modal" data-bs-target="#editModalFamiliar{{ $familiar->id }}">
                <i class="fas fa-edit"></i>
            </button>

            <!-- Modal Editar -->
            <div class="modal fade text-start" id="editModalFamiliar{{ $familiar->id }}" tabindex="-1" aria-hidden="true" style="color: #0f172a;">
                <div class="modal-dialog">
                    <div class="modal-content border-0" style="border-radius: 15px;">
                        <div class="modal-header bg-light border-0" style="border-radius: 15px 15px 0 0;">
                            <h5 class="modal-title fw-bold">Actualizar Datos Paciente</h5>
                            <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('tenant.familiares.update', $familiar->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="modal-body p-4">
                                <div class="text-center mb-4">
                                    @if($familiar->foto_perfil)
                                        <img src="{{ Storage::disk('public')->url($familiar->foto_perfil) }}" class="rounded-circle shadow-sm mb-2" style="width: 80px; height: 80px; object-fit: cover;">
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
                                    <label class="form-label fw-bold small text-muted">Grupo Familiar (Depende de)</label>
                                    <select name="titular_id" class="form-select bg-light border-0 py-2" required>
                                        @foreach($titularesDisponibles as $t)
                                            <option value="{{ $t->id }}" {{ $familiar->titular_id == $t->id ? 'selected' : '' }}>
                                                {{ $t->nombre }} (DNI: {{ $t->dni }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-12 mb-3">
                                        <label class="form-label fw-bold small text-muted">Nombre del Paciente/Alumno</label>
                                        <input type="text" name="nombre" class="form-control bg-light border-0 py-2" value="{{ $familiar->nombre }}" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label fw-bold small text-muted">DNI Paciente</label>
                                        <input type="text" name="dni" class="form-control bg-light border-0 py-2" value="{{ $familiar->dni }}">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label fw-bold small text-muted">Parentesco</label>
                                        <select name="parentesco" class="form-select bg-light border-0 py-2">
                                            <option value="Hijo" {{ $familiar->parentesco == 'Hijo' ? 'selected' : '' }}>Hijo/a</option>
                                            <option value="Hermano" {{ $familiar->parentesco == 'Hermano' ? 'selected' : '' }}>Hermano/a</option>
                                            <option value="Conyuge" {{ $familiar->parentesco == 'Conyuge' ? 'selected' : '' }}>Cónyuge</option>
                                            <option value="Otro" {{ $familiar->parentesco == 'Otro' ? 'selected' : '' }}>Otro</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="mb-3 border-top pt-3 mt-3">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" role="switch" id="checkPatologiaUpd{{ $familiar->id }}" name="tiene_patologia" value="1" {{ $familiar->tiene_patologia ? 'checked' : '' }} onchange="document.getElementById('divDiagUpd{{ $familiar->id }}').style.display = this.checked ? 'block' : 'none';">
                                        <label class="form-check-label fw-bold text-muted small" for="checkPatologiaUpd{{ $familiar->id }}">Tiene diagnóstico clínico formal</label>
                                    </div>
                                    
                                    <div id="divDiagUpd{{ $familiar->id }}" style="display: {{ $familiar->tiene_patologia ? 'block' : 'none' }};">
                                        <label class="form-label fw-bold small text-muted">Selección de Diagnóstico Oficial</label>
                                        <select name="diagnostico_id" class="form-select bg-light border-0 py-2">
                                            <option value="">- Quitar Diagnóstico -</option>
                                            @foreach($diagnosticosDisponibles as $d)
                                                <option value="{{ $d->id }}" {{ $familiar->diagnostico_id == $d->id ? 'selected' : '' }}>
                                                    {{ $d->nombre }} ({{ $d->codigo }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer border-0 pb-4 pe-4">
                                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Mantener Cerrar</button>
                                <button type="submit" class="btn btn-primary rounded-pill px-4" style="background-color: var(--brand-primary); border: none;">Guardar Novedades</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Botón Eliminar -->
            <button class="btn btn-sm btn-outline-danger rounded-circle" title="Desvincular Paciente" onclick="if(confirm('¿Toda la data escolar, facturación social y sesiones se perderán. Está seguro?')) { document.getElementById('delFamiliarForm{{$familiar->id}}').submit(); }">
                <i class="fas fa-user-times"></i>
            </button>
            <form id="delFamiliarForm{{$familiar->id}}" action="{{ route('tenant.familiares.destroy', $familiar->id) }}" method="POST" class="d-none">
                @csrf
                @method('DELETE')
            </form>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="text-center py-5 text-muted">
            <i class="fas fa-search-minus fa-3x mb-3 text-light"></i><br>
            <h5 class="fw-bold">No se encontraron Alumnos/Pacientes</h5>
            <p class="small">No hay historiales que coincidan con la búsqueda, o esta institución aún no tiene matrículas cargadas.</p>
        </td>
    </tr>
@endforelse
