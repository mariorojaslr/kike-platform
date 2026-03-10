@extends('layouts.tenant')

@section('title', 'Terapeutas y Docentes')

@section('content')
<div class="content-card">
    <div class="card-header-styled">
        <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
            <i class="fas fa-chalkboard-teacher text-primary"></i> 
            Padrón de Docentes y Profesionales
        </h5>
        
        <div class="d-flex gap-2 flex-wrap mt-3 mt-md-0">
            <button class="btn btn-warning fw-bold px-3 py-2 btn-sm rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#importExcelModal">
                <i class="fas fa-file-import me-1"></i> Importar
            </button>
            <a href="{{ route('tenant.docentes.export.excel') }}" class="btn btn-success fw-bold px-3 py-2 btn-sm rounded-pill shadow-sm">
                <i class="fas fa-file-excel me-1"></i> Excel
            </a>
            <a href="{{ route('tenant.docentes.export.pdf') }}" target="_blank" class="btn btn-danger fw-bold px-3 py-2 btn-sm rounded-pill shadow-sm">
                <i class="fas fa-file-pdf me-1"></i> PDF
            </a>
            <button class="btn fw-bold px-3 py-2 btn-sm rounded-pill shadow-sm text-white" style="background: var(--brand-primary);" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="fas fa-user-plus me-1"></i> Añadir Profesional
            </button>
        </div>
    </div>

    <!-- Buscador Avanzado en Tiempo Real -->
    <div class="row mb-4 bg-light p-3 rounded mx-0 border" style="background: rgba(0,0,0,0.02) !important;">
        <div class="col-md-9 mb-3 mb-md-0">
            <label class="form-label fw-bold small text-muted"><i class="fas fa-search me-1"></i> Búsqueda en Vivo (LiveSearch)</label>
            <div class="input-group input-group-lg shadow-sm">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-keyboard"></i></span>
                <input type="text" id="liveSearchInput" class="form-control border-start-0 ps-0 form-control-lg bg-white" placeholder="Ej.: Apellido, Nombre o DNI del Docente..." autocomplete="off">
                <span class="input-group-text bg-white" id="searchSpinner" style="display:none;">
                    <i class="fas fa-spinner fa-spin text-primary"></i>
                </span>
            </div>
        </div>
        
        <div class="col-md-3">
            <label class="form-label fw-bold small text-muted"><i class="fas fa-list-ol me-1"></i> Paginación</label>
            <select id="perPageSelect" class="form-select form-select-lg shadow-sm bg-white">
                <option value="10">10 por página</option>
                <option value="25">25 por página</option>
                <option value="50">50 por página</option>
            </select>
        </div>
    </div>

    <!-- Tabla Dinámica -->
    <div class="table-responsive">
        <table class="table table-hover table-custom align-middle">
            <thead class="bg-light">
                <tr>
                    <th scope="col" style="border-top-left-radius: 10px;">Profesional / Contacto</th>
                    <th scope="col">Especialidad / Rol</th>
                    <th scope="col">Documentación & Vencimientos</th>
                    <th scope="col" class="text-end" style="border-top-right-radius: 10px;">Acciones</th>
                </tr>
            </thead>
            <tbody id="tablaResultados" style="transition: opacity 0.3s ease;">
                @include('dashboards.tenant.partials.docentes_table_rows', ['docentes' => $docentes])
            </tbody>
        </table>
    </div>

    <div id="paginacionContainer" class="mt-4 d-flex justify-content-center">
        {!! $docentes->links('pagination::bootstrap-5') !!}
    </div>
</div>

<!-- Modal Creación Docente -->
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true" style="color: #0f172a;">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header bg-light border-0" style="border-radius: 15px 15px 0 0;">
                <h5 class="modal-title fw-bold"><i class="fas fa-user-md text-primary me-2"></i> Alta de Nuevo Docente</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('tenant.docentes.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <p class="small text-muted mb-4">Complete la matriz de datos para dar de alta al profesional. Podrá pedirle certificados y habilitaciones luedo de crearlo en las "Herramientas" (Engranaje) de la tabla.</p>
                    
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
                        <label class="form-label fw-bold small text-muted">Nombre Completo <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control bg-light border-0 py-2" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="form-label fw-bold small text-muted">Documento (DNI) <span class="text-danger">*</span></label>
                            <input type="number" name="dni" class="form-control bg-light border-0 py-2" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Teléfono Móvil</label>
                            <input type="text" name="telefono" class="form-control bg-light border-0 py-2">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Correo Electrónico (Acceso a la App) <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control bg-light border-0 py-2" required>
                        <div class="form-text small text-info"><i class="fas fa-info-circle"></i> Será utilizado para registrar los escaneos en la App Móvil.</div>
                    </div>

                    <div class="mb-3 border-top pt-3 mt-3">
                        <label class="form-label fw-bold small text-muted">Rango / Especialidad de Formación <span class="text-danger">*</span></label>
                        <select name="formacion_id" class="form-select bg-light border-0 py-2" required>
                            <option value="">-- Seleccionar Título --</option>
                            @foreach($formaciones as $formacion)
                                <option value="{{ $formacion->id }}">{{ $formacion->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 pe-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold text-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn text-white rounded-pill px-4 fw-bold" style="background: var(--brand-primary); border: none;">Añadir Profesional</button>
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
                <h5 class="modal-title fw-bold" id="importExcelModalLabel"><i class="fas fa-file-excel me-2"></i>Importación Masiva de Profesionales</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('tenant.docentes.import.excel') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4 bg-light">
                    <p class="text-muted small mb-4">
                        Ahorre tiempo subiendo el padrón completo de Docentes/Terapeutas desde un solo archivo Excel (.xlsx).
                    </p>
                    <div class="d-grid mb-4">
                        <a href="{{ route('tenant.docentes.import.template') }}" class="btn btn-outline-success fw-bold border-2">
                            <i class="fas fa-download me-2"></i>1. Descargar Plantilla Modelo (Vacía)
                        </a>
                    </div>
                    
                    <div class="mb-3">
                        <label for="archivo_excel" class="form-label fw-bold small text-muted">2. Adjuntar Archivo Excel Cargado (.xlsx)</label>
                        <input class="form-control form-control-lg border-2 shadow-sm" type="file" id="archivo_excel" name="archivo_excel" accept=".xlsx,.csv" required>
                        <div class="form-text mt-2"><i class="fas fa-info-circle me-1"></i>Asegúrese de guardar los datos con el formato descargado en la plantilla.</div>
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
                const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                [...tooltips].map(t => new bootstrap.Tooltip(t));
            })
            .catch(err => {
                console.error('Error cargando datos en vivo:', err);
                spinner.style.display = 'none';
                tablaCuerpo.style.opacity = '1';
            });
        }

        function construirYActualizar() {
            let route = "{{ route('tenant.docentes.index') }}";
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
        const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        [...tooltips].map(t => new bootstrap.Tooltip(t));
    });
</script>
@endpush
