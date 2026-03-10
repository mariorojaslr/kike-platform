<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KIKE | Base Geográfica</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primario: #0f172a; /* Slate 900 */
            --secundario: #3b82f6; /* Blue 500 */
            --acento: #10b981; /* Emerald 500 */
            --fondo: #f1f5f9; /* Slate 100 */
            --card-bg: #ffffff;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--fondo);
            color: var(--primario);
        }

        /* --- Sidebar --- */
        .sidebar {
            width: 250px;
            background-color: var(--primario);
            color: white;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            padding-top: 20px;
            z-index: 1000;
        }

        .sidebar-brand {
            font-size: 1.8rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 30px;
            letter-spacing: 1px;
            color: white;
            text-decoration: none;
            display: block;
        }

        .sidebar-nav .nav-link {
            color: rgba(255,255,255,0.7);
            padding: 12px 20px;
            font-weight: 500;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-nav .nav-link:hover, .sidebar-nav .nav-link.active {
            color: white;
            background-color: rgba(255,255,255,0.1);
            border-left: 4px solid var(--secundario);
        }

        /* --- Main Content --- */
        .main-content {
            margin-left: 250px;
            padding: 30px;
        }

        /* Topbar */
        .topbar {
            background: var(--card-bg);
            padding: 15px 30px;
            border-radius: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }

        .card-header-styled {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e2e8f0;
        }

        .content-card {
            background: var(--card-bg);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        
        .table-custom th {
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            font-size: 0.8rem;
            border-bottom: 2px solid #e2e8f0;
        }

        /* --- RESPONSIVE DESIGN --- */
        .mobile-toggle { display: none; background: none; border: none; font-size: 1.5rem; color: var(--primario); }

        @media (max-width: 991px) {
            .sidebar { transform: translateX(-100%); transition: transform 0.3s ease-in-out; }
            .sidebar.active { transform: translateX(0); }
            .main-content { margin-left: 0; padding: 15px; }
            .mobile-toggle { display: block; }
            .topbar { padding: 15px; flex-direction: column; align-items: flex-start; gap: 15px; }
            .card-header-styled { flex-direction: column; align-items: flex-start; gap: 15px; }
            .search-controls { width: 100% !important; flex-direction: column; }
        }
    </style>
</head>
<body>

    <!-- Sidebar Overlay -->
    <div id="sidebarOverlay" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.5); z-index:999;"></div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="d-flex justify-content-between align-items-center px-4 mb-4">
            <a href="#" class="sidebar-brand mb-0 w-100">
                <i class="fas fa-layer-group me-2"></i> KIKE
            </a>
            <button class="btn btn-sm text-white d-lg-none" id="closeSidebar"><i class="fas fa-times fa-lg"></i></button>
        </div>
        <div class="px-3 mb-4 text-center">
            <span class="badge bg-secondary text-uppercase" style="letter-spacing: 1px; font-size: 0.7rem;">Modo Owner</span>
        </div>
        <div class="nav flex-column sidebar-nav">
            <a href="{{ route('dashboard') }}" class="nav-link"><i class="fas fa-tachometer-alt"></i> Cockpit General</a>
            <a href="#" class="nav-link active"><i class="fas fa-map-marker-alt"></i> Base Geográfica</a>
            
            <form method="POST" action="{{ route('logout') }}" id="logoutFormOwner">
                @csrf
                <a href="#" onclick="document.getElementById('logoutFormOwner').submit();" class="nav-link mt-5 text-danger"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        
        <!-- Topbar -->
        <div class="topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="mobile-toggle" id="openSidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <h4 class="mb-0 fw-bold">Padrón Geográfico</h4>
                    <p class="text-muted mb-0 small">Módulo de Localidades de Argentina</p>
                </div>
            </div>
            <div>
                <button class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#nuevaLocalidadModal">
                    <i class="fas fa-plus me-1"></i> Nueva Localidad
                </button>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Tabla CRUD ABM -->
        <div class="content-card">
            <div class="card-header-styled">
                <h5 class="text-primary mb-0"><i class="fas fa-city me-2"></i> Listado Nacional</h5>
                
                <!-- Buscador en Vivo y Paginador -->
                <form method="GET" action="{{ route('owner.geografia') }}" id="formFiltros" class="d-flex gap-3 align-items-center search-controls">
                    <!-- Cantidad por página -->
                    <div class="d-flex align-items-center gap-2">
                        <small class="text-muted text-nowrap">Mostrar:</small>
                        <select name="per_page" class="form-select form-select-sm" onchange="document.getElementById('formFiltros').submit();">
                            <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5</option>
                            <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                            <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20</option>
                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>

                    <!-- Buscador -->
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <!-- El Evento "oninput" simula el LiveSearch enviando el form tras escribir-->
                        <input type="text" name="search" id="searchInput" class="form-control border-start-0 ps-0 bg-light" placeholder="Buscar ciudad o provincia..." value="{{ $search }}">
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-custom table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Provincia / Jurisdicción</th>
                            <th>Localidad</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaResultados">
                        @include('dashboards.partials.geografia_table_rows')
                    </tbody>
                </table>
            </div>

            <!-- Paginación Nativa de Laravel (Bootstrap Ready) -->
            <!-- Paginación Nativa -->
            <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-3" id="paginacionContainer">
                {!! $localidades->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>


    <!-- Modal: Agregar Localidad -->
    <div class="modal fade" id="nuevaLocalidadModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="border-radius: 15px; border: none;">
                <div class="modal-header bg-light border-0" style="border-radius: 15px 15px 0 0;">
                    <h5 class="modal-title fw-bold text-primary"><i class="fas fa-map-marker-alt me-2"></i> Empadronar Localidad</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('geografia.localidad.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">A qué Provincia Pertenece <span class="text-danger">*</span></label>
                            <select name="provincia_id" class="form-select bg-light border-0" required>
                                <option value="">Seleccione una jurisdicción...</option>
                                @foreach($provincias as $prov)
                                    <option value="{{ $prov->id }}">{{ $prov->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Nombre de la Ciudad / Localidad <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control bg-light border-0" placeholder="Ej: Villa Gesell" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pb-4 pe-4">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Guardar en el Sistema</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Lógica de UI Sidebar Móvil calcada de Owner
            const sidebar = document.getElementById('sidebar');
            const openBtn = document.getElementById('openSidebar');
            const closeBtn = document.getElementById('closeSidebar');
            const overlay = document.getElementById('sidebarOverlay');

            function toggleMenu() {
                sidebar.classList.toggle('active');
                if(sidebar.classList.contains('active')) {
                    overlay.style.display = 'block';
                } else {
                    overlay.style.display = 'none';
                }
            }

            if(openBtn) openBtn.addEventListener('click', toggleMenu);
            if(closeBtn) closeBtn.addEventListener('click', toggleMenu);
            if(overlay) overlay.addEventListener('click', toggleMenu);

            // AUTO-SUBMIT DEL BUSCADOR EN VIVO CON FETCH PARA NO PERDER EL FOCO NI LOS ESPACIOS
            let debounceTimer;
            const searchInput = document.getElementById('searchInput');
            const formFiltros = document.getElementById('formFiltros');
            const tbodyResultados = document.getElementById('tablaResultados');
            const paginacionContainer = document.getElementById('paginacionContainer');
            
            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    const searchVal = encodeURIComponent(searchInput.value); // Conservamos el espacio tal cual "La %20"
                    const perPage = document.querySelector('[name="per_page"]').value;
                    const url = `{{ route('owner.geografia') }}?search=${searchVal}&per_page=${perPage}`;

                    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(response => response.json())
                        .then(data => {
                            tbodyResultados.innerHTML = data.html;
                            paginacionContainer.innerHTML = data.pagination;
                        })
                        .catch(error => console.error('Error fetching data:', error));
                }, 400); 
            });

            // Prevenimos el submit con Enter para no recargar
            formFiltros.addEventListener('submit', function(e) { e.preventDefault(); });
            
            // Para mantener la consistencia si cambia la cantidad por página
            document.querySelector('[name="per_page"]').addEventListener('change', function() {
                const searchVal = encodeURIComponent(searchInput.value);
                const perPage = this.value;
                const url = `{{ route('owner.geografia') }}?search=${searchVal}&per_page=${perPage}`;
                window.location.href = url; // Aquí sí recargamos
            });
        });
    </script>
</body>
</html>
