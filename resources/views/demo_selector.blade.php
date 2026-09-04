<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#0f172a">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <title>INTEGRA | Directorio Oficial de Enlaces & Accesos por Sector</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
            padding-bottom: 60px;
        }

        .header-selector {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 30px 0;
            margin-bottom: 30px;
        }

        .sector-title {
            font-size: 1.1rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: #38bdf8;
            margin-top: 25px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 8px;
        }

        .link-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 18px;
            padding: 20px;
            transition: all 0.25s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .link-card:hover {
            transform: translateY(-4px);
            border-color: #6366f1;
            box-shadow: 0 12px 25px rgba(99, 102, 241, 0.25);
            background: rgba(30, 41, 59, 0.95);
        }

        .icon-box {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
        }

        .btn-copy-link {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #f1f5f9;
            font-size: 0.78rem;
            font-weight: 600;
            border-radius: 20px;
            padding: 6px 14px;
            transition: all 0.2s ease;
        }

        .btn-copy-link:hover {
            background: #2563eb;
            color: #ffffff;
            border-color: #2563eb;
        }

        .btn-open-link {
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
            padding: 6px 16px;
        }

        /* Banner PWA */
        #pwa-install-banner {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 99999;
            width: calc(100% - 40px);
            max-width: 500px;
            background: linear-gradient(135deg, #1e1b4b, #312e81);
            border: 1px solid rgba(168, 85, 247, 0.5);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.6);
            padding: 14px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            animation: pwa-slide-up 0.4s ease-out;
        }

        @keyframes pwa-slide-up {
            from { transform: translate(-50%, 100px); opacity: 0; }
            to { transform: translate(-50%, 0); opacity: 1; }
        }
    </style>
