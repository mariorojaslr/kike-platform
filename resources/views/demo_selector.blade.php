<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#0f172a">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <title>APOS La Rioja | Directorio Oficial de Enlaces & Accesos por Rol (INTEGRA)</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #0b1329;
            color: #f8fafc;
            min-height: 100vh;
            padding-bottom: 70px;
        }

        .header-selector {
            background: linear-gradient(135deg, #0f172a 0%, #0369a1 60%, #0284c7 100%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
            padding: 28px 0;
            margin-bottom: 28px;
        }

        .sector-title {
            font-size: 1.15rem;
            font-weight: 700;
            letter-spacing: 0.3px;
            margin-top: 32px;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.14);
            padding-bottom: 10px;
        }

        .link-card {
            background: #16203a;
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 16px;
            padding: 20px;
            transition: all 0.22s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
        }

        .link-card:hover {
            transform: translateY(-4px);
            border-color: #38bdf8;
            box-shadow: 0 14px 28px rgba(2, 132, 199, 0.28);
            background: #1b2848;
        }

        .card-desc {
            color: #cbd5e1;
            font-size: 0.85rem;
            line-height: 1.5;
            margin-bottom: 16px;
        }

        .icon-box {
            width: 48px;
            height: 48px;
            border-radius: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            flex-shrink: 0;
        }

        /* Icon boxes con alto contraste */
        .icon-emerald { background: rgba(16, 185, 129, 0.18); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.35); }
        .icon-sky     { background: rgba(14, 165, 233, 0.18); color: #38bdf8; border: 1px solid rgba(14, 165, 233, 0.35); }
        .icon-amber   { background: rgba(245, 158, 11, 0.18); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.35); }
        .icon-purple  { background: rgba(139, 92, 246, 0.18); color: #c084fc; border: 1px solid rgba(139, 92, 246, 0.35); }
        .icon-blue    { background: rgba(59, 130, 246, 0.18); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.35); }
        .icon-rose    { background: rgba(244, 63, 94, 0.18);  color: #fb7185; border: 1px solid rgba(244, 63, 94, 0.35); }

        /* Badges legibles sobre fondo oscuro */
        .badge-role {
            display: inline-block;
            font-size: 0.7rem;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 50px;
            margin-top: 3px;
        }
        .badge-emerald { background: rgba(16, 185, 129, 0.2); color: #6ee7b7; border: 1px solid rgba(16, 185, 129, 0.4); }
        .badge-sky     { background: rgba(14, 165, 233, 0.2); color: #7dd3fc; border: 1px solid rgba(14, 165, 233, 0.4); }
        .badge-amber   { background: rgba(245, 158, 11, 0.2); color: #fcd34d; border: 1px solid rgba(245, 158, 11, 0.4); }
        .badge-purple  { background: rgba(139, 92, 246, 0.2); color: #d8b4fe; border: 1px solid rgba(139, 92, 246, 0.4); }
        .badge-blue    { background: rgba(59, 130, 246, 0.2); color: #93c5fd; border: 1px solid rgba(59, 130, 246, 0.4); }
        .badge-rose    { background: rgba(244, 63, 94, 0.2);  color: #fda4af; border: 1px solid rgba(244, 63, 94, 0.4); }

        .card-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            padding-top: 14px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn-copy-link {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.22);
            color: #f8fafc;
            font-size: 0.78rem;
            font-weight: 600;
            border-radius: 50px;
            padding: 6px 13px;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-copy-link:hover {
            background: #2563eb;
            color: #ffffff;
            border-color: #2563eb;
        }

        .btn-wa-link {
            background: rgba(22, 163, 74, 0.22);
            border: 1px solid #22c55e;
            color: #86efac;
            font-size: 0.78rem;
            font-weight: 600;
            border-radius: 50px;
            padding: 6px 13px;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-wa-link:hover {
            background: #16a34a;
            color: #ffffff;
        }

        .btn-open-link {
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 700;
            padding: 6px 16px;
            white-space: nowrap;
        }

        @media (max-width: 576px) {
            .header-selector { padding: 20px 0; }
            .link-card { padding: 16px; }
            .card-actions {
                flex-direction: column;
                align-items: stretch;
            }
            .card-actions .share-group {
                display: flex;
                width: 100%;
                gap: 8px;
            }
            .card-actions .share-group button {
                flex: 1;
                justify-content: center;
            }
            .card-actions .btn-open-link {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>

    <!-- Header Principal Institucional APOS La Rioja -->
    <header class="header-selector shadow">
        <div class="container">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ asset('images/apos_logo.svg') }}" alt="APOS La Rioja" class="rounded-4 shadow" style="width: 68px; height: 68px; background: #fff; padding: 3px;">
                    <div>
                        <div class="d-flex flex-wrap gap-2 mb-1">
                            <span class="badge bg-info text-dark fw-bold rounded-pill px-3 py-1" style="font-size: 0.72rem;">
                                <i class="fas fa-landmark me-1"></i> APOS LA RIOJA (160.000 AFILIADOS)
                            </span>
                            <span class="badge bg-light bg-opacity-25 text-white border border-light border-opacity-50 rounded-pill px-3 py-1" style="font-size: 0.72rem;">
                                <i class="fas fa-sitemap me-1"></i> Sucursal: Soluciones Digitales MCR
                            </span>
                        </div>
                        <h3 class="fw-bold text-white mb-1" style="font-size: 1.45rem;">Directorio de Accesos y Links por Rol (WhatsApp & PWA)</h3>
                        <p class="mb-0 small" style="color: #e2e8f0;">
                            Desde aquí el <strong>Jefe de Área</strong> o Administrador comparte por WhatsApp el link exacto a cada <strong>Auditor, Docente, Directora de Escuela o Padre Titular</strong>.
                        </p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('tenant.dashboard') }}" class="btn btn-light fw-bold rounded-pill px-4 shadow-sm text-primary">
                        <i class="fas fa-arrow-left me-1"></i> Panel APOS
                    </a>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-light fw-bold rounded-pill px-3">
                        <i class="fas fa-layer-group me-1"></i> Cockpit Owner
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="container">

        <!-- Toast de Confirmación de Copiado -->
        <div id="toast-copy" class="alert alert-success position-fixed top-0 end-0 m-3 shadow-lg border-0 rounded-pill px-4 py-2" style="display: none; z-index: 999999; font-size: 0.85rem;">
            <i class="fas fa-check-circle me-2"></i> <span id="toast-copy-msg">Link copiado al portapapeles.</span>
        </div>

        <!-- ========================================================================= -->
        <!-- 1. SECTOR PRINCIPAL DEMO APOS: DOCENTES INTEGRADORES & EDUCACIÓN ESPECIAL -->
        <!-- ========================================================================= -->
        <div class="sector-title text-info">
            <i class="fas fa-graduation-cap"></i> 1. Área Docentes Integradores & Educación Especial (Circuito Principal APOS)
        </div>

        <div class="row g-3">
            <!-- 1.1 App Docente / Maestra Integradora -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="link-card" style="border-color: rgba(16, 185, 129, 0.45);">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="icon-box icon-emerald">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-white mb-0">App Docente Integrador (PWA)</h6>
                                <span class="badge-role badge-emerald">Ingreso con Teléfono + PIN 4 dígitos</span>
                            </div>
                        </div>
                        <p class="card-desc">
                            Búsqueda rápida por N° de Resolución APOS, DNI, Afiliado o Nombre. Selección del hijo/alumno, escuela, días y horarios (tope 3 hs/día sin choques) y subida de documentación.
                        </p>
                    </div>
                    <div class="card-actions">
                        <div class="share-group">
                            <button type="button" class="btn-copy-link" onclick="copiarEnlace('{{ url('/app-docente/demo') }}', 'App Docente Integrador')">
                                <i class="fas fa-copy"></i> Copiar
                            </button>
                            <button type="button" class="btn-wa-link" onclick="compartirWhatsapp('{{ url('/app-docente/demo') }}', 'App Docente Integrador APOS')">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </button>
                        </div>
                        <a href="{{ url('/app-docente/demo') }}" class="btn btn-success btn-open-link">
                            <i class="fas fa-rocket me-1"></i> Ingresar
                        </a>
                    </div>
                </div>
            </div>

            <!-- 1.2 Panel del Auditor de Docentes -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="link-card" style="border-color: rgba(245, 158, 11, 0.45);">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="icon-box icon-amber">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-white mb-0">Panel del Auditor APOS</h6>
                                <span class="badge-role badge-amber">Visado Secuencial y Resoluciones</span>
                            </div>
                        </div>
                        <p class="card-desc">
                            Bandeja en tiempo real para preaprobar paso a paso la documentación del docente, validar Resoluciones de APOS o emitir autorización expresa de auditoría.
                        </p>
                    </div>
                    <div class="card-actions">
                        <div class="share-group">
                            <button type="button" class="btn-copy-link" onclick="copiarEnlace('{{ route('auditor.docentes.legajos') }}', 'Panel Auditor APOS')">
                                <i class="fas fa-copy"></i> Copiar
                            </button>
                            <button type="button" class="btn-wa-link" onclick="compartirWhatsapp('{{ route('auditor.docentes.legajos') }}', 'Panel Auditor de Docentes APOS')">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </button>
                        </div>
                        <a href="{{ route('auditor.docentes.legajos') }}" class="btn btn-warning text-dark btn-open-link">
                            <i class="fas fa-rocket me-1"></i> Ingresar
                        </a>
                    </div>
                </div>
            </div>

            <!-- 1.3 Portal Directora de Escuela -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="link-card" style="border-color: rgba(14, 165, 233, 0.45);">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="icon-box icon-sky">
                                <i class="fas fa-school"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-white mb-0">Portal Directora de Escuela</h6>
                                <span class="badge-role badge-sky">Certificación de Horas Trabajadas</span>
                            </div>
                        </div>
                        <p class="card-desc">
                            Las directoras de las 71 escuelas de La Rioja certifican desde su celular las horas reales cumplidas por cada maestra integradora en su establecimiento.
                        </p>
                    </div>
                    <div class="card-actions">
                        <div class="share-group">
                            <button type="button" class="btn-copy-link" onclick="copiarEnlace('{{ url('/app-directora/demo') }}', 'Portal Directora de Escuela')">
                                <i class="fas fa-copy"></i> Copiar
                            </button>
                            <button type="button" class="btn-wa-link" onclick="compartirWhatsapp('{{ url('/app-directora/demo') }}', 'Portal Directora de Escuela APOS')">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </button>
                        </div>
                        <a href="{{ url('/app-directora/demo') }}" class="btn btn-info text-white btn-open-link">
                            <i class="fas fa-rocket me-1"></i> Ingresar
                        </a>
                    </div>
                </div>
            </div>

            <!-- 1.4 Portal Padre / Titular -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="link-card">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="icon-box icon-purple">
                                <i class="fas fa-child-reaching"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-white mb-0">App Padre / Titular Afiliado</h6>
                                <span class="badge-role badge-purple">Control de Asistencia de su Hijo</span>
                            </div>
                        </div>
                        <p class="card-desc">
                            El padre titular visualiza qué días y horas fue atendido su hijo, consulta el estado de su Resolución APOS y presta conformidad digital desde el teléfono.
                        </p>
                    </div>
                    <div class="card-actions">
                        <div class="share-group">
                            <button type="button" class="btn-copy-link" onclick="copiarEnlace('{{ url('/app-padre/demo') }}', 'App Padre / Titular APOS')">
                                <i class="fas fa-copy"></i> Copiar
                            </button>
                            <button type="button" class="btn-wa-link" onclick="compartirWhatsapp('{{ url('/app-padre/demo') }}', 'App Padre / Titular APOS')">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </button>
                        </div>
                        <a href="{{ url('/app-padre/demo') }}" class="btn btn-primary btn-open-link">
                            <i class="fas fa-rocket me-1"></i> Ingresar
                        </a>
                    </div>
                </div>
            </div>

            <!-- 1.5 Flujo de 7 Etapas (Jefe de Área) -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="link-card">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="icon-box icon-blue">
                                <i class="fas fa-project-diagram"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-white mb-0">Tablero Jefe de Área (Workflow)</h6>
                                <span class="badge-role badge-blue">Control Secuencial de Expedientes</span>
                            </div>
                        </div>
                        <p class="card-desc">
                            Supervisión integral del ciclo mensual: Resolución APOS, legajo docente, triple aval (Docente, Directora y Padre), auditoría y pase a liquidación.
                        </p>
                    </div>
                    <div class="card-actions">
                        <div class="share-group">
                            <button type="button" class="btn-copy-link" onclick="copiarEnlace('{{ url('/expedientes/flujo') }}', 'Workflow de Expedientes APOS')">
                                <i class="fas fa-copy"></i> Copiar
                            </button>
                            <button type="button" class="btn-wa-link" onclick="compartirWhatsapp('{{ url('/expedientes/flujo') }}', 'Workflow de Expedientes APOS')">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </button>
                        </div>
                        <a href="{{ url('/expedientes/flujo') }}" class="btn btn-primary btn-open-link">
                            <i class="fas fa-rocket me-1"></i> Ingresar
                        </a>
                    </div>
                </div>
            </div>

            <!-- 1.6 Liquidación Oficial e-SIDIF Nación (CSV + Llave Pública/Privada) -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="link-card" style="border-color: rgba(16, 185, 129, 0.55);">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="icon-box icon-emerald">
                                <i class="fas fa-file-csv"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-white mb-0">Liquidación e-SIDIF | Nación</h6>
                                <span class="badge-role badge-emerald">CSV Carga Masiva + Llave Pública/Privada</span>
                            </div>
                        </div>
                        <p class="card-desc">
                            Orden de pago individual y colectiva de docentes APOS. Descarga inmediata en CSV para Carga Masiva en e-SIDIF y transmisión automática con Llave Pública y Privada.
                        </p>
                    </div>
                    <div class="card-actions">
                        <div class="share-group">
                            <button type="button" class="btn-copy-link" onclick="copiarEnlace('{{ url('/owner/liquidaciones') }}', 'Liquidación e-SIDIF APOS')">
                                <i class="fas fa-copy"></i> Copiar
                            </button>
                            <button type="button" class="btn-wa-link" onclick="compartirWhatsapp('{{ url('/owner/liquidaciones') }}', 'Liquidación e-SIDIF APOS')">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </button>
                        </div>
                        <a href="{{ url('/owner/liquidaciones') }}" class="btn btn-success btn-open-link">
                            <i class="fas fa-rocket me-1"></i> Ingresar
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- 2. SECTOR AFILIADOS APOS (160.000 CÁPITAS - PRÓXIMAS ÁREAS DE EXPANSIÓN)  -->
        <!-- ========================================================================= -->
        <div class="sector-title text-warning">
            <i class="fas fa-id-card-clip"></i> 2. Módulos para los 160.000 Afiliados de APOS (Expansión Obra Social)
        </div>

        <div class="row g-3">
            <!-- Credencial Digital QR -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="link-card">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="icon-box icon-purple">
                                <i class="fas fa-qrcode"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-white mb-0">Credencial Digital & Token QR</h6>
                                <span class="badge-role badge-purple">Titulares y Grupo Familiar</span>
                            </div>
                        </div>
                        <p class="card-desc">
                            Carnet digital con código QR y token de seguridad dinámico cada 30 segundos para validación en farmacias y clínicas de La Rioja.
                        </p>
                    </div>
                    <div class="card-actions">
                        <div class="share-group">
                            <button type="button" class="btn-copy-link" onclick="copiarEnlace('{{ url('/app-afiliado/credencial') }}', 'Credencial Digital QR')">
                                <i class="fas fa-copy"></i> Copiar
                            </button>
                            <button type="button" class="btn-wa-link" onclick="compartirWhatsapp('{{ url('/app-afiliado/credencial') }}', 'Credencial Digital QR APOS')">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </button>
                        </div>
                        <a href="{{ url('/app-afiliado/credencial') }}" class="btn btn-primary btn-open-link">
                            <i class="fas fa-rocket me-1"></i> Ingresar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Cartilla Médica & Turnos -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="link-card">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="icon-box icon-blue">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-white mb-0">Cartilla Médica & Turnero</h6>
                                <span class="badge-role badge-blue">Para Afiliados APOS</span>
                            </div>
                        </div>
                        <p class="card-desc">
                            Buscador de profesionales por especialidad en toda la provincia de La Rioja, reserva de turnos online y recordatorios por WhatsApp.
                        </p>
                    </div>
                    <div class="card-actions">
                        <div class="share-group">
                            <button type="button" class="btn-copy-link" onclick="copiarEnlace('{{ url('/app-afiliado/turnos') }}', 'Cartilla y Turnos')">
                                <i class="fas fa-copy"></i> Copiar
                            </button>
                            <button type="button" class="btn-wa-link" onclick="compartirWhatsapp('{{ url('/app-afiliado/turnos') }}', 'Cartilla y Turnos APOS')">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </button>
                        </div>
                        <a href="{{ url('/app-afiliado/turnos') }}" class="btn btn-primary btn-open-link">
                            <i class="fas fa-rocket me-1"></i> Ingresar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Sala de Telemedicina -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="link-card">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="icon-box icon-rose">
                                <i class="fas fa-video"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-white mb-0">Sala de Telemedicina en Vivo</h6>
                                <span class="badge-role badge-rose">Videoconsulta & Receta Digital</span>
                            </div>
                        </div>
                        <p class="card-desc">
                            Atención médica virtual directa para afiliados del interior provincial con emisión instantánea de Recetas Electrónicas firmadas.
                        </p>
                    </div>
                    <div class="card-actions">
                        <div class="share-group">
                            <button type="button" class="btn-copy-link" onclick="copiarEnlace('{{ url('/app-afiliado/telemedicina') }}', 'Sala de Telemedicina')">
                                <i class="fas fa-copy"></i> Copiar
                            </button>
                            <button type="button" class="btn-wa-link" onclick="compartirWhatsapp('{{ url('/app-afiliado/telemedicina') }}', 'Sala de Telemedicina APOS')">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </button>
                        </div>
                        <a href="{{ url('/app-afiliado/telemedicina') }}" class="btn btn-danger btn-open-link">
                            <i class="fas fa-rocket me-1"></i> Ingresar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Derivaciones & Viáticos -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="link-card">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="icon-box icon-sky">
                                <i class="fas fa-plane-departure"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-white mb-0">Derivaciones & Viáticos</h6>
                                <span class="badge-role badge-sky">Alta Complejidad (Córdoba / Bs. As.)</span>
                            </div>
                        </div>
                        <p class="card-desc">
                            Credenciales provisorias de tránsito con QR, órdenes de pasajes, vouchers de hotelería y acreditación de viáticos para afiliados derivados.
                        </p>
                    </div>
                    <div class="card-actions">
                        <div class="share-group">
                            <button type="button" class="btn-copy-link" onclick="copiarEnlace('{{ url('/app-afiliado/derivaciones') }}', 'Derivaciones y Viáticos')">
                                <i class="fas fa-copy"></i> Copiar
                            </button>
                            <button type="button" class="btn-wa-link" onclick="compartirWhatsapp('{{ url('/app-afiliado/derivaciones') }}', 'Derivaciones y Viáticos APOS')">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </button>
                        </div>
                        <a href="{{ url('/app-afiliado/derivaciones') }}" class="btn btn-info text-white btn-open-link">
                            <i class="fas fa-rocket me-1"></i> Ingresar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Central de Emergencias SOS -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="link-card">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="icon-box icon-rose">
                                <i class="fas fa-truck-medical"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-white mb-0">Central de Emergencias SOS 24/7</h6>
                                <span class="badge-role badge-rose">Despacho Médico & GPS</span>
                            </div>
                        </div>
                        <p class="card-desc">
                            Botón de emergencia médica con geolocalización satelital en vivo de ambulancia y seguimiento del tiempo estimado de arribo.
                        </p>
                    </div>
                    <div class="card-actions">
                        <div class="share-group">
                            <button type="button" class="btn-copy-link" onclick="copiarEnlace('{{ url('/app-afiliado/emergencias') }}', 'Central de Emergencias SOS')">
                                <i class="fas fa-copy"></i> Copiar
                            </button>
                            <button type="button" class="btn-wa-link" onclick="compartirWhatsapp('{{ url('/app-afiliado/emergencias') }}', 'Central de Emergencias SOS APOS')">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </button>
                        </div>
                        <a href="{{ url('/app-afiliado/emergencias') }}" class="btn btn-danger btn-open-link">
                            <i class="fas fa-rocket me-1"></i> Ingresar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Billetera & Coseguros -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="link-card">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="icon-box icon-emerald">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-white mb-0">Billetera Afiliado & Coseguros</h6>
                                <span class="badge-role badge-emerald">Finanzas, Copagos y Reintegros</span>
                            </div>
                        </div>
                        <p class="card-desc">
                            Gestión digital de coseguros de prácticas y farmacia, reintegros directos en cuenta CBU y créditos de salud preaprobados.
                        </p>
                    </div>
                    <div class="card-actions">
                        <div class="share-group">
                            <button type="button" class="btn-copy-link" onclick="copiarEnlace('{{ url('/app-afiliado/billetera') }}', 'Billetera Afiliado APOS')">
                                <i class="fas fa-copy"></i> Copiar
                            </button>
                            <button type="button" class="btn-wa-link" onclick="compartirWhatsapp('{{ url('/app-afiliado/billetera') }}', 'Billetera Afiliado APOS')">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </button>
                        </div>
                        <a href="{{ url('/app-afiliado/billetera') }}" class="btn btn-success btn-open-link">
                            <i class="fas fa-rocket me-1"></i> Ingresar
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- 3. SECTOR FARMACIAS PROPIAS/CONVENIDAS, CLÍNICAS Y ALTA DIRECCIÓN APOS    -->
        <!-- ========================================================================= -->
        <div class="sector-title text-success">
            <i class="fas fa-hospital-user"></i> 3. Farmacias, Clínicas y Conducción Ejecutiva de APOS
        </div>

        <div class="row g-3">
            <!-- Validador de Farmacias -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="link-card">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="icon-box icon-emerald">
                                <i class="fas fa-prescription-bottle-medical"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-white mb-0">Validador de Farmacias APOS</h6>
                                <span class="badge-role badge-emerald">Farmacias Propias y Convenidas</span>
                            </div>
                        </div>
                        <p class="card-desc">
                            Validador en línea de Vademécum APOS (40%, 70% y 100% Planes Especiales/Discapacidad) con emisión de comprobante POS 80mm o A4.
                        </p>
                    </div>
                    <div class="card-actions">
                        <div class="share-group">
                            <button type="button" class="btn-copy-link" onclick="copiarEnlace('{{ url('/farmacia/demo') }}', 'Validador de Farmacias APOS')">
                                <i class="fas fa-copy"></i> Copiar
                            </button>
                            <button type="button" class="btn-wa-link" onclick="compartirWhatsapp('{{ url('/farmacia/demo') }}', 'Validador de Farmacias APOS')">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </button>
                        </div>
                        <a href="{{ url('/farmacia/demo') }}" class="btn btn-success btn-open-link">
                            <i class="fas fa-rocket me-1"></i> Ingresar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Red de Clínicas y Sanatorios -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="link-card">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="icon-box icon-sky">
                                <i class="fas fa-hospital"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-white mb-0">Portal Clínicas y Sanatorios</h6>
                                <span class="badge-role badge-sky">Autorización de Prácticas e Internación</span>
                            </div>
                        </div>
                        <p class="card-desc">
                            Validación de elegibilidad de afiliados APOS, carga de órdenes médicas e internaciones y emisión de bonos con trazabilidad digital.
                        </p>
                    </div>
                    <div class="card-actions">
                        <div class="share-group">
                            <button type="button" class="btn-copy-link" onclick="copiarEnlace('{{ url('/prestadores/demo') }}', 'Portal Clínicas y Sanatorios')">
                                <i class="fas fa-copy"></i> Copiar
                            </button>
                            <button type="button" class="btn-wa-link" onclick="compartirWhatsapp('{{ url('/prestadores/demo') }}', 'Portal Clínicas y Sanatorios APOS')">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </button>
                        </div>
                        <a href="{{ url('/prestadores/demo') }}" class="btn btn-info text-white btn-open-link">
                            <i class="fas fa-rocket me-1"></i> Ingresar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Cuadro de Mando Administradora General APOS -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="link-card" style="border-color: rgba(245, 158, 11, 0.45);">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="icon-box icon-amber">
                                <i class="fas fa-chart-pie"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-white mb-0">Cuadro de Mando Ejecutivo APOS</h6>
                                <span class="badge-role badge-amber">Administración General (160.000 Cápitas)</span>
                            </div>
                        </div>
                        <p class="card-desc">
                            Tablero de comando para la Administradora de APOS: indicadores en vivo por delegación provincial, control presupuestario y ahorro operativo.
                        </p>
                    </div>
                    <div class="card-actions">
                        <div class="share-group">
                            <button type="button" class="btn-copy-link" onclick="copiarEnlace('{{ url('/owner/mutual-dashboard') }}', 'Cuadro de Mando Ejecutivo APOS')">
                                <i class="fas fa-copy"></i> Copiar
                            </button>
                            <button type="button" class="btn-wa-link" onclick="compartirWhatsapp('{{ url('/owner/mutual-dashboard') }}', 'Cuadro de Mando Ejecutivo APOS')">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </button>
                        </div>
                        <a href="{{ url('/owner/mutual-dashboard') }}" class="btn btn-warning text-dark btn-open-link">
                            <i class="fas fa-rocket me-1"></i> Ingresar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Auditoría Médica Central -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="link-card">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="icon-box icon-rose">
                                <i class="fas fa-shield-heart"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-white mb-0">Hub de Auditoría Médica Central</h6>
                                <span class="badge-role badge-rose">Prácticas, Cirugías y Prótesis ANMAT</span>
                            </div>
                        </div>
                        <p class="card-desc">
                            Bandeja unificada de junta médica evaluadora para autorizar cirugías, prótesis con trazabilidad ANMAT y medicación de alto costo.
                        </p>
                    </div>
                    <div class="card-actions">
                        <div class="share-group">
                            <button type="button" class="btn-copy-link" onclick="copiarEnlace('{{ url('/auditoria/central') }}', 'Auditoría Médica Central')">
                                <i class="fas fa-copy"></i> Copiar
                            </button>
                            <button type="button" class="btn-wa-link" onclick="compartirWhatsapp('{{ url('/auditoria/central') }}', 'Auditoría Médica Central APOS')">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </button>
                        </div>
                        <a href="{{ url('/auditoria/central') }}" class="btn btn-danger btn-open-link">
                            <i class="fas fa-rocket me-1"></i> Ingresar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Proyección Presupuestaria BI -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="link-card">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="icon-box icon-blue">
                                <i class="fas fa-brain"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-white mb-0">Proyección Presupuestaria & BI</h6>
                                <span class="badge-role badge-blue">Simulador Financiero 12 Meses</span>
                            </div>
                        </div>
                        <p class="card-desc">
                            Análisis predictivo de gasto prestacional por patologías CUD, farmacia y clínicas para planificación financiera de la obra social.
                        </p>
                    </div>
                    <div class="card-actions">
                        <div class="share-group">
                            <button type="button" class="btn-copy-link" onclick="copiarEnlace('{{ url('/owner/proyeccion-presupuesto') }}', 'Proyección Presupuestaria BI')">
                                <i class="fas fa-copy"></i> Copiar
                            </button>
                            <button type="button" class="btn-wa-link" onclick="compartirWhatsapp('{{ url('/owner/proyeccion-presupuesto') }}', 'Proyección Presupuestaria BI APOS')">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </button>
                        </div>
                        <a href="{{ url('/owner/proyeccion-presupuesto') }}" class="btn btn-primary btn-open-link">
                            <i class="fas fa-rocket me-1"></i> Ingresar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Bot WhatsApp Afiliados -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="link-card">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="icon-box icon-emerald">
                                <i class="fab fa-whatsapp"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-white mb-0">Bot Oficial WhatsApp APOS</h6>
                                <span class="badge-role badge-emerald">Autogestión 24/7 para Afiliados</span>
                            </div>
                        </div>
                        <p class="card-desc">
                            Asistente automático de WhatsApp para consultar estado de resoluciones, descargar credencial QR, validar recetas y pedir turnos.
                        </p>
                    </div>
                    <div class="card-actions">
                        <div class="share-group">
                            <button type="button" class="btn-copy-link" onclick="copiarEnlace('{{ url('/simulador/whatsapp') }}', 'Bot WhatsApp APOS')">
                                <i class="fas fa-copy"></i> Copiar
                            </button>
                            <button type="button" class="btn-wa-link" onclick="compartirWhatsapp('{{ url('/simulador/whatsapp') }}', 'Bot WhatsApp APOS')">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </button>
                        </div>
                        <a href="{{ url('/simulador/whatsapp') }}" class="btn btn-success btn-open-link">
                            <i class="fas fa-rocket me-1"></i> Ingresar
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
    function compartirWhatsapp(url, modulo) {
        const texto = encodeURIComponent("👋 ¡Hola! Te comparto el enlace oficial de acceso a *" + modulo + "* (APOS La Rioja - Plataforma INTEGRA):\n\n🌐 " + url + "\n\n📱 Podés ingresar desde tu teléfono o computadora con tu número de celular y tu PIN de 4 dígitos.");
        window.open("https://wa.me/?text=" + texto, "_blank");
    }

    function copiarEnlace(url, nombreModulo) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(url).then(() => {
                mostrarToastCopiado("✅ Link de '" + nombreModulo + "' copiado. ¡Listo para pegar en WhatsApp!");
            }).catch(() => fallbackCopiar(url, nombreModulo));
        } else {
            fallbackCopiar(url, nombreModulo);
        }
    }

    function fallbackCopiar(url, nombreModulo) {
        const tempInput = document.createElement("input");
        tempInput.value = url;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand("copy");
        document.body.removeChild(tempInput);
        mostrarToastCopiado("✅ Link de '" + nombreModulo + "' copiado.");
    }

    function mostrarToastCopiado(mensaje) {
        const toast = document.getElementById("toast-copy");
        const msgEl = document.getElementById("toast-copy-msg");
        if (toast && msgEl) {
            msgEl.innerText = mensaje;
            toast.style.display = "block";
            setTimeout(() => { toast.style.display = "none"; }, 3500);
        } else {
            alert(mensaje);
        }
    }
    </script>

    @include('partials.ai_assistant_widget')
</body>
</html>
