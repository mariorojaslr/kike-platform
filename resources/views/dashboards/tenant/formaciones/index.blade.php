@extends('layouts.tenant')

@section('title', 'Títulos y Especialidades')

@section('content')
<div class="content-card">
    <div class="card-header-styled">
        <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
            <i class="fas fa-graduation-cap text-primary"></i> 
            Catálogo de Especialidades (Roles)
        </h5>
        
        <div class="d-flex gap-2 flex-wrap mt-3 mt-md-0">
            <button class="btn btn-primary fw-bold px-3 py-2 btn-sm rounded-pill shadow-sm" style="transition: transform 0.2s;" data-bs-toggle="modal" data-bs-target="#createEspecialidadModal">
                <i class="fas fa-plus me-1"></i> Nueva Especialidad
            </button>
            <button class="btn btn-outline-secondary fw-bold px-3 py-2 btn-sm rounded-pill shadow-sm" style="transition: transform 0.2s;" data-bs-toggle="tooltip" title="Listado Oficial de Roles Múltiples">
                <i class="fas fa-lock me-1"></i> Catálogo Global
            </button>
        </div>
    </div>

    <!-- Buscador Avanzado en Tiempo Real -->
    <div class="row mb-4 bg-light p-3 rounded mx-0 border" style="background: rgba(0,0,0,0.02) !important;">
        <div class="col-md-9 mb-3 mb-md-0">
            <label class="form-label fw-bold small text-muted"><i class="fas fa-search me-1"></i> Filtro de Búsqueda Rápida</label>
            <div class="input-group input-group-lg shadow-sm">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-keyboard"></i></span>
                <input type="text" id="liveSearchInput" class="form-control border-start-0 ps-0 form-control-lg bg-white" placeholder="Ej.: Psicopedagogo, Terapista, Acompañante..." autocomplete="off">
                <span class="input-group-text bg-white" id="searchSpinner" style="display:none;">
                    <i class="fas fa-spinner fa-spin text-primary"></i>
                </span>
            </div>
            <div class="form-text mt-2"><i class="fas fa-info-circle me-1"></i>Puede buscar por la denominación del cargo habilitado oficialmente.</div>
        </div>
        
        <div class="col-md-3">
            <label class="form-label fw-bold small text-muted"><i class="fas fa-list-ol me-1"></i> Paginación</label>
            <select id="perPageSelect" class="form-select form-select-lg shadow-sm bg-white" style="cursor: pointer;">
                <option value="10">10 registros</option>
                <option value="50">50 registros</option>
                <option value="100">100 registros</option>
            </select>
        </div>
    </div>

    <!-- Tabla Dinámica -->
    <div class="table-responsive">
        <table class="table table-hover table-custom align-middle">
            <thead class="bg-light">
                <tr>
                    <th scope="col" style="border-top-left-radius: 10px;">Identificador de Tabla</th>
                    <th scope="col">Títulos / Roles del Profesional</th>
                    <th scope="col">Estado de Licencia</th>
                    <th scope="col" style="border-top-right-radius: 10px;">Acciones</th>
                </tr>
            </thead>
            <tbody id="tablaResultados" style="transition: opacity 0.3s ease;">
                <!-- Resultados iniciales desde Backend -->
                @include('dashboards.tenant.partials.formaciones_table_rows', ['formaciones' => $formaciones])
            </tbody>
        </table>
    </div>

    <!-- Contenedor Paginación Dinámica AJAX -->
    <div id="paginacionContainer" class="mt-4 d-flex justify-content-center">
        {!! $formaciones->links('pagination::bootstrap-5') !!}
    </div>
</div>

<!-- Modal Nueva Especialidad -->
<div class="modal fade" id="createEspecialidadModal" tabindex="-1" aria-hidden="true" style="color: #0f172a;">
    <div class="modal-dialog">
        <div class="modal-content border-0" style="border-radius: 15px;">
            <div class="modal-header bg-primary text-white border-0" style="border-radius: 15px 15px 0 0;">
                <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i>Nueva Especialidad</h5>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('tenant.formaciones.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Nombre del Nuevo Título o Especialidad</label>
                        <input type="text" name="nombre" class="form-control bg-light border-0" placeholder="Ej.: Psicomotricista, Nutricionista..." required>
                        <div class="form-text mt-2"><i class="fas fa-info-circle text-primary me-1"></i>Se añadirá al catálogo de tu clínica y podrás asignarlo a tus docentes/terapeutas.</div>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 pe-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold"><i class="fas fa-save me-1"></i> Guardar Especialidad</button>
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
            let route = "{{ route('tenant.formaciones.index') }}";
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
