<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SaaS Facturación - KIKE</title>
    
    <!-- Fonts y CSS Bases -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --primario: #0B132B;
        --secundario: #1C2541;
        --acento: #3A506B;
        --bg-color: #F8F9FA;
    }

    body {
        background-color: var(--bg-color);
        font-family: 'Inter', sans-serif;
    }

    /* Sidebar Styles (Reused from owner) */
    .sidebar {
        background-color: var(--primario);
        color: white;
        height: 100vh;
        position: fixed;
        width: 260px;
        top: 0;
        left: 0;
        z-index: 1040;
        transition: 0.3s;
        box-shadow: 4px 0 15px rgba(0,0,0,0.1);
    }

    .sidebar .logo-container {
        padding: 2rem 1.5rem;
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }

    .sidebar-nav .nav-link {
        color: rgba(255,255,255,0.7);
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        transition: all 0.2s;
        border-left: 4px solid transparent;
        font-weight: 500;
        letter-spacing: 0.3px;
    }

    .sidebar-nav .nav-link i {
        width: 24px;
        margin-right: 12px;
        font-size: 1.1rem;
    }

    .sidebar-nav .nav-link:hover, .sidebar-nav .nav-link.active {
        color: white;
        background-color: rgba(255,255,255,0.05);
        border-left-color: #5BC0BE;
    }

    .main-content {
        margin-left: 260px;
        padding: 2rem;
        min-height: 100vh;
        transition: 0.3s;
    }

    /* Topbar */
    .topbar {
        background: white;
        border-radius: 15px;
        padding: 1rem 1.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .user-profile {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .user-profile .avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background-color: var(--secundario);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.2rem;
    }
    
    /* Config Tariffs Card */
    .tariff-card {
        background: linear-gradient(135deg, var(--primario), var(--acento));
        border-radius: 15px;
        color: white;
        padding: 2rem;
        box-shadow: 0 10px 30px rgba(28, 37, 65, 0.15);
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }

    .tariff-card::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
    }

    .input-transparent {
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.2);
        color: white;
        font-weight: bold;
    }
    .input-transparent:focus {
        background: rgba(255,255,255,0.2);
        border-color: rgba(255,255,255,0.5);
        color: white;
        box-shadow: none;
    }

    /* Stat Cards */
    .stat-card {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        height: 100%;
        border-top: 4px solid var(--secundario);
    }
    
    .table-custom {
        vertical-align: middle;
    }

    @media (max-width: 991.98px) {
        .sidebar { transform: translateX(-100%); }
        .sidebar.active { transform: translateX(0); }
        .main-content { margin-left: 0; }
        .mobile-toggle { display: block !important; }
    }
    
    .mobile-toggle {
        display: none;
        background: none;
        border: none;
        font-size: 1.5rem;
        color: var(--primario);
    }
</style>

