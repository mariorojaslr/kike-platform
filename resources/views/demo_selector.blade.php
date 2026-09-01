<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INTEGRA | Hub de Demostración en Vivo</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #0f172a;
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .demo-card {
            background: #ffffff;
            color: #0f172a;
            border-radius: 20px;
            max-width: 550px;
            width: 100%;
            padding: 35px 30px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .btn-demo-option {
            border-radius: 14px;
            padding: 18px 20px;
            text-align: left;
            transition: all 0.25s ease;
            text-decoration: none;
            display: block;
            border: 2px solid #e2e8f0;
            background: #ffffff;
            margin-bottom: 16px;
        }

        .btn-demo-option:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .btn-admin { border-color: #3b82f6; }
        .btn-admin:hover { background: #f0f6ff; border-color: #2563eb; }

        .btn-auditor { border-color: #f59e0b; }
        .btn-auditor:hover { background: #fffbeb; border-color: #d97706; }

        .btn-pwa { border-color: #10b981; }
        .btn-pwa:hover { background: #ecfdf5; border-color: #059669; }

        .icon-circle {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }
    </style>
</head>
<body>

    <div class="demo-card">
        <div class="text-center mb-4">
            <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle mb-3" style="width: 60px; height: 60px; font-size: 1.8rem;">
                <i class="fas fa-layer-group"></i>
            </div>
            <h4 class="fw-bold mb-1">Simular Rol de Usuario</h4>
            <p class="text-muted small">¿Cómo deseas visualizar el entorno de la plataforma INTEGRA?</p>
        </div>

        <div class="d-grid">
            <!-- 1. Entrar como Administrador -->
            <a href="{{ route('tenant.dashboard') }}" class="btn-demo-option btn-admin">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-primary text-white me-3">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-primary fs-6 mb-0">Entrar como Administrador</div>
                        <small class="text-muted d-block" style="font-size: 0.8rem;">Acceso total al Panel de Cliente (Pagos, Configuración, Estadísticas y Empleados)</small>
                    </div>
                </div>
            </a>

            <!-- 2. Entrar como Auditor Interno -->
            <a href="{{ route('auditor.docentes.legajos') }}" class="btn-demo-option btn-auditor">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-warning text-dark me-3">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-warning fs-6 mb-0" style="color: #d97706 !important;">Entrar como Auditor Interno</div>
                        <small class="text-muted d-block" style="font-size: 0.8rem;">Herramientas para revisar Vencimientos de Documentos y Reportes</small>
                    </div>
                </div>
            </a>

            <!-- 3. Simular PWA Docente (App Móvil) -->
            <a href="{{ url('/app-docente/demo') }}" class="btn-demo-option btn-pwa">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-success text-white me-3">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-success fs-6 mb-0">Simular PWA Docente (App)</div>
                        <small class="text-muted d-block" style="font-size: 0.8rem;">Probar la Interfaz Móvil y link seguro que verán los Terapeutas</small>
                    </div>
                </div>
            </a>
        </div>

        <div class="text-center mt-3 pt-3 border-top">
            <small class="text-muted">Plataforma INTEGRA &bull; Modo Demostración</small>
        </div>
    </div>

</body>
</html>
