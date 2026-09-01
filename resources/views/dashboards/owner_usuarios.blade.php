<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INTEGRA | Control Central de Usuarios y Credenciales</title>
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
            --danger: #ef4444;
            --warning: #f59e0b;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--fondo);
            color: var(--primario);
        }

        /* Sidebar */
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

        /* Main Content */
        .main-content {
            margin-left: 250px;
            padding: 30px;
        }

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

        .gauge-card:hover { transform: translateY(-5px); }

        .gauge-icon {
            width: 55px;
            height: 55px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
        }

        .bg-blue-light { background: #dbeafe; color: #1e40af; }
        .bg-green-light { background: #dcfce7; color: #166534; }
        .bg-purple-light { background: #f3e8ff; color: #6b21a8; }
        .bg-red-light { background: #fee2e2; color: #b91c1c; }

        .gauge-info h3 {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
            line-height: 1.2;
        }

        .gauge-info p {
            margin: 0;
            color: #64748b;
            font-size: 0.85rem;
            font-weight: 500;
            text-transform: uppercase;
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
            font-size: 0.75rem;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 12px;
        }

        .table-custom td {
            vertical-align: middle;
            padding: 14px 10px;
            border-bottom: 1px solid #f1f5f9;
        }

        .badge-role {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .role-owner { background: #1e293b; color: #f8fafc; }
        .role-empresa { background: #dbeafe; color: #1e40af; }
        .role-auditor { background: #f3e8ff; color: #6b21a8; }
        .role-docente { background: #dcfce7; color: #166534; }

        .btn-clipboard {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            color: #334155;
            transition: all 0.2s;
        }
        .btn-clipboard:hover {
            background: #e2e8f0;
            border-color: #94a3b8;
        }

        @media (max-width: 991px) {
            .sidebar { transform: translateX(-100%); transition: transform 0.3s ease-in-out; }
            .sidebar.active { transform: translateX(0); }
            .main-content { margin-left: 0; padding: 15px; }
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="d-flex justify-content-between align-items-center px-4 mb-4">
            <a href="{{ route('dashboard') }}" class="sidebar-brand mb-0 w-100">
                <i class="fas fa-layer-group me-2"></i> INTEGRA
            </a>
        </div>
        <div class="px-3 mb-4 text-center">
            <span class="badge bg-secondary text-uppercase" style="letter-spacing: 1px; font-size: 0.7rem;">Modo Owner</span>
        </div>
        <div class="nav flex-column sidebar-nav">
            <a href="{{ route('dashboard') }}" class="nav-link"><i class="fas fa-tachometer-alt"></i> Cockpit General</a>
            <a href="{{ route('dashboard') }}#empresas-list" class="nav-link"><i class="fas fa-building"></i> Empresas (Clientes)</a>
            <a href="{{ route('owner.usuarios.index') }}" class="nav-link active"><i class="fas fa-users-cog"></i> Gestión de Usuarios</a>
            <a href="{{ route('owner.billing') }}" class="nav-link"><i class="fas fa-file-invoice-dollar"></i> Facturación y Tarifas</a>
            <a href="{{ route('owner.geografia') }}" class="nav-link"><i class="fas fa-map-marker-alt"></i> Base Geográfica</a>
            
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
            <div>
                <h4 class="mb-0 fw-bold"><i class="fas fa-users-cog text-primary me-2"></i> Control Central de Usuarios y Accesos</h4>
                <p class="text-muted mb-0 small">Administra administradores, auditores y contraseñas de todas tus empresas clientes.</p>
            </div>
            <div>
                <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoUsuario">
                    <i class="fas fa-user-plus me-1"></i> Crear Nuevo Usuario
                </button>
            </div>
        </div>

        <!-- Banner de Feedback con Credenciales Copiables -->
        @if(session('credentials_created') || session('credentials_updated'))
            @php
                $cred = session('credentials_created') ?? session('credentials_updated');
                $isNew = session('credentials_created') !== null;
                $loginUrl = url('/login');
                $copyText = "Hola {$cred['name']},\n\nAquí están tus credenciales de acceso para la plataforma INTEGRA:\n📌 Empresa: {$cred['empresa']}\n📧 Usuario: {$cred['email']}\n🔑 Contraseña: {$cred['password']}\n🌐 URL de Ingreso: {$loginUrl}\n\nPor favor guarda este mensaje.";
            @endphp
            <div class="alert alert-success border-0 shadow-sm p-4 mb-4 rounded-3" style="background: #f0fdf4; border-left: 5px solid #10b981 !important;">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h5 class="fw-bold text-success mb-1">
                            <i class="fas fa-check-circle me-2"></i> {{ $isNew ? '¡Nuevo Usuario Creado Con Éxito!' : '¡Contraseña Actualizada Con Éxito!' }}
                        </h5>
                        <p class="mb-0 text-dark small">
                            Usuario: <strong>{{ $cred['name'] }}</strong> ({{ $cred['email'] }}) | 
                            Empresa: <strong>{{ $cred['empresa'] }}</strong> | 
                            Nueva Contraseña: <span class="badge bg-dark text-warning font-monospace fs-6 px-2 py-1">{{ $cred['password'] }}</span>
                        </p>
                    </div>
                    <div>
                        <button class="btn btn-dark btn-sm rounded-pill px-3 shadow-sm" onclick="copiarTextoCredencial(`{{ addslashes($copyText) }}`)">
                            <i class="fab fa-whatsapp me-1 text-success"></i> Copiar Credenciales para WhatsApp
                        </button>
                    </div>
                </div>
            </div>
        @endif

        @if(session('success') && !session('credentials_created') && !session('credentials_updated'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- KPI Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="gauge-card">
                    <div class="gauge-info">
                        <p>Usuarios Registrados</p>
                        <h3>{{ $totalUsuarios }}</h3>
                    </div>
                    <div class="gauge-icon bg-blue-light">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="gauge-card">
                    <div class="gauge-info">
                        <p>Admins de Empresa</p>
                        <h3>{{ $totalAdmins }}</h3>
                    </div>
                    <div class="gauge-icon bg-green-light">
                        <i class="fas fa-user-shield"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="gauge-card">
                    <div class="gauge-info">
                        <p>Auditores Activos</p>
                        <h3>{{ $totalAuditores }}</h3>
                    </div>
                    <div class="gauge-icon bg-purple-light">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="gauge-card">
                    <div class="gauge-info">
                        <p>Empresas sin Admin</p>
                        <h3 class="{{ $empresasSinAdminCount > 0 ? 'text-danger' : 'text-success' }}">{{ $empresasSinAdminCount }}</h3>
                    </div>
                    <div class="gauge-icon {{ $empresasSinAdminCount > 0 ? 'bg-red-light' : 'bg-green-light' }}">
                        <i class="fas {{ $empresasSinAdminCount > 0 ? 'fa-exclamation-triangle' : 'fa-check-double' }}"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros y Tabla -->
        <div class="content-card">
            <form method="GET" action="{{ route('owner.usuarios.index') }}" class="row g-3 align-items-center mb-4 pb-3 border-bottom">
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted mb-1"><i class="fas fa-building me-1"></i> Filtrar por Empresa</label>
                    <select name="empresa_id" class="form-select border-1" onchange="this.form.submit()">
                        <option value="">-- Todas las Empresas --</option>
                        @foreach($empresas as $e)
                            <option value="{{ $e->id }}" {{ request('empresa_id') == $e->id ? 'selected' : '' }}>
                                {{ $e->nombre }} (ID #{{ str_pad($e->id, 3, '0', STR_PAD_LEFT) }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1"><i class="fas fa-user-tag me-1"></i> Filtrar por Rol</label>
                    <select name="role" class="form-select border-1" onchange="this.form.submit()">
                        <option value="">-- Todos los Roles --</option>
                        <option value="empresa" {{ request('role') == 'empresa' ? 'selected' : '' }}>Administrador de Empresa</option>
                        <option value="auditor" {{ request('role') == 'auditor' ? 'selected' : '' }}>Auditor de Empresa</option>
                        <option value="docente" {{ request('role') == 'docente' ? 'selected' : '' }}>Personal / Docente</option>
                        <option value="owner" {{ request('role') == 'owner' ? 'selected' : '' }}>Owner / Master System</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1"><i class="fas fa-search me-1"></i> Buscar por Nombre o Email</label>
                    <input type="text" name="q" class="form-control" placeholder="Escribe un nombre o correo..." value="{{ request('q') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2 pt-4">
                    <button type="submit" class="btn btn-secondary w-100"><i class="fas fa-filter"></i> Filtrar</button>
                    @if(request()->hasAny(['empresa_id', 'role', 'q']))
                        <a href="{{ route('owner.usuarios.index') }}" class="btn btn-outline-danger" title="Limpiar Filtros"><i class="fas fa-times"></i></a>
                    @endif
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-custom table-hover">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Empresa Asignada</th>
                            <th>Rol en Sistema</th>
                            <th>Fecha de Registro</th>
                            <th class="text-end">Acciones / Control</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuarios as $u)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar shadow-sm me-3 text-white fw-bold d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px; background: {{ $u->role === 'owner' ? '#0f172a' : ($u->role === 'empresa' ? '#3b82f6' : ($u->role === 'auditor' ? '#8b5cf6' : '#10b981')) }};">
                                            {{ strtoupper(substr($u->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold">{{ $u->name }}</h6>
                                            <small class="text-muted"><i class="fas fa-envelope me-1"></i>{{ $u->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($u->empresa)
                                        <span class="fw-bold text-dark"><i class="fas fa-building text-primary me-1"></i> {{ $u->empresa->nombre }}</span>
                                        <br><small class="text-muted">ID: #{{ str_pad($u->empresa->id, 3, '0', STR_PAD_LEFT) }}</small>
                                    @else
                                        <span class="badge bg-secondary">Global / Master (Sin Empresa)</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $roleClass = match($u->role) {
                                            'owner' => 'role-owner',
                                            'empresa', 'tenant' => 'role-empresa',
                                            'auditor' => 'role-auditor',
                                            'docente' => 'role-docente',
                                            default => 'bg-light text-dark'
                                        };
                                        $roleName = match($u->role) {
                                            'owner' => 'Master / Owner',
                                            'empresa', 'tenant' => 'Admin Empresa',
                                            'auditor' => 'Auditor',
                                            'docente' => 'Personal / Docente',
                                            default => ucfirst($u->role)
                                        };
                                        $roleIcon = match($u->role) {
                                            'owner' => 'fa-crown',
                                            'empresa', 'tenant' => 'fa-user-shield',
                                            'auditor' => 'fa-clipboard-check',
                                            'docente' => 'fa-user-tie',
                                            default => 'fa-user'
                                        };
                                    @endphp
                                    <span class="badge-role {{ $roleClass }}">
                                        <i class="fas {{ $roleIcon }}"></i> {{ $roleName }}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted"><i class="fas fa-calendar-alt me-1"></i> {{ $u->created_at ? $u->created_at->format('d/m/Y H:i') : 'N/A' }}</small>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex align-items-center justify-content-end gap-1">
                                        
                                        <!-- Botón Resetear Clave -->
                                        <button class="btn btn-sm btn-light text-warning fw-bold border shadow-sm px-2" 
                                                title="Resetear o Asignar Nueva Contraseña" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalResetPassword{{ $u->id }}">
                                            <i class="fas fa-key me-1"></i> Clave
                                        </button>

                                        <!-- Botón God Mode (Suplantar Identidad) -->
                                        @if($u->role !== 'owner')
                                            <a href="{{ route('impersonate.enter', $u->id) }}" 
                                               class="btn btn-sm btn-light text-success fw-bold border shadow-sm px-2" 
                                               title="Ingresar al sistema con la identidad de este usuario">
                                                <i class="fas fa-sign-in-alt me-1"></i> Entrar
                                            </a>
                                        @endif

                                        <!-- Botón Eliminar Usuario -->
                                        @if($u->id !== auth()->id() && $u->role !== 'owner')
                                            <button class="btn btn-sm btn-light text-danger border shadow-sm px-2" 
                                                    title="Eliminar este usuario" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalDeleteUser{{ $u->id }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        @endif

                                    </div>
                                </td>
                            </tr>

                            <!-- Modal Reset Clave Usuario {{ $u->id }} -->
                            <div class="modal fade" id="modalResetPassword{{ $u->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg">
                                        <div class="modal-header border-bottom-0">
                                            <h5 class="modal-title fw-bold text-primary">
                                                <i class="fas fa-key me-2"></i> Resetear Contraseña
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('owner.usuarios.reset_password', $u->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-body p-4 pt-1">
                                                <div class="card bg-light border-0 p-3 mb-3">
                                                    <div class="small text-muted">Usuario Objetivo:</div>
                                                    <div class="fw-bold text-dark fs-6">{{ $u->name }}</div>
                                                    <div class="small text-primary">{{ $u->email }}</div>
                                                    <div class="small text-secondary mt-1">Empresa: {{ $u->empresa->nombre ?? 'Global Master' }}</div>
                                                </div>

                                                <label class="form-label fw-bold small text-muted">Nueva Contraseña para el Usuario</label>
                                                <div class="input-group mb-2">
                                                    <span class="input-group-text bg-white"><i class="fas fa-lock text-warning"></i></span>
                                                    <input type="text" name="new_password" id="passInput{{ $u->id }}" class="form-control" placeholder="Escribe la nueva contraseña...">
                                                    <button type="button" class="btn btn-outline-secondary" onclick="generarClave(`passInput{{ $u->id }}`)">
                                                        <i class="fas fa-random me-1"></i> Generar Clave
                                                    </button>
                                                </div>
                                                <small class="text-muted d-block">Si dejas el campo en blanco y presionas actualizar, se generará automáticamente una clave segura aleatoria.</small>
                                            </div>
                                            <div class="modal-footer border-top-0">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-warning fw-bold text-dark px-4"><i class="fas fa-save me-1"></i> Actualizar Contraseña</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Eliminar Usuario {{ $u->id }} -->
                            @if($u->id !== auth()->id() && $u->role !== 'owner')
                            <div class="modal fade" id="modalDeleteUser{{ $u->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-sm">
                                    <div class="modal-content border-0 shadow-lg">
                                        <div class="modal-body p-4 text-center">
                                            <div class="text-danger mb-3"><i class="fas fa-exclamation-triangle fa-3x"></i></div>
                                            <h6 class="fw-bold">¿Eliminar Usuario?</h6>
                                            <p class="small text-muted mb-4">Vas a eliminar a <strong>{{ $u->name }}</strong> ({{ $u->email }}). Esta acción no se puede deshacer.</p>
                                            <form action="{{ route('owner.usuarios.destroy', $u->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <div class="d-flex gap-2">
                                                    <button type="button" class="btn btn-light w-50" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-danger w-50">Sí, Eliminar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-muted mb-2"><i class="fas fa-user-slash fa-3x"></i></div>
                                    <h6>No se encontraron usuarios con los filtros aplicados.</h6>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="mt-4">
                {{ $usuarios->links() }}
            </div>
        </div>

    </div>

    <!-- Modal Crear Nuevo Usuario Master -->
    <div class="modal fade" id="modalNuevoUsuario" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold text-primary">
                        <i class="fas fa-user-plus me-2"></i> Crear Nuevo Usuario en el Sistema
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('owner.usuarios.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4 pt-1">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Empresa Cliente Asignada *</label>
                            <select name="empresa_id" class="form-select" required>
                                <option value="">-- Seleccionar Empresa --</option>
                                @foreach($empresas as $e)
                                    <option value="{{ $e->id }}" {{ request('empresa_id') == $e->id ? 'selected' : '' }}>
                                        {{ $e->nombre }} (ID #{{ str_pad($e->id, 3, '0', STR_PAD_LEFT) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Rol en el Sistema *</label>
                            <select name="role" class="form-select" required>
                                <option value="empresa">Administrador de Empresa (Tenant Admin)</option>
                                <option value="auditor">Auditor de Empresa</option>
                                <option value="docente">Personal / Docente / Terapeuta</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Nombre Completo *</label>
                            <input type="text" name="name" class="form-control" placeholder="Ej: Juan Pérez" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Correo Electrónico (Login) *</label>
                            <input type="email" name="email" class="form-control" placeholder="Ej: admin@cliente.com" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Contraseña (Opcional)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-lock text-muted"></i></span>
                                <input type="text" name="password" id="newPassInput" class="form-control" placeholder="Dejar vacío para auto-generar">
                                <button type="button" class="btn btn-outline-secondary" onclick="generarClave('newPassInput')">
                                    <i class="fas fa-random"></i> Generar
                                </button>
                            </div>
                            <small class="text-muted">Si se deja vacío, el sistema asignará una contraseña segura automáticamente.</small>
                        </div>

                    </div>
                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary px-4 fw-bold"><i class="fas fa-plus-circle me-1"></i> Crear y Generar Acceso</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function generarClave(inputId) {
            const chars = "ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789!@#$";
            let pass = "";
            for (let i = 0; i < 9; i++) {
                pass += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            document.getElementById(inputId).value = pass;
        }

        function copiarTextoCredencial(texto) {
            navigator.clipboard.writeText(texto).then(() => {
                alert("¡Credenciales copiadas al portapapeles! Puedes pegarlas directamente en WhatsApp o Email.");
            }).catch(err => {
                alert("Error al copiar texto: " + err);
            });
        }
    </script>
</body>
</html>
