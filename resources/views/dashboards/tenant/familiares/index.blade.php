@extends('layouts.tenant')

@section('title', 'Alumnos y Pacientes (Familiares)')

@section('content')
<div class="content-card">
    <div class="card-header-styled">
        <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
            <i class="fas fa-child text-primary"></i> 
            Padrón de Alumnos y Pacientes
        </h5>
        
        <div class="d-flex gap-2 flex-wrap mt-3 mt-md-0">
            <button class="btn btn-warning fw-bold px-3 py-2 btn-sm rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#importExcelModal">
                <i class="fas fa-file-import me-1"></i> Importar
            </button>
            <a href="{{ route('tenant.familiares.export.excel') }}" class="btn btn-success fw-bold px-3 py-2 btn-sm rounded-pill shadow-sm">
                <i class="fas fa-file-excel me-1"></i> Excel
            </a>
            <a href="{{ route('tenant.familiares.export.pdf') }}" target="_blank" class="btn btn-danger fw-bold px-3 py-2 btn-sm rounded-pill shadow-sm">
                <i class="fas fa-file-pdf me-1"></i> PDF
            </a>
            <button class="btn fw-bold px-3 py-2 btn-sm rounded-pill shadow-sm text-white" style="background: var(--brand-primary);" data-bs-toggle="modal" data-bs-target="#createFamiliarModal">
                <i class="fas fa-plus-circle me-1"></i> Inscribir Alumno
            </button>
        </div>
    </div>

    <!-- Buscador Avanzado en Tiempo Real -->
    <div class="row mb-4 bg-light p-3 rounded mx-0 border" style="background: rgba(0,0,0,0.02) !important;">
        <div class="col-md-8 mb-3 mb-md-0">
            <label class="form-label fw-bold small text-muted"><i class="fas fa-search me-1"></i> Búsqueda en Vivo (LiveSearch)</label>
            <div class="input-group input-group-lg shadow-sm">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-keyboard"></i></span>
                <input type="text" id="liveSearchInput" class="form-control border-start-0 ps-0 form-control-lg bg-white" placeholder="Ej.: Nombre Alumno, DNI, DNI Titular o N° Obra Social..." autocomplete="off">
                <span class="input-group-text bg-white" id="searchSpinner" style="display:none;">
                    <i class="fas fa-spinner fa-spin text-primary"></i>
                </span>
            </div>
            <div class="form-text mt-2">Puede buscar por <strong>Número de Afiliado</strong> (Ej: 1 [DNI] 01).</div>
        </div>
        
        <div class="col-md-4">
            <label class="form-label fw-bold small text-muted"><i class="fas fa-list-ol me-1"></i> Paginación</label>
            <select id="perPageSelect" class="form-select form-select-lg shadow-sm bg-white">
                <option value="10">10 registros por página</option>
                <option value="25">25 registros por página</option>
                <option value="50">50 registros por página</option>
            </select>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-custom align-middle">
            <thead class="bg-light">
                <tr>
                    <th scope="col">Paciente/Alumno</th>
                    <th scope="col">N° Afiliado (SO)</th>
                    <th scope="col">Grupo Familiar (Titular)</th>
                    <th scope="col">Diagnóstico / Condición</th>
                    <th scope="col" class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody id="tablaResultados" style="transition: opacity 0.3s ease;">
                @include('dashboards.tenant.partials.familiares_table_rows', ['familiares' => $familiares])
            </tbody>
        </table>
    </div>

    <div id="paginacionContainer" class="mt-4 d-flex justify-content-center">
        {!! $familiares->links('pagination::bootstrap-5') !!}
    </div>
</div>

