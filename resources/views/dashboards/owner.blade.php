<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KIKE | Dashboard Owner (Cockpit)</title>
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
            --danger: #ef4444; /* Red 500 */
            --warning: #f59e0b; /* Amber 500 */
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

        .user-profile {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--secundario);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        /* Gauges / Relojes (Top Cards) */
        .gauge-card {
            background: var(--card-bg);
            border-radius: 15px;
            padding: 20px;
            border: none;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: transform 0.3s;
            margin-bottom: 25px;
        }

        .gauge-card:hover {
            transform: translateY(-5px);
        }

        .gauge-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
        }
        
        .bg-blue-light { background: #dbeafe; color: #1e40af; }
        .bg-green-light { background: #dcfce7; color: #166534; }
        .bg-purple-light { border-radius: 12px; background: #f3e8ff; color: #6b21a8; }
        .bg-red-light { background: #fee2e2; color: #b91c1c; }

        .gauge-info h3 {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
            line-height: 1.2;
        }

        .gauge-info p {
            margin: 0;
            color: #64748b;
            font-size: 0.9rem;
            font-weight: 500;
            text-transform: uppercase;
        }

        /* Content Cards */
        .content-card {
            background: var(--card-bg);
            border-radius: 15px;
            padding: 25px;
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

        .card-header-styled h5 {
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Progress Bar / Vúmetro */
        .vumetro {
            height: 12px;
            border-radius: 10px;
            background-color: #e2e8f0;
            margin-top: 8px;
            margin-bottom: 5px;
            overflow: hidden;
        }
        
        .vumetro-bar {
            height: 100%;
            border-radius: 10px;
            transition: width 0.5s ease;
        }

        .vumetro-safe { background: linear-gradient(90deg, #10b981, #34d399); }
        .vumetro-warn { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
        .vumetro-danger { background: linear-gradient(90deg, #ef4444, #f87171); }

        /* Table */
        .table-custom th {
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            font-size: 0.8rem;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 15px;
        }
        
        .table-custom td {
            vertical-align: middle;
            padding: 15px 10px;
            border-bottom: 1px solid #f1f5f9;
        }

        .badge-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .status-aldia { background: #dcfce7; color: #166534; }
        .status-pendiente { background: #fef9c3; color: #854d0e; }
        .status-suspendida { background: #fee2e2; color: #991b1b; }

        /* --- RESPONSIVE DESIGN (Media Queries) --- */
        
        /* Botón hamburguesa móvil */
        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--primario);
        }

        /* Tablets (Vertical) y Móviles Grandes */
        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
            }
            .sidebar.active {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
                padding: 15px;
            }
            .mobile-toggle {
                display: block;
            }
            .topbar {
                padding: 15px;
            }
            .gauge-card {
                margin-bottom: 15px;
            }
        }

        /* Móviles (Pantallas chicas) */
        @media (max-width: 767px) {
            .topbar {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            .user-profile {
                width: 100%;
                justify-content: space-between;
            }
            .gauge-card {
                padding: 15px;
            }
            .gauge-info h3 {
                font-size: 1.5rem;
            }
            .card-header-styled {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            .card-header-styled .input-group {
                width: 100% !important;
            }
        }
    </style>
</head>
<body>

    <!-- Overlay para móvil -->
    <div class="sidebar-overlay" id="sidebarOverlay" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.5); z-index:999;"></div>

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
            <a href="{{ route('dashboard') }}" class="nav-link active"><i class="fas fa-tachometer-alt"></i> Cockpit General</a>
            <a href="#empresas-list" class="nav-link"><i class="fas fa-building"></i> Empresas (Clientes)</a>
            <a href="{{ route('owner.billing') }}" class="nav-link"><i class="fas fa-file-invoice-dollar"></i> Facturación y Tarifas</a>
            <a href="#" class="nav-link"><i class="fas fa-headset"></i> Mesa de Ayuda (Tickets)</a>
            <a href="{{ route('owner.geografia') }}" class="nav-link"><i class="fas fa-map-marker-alt"></i> Base Geográfica</a>
            <a href="#" class="nav-link"><i class="fas fa-server"></i> Infraestructura (Bunny)</a>
            
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
                    <h4 class="mb-0 fw-bold d-none d-sm-block">Panel de Control Global</h4>
                    <h5 class="mb-0 fw-bold d-sm-none">Cockpit</h5>
                    <p class="text-muted mb-0 small">{{ date('d M Y') }}</p>
                </div>
            </div>
            <div class="user-profile">
                <button class="btn btn-outline-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#nuevaEmpresaModal">
                    <i class="fas fa-plus me-1"></i> Nueva Empresa
                </button>
                @if(Auth::user()->avatar)
                    <img src="{{ Storage::disk('public')->url(Auth::user()->avatar) }}" class="avatar rounded-circle shadow-sm" style="object-fit: cover; border: 2px solid var(--secundario);">
                @else
                    <div class="avatar shadow-sm border border-light">
                        {{ substr(Auth::user()->name ?? 'O', 0, 1) }}
                    </div>
                @endif
                <div class="d-none d-md-block">
                    <h6 class="mb-0 fw-bold">{{ Auth::user()->name ?? 'Mario Rojas' }}</h6>
                    <small class="text-muted">Propietario / Master</small>
                </div>
            </div>
        </div>

        <!-- KPI Gauges (Racing Car Style) -->
        <div class="row">
            <div class="col-md-3">
                <div class="gauge-card">
                    <div class="gauge-info">
                        <p>Empresas Activas</p>
                        <h3>{{ $totalEmpresas ?? 0 }}</h3>
                    </div>
                    <div class="gauge-icon bg-blue-light">
                        <i class="fas fa-city"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="gauge-card">
                    <div class="gauge-info">
                        <p>Usuarios Globales</p>
                        <h3>{{ $totalUsuariosGlobal ?? 0 }}</h3>
                    </div>
                    <div class="gauge-icon bg-green-light">
                        <i class="fas fa-users-rays"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="gauge-card">
                    <div class="gauge-info">
                        <p>Volumen Total (MB)</p>
                        <h3>{{ number_format($volumenTotalMb ?? 0, 1) }}</h3>
                    </div>
                    <div class="gauge-icon bg-purple-light">
                        <i class="fas fa-cloud-arrow-up"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="gauge-card">
                    <div class="gauge-info">
                        <p>Tickets Abiertos</p>
                        <h3>{{ $ticketsAbiertos ?? 0 }}</h3>
                    </div>
                    <div class="gauge-icon bg-red-light">
                        <i class="fas fa-fire"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido Principal: Monitor de Clientes (Empresas) -->
        <div class="row" id="empresas-list">
            <div class="col-12">
                <div class="content-card">
                    <div class="card-header-styled">
                        <h5 class="text-primary"><i class="fas fa-chart-line"></i> Monitor de Clientes (SaaS)</h5>
                        <div class="input-group" style="width: 250px;">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" class="form-control border-start-0 ps-0" placeholder="Buscar empresa...">
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-custom table-hover">
                            <thead>
                                <tr>
                                    <th>Empresa</th>
                                    <th>Cta. Corriente</th>
                                    <th>Usuarios Activos</th>
                                    <th style="width: 30%">Vúmetro de Consumo (Almacenamiento)</th>
                                    <th>Tickets</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($empresas ?? [] as $empresa)
                                    @php
                                        // Fake data for visual mock if DB is empty
                                        $porcentajeMb = $empresa->limite_mb > 0 ? ($empresa->consumo_actual_mb / $empresa->limite_mb) * 100 : 0;
                                        $vumetroClass = 'vumetro-safe';
                                        if($porcentajeMb > 75) $vumetroClass = 'vumetro-warn';
                                        if($porcentajeMb > 90) $vumetroClass = 'vumetro-danger';

                                        $estadoLabel = match($empresa->estado_cuenta) {
                                            'al_dia' => ['text' => 'Al Día', 'class' => 'status-aldia'],
                                            'pendiente' => ['text' => 'Pendiente', 'class' => 'status-pendiente'],
                                            'suspendida' => ['text' => 'Suspendida', 'class' => 'status-suspendida'],
                                            default => ['text' => 'Desconocido', 'class' => 'status-pendiente']
                                        };
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-light rounded p-2 me-3 text-center" style="width:40px;height:40px;line-height:24px; font-weight:bold; color:var(--primario);">
                                                    {{ substr($empresa->nombre ?? 'N/A', 0, 1) }}
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-bold">{{ $empresa->nombre ?? 'Empresa Genérica' }}</h6>
                                                    <small class="text-muted">ID: #{{ str_pad($empresa->id, 4, '0', STR_PAD_LEFT) }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                // Lógica de semáforo de facturación
                                                $vencimiento = $empresa->proximo_vencimiento ? \Carbon\Carbon::parse($empresa->proximo_vencimiento) : now()->addMonth();
                                                $diasRestantes = now()->startOfDay()->diffInDays($vencimiento->startOfDay(), false);
                                                
                                                $semaforoClass = 'status-aldia'; // Verde por defecto
                                                $semaforoTexto = 'Vence: ' . $vencimiento->format('d/m/Y');
                                                $icono = 'fa-check-circle';
                                                
                                                if ($diasRestantes < 0) {
                                                    $semaforoClass = 'status-suspendida'; // Rojo (vencido)
                                                    $semaforoTexto = 'Vencido (' . abs(intval($diasRestantes)) . ' días)';
                                                    $icono = 'fa-times-circle';
                                                } elseif ($diasRestantes <= 7) {
                                                    $semaforoClass = 'status-pendiente'; // Amarillo (por vencer)
                                                    $semaforoTexto = 'Vence en ' . intval($diasRestantes) . ' días';
                                                    $icono = 'fa-exclamation-triangle';
                                                }
                                            @endphp
                                            <div class="fw-bold mb-1" style="font-size: 0.85rem;">
                                                <span class="text-secondary">$</span> {{ number_format($empresa->deuda_actual ?? 0, 2) }} 
                                                @if(($empresa->meses_adeudados ?? 0) > 0)
                                                    <small class="text-danger">({{ $empresa->meses_adeudados }} cuotas)</small>
                                                @endif
                                            </div>
                                            <span class="badge-status {{ $semaforoClass }} d-inline-block">
                                                <i class="fas {{ $icono }}" style="font-size:10px; vertical-align:middle; margin-right:4px;"></i> {{ $semaforoTexto }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $empresa->users_count ?? 0 }} <span class="text-muted fw-normal small">/ {{ $empresa->limite_usuarios }}</span></div>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-between small text-muted mb-1">
                                                <span>{{ number_format($empresa->consumo_actual_mb, 1) }} MB</span>
                                                <span>{{ number_format($empresa->limite_mb, 0) }} MB</span>
                                            </div>
                                            <div class="vumetro">
                                                <div class="vumetro-bar {{ $vumetroClass }}" style="width: {{ min(100, max(0, $porcentajeMb)) }}%"></div>
                                            </div>
                                        </td>
                                        <td>
                                            @if(($empresa->tickets_count ?? 0) > 0)
                                                <span class="badge bg-danger rounded-pill">{{ $empresa->tickets_count }} Activos</span>
                                            @else
                                                <span class="text-muted small"><i class="fas fa-check text-success"></i> 0</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center flex-nowrap gap-1">
                                                <!-- Botón OJO nativo (Ver Detalles del Tenant) -->
                                                <button class="btn btn-sm btn-light text-primary" title="Ver Radiografía Completa" data-bs-toggle="modal" data-bs-target="#modalDetallesEmpresa{{ $empresa->id }}"><i class="fas fa-eye"></i></button>

                                                <!-- Botón GOD MODE (Suplantar) -->
                                                @php
                                                    // Buscamos cualquier usuario asociado a esa empresa que sea admin.
                                                    $adminUser = \App\Models\User::where('empresa_id', $empresa->id)->first();
                                                @endphp
                                                @if($adminUser)
                                                <button class="btn btn-sm btn-light text-success" title="Entrar en esta empresa (Elegir Rol)" data-bs-toggle="modal" data-bs-target="#modalSelectorRoles{{ $empresa->id }}">
                                                    <i class="fas fa-sign-in-alt"></i>
                                                </button>
                                                @else
                                                <button class="btn btn-sm btn-light text-muted" title="Esta empresa aún no tiene un administrador creado" disabled>
                                                    <i class="fas fa-sign-in-alt"></i>
                                                </button>
                                                @endif
                                                
                                                <!-- Funcionalidad SUSPENDER -->
                                                <form action="{{ route('empresas.toggle_status', $empresa->id) }}" method="POST" class="m-0 p-0">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-light {{ $empresa->estado_cuenta === 'suspendida' ? 'text-success' : 'text-danger' }}" 
                                                            title="{{ $empresa->estado_cuenta === 'suspendida' ? 'Reactivar Servicio' : 'Suspender por Falta de Pago' }}">
                                                        <i class="fas {{ $empresa->estado_cuenta === 'suspendida' ? 'fa-play-circle' : 'fa-ban' }}"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center py-5">
                    <div class="text-muted mb-3"><i class="fas fa-box-open fa-3x"></i></div>
                    <h6>No hay empresas registradas aún.</h6>
                    <p class="small">Haz clic en "Nueva Empresa" para comenzar.</p>
                </td>
            </tr>
        @endforelse
        
        <!-- Datos simulados (Mock) para vista previa visual en caso de que la tabla de Base de Datos esté vacía (útil para el diseño) -->
        @if(!isset($empresas) || count($empresas) === 0)
        <tr>
            <td>
                <div class="d-flex align-items-center">
                    <div class="bg-light rounded p-2 me-3 text-center" style="width:40px;height:40px;line-height:24px; font-weight:bold; color:var(--primario);">
                        C
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">Clínica San Lucas</h6>
                        <small class="text-muted">ID: #0045</small>
                    </div>
                </div>
            </td>
            <td>
                <div class="fw-bold mb-1" style="font-size: 0.85rem;"><span class="text-secondary">$</span> 0.00</div>
                <span class="badge-status status-aldia d-inline-block"><i class="fas fa-check-circle" style="font-size:10px; margin-right:4px;"></i> Vence: 12/04/2026</span>
            </td>
            <td><div class="fw-bold">42 <span class="text-muted fw-normal small">/ 50</span></div></td>
            <td>
                <div class="d-flex justify-content-between small text-muted mb-1">
                    <span>320.5 MB</span>
                    <span>500 MB</span>
                </div>
                <div class="vumetro"><div class="vumetro-bar vumetro-safe" style="width: 65%"></div></div>
            </td>
            <td><span class="badge bg-danger rounded-pill">2 Activos</span></td>
            <td>
                <button class="btn btn-sm btn-light text-primary" data-bs-toggle="modal" data-bs-target="#toastProximamente"><i class="fas fa-eye"></i></button>
            </td>
        </tr>
        <tr>
            <td>
                <div class="d-flex align-items-center">
                    <div class="bg-light rounded p-2 me-3 text-center" style="width:40px;height:40px;line-height:24px; font-weight:bold; color:var(--primario);">
                        E
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">Escuela de Especiales</h6>
                        <small class="text-muted">ID: #0089</small>
                    </div>
                </div>
            </td>
            <td>
                <div class="fw-bold mb-1" style="font-size: 0.85rem;"><span class="text-secondary">$</span> 15,000.00 <small class="text-danger">(1 cuotas)</small></div>
                <span class="badge-status status-suspendida d-inline-block"><i class="fas fa-times-circle" style="font-size:10px; margin-right:4px;"></i> Vencido (5 días)</span>
            </td>
                                    <td><div class="fw-bold">98 <span class="text-muted fw-normal small">/ 100</span></div></td>
                                    <td>
                                        <div class="d-flex justify-content-between small text-muted mb-1">
                                            <span>950.0 MB</span>
                                            <span>1000 MB</span>
                                        </div>
                                        <div class="vumetro"><div class="vumetro-bar vumetro-danger" style="width: 95%"></div></div>
                                    </td>
                                    <td><span class="text-muted small"><i class="fas fa-check text-success"></i> 0</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-light text-primary" data-bs-toggle="modal" data-bs-target="#toastProximamente"><i class="fas fa-eye"></i></button>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
    </div>

    @if(session('success') || session('error'))
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 1050">
            <div id="liveToast" class="toast align-items-center text-white bg-{{ session('success') ? 'success' : 'danger' }} border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-{{ session('success') ? 'check-circle' : 'exclamation-circle' }} me-2"></i>
                        {{ session('success') ?? session('error') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif

        <!-- MODAL DETAILS ITERATION -->
    @foreach($empresas ?? [] as $empresa)
    <!-- Modal Detalles Empresa {{ $empresa->id }} -->
    <div class="modal fade" id="modalDetallesEmpresa{{ $empresa->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-building text-primary me-2"></i> {{ $empresa->nombre }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 pt-3">
                    
                    <div class="row g-4">
                        <!-- Fila 1: Finanzas y Estado -->
                        <div class="col-md-6">
                            <div class="card bg-light border-0 h-100">
                                <div class="card-body">
                                    <h6 class="text-muted fw-bold mb-3"><i class="fas fa-file-invoice-dollar me-2"></i>Estado Financiero</h6>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Estado Cuenta:</span>
                                        <span class="badge {{ $empresa->estado_cuenta === 'al_dia' ? 'bg-success' : 'bg-danger' }}">
                                            {{ strtoupper(str_replace('_', ' ', $empresa->estado_cuenta ?? 'N/A')) }}
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Deuda Actual:</span>
                                        <span class="fw-bold {{ ($empresa->deuda_actual ?? 0) > 0 ? 'text-danger' : 'text-success' }}">
                                            ${{ number_format($empresa->deuda_actual ?? 0, 2) }}
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Próx. Vencimiento:</span>
                                        <span class="fw-bold">
                                            {{ $empresa->proximo_vencimiento ? \Carbon\Carbon::parse($empresa->proximo_vencimiento)->format('d/m/Y') : 'N/A' }}
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Meses Adeudados:</span>
                                        <span class="fw-bold">{{ $empresa->meses_adeudados ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                        <!-- Fila 2: Administrador y Accesos (NUEVO) -->
                        <div class="col-md-6">
                            <div class="card bg-light border-0 h-100">
                                <div class="card-body">
                                    <h6 class="text-muted fw-bold mb-3"><i class="fas fa-user-shield me-2"></i>Cuenta de Acceso (Admin)</h6>
                                    @php
                                        // Buscar al primer usuario que sea admin (tenant) de esta empresa
                                        $adminUser = \App\Models\User::where('empresa_id', $empresa->id)
                                                                     ->whereIn('role', ['empresa', 'tenant'])
                                                                     ->first();
                                    @endphp
                                    
                                    @if($adminUser)
                                        <div class="mb-2">
                                            <span class="text-muted small">Correo Electrónico:</span><br>
                                            <span class="fw-bold"><i class="fas fa-envelope text-primary me-1"></i> {{ $adminUser->email }}</span>
                                        </div>
                                        <div class="mb-3">
                                            <span class="text-muted small">Nombre de Cuenta:</span><br>
                                            <span class="fw-bold"><i class="fas fa-user text-secondary me-1"></i> {{ $adminUser->name }}</span>
                                        </div>
                                        
                                        <!-- Formulario para Resetear o Forzar Contraseña a 12345678 -->
                                        <form action="{{ route('owner.empresas.reset_password', $empresa->id) }}" method="POST" class="mt-3 border-top pt-3">
                                            @csrf
                                            <div class="d-flex flex-column gap-2">
                                                <small class="text-muted">Si el cliente perdió su acceso o la empresa fue heredada, puedes forzar una clave temporal para pasársela.</small>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-white"><i class="fas fa-key text-warning"></i></span>
                                                    <input type="text" name="new_password" class="form-control" placeholder="Nueva clave (Ej: 12345678)" required>
                                                    <button class="btn btn-warning fw-bold text-dark" type="submit">Actualizar Clave</button>
                                                </div>
                                            </div>
                                        </form>
                                    @else
                                        <div class="alert alert-danger py-2 px-3 small mb-0">
                                            <i class="fas fa-exclamation-triangle me-1"></i> Esta empresa no tiene ningún usuario administrador asignado.
                                        </div>
                                        <form action="{{ route('owner.empresas.crear_admin', $empresa->id) }}" method="POST" class="mt-2">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger w-100"><i class="fas fa-plus-circle me-1"></i> Crear Admin por Defecto</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    
                        <!-- Fila 3: Consumos y Límites -->
                        <div class="col-md-6">
                            <div class="card bg-light border-0 h-100">
                                <div class="card-body">
                                    <h6 class="text-muted fw-bold mb-3"><i class="fas fa-chart-pie me-2"></i>Consumo Operativo</h6>
                                    
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between small text-muted mb-1">
                                            <span>Usuarios Activos</span>
                                            <span>{{ $empresa->users_count ?? 0 }} / {{ $empresa->limite_usuarios }}</span>
                                        </div>
                                        @php
                                            $porcentajeUsers = $empresa->limite_usuarios > 0 ? (($empresa->users_count ?? 0) / $empresa->limite_usuarios) * 100 : 0;
                                        @endphp
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $porcentajeUsers }}%;"></div>
                                        </div>
                                    </div>

                                    <div>
                                        <div class="d-flex justify-content-between small text-muted mb-1">
                                            <span>Almacenamiento (MB)</span>
                                            <span>{{ number_format($empresa->consumo_actual_mb ?? 0, 1) }} / {{ $empresa->limite_mb }} MB</span>
                                        </div>
                                        @php
                                            $porcentajeStorageInfo = $empresa->limite_mb > 0 ? (($empresa->consumo_actual_mb ?? 0) / $empresa->limite_mb) * 100 : 0;
                                            $storageColorInfo = 'bg-success';
                                            if($porcentajeStorageInfo > 75) $storageColorInfo = 'bg-warning';
                                            if($porcentajeStorageInfo > 90) $storageColorInfo = 'bg-danger';
                                        @endphp
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar {{ $storageColorInfo }}" role="progressbar" style="width: {{ $porcentajeStorageInfo }}%;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Fila 3: Marca Blanca -->
                        <div class="col-12">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <h6 class="text-muted fw-bold mb-3"><i class="fas fa-paint-brush me-2"></i>Configuración de Tenant (Marca Blanca)</h6>
                                    <div class="d-flex align-items-center gap-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <span>Color Primario:</span>
                                            <div style="width: 25px; height: 25px; border-radius: 5px; background-color: {{ $empresa->color_primario ?? '#000000' }}; border: 1px solid #ccc;"></div>
                                            <small class="text-muted">{{ $empresa->color_primario ?? 'N/A' }}</small>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span>Color Secundario:</span>
                                            <div style="width: 25px; height: 25px; border-radius: 5px; background-color: {{ $empresa->color_secundario ?? '#000000' }}; border: 1px solid #ccc;"></div>
                                            <small class="text-muted">{{ $empresa->color_secundario ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer border-top-0 d-flex justify-content-between">
                    <div>
                         @php
                            $adminUsr = \App\Models\User::where('empresa_id', $empresa->id)->first();
                        @endphp
                        @if($adminUsr)
                        <a href="{{ route('impersonate.enter', $adminUsr->id) }}" class="btn btn-outline-success border-2 shadow-sm fw-bold">
                            <i class="fas fa-sign-in-alt"></i> Entrar como Admin (God Mode)
                        </a>
                        @endif
                    </div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Selector de Roles (God Mode) para Empresa {{ $empresa->id }} -->
    @if(isset($adminUsr))
    <div class="modal fade" id="modalSelectorRoles{{ $empresa->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                <div class="modal-header bg-dark text-white border-0" style="border-radius: 15px 15px 0 0;">
                    <h5 class="modal-title fw-bold"><i class="fas fa-user-secret me-2 text-warning"></i> Simular Rol de Usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <p class="text-muted small mb-4 text-center">
                        ¿Cómo deseas visualizar el entorno de la empresa <strong>{{ $empresa->nombre }}</strong>?
                    </p>
                    <div class="d-grid gap-3">
                        <a href="{{ route('impersonate.enter', $adminUsr->id) }}" class="btn btn-outline-primary btn-lg text-start fw-bold shadow-sm p-3 border-2" style="border-radius: 12px;">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary text-white rounded-circle p-2 me-3" style="width: 40px; height: 40px; text-align: center;"><i class="fas fa-user-tie"></i></div>
                                <div>
                                    <div class="fs-6 mb-1">Entrar como Administrador</div>
                                    <small class="text-muted fw-normal" style="font-size: 0.75rem;">Acceso total al Panel de Cliente (Pagos, Configuración, Estadísticas y Empleados)</small>
                                </div>
                            </div>
                        </a>
                        
                        <!-- Opción Auditor (En desarrollo) -->
                        <button class="btn btn-outline-warning btn-lg text-start fw-bold shadow-sm p-3 border-2" style="border-radius: 12px;" onclick="alert('La interfaz de Auditor está en fase de diseño. Entra como Administrador mientras tanto.');">
                            <div class="d-flex align-items-center">
                                <div class="bg-warning text-dark rounded-circle p-2 me-3" style="width: 40px; height: 40px; text-align: center;"><i class="fas fa-user-check"></i></div>
                                <div>
                                    <div class="fs-6 mb-1">Entrar como Auditor Interno</div>
                                    <small class="text-muted fw-normal" style="font-size: 0.75rem;">Herramientas para revisar Vencimientos de Documentos y Reportes</small>
                                </div>
                            </div>
                        </button>

                        <!-- Opción App Docente (Enlace que desarrollaremos a continuación) -->
                        <a href="{{ url('/app-docente/demo') }}" target="_blank" class="btn btn-outline-success btn-lg text-start fw-bold shadow-sm p-3 border-2" style="border-radius: 12px;">
                            <div class="d-flex align-items-center">
                                <div class="bg-success text-white rounded-circle p-2 me-3" style="width: 40px; height: 40px; text-align: center;"><i class="fas fa-mobile-alt"></i></div>
                                <div>
                                    <div class="fs-6 mb-1">Simular PWA Docente (App)</div>
                                    <small class="text-muted fw-normal" style="font-size: 0.75rem;">Probar la Interfaz Móvil y link seguro que verán los Terapeutas</small>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endforeach

    <!-- Toasts Notificaciones Adicionales (Proximamente) -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1050;">
        <div id="toastProximamente" class="toast align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body fw-bold">
                    <i class="fas fa-tools me-2 fa-lg"></i> Este módulo se encuentra en construcción. ¡Estará listo próximamente!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- Modal Nueva Empresa -->
    <div class="modal fade" id="nuevaEmpresaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0" style="border-radius: 15px;">
                <div class="modal-header bg-light border-0" style="border-radius: 15px 15px 0 0;">
                    <h5 class="modal-title fw-bold"><i class="fas fa-building text-primary me-2"></i> Alta de Nueva Empresa</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('empresas.store') }}">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Nombre de Institución/Empresa</label>
                            <input type="text" name="nombre" class="form-control bg-light border-0" placeholder="Ej. Centro de Terapia Integral" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Límite de Usuarios</label>
                                <input type="number" name="limite_usuarios" class="form-control bg-light border-0" value="50" required min="1">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Almacenamiento (MB)</label>
                                <input type="number" name="limite_mb" class="form-control bg-light border-0" value="500" required min="1">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Correo del Administrador Emisor</label>
                            <input type="email" name="admin_email" class="form-control bg-light border-0" placeholder="admin@empresa.com" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pb-4 pe-4">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4" style="background-color: var(--secundario); border: none;">Crear Entorno</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Lógica de UI (Sidebar Mobile y Proximamente) -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
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

            // Toast Auto-hide logic para el flash de session
            const liveToastEl = document.getElementById('liveToast');
            if (liveToastEl) {
                const liveToast = new bootstrap.Toast(liveToastEl, { delay: 4000 });
                liveToast.show();
            }

            // REACTIVIDAD: SISTEMA PRÓXIMAMENTE PARA ENLACES MUERTOS
            const deadLinks = document.querySelectorAll('a[href="#"], button[href="#"]');
            deadLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const toastEl = document.getElementById('toastProximamente');
                    if(toastEl) {
                        const toast = new bootstrap.Toast(toastEl, { delay: 4000 });
                        toast.show();
                    }
                });
            });
            
            // Highlighting del menu activo
            const currentUrl = window.location.pathname;
            const menuItems = document.querySelectorAll('.sidebar-nav .nav-link');
            
            menuItems.forEach(item => {
                if(item.getAttribute('href') && item.getAttribute('href') !== '#' && currentUrl.includes(item.getAttribute('href'))) {
                    menuItems.forEach(i => i.classList.remove('active'));
                    item.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>
