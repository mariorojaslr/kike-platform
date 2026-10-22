<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $empresaId = Auth::user()->empresa_id ?? session('impersonated_tenant_id') ?? 2;
        $empresaLayout = \App\Models\Empresa::find($empresaId) ?? \App\Models\Empresa::find(2);
        $colorPrimario = $empresaLayout?->color_primario ?? '#0284c7';
        $colorSecundario = $empresaLayout?->color_secundario ?? '#0f172a';
        $nombreEmpresa = $empresaLayout?->nombre ?? 'APOS - Obra Social Provincial';
        $subagenciaNombre = $empresaLayout?->subagencia_nombre ?? 'Soluciones Digitales MCR (La Rioja)';
        $capitasAfiliados = $empresaLayout?->capitas_afiliados ?: 160000;
    @endphp
    <title>@yield('title', $nombreEmpresa) | INTEGRA</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            /* Variables de Marca Blanca inyectadas desde BD */
            --brand-primary: {{ $colorPrimario }};
            --brand-secondary: {{ $colorSecundario }};
            
            /* Colores Base (Modo Día) */
            --bg-body: #f1f5f9;
            --bg-card: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
        }

        /* Variables dinámicas para el Modo Noche */
        [data-bs-theme="dark"] {
            --bg-body: #000000;      /* Tu requerimiento: Negro total */
            --bg-card: #111111;      /* Gris sumamente oscuro casi negro */
            --text-main: #ffffff;    /* Blanco puro para alto contraste */
            --text-muted: #a1a1aa;
            --border-color: #333333;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            transition: background-color 0.3s, color 0.3s;
        }

        /* --- Sidebar --- */
        .sidebar {
            width: 250px;
            background-color: var(--brand-secondary);
            color: white;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            padding-top: 20px;
            z-index: 1000;
        }

        .sidebar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 30px;
            color: white;
            text-decoration: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .tenant-logo {
            width: 80px;
            height: 80px;
            border-radius: 15px;
            background-color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--brand-primary);
            font-size: 2rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
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
            border-left: 4px solid var(--brand-primary);
        }

        /* --- Main Content --- */
        .main-content {
            margin-left: 250px;
            padding: 30px;
        }

        .topbar {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            padding: 15px 30px;
            border-radius: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            transition: background-color 0.3s, border-color 0.3s;
        }

        /* Theme Toggle Button */
        .theme-toggle {
            background: none;
            border: 1px solid var(--border-color);
            color: var(--text-main);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        .theme-toggle:hover {
            background: var(--brand-primary);
            color: white;
            border-color: var(--brand-primary);
        }

        /* Cards and Tables from Owner */
        .card-header-styled {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .content-card {
            background: var(--bg-card);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); /* very subtle */
            margin-bottom: 30px;
            border: 1px solid var(--border-color);
            transition: background-color 0.3s, border-color 0.3s;
        }

        .table-custom th {
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            font-size: 0.8rem;
            border-bottom: 2px solid var(--border-color);
        }
        
        .table-custom td {
            color: var(--text-main);
            border-bottom: 1px solid var(--border-color) !important;
        }

        /* Responsive */
        .mobile-toggle { display: none; background: none; border: none; font-size: 1.5rem; color: var(--text-main); }
        
        @media (max-width: 991px) {
            .sidebar { transform: translateX(-100%); transition: transform 0.3s; }
            .sidebar.active { transform: translateX(0); }
            .main-content { margin-left: 0; padding: 15px; }
            .mobile-toggle { display: block; }
            .topbar { padding: 15px; flex-direction: column; align-items: flex-start; gap: 15px;}
            .topbar-right { width: 100%; display: flex; justify-content: space-between; }
        }
    </style>
    @stack('css')
</head>
<body>

    <!-- MODO OMNISCIENTE -->
    @if(session('impersonated_by'))
    <div style="background-color: #dc3545; color: white; text-align: center; padding: 10px; font-weight: bold; position: sticky; top: 0; z-index: 1050; display: flex; justify-content: center; align-items: center; gap: 15px;">
        <span><i class="fas fa-user-secret fa-lg"></i> MODO DIOS ACTIVO: Estás viendo la pantalla como <strong>{{ Auth::user()->name }}</strong> ({{ $nombreEmpresa }}).</span>
        <a href="{{ route('impersonate.leave') }}" class="btn btn-sm btn-dark" style="border: 1px solid white;">Volver a mi cuenta Original</a>
    </div>
    @endif

    <!-- Overlay UI -->
    <div id="sidebarOverlay" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.5); z-index:999;"></div>

    <!-- Sidebar Marca Blanca -->
    <div class="sidebar" id="sidebar">
        <div class="d-flex justify-content-end px-3 d-lg-none">
            <button class="btn btn-sm text-white" id="closeSidebar"><i class="fas fa-times fa-lg"></i></button>
        </div>
        
        <a href="{{ route('tenant.dashboard') }}" class="sidebar-brand mb-2">
            <div class="tenant-logo overflow-hidden p-0">
                @if($empresaLayout && $empresaLayout->logo && str_contains($empresaLayout->logo, 'mcr'))
                    <img src="{{ asset('images/mcr_logo.svg') }}" alt="Logo MCR" style="width: 100%; height: 100%; object-fit: cover;">
                @else
                    <img src="{{ asset('images/apos_logo.svg') }}" alt="Logo APOS La Rioja" style="width: 100%; height: 100%; object-fit: cover;">
                @endif
            </div>
            <span style="font-size: 1.05rem; padding: 0 12px; line-height: 1.25;">{{ $nombreEmpresa }}</span>
            <span class="badge bg-info bg-opacity-25 text-info border border-info border-opacity-50 px-2 py-1" style="font-size: 0.65rem; font-weight: 600;">
                <i class="fas fa-handshake me-1"></i>{{ $subagenciaNombre }}
            </span>
        </a>

        <div class="nav flex-column sidebar-nav mt-2" style="overflow-y: auto; max-height: calc(100vh - 190px);">
            <a href="{{ route('tenant.dashboard') }}" class="nav-link {{ request()->routeIs('tenant.dashboard') ? 'active' : '' }}"><i class="fas fa-home"></i> Inicio (Área Docentes)</a>
            <a href="{{ route('tenant.familiares.index') }}" class="nav-link {{ request()->routeIs('tenant.familiares.*') ? 'active' : '' }}"><i class="fas fa-child"></i> Alumnos / Beneficiarios</a>
            <a href="{{ route('tenant.titulares.index') }}" class="nav-link {{ request()->routeIs('tenant.titulares.*') ? 'active' : '' }}"><i class="fas fa-users"></i> Padres Titulares (Grupo)</a>
            <a href="{{ route('tenant.docentes.index') }}" class="nav-link {{ request()->routeIs('tenant.docentes.*') ? 'active' : '' }}"><i class="fas fa-chalkboard-teacher"></i> Docentes Integradores</a>
            <a href="{{ route('tenant.escuelas.index') }}" class="nav-link {{ request()->routeIs('tenant.escuelas.*') ? 'active' : '' }}"><i class="fas fa-school"></i> Escuelas (La Rioja)</a>
            <a href="{{ route('tenant.diagnosticos.index') }}" class="nav-link {{ request()->routeIs('tenant.diagnosticos.*') ? 'active' : '' }}"><i class="fas fa-notes-medical"></i> Patologías (CUD)</a>
            <a href="{{ route('tenant.formaciones.index') }}" class="nav-link {{ request()->routeIs('tenant.formaciones.*') ? 'active' : '' }}"><i class="fas fa-graduation-cap"></i> Especialidades Docentes</a>
            
            <a href="{{ route('panel.accesos') }}" class="nav-link mt-2 text-info" style="background: rgba(56,189,248,0.1); border-left: 4px solid #38bdf8;"><i class="fas fa-share-nodes"></i> <b>Links de Acceso (Roles)</b></a>
            <a href="{{ route('owner.mutual_dashboard') }}" class="nav-link text-warning"><i class="fas fa-crown"></i> <b>Ejecutivo APOS (160k)</b></a>
            <a href="{{ route('tenant.importador.index') }}" class="nav-link mt-2 text-warning {{ request()->routeIs('tenant.importador.*') ? 'active' : '' }}" style="border: 1px dashed rgba(255,255,255,0.3);"><i class="fas fa-satellite-dish"></i> <b>Importador Padrón</b></a>

            <a href="{{ route('tenant.tipo_documentos.index') }}" class="nav-link mt-2 {{ request()->routeIs('tenant.tipo_documentos.*') ? 'active' : '' }}"><i class="fas fa-file-contract"></i> <b>Requisitos de Legajo</b></a>
            <a href="{{ route('auditor.docentes.legajos') }}" class="nav-link {{ request()->routeIs('auditor.docentes.*') ? 'active' : '' }}"><i class="fas fa-user-check"></i> Auditoría Docentes</a>
            <a href="{{ route('owner.liquidaciones') }}" class="nav-link {{ request()->routeIs('owner.liquidaciones') ? 'active' : '' }}"><i class="fas fa-file-invoice-dollar"></i> Planilla de Liquidación</a>
            
            @if(request()->routeIs('tenant.dashboard'))
                <a href="#" class="nav-link mt-4" style="color: var(--brand-primary);" data-bs-toggle="modal" data-bs-target="#setupModal"><i class="fas fa-paint-roller"></i> Personalizar Marca</a>
            @endif

            <form method="POST" action="{{ route('logout') }}" id="logoutFormTenant">
                @csrf
                <a href="#" onclick="document.getElementById('logoutFormTenant').submit();" class="nav-link mt-2 text-danger"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <div class="topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="mobile-toggle" id="openSidebar"><i class="fas fa-bars"></i></button>
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h4 class="mb-0 fw-bold d-none d-sm-block">{{ $nombreEmpresa }}</h4>
                        <h5 class="mb-0 fw-bold d-sm-none">{{ $nombreEmpresa }}</h5>
                        <span class="badge bg-primary rounded-pill px-3 py-1" style="font-size: 0.72rem;">
                            <i class="fas fa-users me-1"></i>{{ number_format($capitasAfiliados, 0, ',', '.') }} Afiliados (Provincia de La Rioja)
                        </span>
                    </div>
                    <p class="mb-0 small" style="color: var(--text-muted)">
                        <i class="fas fa-building-shield text-primary me-1"></i>Subagencia Oficial: <strong>{{ $subagenciaNombre }}</strong> &bull; Sector: <strong>Área Docentes Integradores y Apoyo Terapéutico</strong>
                    </p>
                </div>
            </div>
            <div class="topbar-right align-items-center gap-3">
                <!-- Widget Notificaciones Internas -->
                @include('partials.notificaciones_widget')

                <!-- Toggle Modo Noche/Día -->
                <button class="theme-toggle" id="btnThemeToggle" title="Cambiar a Modo Nocturno">
                    <i class="fas fa-moon"></i>
                </button>
                
                <div class="d-flex align-items-center gap-2">
                    @if(Auth::user()->avatar)
                        <img src="{{ Storage::disk('public')->url(Auth::user()->avatar) }}" alt="Avatar" class="rounded-circle shadow-sm" style="width: 40px; height: 40px; object-fit: cover; border: 2px solid var(--brand-primary);">
                    @else
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 40px; height: 40px; background: var(--brand-primary); border: 2px solid #fff;">
                            {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger mb-4 alert-dismissible fade show">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @yield('content')
        
    </div>

    <!-- Toasts Notificaciones -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1050;">
        <div id="toastMessage" class="toast align-items-center border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body fw-bold">
                    <span></span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Lógica de Interfaz y Tema -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            // REACTIVIDAD: SISTEMA PRÓXIMAMENTE PARA ENLACES MUERTOS
            const deadLinks = document.querySelectorAll('a[href="#"], button[href="#"]:not([data-bs-dismiss])');
            deadLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    if (this.getAttribute('data-bs-toggle') === 'modal') return; 
                    e.preventDefault();
                    showReactToast();
                });
            });

            function showReactToast() {
                const toastEl = document.getElementById('toastMessage');
                if(toastEl) {
                    toastEl.classList.remove('text-bg-success', 'text-bg-danger');
                    toastEl.classList.add('text-bg-primary');
                    
                    const bodyEl = toastEl.querySelector('.toast-body span');
                    if(bodyEl) {
                        bodyEl.innerHTML = '<i class="fas fa-tools me-2 fa-lg"></i> Este módulo se encuentra en construcción. ¡Estará listo próximamente!';
                    }
                    
                    const toast = new bootstrap.Toast(toastEl, { delay: 4000 });
                    toast.show();
                }
            }

            // Lógica de Menú Móvil
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('openSidebar'); 
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

            if(toggleBtn) toggleBtn.addEventListener('click', toggleMenu);
            if(closeBtn) closeBtn.addEventListener('click', toggleMenu);
            if(overlay) overlay.addEventListener('click', toggleMenu);

            // Persistencia del Tema "Modo Noche / Claro"
            const themeToggleBtn = document.getElementById('btnThemeToggle'); 
            const htmlTag = document.documentElement;
            
            const currentTheme = localStorage.getItem('integra-tenant-theme'); 
            if (currentTheme) {
                htmlTag.setAttribute('data-bs-theme', currentTheme);
                actualizarIconoLunaSol(currentTheme);
            }

            themeToggleBtn.addEventListener('click', () => {
                let current = htmlTag.getAttribute('data-bs-theme');
                let newTheme = current === 'dark' ? 'light' : 'dark';
                
                htmlTag.setAttribute('data-bs-theme', newTheme);
                localStorage.setItem('integra-tenant-theme', newTheme); 
                
                actualizarIconoLunaSol(newTheme);
            });

            function actualizarIconoLunaSol(theme) {
                const icon = themeToggleBtn.querySelector('i');
                if(theme === 'dark') {
                    icon.classList.remove('fa-moon');
                    icon.classList.add('fa-sun');
                } else {
                    icon.classList.remove('fa-sun');
                    icon.classList.add('fa-moon');
                }
            }
        });
    </script>
    @stack('scripts')
    @include('partials.ai_assistant_widget')
</body>
</html>