<!-- Overlay para el menú en mobile -->
<div class="sidebar-overlay" id="sidebarOverlay" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:1030;"></div>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="logo-container d-flex justify-content-between align-items-center">
        <h3 class="mb-0 fw-bold text-white"><i class="fas fa-layer-group me-2"></i> KIKE</h3>
        <button class="btn btn-sm text-white d-lg-none" id="closeSidebar"><i class="fas fa-times fa-lg"></i></button>
    </div>
    <div class="px-3 mb-4 text-center">
        <span class="badge bg-secondary text-uppercase" style="letter-spacing: 1px; font-size: 0.7rem;">SaaS Operative</span>
    </div>
    <div class="nav flex-column sidebar-nav">
        <a href="{{ route('dashboard') }}" class="nav-link"><i class="fas fa-tachometer-alt"></i> Cockpit General</a>
        <a href="{{ route('dashboard') }}#empresas-list" class="nav-link"><i class="fas fa-building"></i> Empresas (Clientes)</a>
        <a href="{{ route('owner.billing') }}" class="nav-link active"><i class="fas fa-file-invoice-dollar"></i> Facturación y Tarifas</a>
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
                <h4 class="mb-0 fw-bold d-none d-sm-block">Facturación y Reglas de Negocio</h4>
                <p class="text-muted mb-0 small">Escalas de tarifas y consumos globales</p>
            </div>
        </div>
        <div class="user-profile">
            @if(Auth::user()->avatar)
                <img src="{{ Storage::disk('public')->url(Auth::user()->avatar) }}" class="avatar rounded-circle shadow-sm" style="object-fit: cover; border: 2px solid var(--secundario);">
            @else
                <div class="avatar shadow-sm border border-light">
                    {{ substr(Auth::user()->name ?? 'O', 0, 1) }}
                </div>
            @endif
        </div>
    </div>

    <!-- Escala de Tarifas (Settings) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="tariff-card">
                <h5 class="fw-bold mb-1"><i class="fas fa-sliders-h me-2"></i> Simulador & Motor de Tarifas (Global)</h5>
                <p class="small mb-4 text-white-50">Configura cuánto vas a cobrar el mantenimiento base y los excedentes de consumo (Applies a todas las empresas como Default, luego podrás sobreescribir por empresa particular).</p>

                <form action="{{ route('owner.billing.update_tarifas') }}" method="POST">
                    @csrf
                    <div class="row g-3 px-2">
                        <div class="col-md-3">
                            <label class="small text-white-50 fw-bold mb-1">Mantenimiento Base (USD/ARS)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent text-white border-end-0 border-white-50"><i class="fas fa-dollar-sign"></i></span>
                                <input type="number" step="0.01" class="form-control input-transparent border-start-0" value="{{ $tarifaBase }}">
                            </div>
                            <small class="text-white-50" style="font-size: 0.70rem;">Precio mensual de la suscripción.</small>
                        </div>
                        <div class="col-md-3">
                            <label class="small text-white-50 fw-bold mb-1">Costo Usuario Excedido</label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent text-white border-end-0 border-white-50"><i class="fas fa-user-plus"></i></span>
                                <input type="number" step="0.01" class="form-control input-transparent border-start-0" value="{{ $precioPorUsuarioExtra }}">
                            </div>
                            <small class="text-white-50" style="font-size: 0.70rem;">Por c/ usuario arriba de los 50 iniciales.</small>
                        </div>
                        <div class="col-md-3">
                            <label class="small text-white-50 fw-bold mb-1">Costo Almacenamiento. (x GB Extra)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent text-white border-end-0 border-white-50"><i class="fas fa-hdd"></i></span>
                                <input type="number" step="0.01" class="form-control input-transparent border-start-0" value="{{ $precioPorGBExtra }}">
                            </div>
                            <small class="text-white-50" style="font-size: 0.70rem;">Por c/ 1,024 MB arriba de sus límites.</small>
                        </div>
                        <div class="col-md-3 d-flex align-items-center mt-4">
                            <button type="submit" class="btn btn-light fw-bold w-100 mt-2 px-4 shadow-sm" style="color: var(--primario);">
                                <i class="fas fa-save me-1"></i> Guardar Tarifario
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- KPIs Rápidos de Facturación -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="stat-card border-top-0 border-start border-4 border-primary">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small fw-bold mb-1">ESTIMADO A COBRAR</p>
                        <h4 class="mb-0 fw-bold text-dark">${{ number_format($ingresosEstimados ?? 0, 2) }}</h4>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary">
                        <i class="fas fa-money-bill-wave fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card border-top-0 border-start border-4 border-success">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small fw-bold mb-1">EMPRESAS AL DÍA</p>
                        <h4 class="mb-0 fw-bold text-dark">{{ $empresasAlDia }} <span class="text-muted fs-6">Clientes</span></h4>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success">
                        <i class="fas fa-smile fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card border-top-0 border-start border-4 border-danger">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small fw-bold mb-1">MORA O SUSPENDIDAS</p>
                        <h4 class="mb-0 fw-bold text-dark">{{ $empresasDeudoras }} <span class="text-muted fs-6">Clientes</span></h4>
                    </div>
                    <div class="bg-danger bg-opacity-10 p-3 rounded-circle text-danger">
                        <i class="fas fa-exclamation-circle fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card border-top-0 border-start border-4 text-white" style="background-color: var(--primario);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-white-50 small fw-bold mb-1">VOLUMEN OCUPADO (SaaS)</p>
                        <h4 class="mb-0 fw-bold text-white">{{ number_format($totalMbConsumidos ?? 0, 1) }} <span class="text-white-50 fs-6">MB Totales</span></h4>
                    </div>
                    <div class="bg-white bg-opacity-10 p-3 rounded-circle text-white">
                        <i class="fas fa-server fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Cobros Recientes / Simulador por Empresa -->
    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
            <h5 class="fw-bold"><i class="fas fa-file-invoice me-2 text-primary"></i> Liquidación de Clientes según Consumo</h5>
            <p class="text-muted small mb-0">Listado de empresas con su estimación de liquidación actual comparando uso vs límites.</p>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover table-custom">
                    <thead class="text-muted small text-uppercase">
                        <tr>
                            <th>Cliente</th>
                            <th>Usuarios / Límite</th>
                            <th>MB Utilizados / Límite</th>
                            <th class="text-center">Cobro Base</th>
                            <th class="text-center">Excedentes</th>
                            <th class="text-end">Total a Facturar</th>
                            <th class="text-center">Próx. Cobro</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($empresas as $emp)
                            @php
                                $usrCount = $emp->users_count ?? 0; // O la relación que indique los usuarios, por ej. Titulares + Familiares + Docentes...
                                // Si no está cargada la cuenta real, vamos a contar la cantidad de usuarios que tienen el empresa_id asociado
                                $usuariosReales = \App\Models\User::where('empresa_id', $emp->id)->count();
                                
                                $usuariosExtra = max(0, $usuariosReales - $emp->limite_usuarios);
                                $mbsExtra = max(0, $emp->consumo_actual_mb - $emp->limite_mb);
                                $gbsExtra = ceil($mbsExtra / 1024);
                                
                                $cobroExcedentesUsr = $usuariosExtra * $precioPorUsuarioExtra;
                                $cobroExcedentesMb = $gbsExtra * $precioPorGBExtra;
                                $totalExcedentes = $cobroExcedentesUsr + $cobroExcedentesMb;
                                
                                $totalMes = $tarifaBase + $totalExcedentes;
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light rounded p-2 me-3 text-center fw-bold text-primary" style="width:35px;height:35px;line-height:19px;">
                                            {{ substr($emp->nombre, 0, 1) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold">{{ $emp->nombre }}</h6>
                                            <span class="badge {{ $emp->estado_cuenta == 'al_dia' ? 'bg-success' : 'bg-danger' }} fw-normal" style="font-size:0.65rem;">{{ strtoupper($emp->estado_cuenta) }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold {{ $usuariosReales > $emp->limite_usuarios ? 'text-danger' : 'text-dark' }}">{{ $usuariosReales }}</span> <span class="text-muted small">/ {{ $emp->limite_usuarios }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold {{ $emp->consumo_actual_mb > $emp->limite_mb ? 'text-danger' : 'text-dark' }}">{{ number_format($emp->consumo_actual_mb, 1) }}</span> <span class="text-muted small">/ {{ $emp->limite_mb }} MB</span>
                                </td>
                                <td class="text-center">
                                    <span class="text-muted">${{ number_format($tarifaBase, 2) }}</span>
                                </td>
                                <td class="text-center">
                                    @if($totalExcedentes > 0)
                                        <span class="badge bg-warning text-dark px-2 py-1 shadow-sm">+ ${{ number_format($totalExcedentes, 2) }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <h6 class="mb-0 fw-bold text-primary">${{ number_format($totalMes, 2) }}</h6>
                                </td>
                                <td class="text-center text-muted small">
                                    {{ $emp->proximo_vencimiento ? \Carbon\Carbon::parse($emp->proximo_vencimiento)->format('d M') : \Carbon\Carbon::now()->addMonth()->format('d M') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No existen clientes todavía.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4 alert alert-info py-2 small d-flex align-items-center">
                <i class="fas fa-info-circle fa-lg me-3"></i>
                <p class="mb-0">
                    <strong>Nota Arquitectónica:</strong> El sistema calculará el cierre mensual de cada empresa e insertará automáticamente estas facturas (Cobro Base + Excedibles) al historial contable. Aún no está la pasarela de pago para cobranzas en automático conectada, pero puedes usar este simulador.
                </p>
            </div>
        </div>
    </div>
</div>

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
    });
</script>
</script>
</body>
</html>
