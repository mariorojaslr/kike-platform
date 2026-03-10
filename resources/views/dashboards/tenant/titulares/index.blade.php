@extends('layouts.tenant')

@section('title', 'Referentes y Titulares')

@section('content')
<div class="content-card">
    <div class="card-header-styled">
        <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
            <i class="fas fa-users text-primary"></i> 
            Padrón de Titulares / Referentes
        </h5>
        
        <!-- Botonera Acción -->
        <div class="d-flex gap-2 flex-wrap mt-3 mt-md-0">
            <button class="btn btn-warning fw-bold px-3 py-2 btn-sm rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#importExcelModal">
                <i class="fas fa-file-import me-1"></i> Importar
            </button>
            <a href="{{ route('tenant.titulares.export.excel') }}" class="btn btn-success fw-bold px-3 py-2 btn-sm rounded-pill shadow-sm" style="transition: transform 0.2s;" data-bs-toggle="tooltip" title="Exportar Padrón Completo a .XLSX">
                <i class="fas fa-file-excel me-1"></i> Excel
            </a>
            <a href="{{ route('tenant.titulares.export.pdf') }}" target="_blank" class="btn btn-danger fw-bold px-3 py-2 btn-sm rounded-pill shadow-sm" style="transition: transform 0.2s;" data-bs-toggle="tooltip" title="Generar PDF Listado Imprimible">
                <i class="fas fa-file-pdf me-1"></i> PDF
            </a>
            <button class="btn fw-bold px-3 py-2 btn-sm rounded-pill shadow-sm text-white" style="background: var(--brand-primary); transition: transform 0.2s;" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="fas fa-plus-circle me-1"></i> Añadir Referente
            </button>
        </div>
    </div>

    <!-- Buscador Avanzado en Tiempo Real -->
    <div class="row mb-4 bg-light p-3 rounded mx-0 border" style="background: rgba(0,0,0,0.02) !important;">
        <div class="col-md-8 mb-3 mb-md-0">
            <label class="form-label fw-bold small text-muted"><i class="fas fa-search me-1"></i> Búsqueda en Vivo (LiveSearch)</label>
            <div class="input-group input-group-lg shadow-sm">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-keyboard"></i></span>
                <input type="text" id="liveSearchInput" class="form-control border-start-0 ps-0 form-control-lg bg-white" placeholder="Ej.: Nombre, DNI, Email, N° Obra Social..." autocomplete="off">
                <span class="input-group-text bg-white" id="searchSpinner" style="display:none;">
                    <i class="fas fa-spinner fa-spin text-primary"></i>
                </span>
            </div>
            <div class="form-text mt-2"><i class="fas fa-info-circle me-1"></i>Puede buscar directamente por el <strong>Número de Afiliado</strong> de Obra Social (Ej: 1 [DNI] 01).</div>
        </div>
        
        <div class="col-md-4">
            <label class="form-label fw-bold small text-muted"><i class="fas fa-list-ol me-1"></i> Paginación</label>
            <select id="perPageSelect" class="form-select form-select-lg shadow-sm bg-white" style="cursor: pointer;">
                <option value="10">10 registros por página</option>
                <option value="25">25 registros por página</option>
                <option value="50">50 registros por página</option>
                <option value="100">100 registros por página</option>
            </select>
        </div>
    </div>

    <!-- Tabla Dinámica -->
    <div class="table-responsive">
        <table class="table table-hover table-custom align-middle">
            <thead class="bg-light">
                <tr>
                    <th scope="col" style="border-top-left-radius: 10px;">Nombre y Apellido</th>
                    <th scope="col">Documentación</th>
                    <th scope="col">Contacto</th>
                    <th scope="col" class="text-end" style="border-top-right-radius: 10px;">Acciones</th>
                </tr>
            </thead>
            <tbody id="tablaResultados" style="transition: opacity 0.3s ease;">
                <!-- Resultados iniciales desde Backend -->
                @include('dashboards.tenant.partials.titulares_table_rows', ['titulares' => $titulares])
            </tbody>
        </table>
    </div>

    <!-- Contenedor Paginación Dinámica AJAX -->
    <div id="paginacionContainer" class="mt-4 d-flex justify-content-center">
        {!! $titulares->links('pagination::bootstrap-5') !!}
    </div>
</div>