</head>
<body>

    <!-- Banner de Instalación PWA (Móvil) -->
    <div id="pwa-install-banner" style="display: none;">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px; font-size: 1.2rem;">
                <i class="fas fa-mobile-screen-button"></i>
            </div>
            <div>
                <strong class="d-block text-white style-sm" style="font-size: 0.85rem;">¿Instalar INTEGRA en tu celular?</strong>
                <span class="text-white-50" style="font-size: 0.72rem;">Crea el acceso directo con logo oficial</span>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-light rounded-pill fw-bold text-purple px-3" onclick="instalarPwaApp()" style="font-size: 0.78rem;">
                📲 Instalar
            </button>
            <button type="button" class="btn btn-sm btn-link text-white-50 p-0" onclick="cerrarBannerPwa()">
                <i class="fas fa-times fs-6"></i>
            </button>
        </div>
    </div>

    <!-- Header Principal -->
    <header class="header-selector shadow-sm">
        <div class="container text-center">
            <div class="d-inline-flex align-items-center justify-content-center bg-gradient text-white rounded-circle mb-3 shadow" style="width: 65px; height: 65px; font-size: 2rem; background: linear-gradient(135deg, #6366f1, #a855f7);">
                <i class="fas fa-sitemap"></i>
            </div>
            <h2 class="fw-bold text-white mb-1">Directorio de Accesos Oficiales INTEGRA</h2>
            <p class="text-muted small mb-0" style="max-width: 600px; margin: 0 auto;">
                Panel centralizado para administración: copia y envía los links específicos a maestras, directoras, padres, afiliados y sanatorios por WhatsApp.
            </p>
        </div>
    </header>

    <div class="container">
        
        <!-- Toast de Confirmación de Copiado -->
        <div id="toast-copy" class="alert alert-success position-fixed top-0 end-0 m-3 shadow-lg border-0 rounded-pill px-4 py-2" style="display: none; z-index: 999999; font-size: 0.85rem;">
            <i class="fas fa-check-circle me-2"></i> <span id="toast-copy-msg">Link copiado al portapapeles. ¡Listo para compartir por WhatsApp!</span>
        </div>

        <!-- 1. SECTOR EDUCACIÓN ESPECIAL & TERAPEUTAS -->
        <div class="sector-title text-info">
            <i class="fas fa-graduation-cap"></i> 1. Módulos de Educación Especial & Terapeutas
        </div>

        <div class="row g-3">
            <!-- App Maestras Integradoras -->
            <div class="col-md-6 col-lg-4">
                <div class="link-card">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="icon-box bg-success bg-opacity-20 text-success">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-white mb-0">App Maestras Integradoras</h6>
                                <span class="badge bg-success bg-opacity-20 text-success border border-success" style="font-size: 0.65rem;">Para Terapeutas / Docentes</span>
                            </div>
                        </div>
                        <p class="small text-muted mb-3">Carga de legajos de alumnos, facturas ARCA/AFIP, avales por WhatsApp, billetera virtual y dictado por micrófono.</p>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top border-secondary border-opacity-25">
                        <button type="button" class="btn-copy-link" onclick="copiarEnlace('{{ url('/app-docente/demo') }}', 'App Maestras Integradoras')">
                            <i class="fas fa-copy me-1"></i> Copiar Link
                        </button>
                        <a href="{{ url('/app-docente/demo') }}" class="btn btn-success btn-open-link">
                            🚀 Ingresar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Portal Directora de Escuela -->
            <div class="col-md-6 col-lg-4">
                <div class="link-card">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="icon-box bg-info bg-opacity-20 text-info">
                                <i class="fas fa-signature"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-white mb-0">Portal Directora de Escuela</h6>
                                <span class="badge bg-info bg-opacity-20 text-info border border-info" style="font-size: 0.65rem;">Para Directoras de Colegio</span>
                            </div>
                        </div>
                        <p class="small text-muted mb-3">Certificación digital de asistencia docente en 1-Clic con control del límite máximo regulado de 3hs/día por alumno.</p>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top border-secondary border-opacity-25">
                        <button type="button" class="btn-copy-link" onclick="copiarEnlace('{{ url('/app-directora/demo') }}', 'Portal Directora de Escuela')">
                            <i class="fas fa-copy me-1"></i> Copiar Link
                        </button>
                        <a href="{{ url('/app-directora/demo') }}" class="btn btn-info btn-open-link text-white">
                            🚀 Ingresar
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. SECTOR AFILIADOS & TITULARES -->
        <div class="sector-title text-warning">
            <i class="fas fa-users"></i> 2. Módulos de Afiliados & Titulares (Familias)
        </div>

        <div class="row g-3">
            <!-- App Padre / Titular Reintegros -->
            <div class="col-md-6 col-lg-4">
                <div class="link-card">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="icon-box bg-warning bg-opacity-20 text-warning">
                                <i class="fas fa-hand-holding-usd"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-white mb-0">Portal Padre / Titular (Reintegros)</h6>
                                <span class="badge bg-warning bg-opacity-20 text-warning border border-warning" style="font-size: 0.65rem;">Para Padres de Familia</span>
                            </div>
                        </div>
                        <p class="small text-muted mb-3">Solicitudes de reintegro por atención prestada, carga de Resolución OSP y rúbrica de conformidad del servicio.</p>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top border-secondary border-opacity-25">
                        <button type="button" class="btn-copy-link" onclick="copiarEnlace('{{ url('/app-padre/demo') }}', 'Portal Titular / Reintegros')">
                            <i class="fas fa-copy me-1"></i> Copiar Link
                        </button>
                        <a href="{{ url('/app-padre/demo') }}" class="btn btn-warning btn-open-link text-dark">
                            🚀 Ingresar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Credencial Digital QR Afiliado -->
            <div class="col-md-6 col-lg-4">
                <div class="link-card">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="icon-box bg-purple bg-opacity-20 text-purple" style="background: rgba(139, 92, 246, 0.2); color: #a78bfa;">
                                <i class="fas fa-id-card"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-white mb-0">Credencial Digital & Token QR</h6>
                                <span class="badge bg-purple bg-opacity-20 text-purple border border-purple" style="font-size: 0.65rem; color: #a78bfa;">Para Todos los Afiliados</span>
                            </div>
                        </div>
                        <p class="small text-muted mb-3">Credencial de salud con código QR y Token dinámico renovable cada 30 segundos para presentar en farmacias y sanatorios.</p>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top border-secondary border-opacity-25">
                        <button type="button" class="btn-copy-link" onclick="copiarEnlace('{{ url('/app-afiliado/credencial') }}', 'Credencial Digital QR')">
                            <i class="fas fa-copy me-1"></i> Copiar Link
                        </button>
                        <a href="{{ url('/app-afiliado/credencial') }}" class="btn btn-primary btn-open-link" style="background: #8b5cf6; border: none;">
                            🚀 Ingresar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Turnos & Cartilla Médica -->
            <div class="col-md-6 col-lg-4">
                <div class="link-card">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="icon-box bg-primary bg-opacity-20 text-primary">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-white mb-0">Cartilla Médica & Turnero</h6>
                                <span class="badge bg-primary bg-opacity-20 text-primary border border-primary" style="font-size: 0.65rem;">Para Afiliados</span>
                            </div>
                        </div>
                        <p class="small text-muted mb-3">Buscador inteligente de profesionales por especialidad, reserva de turnos en 1-Clic y alertas por WhatsApp.</p>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top border-secondary border-opacity-25">
                        <button type="button" class="btn-copy-link" onclick="copiarEnlace('{{ url('/app-afiliado/turnos') }}', 'Cartilla y Turnos')">
                            <i class="fas fa-copy me-1"></i> Copiar Link
                        </button>
                        <a href="{{ url('/app-afiliado/turnos') }}" class="btn btn-primary btn-open-link">
                            🚀 Ingresar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Telemedicina en Vivo -->
            <div class="col-md-6 col-lg-4">
                <div class="link-card">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="icon-box bg-danger bg-opacity-20 text-danger">
                                <i class="fas fa-video"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-white mb-0">Sala de Telemedicina en Vivo</h6>
                                <span class="badge bg-danger bg-opacity-20 text-danger border border-danger" style="font-size: 0.65rem;">Videoconsulta WebRTC</span>
                            </div>
                        </div>
                        <p class="small text-muted mb-3">Sala de atención virtual directa con médicos de cartilla y emisión instantánea de Recetas Digitales firmadas.</p>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top border-secondary border-opacity-25">
                        <button type="button" class="btn-copy-link" onclick="copiarEnlace('{{ url('/app-afiliado/telemedicina') }}', 'Sala de Telemedicina')">
                            <i class="fas fa-copy me-1"></i> Copiar Link
                        </button>
                        <a href="{{ url('/app-afiliado/telemedicina') }}" class="btn btn-danger btn-open-link">
                            🚀 Ingresar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Derivaciones & Viáticos -->
            <div class="col-md-6 col-lg-4">
                <div class="link-card">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="icon-box bg-info bg-opacity-20 text-info">
                                <i class="fas fa-plane-departure"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-white mb-0">Derivaciones & Viáticos</h6>
                                <span class="badge bg-info bg-opacity-20 text-info border border-info" style="font-size: 0.65rem;">Alta Complejidad fuera de provincia</span>
                            </div>
                        </div>
                        <p class="small text-muted mb-3">Credenciales Provisorias de Tránsito QR (Córdoba / Buenos Aires), vales de hotel y acreditación de viáticos.</p>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top border-secondary border-opacity-25">
                        <button type="button" class="btn-copy-link" onclick="copiarEnlace('{{ url('/app-afiliado/derivaciones') }}', 'Derivaciones y Viáticos')">
                            <i class="fas fa-copy me-1"></i> Copiar Link
                        </button>
                        <a href="{{ url('/app-afiliado/derivaciones') }}" class="btn btn-info btn-open-link text-white">
                            🚀 Ingresar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Certificado Anual ARCA -->
            <div class="col-md-6 col-lg-4">
                <div class="link-card">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="icon-box bg-success bg-opacity-20 text-success">
                                <i class="fas fa-file-invoice"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-white mb-0">Certificado Anual ARCA / AFIP</h6>
                                <span class="badge bg-success bg-opacity-20 text-success border border-success" style="font-size: 0.65rem;">Deducción de Impuestos</span>
                            </div>
                        </div>
                        <p class="small text-muted mb-3">Descarga oficial del certificado de aportes y cobertura mutual para deducción de ganancias e impuestos.</p>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top border-secondary border-opacity-25">
                        <button type="button" class="btn-copy-link" onclick="copiarEnlace('{{ url('/app-afiliado/certificado-anual') }}', 'Certificado Anual ARCA')">
                            <i class="fas fa-copy me-1"></i> Copiar Link
                        </button>
                        <a href="{{ url('/app-afiliado/certificado-anual') }}" class="btn btn-success btn-open-link">
                            🚀 Ingresar
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. SECTOR FARMACIAS & SANATORIOS -->
        <div class="sector-title text-success">
            <i class="fas fa-clinic-medical"></i> 3. Módulos de Farmacias Convenidas & Sanatorios
        </div>

        <div class="row g-3">
            <!-- Validador de Farmacias -->
            <div class="col-md-6 col-lg-4">
                <div class="link-card">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="icon-box bg-success bg-opacity-20 text-success">
                                <i class="fas fa-pills"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-white mb-0">Validador de Farmacias Convenidas</h6>
                                <span class="badge bg-success bg-opacity-20 text-success border border-success" style="font-size: 0.65rem;">Para Farmacias</span>
                            </div>
                        </div>
                        <p class="small text-muted mb-3">Validador online en vivo de Vademécum Mutual (40%, 70%, 100%) e emisión de tickets POS 80mm u A4 Troquelado.</p>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top border-secondary border-opacity-25">
                        <button type="button" class="btn-copy-link" onclick="copiarEnlace('{{ url('/farmacia/demo') }}', 'Validador de Farmacias')">
                            <i class="fas fa-copy me-1"></i> Copiar Link
                        </button>
                        <a href="{{ url('/farmacia/demo') }}" class="btn btn-success btn-open-link">
                            🚀 Ingresar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Red de Prestadores Médicos -->
            <div class="col-md-6 col-lg-4">
                <div class="link-card">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="icon-box bg-info bg-opacity-20 text-info">
                                <i class="fas fa-hospital"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-white mb-0">Red de Prestadores & Sanatorios</h6>
                                <span class="badge bg-info bg-opacity-20 text-info border border-info" style="font-size: 0.65rem;">Para Clínicas y Médicos</span>
                            </div>
                        </div>
                        <p class="small text-muted mb-3">Solicitud de autorizaciones de internación y emisión e impresión limpia de Bonos Digitales con Hash MD5.</p>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top border-secondary border-opacity-25">
                        <button type="button" class="btn-copy-link" onclick="copiarEnlace('{{ url('/prestadores/demo') }}', 'Red de Prestadores Médicos')">
                            <i class="fas fa-copy me-1"></i> Copiar Link
                        </button>
                        <a href="{{ url('/prestadores/demo') }}" class="btn btn-info btn-open-link text-white">
                            🚀 Ingresar
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. SECTOR AUDITORÍA & ALTA DIRECCIÓN MUTUAL -->
        <div class="sector-title text-primary">
            <i class="fas fa-crown"></i> 4. Módulos de Auditoría & Alta Dirección Mutual
        </div>

        <div class="row g-3">
            <!-- Panel Ejecutivo del Dueño -->
            <div class="col-md-6 col-lg-4">
                <div class="link-card" style="border-color: #f59e0b;">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="icon-box text-white" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                                <i class="fas fa-crown"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-white mb-0">Cuadro de Mando Ejecutivo</h6>
                                <span class="badge bg-warning text-dark" style="font-size: 0.65rem;">Alta Dirección Mutual (130k Cápitas)</span>
                            </div>
                        </div>
                        <p class="small text-muted mb-3">Filtro por sucursales (Chilecito, La Rioja, Cba, BsAs), superávit ($332.2M) e informe de auditoría ecológica de papel.</p>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top border-secondary border-opacity-25">
                        <button type="button" class="btn-copy-link" onclick="copiarEnlace('{{ url('/owner/mutual-dashboard') }}', 'Cuadro de Mando Ejecutivo')">
                            <i class="fas fa-copy me-1"></i> Copiar Link
                        </button>
                        <a href="{{ url('/owner/mutual-dashboard') }}" class="btn btn-warning btn-open-link text-dark">
                            🚀 Ingresar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Cierre de Liquidaciones CBU -->
            <div class="col-md-6 col-lg-4">
                <div class="link-card">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="icon-box bg-success bg-opacity-20 text-success">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-white mb-0">Cierre de Liquidaciones CBU</h6>
                                <span class="badge bg-success bg-opacity-20 text-success border border-success" style="font-size: 0.65rem;">Tesorería & Billeteras</span>
                            </div>
                        </div>
                        <p class="small text-muted mb-3">Acreditación masiva de honorarios a cuentas bancarias CBU/Alias de sanatorios, farmacias y docentes auditados.</p>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top border-secondary border-opacity-25">
                        <button type="button" class="btn-copy-link" onclick="copiarEnlace('{{ url('/owner/liquidaciones') }}', 'Cierre de Liquidaciones CBU')">
                            <i class="fas fa-copy me-1"></i> Copiar Link
                        </button>
                        <a href="{{ url('/owner/liquidaciones') }}" class="btn btn-success btn-open-link">
                            🚀 Ingresar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Hub Auditoría Médica Central -->
            <div class="col-md-6 col-lg-4">
                <div class="link-card">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="icon-box bg-danger bg-opacity-20 text-danger">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-white mb-0">Hub Auditoría Médica Central</h6>
                                <span class="badge bg-danger bg-opacity-20 text-danger border border-danger" style="font-size: 0.65rem;">Junta Evaluadora MD5</span>
                            </div>
                        </div>
                        <p class="small text-muted mb-3">Aprobación en 1-Clic de cirugías, prótesis e internaciones con sello criptográfico Hash MD5 inalterable.</p>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top border-secondary border-opacity-25">
                        <button type="button" class="btn-copy-link" onclick="copiarEnlace('{{ url('/auditoria/central') }}', 'Auditoría Médica Central')">
                            <i class="fas fa-copy me-1"></i> Copiar Link
                        </button>
                        <a href="{{ url('/auditoria/central') }}" class="btn btn-danger btn-open-link">
                            🚀 Ingresar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Proyección BI & Presupuesto -->
            <div class="col-md-6 col-lg-4">
                <div class="link-card">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="icon-box bg-primary bg-opacity-20 text-primary">
                                <i class="fas fa-brain"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-white mb-0">Proyección BI & Presupuesto IA</h6>
                                <span class="badge bg-primary bg-opacity-20 text-primary border border-primary" style="font-size: 0.65rem;">Business Intelligence 12M</span>
                            </div>
                        </div>
                        <p class="small text-muted mb-3">Modelado predictivo de costos por patologías prevalentes, simulación de inflación médica y variación de cápitas.</p>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top border-secondary border-opacity-25">
                        <button type="button" class="btn-copy-link" onclick="copiarEnlace('{{ url('/owner/proyeccion-presupuesto') }}', 'Proyección Presupuestaria BI')">
                            <i class="fas fa-copy me-1"></i> Copiar Link
                        </button>
                        <a href="{{ url('/owner/proyeccion-presupuesto') }}" class="btn btn-primary btn-open-link">
                            🚀 Ingresar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Bot Oficial WhatsApp Simulador -->
            <div class="col-md-6 col-lg-4">
                <div class="link-card">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="icon-box bg-success bg-opacity-20 text-success">
                                <i class="fab fa-whatsapp"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-white mb-0">Simulador Bot WhatsApp Mutual</h6>
                                <span class="badge bg-success bg-opacity-20 text-success border border-success" style="font-size: 0.65rem;">Atención por WhatsApp</span>
                            </div>
                        </div>
                        <p class="small text-muted mb-3">Réplica del bot de WhatsApp para prueba de validación por foto de receta, consulta de token QR y turnos.</p>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top border-secondary border-opacity-25">
                        <button type="button" class="btn-copy-link" onclick="copiarEnlace('{{ url('/simulador/whatsapp') }}', 'Simulador Bot WhatsApp')">
                            <i class="fas fa-copy me-1"></i> Copiar Link
                        </button>
                        <a href="{{ url('/simulador/whatsapp') }}" class="btn btn-success btn-open-link">
                            🚀 Ingresar
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Scripts JavaScript de Copiado y PWA Banner -->
    <script>
    let deferredPrompt = null;

    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        const banner = document.getElementById('pwa-install-banner');
        if (banner) banner.style.display = 'flex';
    });

    function instalarPwaApp() {
        if (deferredPrompt) {
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then((choiceResult) => {
                if (choiceResult.outcome === 'accepted') {
                    console.log('El usuario aceptó la instalación de la PWA INTEGRA');
                }
                deferredPrompt = null;
                cerrarBannerPwa();
            });
        } else {
            alert("📲 Para instalar INTEGRA en la pantalla de tu celular:\n\n- En Android (Chrome): Toca los 3 puntos (⋮) ➔ 'Agregar a la pantalla principal'.\n- En iPhone (Safari): Toca el botón Compartir (⎋) ➔ 'Agregar a inicio'.");
        }
    }

    function cerrarBannerPwa() {
        const banner = document.getElementById('pwa-install-banner');
        if (banner) banner.style.display = 'none';
    }

    function copiarEnlace(url, nombreModulo) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(url).then(() => {
                mostrarToastCopiado("✅ Link de '" + nombreModulo + "' copiado al portapapeles. ¡Listo para enviar por WhatsApp!");
            }).catch(err => fallbackCopiar(url, nombreModulo));
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
        mostrarToastCopiado("✅ Link de '" + nombreModulo + "' copiado. ¡Listo para compartir!");
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
