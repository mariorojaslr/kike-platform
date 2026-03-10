@extends('layouts.tenant')

@section('title', 'Catálogo de Diagnósticos Clínicos')

@section('content')
<div class="content-card">
    <div class="card-header-styled">
        <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
            <i class="fas fa-notes-medical text-primary"></i> 
            Catálogo Global de Patologías (CIE / DSM)
        </h5>
        
        <div class="d-flex gap-2 flex-wrap mt-3 mt-md-0">
            <button class="btn btn-outline-secondary fw-bold px-3 py-2 btn-sm rounded-pill shadow-sm" style="transition: transform 0.2s;" data-bs-toggle="tooltip" title="Listado Oficial Sincronizado">
                <i class="fas fa-lock me-1"></i> Solo Lectura
            </button>
        </div>
    </div>

    <!-- Buscador Avanzado en Tiempo Real -->
    <div class="row mb-4 bg-light p-3 rounded mx-0 border" style="background: rgba(0,0,0,0.02) !important;">
        <div class="col-md-9 mb-3 mb-md-0">
            <label class="form-label fw-bold small text-muted"><i class="fas fa-search me-1"></i> Filtro de Búsqueda Rápida</label>
            <div class="input-group input-group-lg shadow-sm">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-keyboard"></i></span>
                <input type="text" id="liveSearchInput" class="form-control border-start-0 ps-0 form-control-lg bg-white" placeholder="Ej.: Autismo, TDAH, F84..." autocomplete="off">
                <span class="input-group-text bg-white" id="searchSpinner" style="display:none;">
                    <i class="fas fa-spinner fa-spin text-primary"></i>
                </span>
            </div>
            <div class="form-text mt-2"><i class="fas fa-info-circle me-1"></i>Puede buscar por el Código Oficial o por el Nombre Científico.</div>
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
                    <th scope="col" style="border-top-left-radius: 10px;">Código Técnico</th>
                    <th scope="col">Denominación del Diagnóstico Público</th>
                    <th scope="col" style="border-top-right-radius: 10px;">Estado en Sistema</th>
                </tr>
            </thead>
            <tbody id="tablaResultados" style="transition: opacity 0.3s ease;">
                <!-- Resultados iniciales desde Backend -->
                @include('dashboards.tenant.partials.diagnosticos_table_rows', ['diagnosticos' => $diagnosticos])
            </tbody>
        </table>
    </div>

    <!-- Contenedor Paginación Dinámica AJAX -->
    <div id="paginacionContainer" class="mt-4 d-flex justify-content-center">
        {!! $diagnosticos->links('pagination::bootstrap-5') !!}
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
            let route = "{{ route('tenant.diagnosticos.index') }}";
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
