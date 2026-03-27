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

@forelse($docentes as $docente)
    <tr>
        <td class="align-middle">
            <div class="d-flex align-items-center gap-3">
                <div class="position-relative">
                    @if($docente->foto_perfil)
                        <img src="{{ Storage::disk('public')->url($docente->foto_perfil) }}" alt="Avatar Docente" class="rounded-circle shadow-sm" style="width: 55px; height: 55px; object-fit: cover; border: 2px solid var(--brand-primary);">
                    @else
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 55px; height: 55px; background: linear-gradient(135deg, #0ea5e9, #0369a1); font-size: 1.2rem; border: 2px solid #fff;">
                            {{ substr($docente->nombre, 0, 1) }}
                        </div>
                    @endif
                </div>
                <div>
                    <span class="fw-bold d-block" style="color: var(--text-main);">{!! highlightText($docente->nombre, $search ?? '') !!}</span>
                    <div class="d-flex gap-2 mt-1">
                        <small class="badge rounded-pill bg-light text-dark shadow-sm border" style="font-size: 0.70rem;"><i class="fas fa-id-card me-1 text-muted"></i>{!! highlightText($docente->dni, $search ?? '') !!}</small>
                        <small class="badge rounded-pill bg-light text-dark shadow-sm border" style="font-size: 0.70rem;"><i class="fas fa-envelope me-1 text-muted"></i>{!! highlightText($docente->email, $search ?? '') !!}</small>
                    </div>
                </div>
            </div>
        </td>
        <td class="align-middle">
            @if($docente->formacion)
                <span class="badge bg-info text-dark">{{ $docente->formacion->nombre }}</span>
            @else
                <span class="badge bg-secondary">Sin especialidad</span>
            @endif
        </td>
        <td class="align-middle">
            @php
                $docsVencidos = 0;
                $docsAprobados = 0;
                $totalDocs = $docente->documentos->count();

                foreach($docente->documentos as $doc) {
                    if($doc->esta_vencido) { $docsVencidos++; }
                    elseif($doc->estado == 'aprobado') { $docsAprobados++; }
                }
            @endphp
            
            <div class="d-flex align-items-center gap-2">
                @if($totalDocs == 0)
                    <span class="text-muted small"><i class="fas fa-exclamation-triangle text-warning"></i> Ficha incompleta</span>
                @else
                    <div class="progress flex-grow-1" style="height: 10px; max-width: 100px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ ($totalDocs > 0) ? ($docsAprobados / $totalDocs * 100) : 0 }}%"></div>
                        <div class="progress-bar bg-danger" role="progressbar" style="width: {{ ($totalDocs > 0) ? ($docsVencidos / $totalDocs * 100) : 0 }}%"></div>
                    </div>
                    <span class="small fw-bold">{{ $docsAprobados }}/{{ $totalDocs }}</span>
                @endif
            </div>

            @if($docsVencidos > 0)
                <div class="mt-1 small text-danger fw-bold blink-text" style="animation: blink 2s infinite;"><i class="fas fa-bell text-danger"></i> ¡{{ $docsVencidos }} doc. vencido!</div>
            @endif
        </td>
        <td class="align-middle text-end">
            <!-- Botón Documentación -->
            <button class="btn btn-sm btn-outline-warning rounded-circle me-1" title="Gestionar Documentos y Certificados" data-bs-toggle="modal" data-bs-target="#docsModal{{ $docente->id }}">
                <i class="fas fa-folder-open text-warning"></i>
            </button>
            
            <!-- Modal de Documentación -->
            <div class="modal fade text-start" id="docsModal{{ $docente->id }}" tabindex="-1" aria-hidden="true" style="color: #0f172a;">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content border-0" style="border-radius: 15px;">
                        <div class="modal-header bg-light border-0" style="border-radius: 15px 15px 0 0;">
                            <h5 class="modal-title fw-bold"><i class="fas fa-folder-open text-warning me-2"></i> Expediente Clínico/Laboral de {{ $docente->nombre }}</h5>
                            <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <!-- Listado de Documentos (Solo lectura y borrado/descarga) -->
                            <div class="table-responsive mb-4">
                                <table class="table table-sm table-bordered align-middle">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Trámite/Certificado</th>
                                            <th>Estado / Auditoría</th>
                                            <th>Vencimiento</th>
                                            <th class="text-end">Archivo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($docente->documentos as $doc)
                                            <tr>
                                                <td class="fw-bold"><i class="fas fa-file-pdf text-danger me-2"></i>{{ $doc->tipo_documento }}</td>
                                                <td>
                                                    @if($doc->estado == 'aprobado') <span class="badge bg-success">Aprobado</span>
                                                    @elseif($doc->estado == 'rechazado') <span class="badge bg-danger">Rechazado</span>
                                                    @else <span class="badge bg-secondary">Pendiente de Revisión</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($doc->fecha_vencimiento)
                                                        @if($doc->esta_vencido)
                                                            <span class="text-danger fw-bold"><i class="fas fa-times-circle"></i> Vencido ({{ $doc->fecha_vencimiento->format('d/m/Y') }})</span>
                                                        @else
                                                            <span class="text-success"><i class="fas fa-check-circle"></i> Vigente ({{ $doc->fecha_vencimiento->format('d/m/Y') }})</span>
                                                        @endif
                                                    @else
                                                        <span class="text-muted small">No vence</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <a href="{{ route('tenant.docentes.docs.download', $doc->id) }}" class="btn btn-sm btn-info text-white" title="Descargar"><i class="fas fa-download"></i></a>
                                                    
                                                    <button class="btn btn-sm btn-danger" title="Borrar Certificado" onclick="if(confirm('¿Borrar este documento permanentemente?')) { document.getElementById('delDocFisc{{$doc->id}}').submit(); }">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    <form id="delDocFisc{{$doc->id}}" action="{{ route('tenant.docentes.docs.destroy', $doc->id) }}" method="POST" class="d-none">
                                                        @csrf @method('DELETE')
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-3">El expediente físico del terapeuta está vacío.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Formulario para Anexar Nuevo -->
                            <div class="bg-light p-3 rounded border">
                                <h6 class="fw-bold mb-3"><i class="fas fa-cloud-upload-alt text-primary me-2"></i> Anexar Nuevo Fichero</h6>
                                <form action="{{ route('tenant.docentes.docs.store', $docente->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row align-items-end">
                                        <div class="col-md-4 mb-2">
                                            <label class="small fw-bold text-muted">Nombre del Documento / Tipo</label>
                                            <input type="text" name="tipo_documento" class="form-control" placeholder="Ej: Título, Buena Conducta" required>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label class="small fw-bold text-muted">¿Cuándo Caduca?</label>
                                            <input type="date" name="fecha_vencimiento" class="form-control">
                                        </div>
                                        <div class="col-md-5 mb-2">
                                            <label class="small fw-bold text-muted">Seleccionar PDF/Imagen</label>
                                            <div class="input-group">
                                                <input type="file" name="archivo" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                                                <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Subir</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-text small"><i class="fas fa-info-circle"></i> Tamaños admitidos: Jpg, Png o PDF (Max 4MB por ficha).</div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botón Editar -->
            <button class="btn btn-sm btn-outline-primary rounded-circle me-1" title="Editar Expediente" data-bs-toggle="modal" data-bs-target="#editModal{{ $docente->id }}">
                <i class="fas fa-edit"></i>
            </button>

            <!-- Modal Editar Docente -->
            <div class="modal fade text-start" id="editModal{{ $docente->id }}" tabindex="-1" aria-hidden="true" style="color: #0f172a;">
                <div class="modal-dialog">
                    <div class="modal-content border-0" style="border-radius: 15px;">
                        <div class="modal-header bg-light border-0" style="border-radius: 15px 15px 0 0;">
                            <h5 class="modal-title fw-bold">Actualizar Terapeuta</h5>
                            <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('tenant.docentes.update', $docente->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="modal-body p-4">
                                <div class="text-center mb-4">
                                    @if($docente->foto_perfil)
                                        <img src="{{ Storage::disk('public')->url($docente->foto_perfil) }}" class="rounded-circle shadow-sm mb-2" style="width: 80px; height: 80px; object-fit: cover;">
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
                                    <label class="form-label fw-bold small text-muted">Nombre del Terapeuta</label>
                                    <input type="text" name="nombre" class="form-control bg-light border-0 py-2" value="{{ $docente->nombre }}" required>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <label class="form-label fw-bold small text-muted">DNI</label>
                                        <input type="text" name="dni" class="form-control bg-light border-0 py-2" value="{{ $docente->dni }}">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label fw-bold small text-muted">Teléfono</label>
                                        <input type="text" name="telefono" class="form-control bg-light border-0 py-2" value="{{ $docente->telefono }}">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-muted">Rango / Especialidad de Formación</label>
                                    <select name="formacion_id" class="form-select bg-light border-0 py-2" required>
                                        @foreach($formaciones as $f)
                                            <option value="{{ $f->id }}" {{ $docente->formacion_id == $f->id ? 'selected' : '' }}>
                                                {{ $f->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer border-0 pb-4 pe-4">
                                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary rounded-pill px-4" style="background-color: var(--brand-primary); border: none;">Guardar Novedades</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Botón Eliminar -->
            <button class="btn btn-sm btn-outline-danger rounded-circle" title="Dar de Baja a Institución" onclick="if(confirm('Al borrar este terapeuta se eliminarán sus documentos PDF de seguridad. ¿Continuar?')) { document.getElementById('delDocForm{{$docente->id}}').submit(); }">
                <i class="fas fa-trash-alt"></i>
            </button>
            <form id="delDocForm{{$docente->id}}" action="{{ route('tenant.docentes.destroy', $docente->id) }}" method="POST" class="d-none">
                @csrf
                @method('DELETE')
            </form>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="4" class="text-center py-5 text-muted">
            <i class="fas fa-user-md fa-3x mb-3 text-light"></i><br>
            <h5 class="fw-bold">No hay Profesionales Vinculados</h5>
            <p class="small">Inscriba el padrón de trabajadores de su institución aquí.</p>
        </td>
    </tr>
@endforelse

<style>
@keyframes blink {
  0% { opacity: 1; }
  50% { opacity: 0.3; }
  100% { opacity: 1; }
}
</style>
