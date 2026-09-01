<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SaaS Facturación - INTEGRA</title>
    
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

    /* Sidebar Styles */
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
    }

    .stat-card {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        transition: transform 0.2s;
    }

    .table-custom {
        background: white;
        border-radius: 15px;
    }

    .table-custom th {
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        color: #8898aa;
        border-bottom: 1px solid #e9ecef;
        padding: 1rem;
    }

    .table-custom td {
        padding: 1rem;
        vertical-align: middle;
    }

    /* Overlay */
    .sidebar-overlay {
        display: none;
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 1030;
    }

    @media (max-width: 991.98px) {
        .sidebar { transform: translateX(-100%); }
        .sidebar.active { transform: translateX(0); }
        .main-content { margin-left: 0; padding: 1rem; }
    }
</style>
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="logo-container d-flex justify-content-between align-items-center">
        <h3 class="mb-0 fw-bold text-white"><i class="fas fa-layer-group me-2"></i> INTEGRA</h3>
        <button class="btn btn-sm text-white d-lg-none" id="closeSidebar"><i class="fas fa-times fa-lg"></i></button>
    </div>
    <div class="px-3 mb-4 text-center">
        <span class="badge bg-danger bg-opacity-20 text-danger px-3 py-2 rounded-pill fw-bold border border-danger border-opacity-20" style="font-size: 0.7rem; letter-spacing: 1px;">
            <i class="fas fa-crown me-1"></i> OWNER COCKPIT
        </span>
    </div>
    
    <nav class="sidebar-nav">
        <a href="{{ route('dashboard') }}" class="nav-link">
            <i class="fas fa-chart-pie"></i> Visión General
        </a>
        <a href="{{ route('owner.geografia') }}" class="nav-link">
            <i class="fas fa-globe-americas"></i> Base Geográfica
        </a>
        <a href="{{ route('owner.billing') }}" class="nav-link active">
            <i class="fas fa-file-invoice-dollar"></i> Facturación y Reglas
        </a>
        <a href="{{ route('auditor.facturas') }}" class="nav-link">
            <i class="fas fa-receipt"></i> Auditoría Facturas
        </a>
        <a href="{{ route('auditor.documentos') }}" class="nav-link">
            <i class="fas fa-file-signature"></i> Auditoría Docs
        </a>
        
        <div class="px-4 my-4"><hr class="text-white-50 opacity-10"></div>
        
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <a href="{{ route('logout') }}" class="nav-link text-danger" onclick="event.preventDefault(); this.closest('form').submit();">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </a>
        </form>
    </nav>
</div>