<!-- Modal Creación -->
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true" style="color: #0f172a;">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header bg-light border-0" style="border-radius: 15px 15px 0 0;">
                <h5 class="modal-title fw-bold"><i class="fas fa-user-plus text-primary me-2"></i> Alta de Nuevo Titular</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('tenant.titulares.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <p class="small text-muted mb-4">Ingrese aquí la entidad paternal/maternal. Luego dentro del registro podrá atar al Alumno/Paciente como parte del grupo familiar y generar su carnet digital.</p>
                    
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
                        <input type="text" name="nombre" class="form-control bg-light border-0 py-2" placeholder="Ej. Juan Pérez" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small text-muted">DNI <span class="text-danger">*</span></label>
                            <input type="number" name="dni" class="form-control bg-light border-0 py-2" placeholder="Sin puntos" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small text-muted">CUIL</label>
                            <input type="number" name="cuil" class="form-control bg-light border-0 py-2" placeholder="Opcional">
                        </div>
                    </div>
                    <div class="mb-3 border-top pt-3 mt-3">
                        <label class="form-label fw-bold small text-muted">N° de Afiliado (SO)</label>
                        <input type="text" name="n_afiliado" class="form-control bg-light border-0 py-2" placeholder="Ej. 12345678/00">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Resolución</label>
                        <input type="text" name="resolucion" class="form-control bg-light border-0 py-2" placeholder="Nro de Resolución">
                    </div>
                </div>
                <!-- Alertas visuales dentro de formulatios -->
                <div class="px-4 pb-3">
                    <div class="alert alert-info border-0 p-2 m-0 bg-info-subtle small">
                        <i class="fas fa-info-circle fa-sm me-1"></i> El Número de Afiliado se generará del cruce del DNI que ha provisto arriba en conjunción con el Alta del Alumno.
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 pe-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold text-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn text-white rounded-pill px-4 fw-bold" style="background: var(--brand-primary); border: none;">Inscribir Padrón</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal Importar Lote Masivo Excel -->
<div class="modal fade" id="importExcelModal" tabindex="-1" aria-labelledby="importExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header bg-warning text-dark border-0">
                <h5 class="modal-title fw-bold" id="importExcelModalLabel"><i class="fas fa-file-excel me-2"></i>Importación Masiva de Titulares</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('tenant.titulares.import.excel') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4 bg-light">
                    <p class="text-muted small mb-4">
                        Ahorre tiempo subiendo un lote completo de Titulares. Por favor, descargue la plantilla vacía para asegurar que el formato (cabeceras) de su Excel coincida exactamente con nuestro estándar.
                    </p>
                    <div class="d-grid mb-4">
                        <a href="{{ route('tenant.titulares.import.template') }}" class="btn btn-outline-success fw-bold border-2">
                            <i class="fas fa-download me-2"></i>1. Descargar Plantilla Modelo (Vacía)
                        </a>
                    </div>
                    
                    <div class="mb-3">
                        <label for="archivo_excel" class="form-label fw-bold small text-muted">2. Adjuntar Archivo Excel Cargado (.xlsx)</label>
                        <input class="form-control form-control-lg border-2 shadow-sm" type="file" id="archivo_excel" name="archivo_excel" accept=".xlsx,.csv" required>
                        <div class="form-text mt-2"><i class="fas fa-info-circle me-1"></i>Asegúrese de guardar los datos sin saltos de línea extraños.</div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 pt-0">
                    <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning px-4 fw-bold"><i class="fas fa-cloud-upload-alt me-2"></i>Iniciar Importación</button>
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
        
        // Mantener última búsqueda
        let q = ''; 
        let perPage = 10;
        let timeout = null;
        // Evitador de rebotes (Debounce)
        const delay = 400; // 400ms  

        function hacerPeticion(url) {
            spinner.style.display = 'block';
            tablaCuerpo.style.opacity = '0.5';
            
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest', // Fundamental en Laravel Paginator/AJAX
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                tablaCuerpo.innerHTML = data.html;
                paginacionDiv.innerHTML = data.pagination;
                // Devolvemos el CSS y frenamos loader
                tablaCuerpo.style.opacity = '1';
                spinner.style.display = 'none';

                // Reenganchar el listener a los nuevos botones de paginación que devolvió el Laravel
                bindearPaginacion();
                
                // Reinicializar tooltips (Bootstrap 5) en contenido dinámico
                const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
                const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
            })
            .catch(err => {
                console.error('Error cargando datos en vivo:', err);
                spinner.style.display = 'none';
                tablaCuerpo.style.opacity = '1';
                alert('No se pudieron traer los datos debido a un error de conexión');
            });
        }

        function construirYActualizar() {
            // Construimos la URL manual apuntando a la ruta del JSON Index.blade 
            let route = "{{ route('tenant.titulares.index') }}";
            let qStr = '?search=' + encodeURIComponent(q) + '&per_page=' + perPage;
            hacerPeticion(route + qStr);
        }

        // Listener Buscador
        searchInput.addEventListener('input', function(e) {
            clearTimeout(timeout);
            q = e.target.value;
            timeout = setTimeout(construirYActualizar, delay);
        });

        // Listener Select Per Page
        perPageSelect.addEventListener('change', function(e) {
            perPage = parseInt(e.target.value);
            // Cuando cambian los resultados, empezamos de la pag 1 de nuevo
            construirYActualizar();
        });

        /**
         * Las opciones de paginado cambian tras cada Request (Pag. 2, 3...)
         * Esta función reemplaza el comportamiento normal del link de Laravel (que hace full page reload).
         */
        function bindearPaginacion() {
            const links = paginacionDiv.querySelectorAll('a.page-link');
            links.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    let pageUrl = this.getAttribute('href'); 
                    // Ya trae el string: ?page=2&search=xxx&per_page=10
                    hacerPeticion(pageUrl);
                });
            });
        }
        
        // Binding inicial para cuando carga la página directo desde el Controller
        bindearPaginacion();
        
        // Tooltips Globales Initial
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    });
</script>
@endpush