<!-- Modal Creación -->
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true" style="color: #0f172a;">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header bg-light border-0" style="border-radius: 15px 15px 0 0;">
                <h5 class="modal-title fw-bold"><i class="fas fa-user-plus text-primary me-2"></i> Alta de Paciente (Familiar)</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('tenant.familiares.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <p class="small text-muted mb-4 d-flex align-items-center gap-2">
                        <i class="fas fa-info-circle text-info fa-lg"></i>
                        Vincule este paciente al Titular correspondiente para generar su Número de Obra Social.
                    </p>
                    
                    <div class="text-center mb-4">
                        <div class="rounded-circle d-inline-flex border shadow-sm align-items-center justify-content-center bg-light text-muted mb-2" style="width: 80px; height: 80px;">
                            <i class="fas fa-camera fa-2x"></i>
                        </div>
                        <div>
                            <label class="btn btn-sm btn-outline-secondary rounded-pill" style="cursor: pointer;">
                                <i class="fas fa-upload me-1"></i> Subir Fotografía
                                <input type="file" name="foto_perfil" class="d-none" accept=".jpg,.jpeg,.png">
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Vincular al Titular / Padre <span class="text-danger">*</span></label>
                        <select name="titular_id" class="form-select bg-light border-0 py-2" required>
                            <option value="">-- Seleccionar Titular --</option>
                            @foreach($titularesDisponibles as $t)
                                <option value="{{ $t->id }}">{{ $t->nombre }} (DNI: {{ $t->dni }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Nombre del Alumno/Paciente <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control bg-light border-0 py-2" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small text-muted">DNI del Alumno</label>
                            <input type="number" name="dni" class="form-control bg-light border-0 py-2">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small text-muted">Parentesco</label>
                            <select name="parentesco" class="form-select bg-light border-0 py-2">
                                <option value="Hijo">Hijo/a</option>
                                <option value="Hermano">Hermano/a</option>
                                <option value="Conyuge">Cónyuge</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 border-top pt-3 mt-3">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="checkPatologiaStore" name="tiene_patologia" value="1" onchange="document.getElementById('divDiagStore').style.display = this.checked ? 'block' : 'none';">
                            <label class="form-check-label fw-bold text-muted small" for="checkPatologiaStore">¿El paciente presenta un diagnóstico?</label>
                        </div>
                        
                        <div id="divDiagStore" style="display: none;">
                            <label class="form-label fw-bold small text-muted">Diagnóstico Clínico</label>
                            <select name="diagnostico_id" class="form-select bg-light border-0 py-2">
                                <option value="">-- Elegir Diagnóstico Oficial --</option>
                                @foreach($diagnosticosDisponibles as $d)
                                    <option value="{{ $d->id }}">{{ $d->nombre }} - {{ $d->codigo }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 pe-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold text-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn text-white rounded-pill px-4 fw-bold" style="background: var(--brand-primary); border: none;">Guardar Paciente</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Importar Lote Masivo Excel -->
<div class="modal fade" id="importExcelModal" tabindex="-1" aria-labelledby="importExcelModalLabel" aria-hidden="true" style="color:#0f172a;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header bg-warning text-dark border-0">
                <h5 class="modal-title fw-bold" id="importExcelModalLabel"><i class="fas fa-file-excel me-2"></i>Importación Masiva de Alumnos/Pacientes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('tenant.familiares.import.excel') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4 bg-light">
                    <p class="text-muted small mb-4">
                        Ahorre tiempo subiendo un lote completo de Alumnos. Por favor, descargue la plantilla vacía para asegurar que el formato (cabeceras) de su Excel coincida exactamente con nuestro estándar. 
                        <br><strong>Importante:</strong> Para que la importación funcione, los Titulares/Padres declarados en el Excel (DNI) deben estar inscriptos previamente en el sistema.
                    </p>
                    <div class="d-grid mb-4">
                        <a href="{{ route('tenant.familiares.import.template') }}" class="btn btn-outline-success fw-bold border-2">
                            <i class="fas fa-download me-2"></i>1. Descargar Plantilla Modelo (Vacía)
                        </a>
                    </div>
                    
                    <div class="mb-3">
                        <label for="archivo_excel" class="form-label fw-bold small text-muted">2. Adjuntar Archivo Excel Cargado (.xlsx)</label>
                        <input class="form-control form-control-lg border-2 shadow-sm" type="file" id="archivo_excel" name="archivo_excel" accept=".xlsx,.csv" required>
                        <div class="form-text mt-2"><i class="fas fa-info-circle me-1"></i>Asegúrese de guardar los datos sin saltos de línea extraños.</div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 pt-0 d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary px-4 fw-bold shadow-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning px-4 fw-bold shadow-sm"><i class="fas fa-cloud-upload-alt me-2"></i>Iniciar Importación</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('liveSearchInput');
        const perPageSelect = document.getElementById('perPageSelect');
        const tablaCuerpo = document.getElementById('tablaResultados');
        const paginacionDiv = document.getElementById('paginacionContainer');
        const spinner = document.getElementById('searchSpinner');
        
        let q = ''; 
        let perPage = 10;
        let timeout = null;
        const delay = 400; 

        function hacerPeticion(url) {
            spinner.style.display = 'block';
            tablaCuerpo.style.opacity = '0.5';
            
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                tablaCuerpo.innerHTML = data.html;
                paginacionDiv.innerHTML = data.pagination;
                tablaCuerpo.style.opacity = '1';
                spinner.style.display = 'none';
                bindearPaginacion();
            })
            .catch(err => {
                console.error('Error cargando datos en vivo:', err);
                spinner.style.display = 'none';
                tablaCuerpo.style.opacity = '1';
            });
        }

        function construirYActualizar() {
            let route = "{{ route('tenant.familiares.index') }}";
            let qStr = '?search=' + encodeURIComponent(q) + '&per_page=' + perPage;
            hacerPeticion(route + qStr);
        }

        searchInput.addEventListener('input', function(e) {
            clearTimeout(timeout);
            q = e.target.value;
            timeout = setTimeout(construirYActualizar, delay);
        });

        perPageSelect.addEventListener('change', function(e) {
            perPage = parseInt(e.target.value);
            construirYActualizar();
        });

        function bindearPaginacion() {
            const links = paginacionDiv.querySelectorAll('a.page-link');
            links.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    let pageUrl = this.getAttribute('href'); 
                    hacerPeticion(pageUrl);
                });
            });
        }
        bindearPaginacion();
    });
</script>
@endpush