<!-- Main Content -->
<div class="main-content">
    
    <!-- Topbar -->
    <div class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="mobile-toggle btn btn-sm text-dark d-lg-none" id="openSidebar">
                <i class="fas fa-bars fa-lg"></i>
            </button>
            <div>
                <h4 class="mb-0 fw-bold">Gestión Comercial & Cobranzas por Empresa</h4>
                <p class="text-muted mb-0 small">Configura planes, cuotas personalizadas, fechas de gracia y verifica comprobantes</p>
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

    <!-- Alert Feedback Flash -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- KPIs Rápidos de Facturación -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card border-top-0 border-start border-4 border-primary">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small fw-bold mb-1">ESTIMADO A COBRAR</p>
                        <h4 class="mb-0 fw-bold text-dark">${{ number_format($ingresosEstimados ?? 0, 2, ',', '.') }}</h4>
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
                        <p class="text-muted small fw-bold mb-1">MORA / DEMO / GRACIA</p>
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

    <!-- TABLA PRINCIPAL: LIQUIDACIÓN Y CONFIGURACIÓN COMERCIAL POR EMPRESA -->
    <div class="card border-0 shadow-sm mb-5" style="border-radius: 15px;">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4 d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h5 class="fw-bold mb-1"><i class="fas fa-building me-2 text-primary"></i> Control Comercial & Tarifas por Empresa</h5>
                <p class="text-muted small mb-0">Administra cuotas fojas, período de gracia, planes demo y vencimientos de cada cliente de forma individual.</p>
            </div>
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill font-monospace">
                Total: {{ count($empresas) }} Instituciones
            </span>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover table-custom align-middle">
                    <thead class="text-muted small text-uppercase">
                        <tr>
                            <th>Cliente / Institución</th>
                            <th>Usuarios / Límite</th>
                            <th>MB Utilizados / Límite</th>
                            <th class="text-center">Cuota Fija Mensual</th>
                            <th class="text-center">Excedentes</th>
                            <th class="text-end">Total a Facturar</th>
                            <th class="text-center">Próx. Cobro</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($empresas as $emp)
                            @php
                                $usuariosReales = \App\Models\User::where('empresa_id', $emp->id)->count();
                                $usuariosExtra = max(0, $usuariosReales - $emp->limite_usuarios);
                                $mbsExtra = max(0, $emp->consumo_actual_mb - $emp->limite_mb);
                                $gbsExtra = ceil($mbsExtra / 1024);
                                
                                $cobroExcedentesUsr = $usuariosExtra * ($precioPorUsuarioExtra ?? 1.50);
                                $cobroExcedentesMb = $gbsExtra * ($precioPorGBExtra ?? 5.00);
                                $totalExcedentes = $cobroExcedentesUsr + $cobroExcedentesMb;
                                
                                $cuotaBase = $emp->monto_cuota_mensual ?? 50.00;
                                $esDemo = ($emp->plan_tipo === 'demo');
                                $enGracia = ($emp->periodo_gracia_hasta && \Carbon\Carbon::parse($emp->periodo_gracia_hasta)->isFuture());

                                if ($esDemo || $enGracia) {
                                    $totalMes = 0.00;
                                } else {
                                    $totalMes = $cuotaBase + $totalExcedentes;
                                }

                                $proxFecha = $emp->proximo_vencimiento 
                                    ? \Carbon\Carbon::parse($emp->proximo_vencimiento)->format('d/m/Y') 
                                    : \Carbon\Carbon::now()->addMonth()->format('d/m/Y');
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light rounded-circle p-2 me-3 text-center fw-bold text-primary shadow-sm" style="width:40px;height:40px;line-height:24px;">
                                            {{ strtoupper(substr($emp->nombre, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-bold text-dark">{{ $emp->nombre }}</h6>
                                            <div class="d-flex align-items-center gap-1 flex-wrap">
                                                <span class="badge {{ $emp->estado_cuenta == 'al_dia' ? 'bg-success' : 'bg-danger' }} fw-normal" style="font-size:0.65rem;">
                                                    {{ strtoupper($emp->estado_cuenta) }}
                                                </span>
                                                @if($esDemo)
                                                    <span class="badge text-white font-monospace" style="font-size:0.65rem; background: #8b5cf6;">🎁 DEMO SIN CARGO</span>
                                                @elseif($enGracia)
                                                    <span class="badge bg-warning text-dark font-monospace" style="font-size:0.65rem;">⏳ GRACIA HASTA {{ \Carbon\Carbon::parse($emp->periodo_gracia_hasta)->format('d/m/Y') }}</span>
                                                @elseif($emp->plan_tipo === 'personalizado')
                                                    <span class="badge bg-info text-dark font-monospace" style="font-size:0.65rem;">✨ PERSONALIZADO</span>
                                                @elseif($emp->plan_tipo === 'bonificado')
                                                    <span class="badge bg-secondary text-white font-monospace" style="font-size:0.65rem;">🏷️ BONIFICADO</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold {{ $usuariosReales > $emp->limite_usuarios ? 'text-danger' : 'text-dark' }}">{{ $usuariosReales }}</span> 
                                    <span class="text-muted small">/ {{ $emp->limite_usuarios }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold {{ $emp->consumo_actual_mb > $emp->limite_mb ? 'text-danger' : 'text-dark' }}">{{ number_format($emp->consumo_actual_mb, 1) }}</span> 
                                    <span class="text-muted small">/ {{ $emp->limite_mb }} MB</span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold text-dark">${{ number_format($cuotaBase, 2, ',', '.') }}</span>
                                </td>
                                <td class="text-center">
                                    @if($totalExcedentes > 0)
                                        <span class="badge bg-warning text-dark px-2 py-1 shadow-sm">+ ${{ number_format($totalExcedentes, 2, ',', '.') }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($esDemo || $enGracia)
                                        <h6 class="mb-0 fw-bold text-success">$0.00 <small class="text-muted small">(Exento)</small></h6>
                                    @else
                                        <h6 class="mb-0 fw-bold text-primary">${{ number_format($totalMes, 2, ',', '.') }}</h6>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border font-monospace px-2 py-1">{{ $proxFecha }}</span>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalConfigPlan{{ $emp->id }}" title="Configurar Tarifa y Vencimiento">
                                        <i class="fas fa-sliders-h me-1"></i> Configurar Plan
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">No existen empresas registradas en el sistema.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4 alert alert-info py-2 small d-flex align-items-center mb-0">
                <i class="fas fa-info-circle fa-lg me-3 text-info"></i>
                <p class="mb-0">
                    <strong>Cobro Directo sin Comisiones:</strong> Tus clientes pueden transferir a tus cuentas/billeteras habilitadas y subir el comprobante. El motor de IA (Gemini Vision) extraerá automáticamente el monto y número de operación para tu verificación en 1-Clic.
                </p>
            </div>
        </div>
    </div>

    <!-- SECCIÓN: CUENTAS BANCARIAS HABILITADAS Y RECEPCIÓN DE PAGOS -->
    <div class="row mb-4">
        <!-- Cuentas Habilitadas -->
        <div class="col-lg-4 mb-3">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 15px;">
                <div class="card-header bg-dark text-white fw-bold py-3 d-flex align-items-center justify-content-between" style="border-radius: 15px 15px 0 0;">
                    <span><i class="fas fa-university me-2 text-warning"></i> Mis Cuentas / Billeteras</span>
                    <span class="badge bg-success">Sin Comisiones</span>
                </div>
                <div class="card-body">
                    <div class="border-start border-primary border-4 ps-3 mb-3 bg-light p-2 rounded">
                        <h6 class="mb-1 fw-bold text-dark"><i class="fas fa-building-columns me-1 text-primary"></i> Banco Santander</h6>
                        <small class="text-muted d-block font-monospace">CBU: 0720000020000012345678</small>
                        <small class="text-muted d-block">Alias: INTEGRA.SANTANDER</small>
                    </div>
                    <div class="border-start border-success border-4 ps-3 mb-3 bg-light p-2 rounded">
                        <h6 class="mb-1 fw-bold text-dark"><i class="fas fa-landmark me-1 text-success"></i> Banco Provincia</h6>
                        <small class="text-muted d-block font-monospace">CBU: 0140000011000087654321</small>
                        <small class="text-muted d-block">Alias: INTEGRA.PROVINCIA</small>
                    </div>
                    <div class="border-start border-warning border-4 ps-3 mb-3 bg-light p-2 rounded">
                        <h6 class="mb-1 fw-bold text-dark"><i class="fas fa-wallet me-1 text-warning"></i> Billetera ARQ / DollarApp</h6>
                        <small class="text-muted d-block font-monospace">CVU: 0000003100087654321098</small>
                        <small class="text-muted d-block">Alias: INTEGRA.ARQ</small>
                    </div>
                    <div class="border-start border-secondary border-4 ps-3 bg-light p-2 rounded">
                        <h6 class="mb-1 fw-bold text-dark"><i class="fas fa-money-bill-wave me-1 text-secondary"></i> Pago Presencial / Efectivo</h6>
                        <small class="text-muted d-block">Cobro directo en oficina / sede principal</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Comprobantes Recibidos (Verificación por IA) -->
        <div class="col-lg-8 mb-3">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 15px;">
                <div class="card-header bg-dark text-white fw-bold py-3 d-flex justify-content-between align-items-center" style="border-radius: 15px 15px 0 0;">
                    <span><i class="fas fa-receipt me-2 text-info"></i> Comprobantes de Pago (IA Reader Gemini)</span>
                    <span class="badge bg-info text-dark">{{ count($pagos ?? []) }} Reportados</span>
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light small">
                            <tr>
                                <th>Empresa</th>
                                <th>N° Comprobante (IA)</th>
                                <th>Monto</th>
                                <th>Banco</th>
                                <th>Estado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pagos ?? [] as $pago)
                                <tr>
                                    <td>
                                        <strong class="text-dark">{{ $pago->empresa->nombre ?? 'Tenant #' . $pago->empresa_id }}</strong>
                                        <br><small class="text-muted">{{ $pago->created_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary font-monospace">{{ $pago->nro_comprobante ?? 'Sin N°' }}</span>
                                    </td>
                                    <td>
                                        <strong class="text-success">${{ number_format($pago->monto, 2, ',', '.') }}</strong>
                                    </td>
                                    <td>
                                        <small class="text-muted"><i class="fas fa-university me-1"></i>{{ $pago->banco_origen ?? 'Transferencia' }}</small>
                                    </td>
                                    <td>
                                        @if($pago->estado === 'aprobado')
                                            <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Aprobado</span>
                                        @elseif($pago->estado === 'rechazado')
                                            <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Rechazado</span>
                                        @else
                                            <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Pendiente</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($pago->comprobante_url)
                                            <a href="{{ asset('storage/' . $pago->comprobante_url) }}" target="_blank" class="btn btn-sm btn-outline-secondary me-1" title="Ver Comprobante">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endif

                                        @if($pago->estado === 'pendiente_verificacion')
                                            <form action="{{ route('pagos.aprobar', $pago->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success shadow-sm" title="Aprobar Pago en 1-Clic" onclick="return confirm('¿Confirmas que verificaste esta transferencia en tu homebanking?')">
                                                    <i class="fas fa-check me-1"></i> Aprobar
                                                </button>
                                            </form>
                                            <form action="{{ route('pagos.rechazar', $pago->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger shadow-sm" title="Rechazar Comprobante">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted small">No hay comprobantes de pago reportados pendientes.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CONTENEDOR DE MODALES FUERA DE LA TABLA (COMPATIBILIDAD BOOTSTRAP 5) -->
@foreach($empresas as $emp)
    <div class="modal fade" id="modalConfigPlan{{ $emp->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $emp->id }}" aria-hidden="true" style="color: #0f172a;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 18px;">
                <div class="modal-header bg-dark text-white border-0" style="border-radius: 18px 18px 0 0;">
                    <h5 class="modal-title fw-bold" id="modalLabel{{ $emp->id }}">
                        <i class="fas fa-sliders-h me-2 text-warning"></i> Configuración Comercial: {{ $emp->nombre }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('owner.empresas.update_billing_config', $emp->id) }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Tipo de Plan Comercial</label>
                            <select name="plan_tipo" class="form-select border-secondary border-opacity-20">
                                <option value="estandar" {{ ($emp->plan_tipo ?? 'estandar') === 'estandar' ? 'selected' : '' }}>Estándar (Tarifa Normal)</option>
                                <option value="demo" {{ ($emp->plan_tipo ?? '') === 'demo' ? 'selected' : '' }}>🎁 Demo / Demostración (Sin Cobro Permanente)</option>
                                <option value="personalizado" {{ ($emp->plan_tipo ?? '') === 'personalizado' ? 'selected' : '' }}>✨ Personalizado / Acuerdo Especial</option>
                                <option value="bonificado" {{ ($emp->plan_tipo ?? '') === 'bonificado' ? 'selected' : '' }}>🏷️ Bonificado / Descuento</option>
                            </select>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Cuota Mensual Fija ($)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" min="0" name="monto_cuota_mensual" class="form-control" value="{{ $emp->monto_cuota_mensual ?? 50.00 }}" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Próximo Vencimiento</label>
                                <input type="date" name="proximo_vencimiento" class="form-control" value="{{ $emp->proximo_vencimiento ? \Carbon\Carbon::parse($emp->proximo_vencimiento)->format('Y-m-d') : '' }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Período de Gracia (No Paga Hasta)</label>
                            <input type="date" name="periodo_gracia_hasta" class="form-control" value="{{ $emp->periodo_gracia_hasta ? \Carbon\Carbon::parse($emp->periodo_gracia_hasta)->format('Y-m-d') : '' }}">
                            <small class="text-muted">Si está en fecha futura, el sistema mostrará la cuota exenta a $0.</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Límite Usuarios</label>
                                <input type="number" name="limite_usuarios" class="form-control" value="{{ $emp->limite_usuarios ?? 50 }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Límite MB</label>
                                <input type="number" name="limite_mb" class="form-control" value="{{ $emp->limite_mb ?? 500 }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Notas Internas de Facturación</label>
                            <textarea name="notas_facturacion" class="form-control" rows="2" placeholder="Ej: Cliente socio fundador o plazo especial negociado.">{{ $emp->notas_facturacion ?? '' }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pb-4 pe-4">
                        <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
</body>
</html>
