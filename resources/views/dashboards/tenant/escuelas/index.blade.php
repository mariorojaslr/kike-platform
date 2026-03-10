@extends('layouts.tenant')

@section('title', 'Terapeutas y Docentes')

@section('content')
<div class="content-card">
    <div class="card-header-styled">
        <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
            <i class="fas fa-school text-primary"></i> 
            Padrón de Instituciones (Escuelas)
        </h5>
        
        <div class="d-flex gap-2 flex-wrap mt-3 mt-md-0">
            <button class="btn btn-warning fw-bold px-3 py-2 btn-sm rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#importExcelModal">
                <i class="fas fa-file-import me-1"></i> Importar Masivo
            </button>
            <a href="{{ route('tenant.escuelas.export.excel') }}" class="btn btn-success fw-bold px-3 py-2 btn-sm rounded-pill shadow-sm">
                <i class="fas fa-file-excel me-1"></i> Bajar Excel
            </a>
            <a href="{{ route('tenant.escuelas.export.pdf') }}" target="_blank" class="btn btn-danger fw-bold px-3 py-2 btn-sm rounded-pill shadow-sm">
                <i class="fas fa-file-pdf me-1"></i> Imprimir PDF
            </a>
            <button class="btn fw-bold px-3 py-2 btn-sm rounded-pill shadow-sm text-white" style="background: var(--brand-primary);" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="fas fa-plus-circle me-1"></i> Nueva Escuela
            </button>
        </div>
    </div>

    <!-- Buscador Avanzado en Tiempo Real -->
    <div class="row mb-4 bg-light p-3 rounded mx-0 border" style="background: rgba(0,0,0,0.02) !important;">
        <div class="col-md-9 mb-3 mb-md-0">
            <label class="form-label fw-bold small text-muted"><i class="fas fa-search me-1"></i> Búsqueda Rápida (LiveSearch)</label>
            <div class="input-group input-group-lg shadow-sm">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-keyboard"></i></span>
                <input type="text" id="liveSearchInput" class="form-control border-start-0 ps-0 form-control-lg bg-white" placeholder="Ej.: Nombre Escuela, CUE o Email..." autocomplete="off">
                <span class="input-group-text bg-white" id="searchSpinner" style="display:none;">
                    <i class="fas fa-spinner fa-spin text-primary"></i>
                </span>
            </div>
        </div>
        
        <div class="col-md-3">
            <label class="form-label fw-bold small text-muted"><i class="fas fa-list-ol me-1"></i> Paginación</label>
            <select id="perPageSelect" class="form-select form-select-lg shadow-sm bg-white">
                <option value="10">10 resultados</option>
                <option value="25">25 resultados</option>
                <option value="50">50 resultados</option>
            </select>
        </div>
    </div>

    <!-- Tabla Dinámica -->
    <div class="table-responsive">
        <table class="table table-hover table-custom align-middle">
            <thead class="bg-light">
                <tr>
                    <th scope="col" style="border-top-left-radius: 10px;">Institución & Info Base</th>
                    <th scope="col">Contacto / Director</th>
                    <th scope="col">Estado</th>
                    <th scope="col" class="text-end" style="border-top-right-radius: 10px;">Acciones</th>
                </tr>
            </thead>
            <tbody id="tablaResultados" style="transition: opacity 0.3s ease;">
                @include('dashboards.tenant.partials.escuelas_table_rows', ['escuelas' => $escuelas])
            </tbody>
        </table>
    </div>

    <div id="paginacionContainer" class="mt-4 d-flex justify-content-center">
        {!! $escuelas->links('pagination::bootstrap-5') !!}
    </div>
</div>

<!-- Modal Creación Institución -->
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true" style="color: #0f172a;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header bg-light border-0" style="border-radius: 15px 15px 0 0;">
                <h5 class="modal-title fw-bold"><i class="fas fa-school text-primary me-2"></i> Dar de Alta Institución</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('tenant.escuelas.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <p class="small text-muted mb-4">Ingrese la información básica de la escuela para registrar a los pacientes y los docentes que asistan allí.</p>

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-bold small text-muted">Nombre de la Institución <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control bg-light border-0 py-2" required placeholder="Ej. Colegio Sagrada Familia">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold small text-muted">CUE</label>
                            <input type="text" name="cue" class="form-control bg-light border-0 py-2" placeholder="Código Único">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="form-label fw-bold small text-muted">Teléfono Fijo / Móvil</label>
                            <input type="text" name="telefono" class="form-control bg-light border-0 py-2" placeholder="ej. 011-4455-6677">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Correo Electrónico (Institucional)</label>
                            <input type="email" name="email" class="form-control bg-light border-0 py-2" placeholder="info@escuela.com">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="form-label fw-bold small text-muted">Dirección / Ubicación Fsica</label>
                            <input type="text" name="direccion" class="form-control bg-light border-0 py-2" placeholder="Ej. Av. Sarmiento 1200">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Datos Confidenciales (Director/Contacto)</label>
                            <input type="text" name="contacto_principal" class="form-control bg-light border-0 py-2" placeholder="Sra. María Directora">
                        </div>
                    </div>

                </div>
                <div class="modal-footer border-0 pb-4 pe-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold text-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn text-white rounded-pill px-4 fw-bold" style="background: var(--brand-primary); border: none;">Guardar Escuela</button>
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
                <h5 class="modal-title fw-bold" id="importExcelModalLabel"><i class="fas fa-file-excel me-2"></i>Importación Masiva de Escuelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('tenant.escuelas.import.excel') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4 bg-light">
                    <p class="text-muted small mb-4">
                        Suba directamente un padrón completo o base de datos de instituciones mediante un archivo (Excel/CSV).
                    </p>
                    <div class="d-grid mb-4">
                        <a href="{{ route('tenant.escuelas.import.template') }}" class="btn btn-outline-success fw-bold border-2">
                            <i class="fas fa-download me-2"></i>1. Descargar Plantilla Matriz de Excel
                        </a>
                    </div>
                    
                    <div class="mb-3">
                        <label for="archivo_excel" class="form-label fw-bold small text-muted">2. Adjuntar el Archivo (.csv/.xlsx)</label>
                        <input class="form-control form-control-lg border-2 shadow-sm" type="file" id="archivo_excel" name="archivo_excel" accept=".xlsx,.csv" required>
                        <div class="form-text mt-2"><i class="fas fa-info-circle me-1"></i>Siga exactamente el orden de columnas de la plantilla matriz para un éxito garantizado.</div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 pt-0 d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary px-4 fw-bold shadow-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning px-4 fw-bold shadow-sm"><i class="fas fa-magic me-2"></i>Subir y Sincronizar</button>
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
                console.error('Error cargando datos:', err);
                spinner.style.display = 'none';
                tablaCuerpo.style.opacity = '1';
            });
        }

        function construirYActualizar() {
            let route = "{{ route('tenant.escuelas.index') }}";
            let qStr = '?search=' + encodeURIComponent(q) + '&per_page=' + perPage;
            hacerPeticion(route + qStr);
        }

        if(searchInput) {
            searchInput.addEventListener('input', function(e) {
                clearTimeout(timeout);
                q = e.target.value;
                timeout = setTimeout(construirYActualizar, delay);
            });
        }

        if(perPageSelect) {
            perPageSelect.addEventListener('change', function(e) {
                perPage = parseInt(e.target.value);
                construirYActualizar();
            });
        }

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
