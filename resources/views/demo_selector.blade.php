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
            <!-- 1. Simular PWA Docente (App Móvil) -->
            <a href="{{ url('/app-docente/demo') }}" class="btn-demo-option btn-pwa">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-success text-white me-3">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-success fs-6 mb-0">1. Simular PWA Docente (App Móvil)</div>
                        <small class="text-muted d-block" style="font-size: 0.8rem;">Carga de facturas, scanner de resolución, dictado por voz y lista de alumnos</small>
                    </div>
                </div>
            </a>

            <!-- 2. Portal Directora de Escuela -->
            <a href="{{ route('directora.asistencias.demo') }}" class="btn-demo-option btn-admin">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-info text-white me-3">
                        <i class="fas fa-signature"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-info fs-6 mb-0" style="color: #0284c7 !important;">2. Portal Directora de Escuela</div>
                        <small class="text-muted d-block" style="font-size: 0.8rem;">Certificación digital oficial de asistencia y horarios (Trazabilidad expresa)</small>
                    </div>
                </div>
            </a>

            <!-- 3. App Padre / Titular (Reintegros) -->
            <a href="{{ route('padre.dashboard.demo') }}" class="btn-demo-option btn-auditor">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-warning text-dark me-3">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-warning fs-6 mb-0" style="color: #d97706 !important;">3. App Padre / Titular (Reintegros)</div>
                        <small class="text-muted d-block" style="font-size: 0.8rem;">Modalidad Reintegro, carga de resolución por el padre y aval de servicio</small>
                    </div>
                </div>
            </a>

            <!-- 4. Entrar como Auditor Interno / Admin -->
            <a href="{{ route('auditor.docentes.legajos') }}" class="btn-demo-option btn-admin" style="border-color: #6366f1;">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-primary text-white me-3">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-primary fs-6 mb-0">4. Portal Auditoría y Administración</div>
                        <small class="text-muted d-block" style="font-size: 0.8rem;">Control de expediente total, liquidación, salto de billeteras y novedades</small>
                    </div>
                </div>
            </a>

            <!-- 5. Red de Prestadores Médicos y Sanatorios -->
            <a href="{{ route('prestadores.demo') }}" class="btn-demo-option btn-pwa" style="border-color: #0284c7;">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-info text-white me-3">
                        <i class="fas fa-hospital"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-info fs-6 mb-0" style="color: #0284c7 !important;">5. Red de Prestadores Médicos y Clínicas</div>
                        <small class="text-muted d-block" style="font-size: 0.8rem;">Médicos particulares y sanatorios (Órdenes, internaciones para 130.000 abonados)</small>
                    </div>
                </div>
            </a>

            <!-- 6. Auditoría Médica Central Mutual -->
            <a href="{{ route('auditor.prestaciones.demo') }}" class="btn-demo-option btn-auditor" style="border-color: #ef4444;">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-danger text-white me-3">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-danger fs-6 mb-0">6. Auditoría Médica Central Mutual</div>
                        <small class="text-muted d-block" style="font-size: 0.8rem;">Aprobación de quirófano, días de cama, resonancias y prácticas en 1-Clic</small>
                    </div>
                </div>
            </a>

            <!-- 7. Cuadro de Mando Ejecutivo (Panel del Dueño de la Mutual - 130.000 Cápitas) -->
            <a href="{{ route('owner.mutual_dashboard') }}" class="btn-demo-option btn-admin" style="border-color: #f59e0b; background: linear-gradient(135deg, #fffbeb, #ffffff);">
                <div class="d-flex align-items-center">
                    <div class="icon-circle text-white me-3" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                        <i class="fas fa-crown"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-6 mb-0" style="color: #b45309 !important;">7. Cuadro de Mando Ejecutivo (Panel Dueño Mutual)</div>
                        <small class="text-muted d-block" style="font-size: 0.8rem;">Visión 360° Recaudación $1.820M, 130.000 cápitas, siniestralidad y ahorro IA</small>
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
