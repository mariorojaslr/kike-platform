<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <!-- Tema Nocturno Obligatorio App Móvil -->
    <meta name="theme-color" content="#0f172a">
    <title>INTEGRA | Portal de Terapeutas</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --bg-oscuro: #0f172a;      /* Fondo Principal Android/iOS */
            --tarjeta-bg: #1e293b;     /* Fondo de las cartas */
            --tarjeta-borde: #334155;
            
            --dinero-ok: #10b981;      /* Esmeralda / Seguro para sacar */
            --dinero-ok-glow: rgba(16, 185, 129, 0.4);
            
            --dinero-espera: #f59e0b;  /* Ambar / Pendiente auditoría */
            --dinero-espera-glow: rgba(245, 158, 11, 0.4);
            
            --gris-texto: #94a3b8;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-oscuro);
            color: white;
            margin: 0;
            padding: 0;
            -webkit-tap-highlight-color: transparent;
            user-select: none; /* Prevenir selecciones accidentales */
            padding-bottom: 90px;
        }

        /* ----- HEADER (BARRA SUPERIOR PWA) ----- */
        .header-pwa {
            background-color: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(10px);
            position: sticky;
            top: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            border-bottom: 1px solid var(--tarjeta-borde);
        }

        .header-pwa .docente-perfil {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .avatar {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #1d4ed8, #3b82f6);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 700;
            font-size: 1.1rem;
            color: white;
            box-shadow: 0 4px 10px rgba(59, 130, 246, 0.4);
        }

        .saludo p { margin: 0; font-size: 0.8rem; color: var(--gris-texto); }
        .saludo h1 { margin: 0; font-size: 1.1rem; font-weight: 700; }

        /* ----- CONTENEDOR PRINCIPAL ----- */
        .container {
            padding: 20px;
        }

        /* ----- BILLETERAS / TARJETAS GEMELAS ----- */
        .billeteras-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 30px;
        }

        .billetera-card {
            background: var(--tarjeta-bg);
            border: 1px solid var(--tarjeta-borde);
            border-radius: 20px;
            padding: 20px 15px;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            transition: transform 0.2s;
        }

        /* Tarjeta Izquierda (Plata lista) */
        .billetera-ok {
            border-bottom: 4px solid var(--dinero-ok);
            box-shadow: 0 10px 20px var(--dinero-ok-glow);
        }

        /* Tarjeta Derecha (Plata proyectada/pretendida) */
        .billetera-pretendido {
            border-bottom: 4px solid var(--dinero-espera);
            box-shadow: 0 10px 20px var(--dinero-espera-glow);
        }

        .billetera-titulo {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .ok-text { color: var(--dinero-ok); }
        .espera-text { color: var(--dinero-espera); }

        .billetera-monto {
            font-size: 1.8rem;
            font-weight: 800;
            margin: 0;
            line-height: 1.1;
        }

        .moneda { font-size: 1.2rem; margin-right: 2px; opacity: 0.8; font-weight: 600; }

        .billetera-subtexto {
            font-size: 0.65rem;
            color: var(--gris-texto);
            margin-top: 5px;
        }

        /* ----- VÚMETRO MOTIVACIONAL (RELOJ DE RENTABILIDAD) ----- */
        .vumetro-container {
            background: var(--tarjeta-bg);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            border: 1px solid var(--tarjeta-borde);
        }
        
        .vumetro-header {
            display: flex;
            justify-content: space-between;
            font-size: 0.8rem;
            color: var(--gris-texto);
            font-weight: 600;
            margin-bottom: 12px;
            text-transform: uppercase;
        }

        /* Las Barritas de Sonido / Ecualizador */
        .ecualizador {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            height: 60px;
            gap: 4px;
        }

        .barra {
            flex-grow: 1;
            width: 100%;
            border-radius: 3px;
            transition: height 0.4s ease, opacity 0.4s;
            opacity: 0.3; /* Por defecto apagadas */
        }

        /* Colores del Vúmetro según el nivel de rentabilidad (de izq a der) */
        .barra.lvl-bajo { background-color: var(--dinero-ok); } /* Verdes al principio */
        .barra.lvl-medio { background-color: var(--dinero-espera); } /* Amarillas en el medio */
        .barra.lvl-alto { background-color: #ef4444; } /* Rojas (fuego/máximo) a la derecha */

        /* Encendidas */
        .barra.activa { opacity: 1; box-shadow: 0 0 10px inherit; }

        /* ----- LISTADO DE ALUMNOS A CARGO ----- */
        .seccion-titulo {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .alumno-card {
            background: var(--tarjeta-bg);
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid var(--tarjeta-borde);
            transition: transform 0.1s, background 0.2s;
        }

        .alumno-card:active {
            transform: scale(0.98);
            background: #283548;
        }

        .alumno-info h4 { margin: 0 0 4px 0; font-size: 1rem; font-weight: 600; }
        .alumno-info p { margin: 0; font-size: 0.8rem; color: var(--gris-texto); }

        /* Estados (Semáforo de validación) */
        .estado { display: flex; align-items: center; justify-content: center; width: 45px; height: 45px; border-radius: 50%; font-size: 1.2rem; }
        .estado.sin_informar { background: rgba(51, 65, 85, 0.5); color: #94a3b8; border: 2px dashed #475569; cursor: pointer; }
        .estado.pendiente { background: rgba(245, 158, 11, 0.15); color: var(--dinero-espera); border: 2px solid var(--dinero-espera); }
        .estado.aprobado { background: rgba(16, 185, 129, 0.15); color: var(--dinero-ok); border: 2px solid var(--dinero-ok); }

        /* ----- BARRA INFERIOR (TAB BAR PWA) ----- */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: var(--tarjeta-bg);
            border-top: 1px solid var(--tarjeta-borde);
            display: flex;
            justify-content: space-around;
            padding: 12px 0 25px 0;
            z-index: 1000;
        }

        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            color: var(--gris-texto);
            text-decoration: none;
            font-size: 0.7rem;
            gap: 5px;
        }

        .nav-item.active {
            color: #3b82f6;
        }
        
        .nav-item i { font-size: 1.3rem; }

        /* ----- SECTOR: BOTONERA DE ACCIÓN RÁPIDA ----- */
        .accion-rapida {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            border-radius: 16px;
            padding: 16px 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: white;
            box-shadow: 0 8px 15px rgba(59, 130, 246, 0.3);
            cursor: pointer;
            transition: transform 0.1s;
        }

        .accion-rapida:active { transform: scale(0.98); }

        .accion-rapida-info h3 { margin: 0 0 5px 0; font-size: 1.1rem; font-weight: 700; }
        .accion-rapida-info p { margin: 0; font-size: 0.8rem; color: #bfdbfe; }
        
        .accion-rapida-icon {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        /* ----- BOTTOM SHEET MODAL (PWA MOBILE) ----- */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.75);
            z-index: 2000;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }
        .modal-overlay.active { opacity: 1; pointer-events: all; }

        .bottom-sheet {
            position: fixed;
            bottom: -100%;
            left: 0; right: 0;
            background: var(--tarjeta-bg);
            border-radius: 24px 24px 0 0;
            padding: 20px 20px 30px 20px;
            z-index: 2001;
            transition: bottom 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.1);
            border-top: 1px solid var(--tarjeta-borde);
            box-shadow: 0 -10px 40px rgba(0,0,0,0.6);
            max-height: 88vh;
            overflow-y: auto;
            box-sizing: border-box;
        }
        .bottom-sheet.active { bottom: 0; }

        .sheet-pill {
            width: 40px; height: 5px;
            background: #475569;
            border-radius: 5px;
            margin: 0 auto 15px auto;
        }

        .sheet-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .sheet-header h3 { margin: 0; font-size: 1.2rem; font-weight: 700; }
        .sheet-close { background: none; border: none; color: var(--gris-texto); font-size: 1.5rem; cursor: pointer; }

        /* Input y Micrófono 100% Responsive */
        .input-group-voice {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 15px;
            width: 100%;
            box-sizing: border-box;
        }

        .input-dark {
            flex: 1;
            min-width: 0; /* Clave para evitar desbordamientos en celulares */
            width: 100%;
            background: #0f172a;
            border: 1px solid var(--tarjeta-borde);
            border-radius: 12px;
            padding: 14px;
            color: white;
            font-family: inherit;
            font-size: 0.95rem;
            box-sizing: border-box;
        }
        .input-dark::placeholder { color: #64748b; }
        .input-dark:focus { outline: none; border-color: #3b82f6; }

        .btn-mic {
            flex-shrink: 0; /* El botón nunca se comprime ni sale de pantalla */
            background: linear-gradient(135deg, #8b5cf6, #6d28d9);
            border: none;
            border-radius: 12px;
            width: 50px;
            height: 50px;
            color: white;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(139, 92, 246, 0.4);
            cursor: pointer;
            box-sizing: border-box;
        }
        .btn-mic:active { transform: scale(0.95); }
        .btn-mic.listening { animation: pulseBg 1.5s infinite; }

        @keyframes pulseBg {
            0% { box-shadow: 0 0 0 0 rgba(139, 92, 246, 0.7); }
            70% { box-shadow: 0 0 0 15px rgba(139, 92, 246, 0); }
            100% { box-shadow: 0 0 0 0 rgba(139, 92, 246, 0); }
        }

        /* Botones de Escaneo */
        .scan-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 15px;
        }
        .scan-btn {
            background: #0f172a;
            border: 1px solid var(--tarjeta-borde);
            border-radius: 16px;
            padding: 16px 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            color: white;
            text-align: center;
            cursor: pointer;
            box-sizing: border-box;
        }
        .scan-btn i { font-size: 1.8rem; color: #3b82f6; }
        .scan-btn span { font-size: 0.8rem; font-weight: 600; color: var(--gris-texto); }
        .scan-btn:active { background: #1e293b; border-color: #3b82f6; }

        .btn-primary-dark {
            background: var(--dinero-ok);
            color: #064e3b;
            font-weight: 700;
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            border: none;
            font-size: 1rem;
            margin-top: 10px;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <!-- CABECERA PRINCIPAL PWA CON LOGO OFICIAL APOS -->
    <header class="header-pwa">
        <div class="docente-perfil">
            <img src="{{ asset('images/apos_logo.svg') }}" alt="APOS La Rioja" style="height: 36px; width: auto; background: #ffffff; padding: 3px 7px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.4);">
            <div class="saludo">
                <p style="color:#38bdf8; font-weight:700; font-size:0.72rem; letter-spacing:0.5px;">APOS LA RIOJA • DOCENTE</p>
                <h1>{{ trim(str_replace('(Demo)', '', $docenteNombre)) }}</h1>
            </div>
        </div>
        <div style="display:flex; align-items:center; gap:10px;">
            <a href="{{ route('auditor.docentes.index') }}" target="_blank" style="background: rgba(56,189,248,0.15); border: 1px solid #38bdf8; color: #38bdf8; padding: 5px 10px; border-radius: 20px; font-size: 0.72rem; font-weight: 700; text-decoration: none;" title="Abrir Bandeja del Auditor en vivo">
                <i class="fas fa-user-check me-1"></i> Ver Auditor
            </a>
            <i class="fas fa-bell text-white" style="font-size: 1.3rem; position: relative;">
                <span style="position:absolute; top:-4px; right:-2px; background:var(--dinero-espera); width:10px; height:10px; border-radius:50%; border:2px solid var(--bg-oscuro);"></span>
            </i>
        </div>
    </header>

    <div class="container">
        
        <!-- BILLETERAS GEMELAS -->
        <div class="billeteras-grid">
            <!-- Izquierda: Lo que ya es suyo, aprobado 100% por Auditoría -->
            <div class="billetera-card billetera-ok">
                <div class="billetera-titulo ok-text">Lo que vas a cobrar</div>
                <div class="billetera-monto"><span class="moneda">$</span><span id="lblMontoCobradoLive">{{ number_format($montoCobrado, 0, ',', '.') }}</span></div>
                <div class="billetera-subtexto"><i class="fas fa-check-double me-1"></i>OK Auditor + Jefe de Área</div>
            </div>

            <!-- Derecha: Lo que proyecta si completa expedientes y facturas -->
            <div class="billetera-card billetera-pretendido">
                <div class="billetera-titulo espera-text">Lo que deberías cobrar</div>
                <div class="billetera-monto"><span class="moneda">$</span><span id="lblMontoPretendidoLive">{{ number_format($montoPretendido, 0, ',', '.') }}</span></div>
                <div class="billetera-subtexto"><i class="fas fa-hourglass-half me-1"></i>En Trámite / Potencial Mes</div>
            </div>
        </div>

        <!-- SECTOR EN VIVO: RESOLUCIONES EN CAMINO (EMITIDAS POR ADMINISTRACIÓN APOS) -->
        <div id="boxResolucionesEnCamino" style="background: linear-gradient(135deg, rgba(14, 165, 233, 0.16), rgba(30, 41, 59, 0.95)); border: 1.5px solid rgba(56, 189, 248, 0.45); border-radius: 16px; padding: 14px 16px; margin-bottom: 18px; box-shadow: 0 8px 25px rgba(2, 132, 199, 0.2);">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                <div style="font-size:0.86rem; font-weight:800; color:#38bdf8; text-transform:uppercase; letter-spacing:0.4px;">
                    <i class="fas fa-paper-plane me-1 text-warning"></i> Resoluciones en Camino (APOS)
                </div>
                <span class="badge bg-warning text-dark rounded-pill" id="badgeCountEnCamino" style="font-size:0.7rem; font-weight:800;">
                    {{ isset($resolucionesEnCamino) ? count($resolucionesEnCamino) : 0 }} Disponible(s)
                </span>
            </div>
            <p style="font-size:0.76rem; color:#cbd5e1; margin:0 0 10px 0; line-height:1.35;">
                Resoluciones cargadas por Administración APOS. Tocá una para <strong>traer automáticamente Padre Titular + Alumno + Escuela + Horas</strong> y completar tu documentación:
            </p>
            <div id="contenedorResolucionesEnCamino">
                @forelse($resolucionesEnCamino ?? [] as $resCamino)
                    @php
                        $alNom = $resCamino->alumno ? $resCamino->alumno->nombre_completo : 'Alumno APOS';
                        $alDni = $resCamino->alumno ? ($resCamino->alumno->dni ?? '') : '';
                        $titNom = $resCamino->titular ? $resCamino->titular->nombre_completo : ($resCamino->alumno && $resCamino->alumno->titular ? $resCamino->alumno->titular->nombre_completo : 'Titular APOS');
                        $titAfil = $resCamino->titular ? ($resCamino->titular->nro_afiliado ?? '') : '';
                        $escNom = $resCamino->escuela ? $resCamino->escuela->nombre_completo : 'Escuela Provincial';
                        $diagNom = $resCamino->diagnostico ?: ($resCamino->alumno && $resCamino->alumno->diagnostico ? $resCamino->alumno->diagnostico->nombre : 'CUD Activo');
                    @endphp
                    <div style="background:#0f172a; border:1px solid rgba(250,204,21,0.4); border-left:4px solid #facc15; border-radius:12px; padding:10px 12px; margin-bottom:8px; display:flex; justify-content:space-between; align-items:center; gap:10px;">
                        <div style="min-width:0; flex:1;">
                            <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
                                <span style="background:#facc15; color:#0f172a; font-weight:800; font-size:0.7rem; padding:2px 7px; border-radius:6px;">
                                    <i class="fas fa-file-signature me-1"></i>{{ $resCamino->nro_resolucion }}
                                </span>
                                <span style="font-weight:700; color:#f8fafc; font-size:0.86rem;">{{ $alNom }}</span>
                            </div>
                            <div style="font-size:0.74rem; color:#94a3b8; margin-top:3px;">
                                <i class="fas fa-user-shield text-info me-1"></i>Padre: <strong style="color:#e2e8f0;">{{ $titNom }}</strong> &bull; <i class="fas fa-school text-warning ms-1 me-1"></i>{{ $escNom }}
                            </div>
                        </div>
                        <button type="button"
                            onclick='tomarResolucionEnCamino(@json([
                                "expediente_id" => $resCamino->id,
                                "alumno_id" => $resCamino->alumno_id,
                                "titular_id" => $resCamino->titular_id,
                                "escuela_id" => $resCamino->escuela_id,
                                "nro_resolucion" => $resCamino->nro_resolucion,
                                "alumno_nombre" => $alNom,
                                "alumno_dni" => $alDni,
                                "titular_nombre" => $titNom,
                                "titular_afiliado" => $titAfil,
                                "escuela_nombre" => $escNom,
                                "diagnostico" => $diagNom,
                                "horarios_atencion" => $resCamino->horarios_atencion ?: "Lunes a Viernes de 08:00 a 11:00 hs (3 hs/día)"
                            ]))'
                            style="background:linear-gradient(135deg, #10b981, #059669); color:white; border:none; border-radius:10px; padding:8px 12px; font-size:0.74rem; font-weight:800; cursor:pointer; white-space:nowrap; box-shadow:0 4px 10px rgba(16,185,129,0.3);">
                            <i class="fas fa-hand-pointer me-1"></i> Tomar
                        </button>
                    </div>
                @empty
                    <div style="font-size:0.78rem; color:#94a3b8; text-align:center; padding:6px;">
                        No hay resoluciones pendientes de tomar. Podés buscar por N° de Resolución en "Alumno Nuevo".
                    </div>
                @endforelse
            </div>
        </div>

        <!-- EL VÚMETRO (ECUALIZADOR DE RENDIMIENTO) -->
        <div class="vumetro-container">
            <div class="vumetro-header">
                <span>Rendimiento Mensual</span>
                <span class="ok-text">75% Óptimo</span>
            </div>
            <div class="ecualizador" id="ecualizadorPuas">
                <!-- Se crean dinámicamente por JS -->
            </div>
        </div>

        <!-- SECTOR: ALUMNO NUEVO / ACCIÓN RÁPIDA -->
        <div class="accion-rapida" onclick="abrirModalNuevo()">
            <div class="accion-rapida-info">
                <h3>Alumno Nuevo</h3>
                <p>Escanear o buscar por comando de voz</p>
            </div>
            <div class="accion-rapida-icon">
                <i class="fas fa-user-plus"></i>
            </div>
        </div>

        <!-- SECTOR: MIS ALUMNOS / ACCIÓN RÁPIDA -->
        <div class="accion-rapida" style="background: linear-gradient(135deg, #10b981, #059669);" onclick="abrirModalMisAlumnos()">
            <div class="accion-rapida-info">
                <h3>Mis Alumnos</h3>
                <p>Buscar paciente para cargar novedad</p>
            </div>
            <div class="accion-rapida-icon">
                <i class="fas fa-users"></i>
            </div>
        </div>

        <!-- SECTOR: MIS REQUISITOS (DOCUMENTACIÓN) -->
        <div class="accion-rapida" style="background: linear-gradient(135deg, #f59e0b, #d97706); margin-top: 15px;" onclick="abrirModalRequisitos()">
            <div class="accion-rapida-info">
                @php $pendientesCount = isset($tiposDocumentos) ? $tiposDocumentos->whereIn('estado_subida', ['sin_entregar', 'rechazado', 'observado'])->count() : 0; @endphp
                <h3>Mis Requisitos <span class="badge bg-danger rounded-pill ms-2" style="font-size:0.7rem;">{{ $pendientesCount }} Pendientes</span></h3>
                <p>Sube tu documentación obligatoria (Ej: Seguro Méd., DNI)</p>
            </div>
            <div class="accion-rapida-icon">
                <i class="fas fa-file-invoice"></i>
            </div>
        </div>

    </div>

    <!-- NAVEGACIÓN INFERIOR (TAB NAV) -->
    <nav class="bottom-nav">
        <a href="#" class="nav-link nav-item active">
            <i class="fas fa-home"></i>
            <span>Hoy</span>
        </a>
        <a href="#" class="nav-link nav-item">
            <i class="fas fa-history"></i>
            <span>Historial</span>
        </a>
        <a href="#" class="nav-link nav-item">
            <i class="fas fa-file-invoice-dollar"></i>
            <span>Mi Billetera</span>
        </a>
        <a href="#" class="nav-link nav-item">
            <i class="fas fa-user-circle"></i>
            <span>Perfil</span>
        </a>
    </nav>

    <!-- OVERLAY ÚNICO PARA MODALES -->
    <div class="modal-overlay" id="overlayNuevo"></div>

    <!-- ESTRUCTURA DEL MODAL BOTTOM SHEET (NUEVO ALUMNO) -->
    <div class="bottom-sheet" id="sheetNuevo">
        <div class="sheet-pill"></div>
        <div class="sheet-header">
            <h3>Nuevo Alumno / Ingreso</h3>
            <button class="sheet-close" onclick="cerrarModalNuevo()"><i class="fas fa-times-circle"></i></button>
        </div>

        <!-- Selector de Métodos -->
        <div style="display: flex; gap: 10px; margin: -5px auto 15px auto; width: 100%;">
            <div id="tabModoComun" class="scan-btn" style="flex: 1; padding: 12px; cursor: pointer; border: 1px solid #3b82f6; background: rgba(59, 130, 246, 0.1);" onclick="cambiarMetodo('comun')">
                <i class="fas fa-list-ul" style="font-size: 1.2rem; color: #3b82f6;"></i>
                <span style="font-size: 0.8rem; color: white;">Método Común</span>
            </div>
            <div id="tabModoParlante" class="scan-btn" style="flex: 1; padding: 12px; cursor: pointer; border: 1px solid var(--tarjeta-borde); background: var(--bg-oscuro);" onclick="cambiarMetodo('parlante')">
                <i class="fas fa-microphone" style="font-size: 1.2rem; color: #8b5cf6;"></i>
                <span style="font-size: 0.8rem; color: var(--gris-texto);">Modo Parlante</span>
            </div>
        </div>

        <p id="subtextoInteraccion" style="font-size: 0.85rem; color: var(--gris-texto); margin-bottom: 12px;">
            Si tenés el <strong>N° de Resolución APOS</strong>, escribilo en el 1° campo para traer automáticamente al Titular, Alumno y Escuela; o buscalos manualmente debajo.
        </p>

        <!-- === VISTA MÉTODO COMÚN (TRADICIONAL) === -->
        <div id="seccionModoComun">
            <!-- 1° PRIMERA PREGUNTA: BUSCADOR POR N° DE RESOLUCIÓN APOS -->
            <div style="background: rgba(245, 158, 11, 0.12); border: 1.5px solid #f59e0b; border-radius: 14px; padding: 12px; margin-bottom: 14px;">
                <label style="font-size: 0.78rem; font-weight: 800; color: #fbbf24; display: block; margin-bottom: 6px; text-transform: uppercase;">
                    <i class="fas fa-file-signature me-1"></i> 1° Resolución N° (Verifica en BD y trae Titular + Hijo + Escuela):
                </label>
                <div class="input-group-voice" style="margin-bottom: 0;">
                    <input type="text" id="comunResolucionSearchInput" class="input-dark shadow-sm" style="border-color: #f59e0b; font-weight: 700; color: #fef08a;" placeholder="🔢 Escribir N° Resolución (Ej: 599, 611, 999 o RES-...)" oninput="filtrarResolucionComun()">
                </div>
                <div id="listaResolucionesComun" style="display: none; max-height: 230px; overflow-y: auto; background: #0f172a; border-radius: 12px; border: 1px solid #f59e0b; margin-top: 8px; padding: 8px;">
                </div>
            </div>

            <!-- 2° Buscador de Titular -->
            <div class="input-group-voice" style="margin-bottom: 12px;">
                <input type="text" id="comunTitularInput" class="input-dark shadow-sm" placeholder="🔍 2° Buscar Titular / Padre (o se autocompleta por Resolución)..." oninput="filtrarTitularComun()">
            </div>
            <div id="listaTitularesComun" style="display: none; max-height: 220px; overflow-y: auto; background: #0f172a; border-radius: 12px; border: 1px solid var(--tarjeta-borde); margin-bottom: 15px; padding: 8px;">
            </div>

            <!-- 3° Buscador de Alumno (Dependiente) -->
            <div class="input-group-voice" style="margin-bottom: 12px;">
                <input type="text" id="comunAlumnoInput" class="input-dark shadow-sm" placeholder="🔍 3° Buscar Nombre del Alumno (Hijo/Paciente)..." oninput="filtrarAlumnoComun()">
            </div>
            <div id="listaAlumnosComun" style="display: none; max-height: 220px; overflow-y: auto; background: #0f172a; border-radius: 12px; border: 1px solid var(--tarjeta-borde); margin-bottom: 15px; padding: 8px;">
            </div>

            <!-- 4° Buscador de Escuela -->
            <div class="input-group-voice" style="margin-bottom: 12px;">
                <input type="text" id="comunEscuelaInput" class="input-dark shadow-sm" placeholder="🔍 4° Buscar Institución / Escuela..." oninput="filtrarEscuelaComun()">
            </div>
            <div id="listaEscuelasComun" style="display: none; max-height: 220px; overflow-y: auto; background: #0f172a; border-radius: 12px; border: 1px solid var(--tarjeta-borde); margin-bottom: 15px; padding: 8px;">
            </div>

            <!-- Formulario Datos del Alumno y Resolución APOS -->
            <div id="formDatosAlumno" style="display: none; background: #1e293b; padding: 15px; border-radius: 12px; border: 1px solid #38bdf8; margin-bottom: 15px;">
                <h4 style="margin: 0 0 10px 0; font-size: 0.92rem; color: #38bdf8;">
                    <i class="fas fa-file-signature me-1"></i> Resolución APOS, Horarios y Vínculo con Padre
                </h4>

                <label style="font-size: 0.75rem; color: #fbbf24; font-weight: 700; display: block; margin-bottom: 4px;">
                    N° de Resolución APOS (si aún no la tiene, dejar vacío para pedir autorización al Auditor):
                </label>
                <input type="text" id="alumnoResolucionInput" class="input-dark mb-2" style="width: 100%; margin-bottom: 10px; padding: 10px; border-color: #f59e0b; font-weight: 700;" placeholder="Ej: 505/25 o RES-APOS-2026 (Opcional)">

                <div style="display: grid; grid-template-columns: 110px 1fr; gap: 10px; margin-bottom: 10px;">
                    <div>
                        <label style="font-size: 0.75rem; color: #fbbf24; font-weight: 700; display: block; margin-bottom: 4px;">
                            Horas por Día:
                        </label>
                        <input type="number" id="alumnoHorasDiariasInput" min="1" max="12" value="3" class="input-dark js-input-horas-diarias" style="width: 100%; padding: 10px; border-color: #38bdf8; font-weight: 800; text-align: center; color: #fbbf24;">
                    </div>
                    <div>
                        <label style="font-size: 0.75rem; color: #94a3b8; display: block; margin-bottom: 4px;">
                            Días y Horarios de Atención:
                        </label>
                        <input type="text" id="alumnoHorariosInput" class="input-dark" style="width: 100%; padding: 10px;" value="Lunes a Viernes de 08:00 a 11:00 hs (3 hs/día)">
                    </div>
                </div>

                <input type="text" id="alumnoDni" class="input-dark mb-2" style="width: 100%; margin-bottom: 10px; padding: 10px;" placeholder="DNI del Alumno">
                <input type="number" id="alumnoEdad" class="input-dark mb-2" style="width: 100%; margin-bottom: 10px; padding: 10px; display: none;" placeholder="Edad">
                <input type="text" id="alumnoPatologia" class="input-dark mb-2" style="width: 100%; margin-bottom: 10px; padding: 10px;" placeholder="Diagnóstico / Patología CUD">
                <input type="text" id="alumnoCUD" class="input-dark mb-2" style="width: 100%; margin-bottom: 10px; padding: 10px; display: none;" placeholder="Nro de CUD (Opcional)">

                <div id="bannerEstadoNotificacionAuditor" class="mt-2" style="font-size: 0.8rem; background: rgba(16, 185, 129, 0.15); padding: 12px; border-radius: 8px; border-left: 3px solid #10b981; color: #d1fae5;">
                    <div class="text-warning mb-1" style="font-weight: 700;"><i class="fas fa-bell me-1"></i> Notificación Automática al Auditor APOS:</div>
                    <div>Al señalar a <strong id="lblTitularReq" class="text-white">el Titular</strong> y su hijo en <strong id="lblEscuelaReq" class="text-white">la Escuela</strong>, el Auditor ya recibe en vivo la vinculación <strong>Alumno + Padre + Docente + Resolución</strong>.</div>
                </div>
            </div>
        </div>

        <!-- === VISTA MÉTODO PARLANTE (DICTADO) === -->
        <div id="seccionModoParlante" style="display: none;">
            <div class="input-group-voice" style="margin-bottom: 15px; width: 100%; box-sizing: border-box;">
                <input type="text" id="alumnoSearchInput" class="input-dark" style="min-width: 0; flex: 1;" placeholder="Ej: Dicta o escribe el nombre (ej: Abayay)...">
                <button class="btn-mic" id="btnMicSearch" onclick="iniciarDictadoVoz()" title="Búsqueda por Voz">
                    <i class="fas fa-microphone"></i>
                </button>
            </div>
            
            <button type="button" onclick="iniciarDictadoVoz()" class="btn w-100 py-3 rounded-3 fw-bold mb-3 d-flex align-items-center justify-content-center gap-2 shadow-sm" style="background: linear-gradient(135deg, #8b5cf6, #6d28d9); color: white; border: none; font-size: 0.95rem; box-shadow: 0 4px 15px rgba(139, 92, 246, 0.4); cursor: pointer;">
                <i class="fas fa-microphone fa-lg me-1"></i> Tocar para Hablar / Activar Micrófono
            </button>
        </div>

        <!-- Tarjeta de Confirmación de Coincidencia en BD -->
        <div id="cardVerificadoBd" style="display: none; background: rgba(16, 185, 129, 0.15); border: 2px solid #10b981; border-radius: 14px; padding: 15px; margin-bottom: 15px; box-shadow: 0 0 15px rgba(16, 185, 129, 0.3);">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
                <span style="font-weight: 700; color: #10b981; font-size: 0.9rem; text-transform: uppercase;">
                    <i class="fas fa-check-circle me-1" style="font-size: 1.1rem;"></i> ¡REGISTRO OFICIAL VERIFICADO EN BD!
                </span>
                <span class="badge bg-success" style="font-size: 0.75rem; padding: 5px 8px;">VÁLIDO 100%</span>
            </div>
            <div style="font-size: 0.85rem; color: white; line-height: 1.6;">
                <div><strong style="color: #60a5fa;"><i class="fas fa-user-shield me-1"></i> Titular / Padre:</strong> <span id="verificadoTitular" class="fw-bold"></span></div>
                <div><strong style="color: #fbbf24;"><i class="fas fa-child me-1"></i> Alumno Atendido:</strong> <span id="verificadoAlumno" class="fw-bold"></span></div>
                <div><strong style="color: #34d399;"><i class="fas fa-school me-1"></i> Institución:</strong> <span id="verificadoEscuela" class="fw-bold"></span></div>
            </div>
        </div>

        <!-- Escáneres Especiales con Subida de Archivos -->
        <form id="formSubidaDoc" style="display: none;">
            @csrf
            <input type="file" id="cameraPicker" name="documento" accept="image/*" capture="environment" onchange="manejarSubidaArchivo('cameraPicker')">
            <input type="file" id="galleryPicker" name="documento" accept="image/*,.pdf" onchange="manejarSubidaArchivo('galleryPicker')">
        </form>

        <div class="scan-grid" style="grid-template-columns: 1fr 1fr; gap: 10px;">
            <div class="scan-btn" id="btnScanCamera" onclick="abrirCamaraUpload('DNI / Documento Frontal', 'cameraPicker')">
                <i class="fas fa-camera text-success"></i>
                <span id="spanScanCamera" style="font-size: 0.75rem;">Tomar Foto Directa</span>
            </div>
            <div class="scan-btn" id="btnScanGallery" onclick="abrirCamaraUpload('Archivo Adjunto / PDF', 'galleryPicker')">
                <i class="fas fa-file-upload text-primary"></i>
                <span id="spanScanGallery" style="font-size: 0.75rem;">Subir de Galería/PDF</span>
            </div>
            <div class="scan-btn" onclick="abrirCamaraUpload('Código QR / Factura', 'cameraPicker')" style="grid-column: span 2;">
                <i class="fas fa-qrcode text-warning"></i>
                <span style="font-size: 0.8rem;">Escanear Código QR en Vivo</span>
            </div>
        </div>

        <button class="btn-primary-dark mt-3" id="btnConfirmarVinculoAuditor" onclick="cerrarYCrear()">
            <i class="fas fa-bell me-2"></i> Vincular Alumno con Resolución y Notificar al Auditor
        </button>
    </div>

    <!-- ESTRUCTURA DEL MODAL BOTTOM SHEET (MIS ALUMNOS) -->
    <div class="bottom-sheet" id="sheetMisAlumnos">
        <div class="sheet-pill"></div>
        <div class="sheet-header">
            <h3><i class="fas fa-users text-primary me-2"></i> Mis Alumnos Recurrentes</h3>
            <button class="sheet-close" onclick="cerrarModalMisAlumnos()"><i class="fas fa-times-circle"></i></button>
        </div>

        <p style="font-size: 0.85rem; color: var(--gris-texto); margin-bottom: 15px;">
            Alumnos asignados en el mes activo. Verifique el cumplimiento de <b>Factura ARCA</b> y <b>Certificación Digital de Directora</b>.
        </p>

        <!-- Barra de búsqueda rápida -->
        <div class="input-group-voice" style="margin-bottom: 15px;">
            <input type="text" id="misAlumnosSearchInput" class="input-dark shadow-sm" placeholder="🔍 Buscar por nombre de alumno o escuela..." oninput="filtrarAlumnosActivos()">
        </div>

        <!-- Lista Integrada de Alumnos y Requisitos Mensuales -->
        <div id="listaMisAlumnosContenedor">
            @foreach($alumnosDemo as $alumno)
                <div class="alumno-card item-alumno-filtrable" data-nombre="{{ strtolower($alumno->nombre) }}" data-curso="{{ strtolower($alumno->curso) }}" data-raw-nombre="{{ $alumno->nombre }}" data-raw-curso="{{ $alumno->curso }}" style="flex-direction: column; align-items: stretch; gap: 10px; padding: 16px; margin-bottom: 14px; background: #1e293b; border-radius: 14px; border: 1px solid var(--tarjeta-borde);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h4 style="font-size: 1.05rem; margin: 0; font-weight: 700; color: white;">
                                <i class="fas fa-child text-warning me-1"></i> <span class="js-hl-nombre">{{ $alumno->nombre }}</span>
                            </h4>
                            <p style="font-size: 0.8rem; color: #94a3b8; margin: 2px 0 0 0;"><i class="fas fa-school me-1 text-info"></i> <span class="js-hl-curso">{{ $alumno->curso }}</span></p>
                        </div>
                        <div>
                            @if(($alumno->progreso ?? 0) == 100)
                                <span class="badge bg-success" style="font-size: 0.75rem;"><i class="fas fa-check-circle me-1"></i> 100% AL DÍA</span>
                            @else
                                <span class="badge bg-warning text-dark" style="font-size: 0.75rem;"><i class="fas fa-clock me-1"></i> EN PROCESO</span>
                            @endif
                        </div>
                    </div>

                    <!-- Barra de Progreso del Mes -->
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div style="height: 6px; flex: 1; background: #0f172a; border-radius: 6px; overflow: hidden;">
                            <div style="height: 100%; width: {{ $alumno->progreso ?? 25 }}%; background: {{ ($alumno->progreso ?? 0) == 100 ? '#10b981' : '#f59e0b' }}; border-radius: 6px;"></div>
                        </div>
                        <span style="font-size: 0.7rem; font-weight: 700; color: {{ ($alumno->progreso ?? 0) == 100 ? '#10b981' : '#f59e0b' }};">{{ $alumno->progreso ?? 25 }}%</span>
                    </div>

                    <!-- Requisitos Mensuales del Alumno -->
                    <div style="background: #0f172a; border-radius: 10px; padding: 10px; display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 0.75rem;">
                        <!-- Requisito 1: Factura ARCA -->
                        <div style="border-right: 1px solid #334155; padding-right: 5px;">
                            <span class="text-muted d-block">1. Factura ARCA:</span>
                            @if(($alumno->progreso ?? 0) >= 75)
                                <strong class="text-success"><i class="fas fa-check-circle me-1"></i> Presentada</strong>
                            @else
                                <button type="button" class="btn btn-sm btn-outline-warning p-1 py-0 mt-1" style="font-size: 0.7rem;" onclick="abrirCamaraUpload('Factura ARCA - {{ $alumno->nombre }}', 'galleryPicker')">
                                    <i class="fas fa-file-invoice"></i> Subir Factura
                                </button>
                            @endif
                        </div>
                        <!-- Requisito 2: Certificación Directora -->
                        <div>
                            <span class="text-muted d-block">2. Aval Directora:</span>
                            @if(($alumno->progreso ?? 0) >= 50)
                                <strong class="text-success" title="Firmado digitalmente"><i class="fas fa-signature me-1"></i> Certificado</strong>
                            @else
                                @php
                                    $msgWa = urlencode("Hola Directora, le solicito la certificación digital de las asistencias del alumno " . $alumno->nombre . " para el mes activo. Puede certificar digitalmente ingresando aquí: " . route('directora.asistencias.demo'));
                                @endphp
                                <a href="https://wa.me/?text={{ $msgWa }}" class="btn btn-sm btn-outline-success p-1 py-0 mt-1" style="font-size: 0.7rem;" target="_blank" title="Enviar enlace de firma por WhatsApp">
                                    <i class="fab fa-whatsapp me-1"></i> Enviar WhatsApp
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
            <div id="noResultsMsg" style="display: none; text-align: center; color: var(--gris-texto); margin-top: 20px;">
                <i class="fas fa-search mb-2" style="font-size: 2rem; opacity: 0.5;"></i>
                <p>No se encontraron alumnos con ese criterio.</p>
            </div>
        </div>
    </div>

    <!-- ESTRUCTURA DEL MODAL BOTTOM SHEET (MIS REQUISITOS) -->
    <div class="bottom-sheet" id="sheetRequisitos">
        <div class="sheet-pill"></div>
        <div class="sheet-header">
            <h3>Documentación Docente</h3>
            <button class="sheet-close" onclick="cerrarModalRequisitos()"><i class="fas fa-times-circle"></i></button>
        </div>

        <p style="font-size: 0.85rem; color: var(--gris-texto); margin-bottom: 15px;">
            Tu perfil requiere adjuntar la siguiente documentación (exigido por Auditoría)
        </p>

        <!-- Lista Integrada de Requisitos -->
        <div id="listaMisRequisitosContenedor">
            @forelse($tiposDocumentos as $req)
                <div class="alumno-card" style="align-items: center;" @if(in_array($req->estado_subida, ['sin_entregar', 'rechazado', 'observado'])) onclick="abrirCamaraUpload('{{ $req->nombre }}', 'cameraPicker')" @endif>
                    <div class="alumno-info" style="color: {{ in_array($req->estado_subida, ['sin_entregar', 'rechazado']) ? '#facc15' : '#a1a1aa' }}">
                        <h4 style="font-size: 1rem; color:inherit;">
                            {{ $req->nombre }} 
                            {!! $req->es_obligatorio ? '<span class="text-danger" style="font-size:0.7rem;">(Obligatorio)</span>' : '' !!}
                        </h4>
                        <p style="font-size: 0.75rem;"><i class="fas fa-clock me-1 mb-2 text-warning"></i> @if($req->vencimiento_dias) Vence cada {{ $req->vencimiento_dias }} días @else Única Vez @endif</p>
                        
                        @if($req->estado_subida == 'observado' || $req->estado_subida == 'rechazado')
                            <p style="font-size: 0.75rem; color: #ef4444; font-weight: bold;"><i class="fas fa-exclamation-triangle"></i> {{ $req->comentarios ?? 'Documento rechazado o requiere cambios.' }}</p>
                        @elseif($req->estado_subida == 'pendiente')
                            <p style="font-size: 0.75rem; color: #3b82f6;"><i class="fas fa-search"></i> En Revisión por Auditor</p>
                        @elseif($req->estado_subida == 'aprobado')
                            <p style="font-size: 0.75rem; color: #10b981;"><i class="fas fa-check-double"></i> Aprobado Vigente</p>
                        @else
                            <p style="font-size: 0.75rem; color: #a1a1aa;">{{ $req->descripcion }}</p>
                        @endif
                    </div>
                    
                    <div class="text-center">
                        @if(in_array($req->estado_subida, ['sin_entregar', 'rechazado', 'observado']))
                            <div class="estado sin_informar text-center" style="width: 40px; height: 40px; font-size: 1rem;">
                                <i class="fas fa-camera"></i>
                            </div>
                        @else
                            <div class="estado {{ $req->estado_subida == 'aprobado' ? 'aprobado' : 'pendiente' }}" style="width: 40px; height: 40px; font-size: 1rem;">
                                <i class="fas {{ $req->estado_subida == 'aprobado' ? 'fa-check' : 'fa-hourglass-half' }}"></i>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div style="text-align: center; color: var(--gris-texto); margin-top: 20px;">
                    <i class="fas fa-check-circle mb-2 text-success" style="font-size: 2rem;"></i>
                    <p>No tienes requisitos pendientes por entregar.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- SCRIPTS INTEGRADOS -->
    <script>
        // 1. ECUALIZADOR / VÚMETRO DE RENDIMIENTO
        const ecualizadorContenedor = document.getElementById('ecualizadorPuas');
        if (ecualizadorContenedor) {
            const totalBarras = 18;
            const rendimientoActivo = Math.floor(totalBarras * 0.75);
            const alturasPattern = [20, 30, 45, 60, 80, 50, 40, 65, 90, 100, 85, 75, 55, 65, 80, 95, 85, 75];

            for (let i = 0; i < totalBarras; i++) {
                const barra = document.createElement('div');
                barra.classList.add('barra');
                barra.style.height = alturasPattern[i] + '%';
                
                if (i < 8) {
                    barra.classList.add('lvl-bajo');
                } else if (i < 13) {
                    barra.classList.add('lvl-medio');
                } else {
                    barra.classList.add('lvl-alto');
                }

                if (i < rendimientoActivo) {
                    setTimeout(() => {
                        barra.classList.add('activa');
                    }, i * 50);
                }

                ecualizadorContenedor.appendChild(barra);
            }
        }

        // 2. ELEMENTOS Y ESTADOS GLOBALES DE MODALES
        const overlay = document.getElementById('overlayNuevo');
        const sheet = document.getElementById('sheetNuevo');
        const sheetMis = document.getElementById('sheetMisAlumnos');
        const sheetReq = document.getElementById('sheetRequisitos');

        let pasoActual = 0;
        let datosAsistidos = {};
        let timeoutComun = null;
        let metodoActual = 'comun';

        function abrirModalNuevo() {
            cerrarTodosLosModales();
            overlay.classList.add('active');
            sheet.classList.add('active');
            cambiarMetodo('comun');
            datosAsistidos = {};
            pasoActual = 1;
        }

        function cerrarModalNuevo() {
            sheet.classList.remove('active');
            evaluarOverlay();
            if (window.speechSynthesis) window.speechSynthesis.cancel();
            pasoActual = 0;
            if (document.getElementById('btnScanCamera')) {
                document.getElementById('btnScanCamera').style.border = "1px solid var(--tarjeta-borde)";
                document.getElementById('btnScanCamera').style.boxShadow = "none";
                document.getElementById('spanScanCamera').innerText = "Tomar Foto Directa";
            }
        }

        function abrirModalMisAlumnos() {
            cerrarTodosLosModales();
            overlay.classList.add('active');
            sheetMis.classList.add('active');
            if (window.speechSynthesis) window.speechSynthesis.cancel();
        }

        function cerrarModalMisAlumnos() {
            sheetMis.classList.remove('active');
            evaluarOverlay();
        }

        function abrirModalRequisitos() {
            cerrarTodosLosModales();
            overlay.classList.add('active');
            if (sheetReq) sheetReq.classList.add('active');
            if (window.speechSynthesis) window.speechSynthesis.cancel();
        }

        function cerrarModalRequisitos() {
            if (sheetReq) sheetReq.classList.remove('active');
            evaluarOverlay();
        }

        function cerrarTodosLosModales() {
            if (sheet) sheet.classList.remove('active');
            if (sheetMis) sheetMis.classList.remove('active');
            if (sheetReq) sheetReq.classList.remove('active');
            overlay.classList.remove('active');
            if (window.speechSynthesis) window.speechSynthesis.cancel();
        }

        function evaluarOverlay() {
            const ningunActivo = !sheet.classList.contains('active') && 
                                !sheetMis.classList.contains('active') && 
                                (!sheetReq || !sheetReq.classList.contains('active'));
            if (ningunActivo) {
                overlay.classList.remove('active');
            }
        }

        if (overlay) {
            overlay.addEventListener('click', cerrarTodosLosModales);
        }

        // 3. CAMBIO ENTRE MÉTODO COMÚN Y MODO PARLANTE
        function cambiarMetodo(modo) {
            metodoActual = modo;
            const tabComun = document.getElementById('tabModoComun');
            const tabParlante = document.getElementById('tabModoParlante');
            const secComun = document.getElementById('seccionModoComun');
            const secParlante = document.getElementById('seccionModoParlante');
            const subtexto = document.getElementById('subtextoInteraccion');

            if (modo === 'parlante') {
                tabParlante.style.background = "rgba(139, 92, 246, 0.1)";
                tabParlante.style.borderColor = "#8b5cf6";
                tabComun.style.background = "var(--bg-oscuro)";
                tabComun.style.borderColor = "var(--tarjeta-borde)";
                secParlante.style.display = 'block';
                secComun.style.display = 'none';
                subtexto.innerText = "Siga las instrucciones habladas por el asistente y dictée sus respuestas.";
                iniciarAsistenteSecuencial();
            } else {
                tabComun.style.background = "rgba(59, 130, 246, 0.1)";
                tabComun.style.borderColor = "#3b82f6";
                tabParlante.style.background = "var(--bg-oscuro)";
                tabParlante.style.borderColor = "var(--tarjeta-borde)";
                secComun.style.display = 'block';
                secParlante.style.display = 'none';
                subtexto.innerText = "Inicie la búsqueda seleccionando Escuela y Alumno manualmente.";
                if (window.speechSynthesis) window.speechSynthesis.cancel();
            }
        }

        function habilitarFormularioDatos() {
            if (datosAsistidos.alumno) {
                document.getElementById('formDatosAlumno').style.display = 'block';
                document.getElementById('lblEscuelaReq').innerText = datosAsistidos.escuela || "la Institución";
                document.getElementById('lblTitularReq').innerText = datosAsistidos.titular || "el Responsable";
                pasoActual = 3; 
                asistenteHabla("Has seleccionado todo. Por favor completa los datos del estudiante y adjunta la documentación requerida escaneando con la cámara.");
            }
        }

        function resaltarCoincidencia(texto, termino) {
            if (!texto) return '';
            if (!termino) return texto;
            const re = new RegExp(`(${termino.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
            return texto.toString().replace(re, '<mark style="background:#facc15; color:#0f172a; font-weight:800; padding:0 3px; border-radius:3px;">$1</mark>');
        }

        function mostrarToastAuditorNotificado(mensaje) {
            let toast = document.getElementById('toastAuditorLive');
            if (!toast) {
                toast = document.createElement('div');
                toast.id = 'toastAuditorLive';
                toast.style.cssText = 'position:fixed; top:16px; left:50%; transform:translateX(-50%); z-index:99999; width:92%; max-width:440px; background:linear-gradient(135deg, #065f46, #047857); color:white; border:2px solid #34d399; border-radius:14px; padding:12px 16px; box-shadow:0 12px 30px rgba(0,0,0,0.6); font-size:0.82rem;';
                document.body.appendChild(toast);
            }
            toast.innerHTML = `
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <div style="font-weight:800; color:#a7f3d0; text-transform:uppercase; font-size:0.74rem; margin-bottom:2px;">
                            <i class="fas fa-bell me-1"></i> AUDITOR APOS NOTIFICADO EN VIVO
                        </div>
                        <div style="line-height:1.35;">${mensaje}</div>
                    </div>
                    <a href="{{ route('auditor.docentes.index') }}" target="_blank" style="background:#facc15; color:#0f172a; font-weight:800; padding:5px 10px; border-radius:20px; text-decoration:none; white-space:nowrap; font-size:0.72rem;">
                        Ver en Auditor <i class="fas fa-external-link-alt ms-1"></i>
                    </a>
                </div>
            `;
            toast.style.display = 'block';
            setTimeout(() => { if (toast) toast.style.display = 'none'; }, 7500);
        }

        function enviarVinculoAlAuditor(cerrarAlFinalizar = false) {
            const alumnoData = datosAsistidos.alumno_data || {};
            const nroRes = document.getElementById('alumnoResolucionInput') ? document.getElementById('alumnoResolucionInput').value.trim() : (alumnoData.nro_resolucion || '');
            const horarios = document.getElementById('alumnoHorariosInput') ? document.getElementById('alumnoHorariosInput').value.trim() : 'Lunes a Viernes (3 hs/día)';
            const patologia = document.getElementById('alumnoPatologia') ? document.getElementById('alumnoPatologia').value.trim() : '';
            const escuelaNom = document.getElementById('comunEscuelaInput') ? document.getElementById('comunEscuelaInput').value.trim() : (datosAsistidos.escuela || '');
            const alumnoNom = document.getElementById('comunAlumnoInput') ? document.getElementById('comunAlumnoInput').value.trim() : (datosAsistidos.alumno || '');
            const titularNom = document.getElementById('comunTitularInput') ? document.getElementById('comunTitularInput').value.trim() : (datosAsistidos.titular || '');

            if (!alumnoNom && !alumnoData.id) {
                if (cerrarAlFinalizar) {
                    alert("Por favor seleccioná primero un Alumno o Titular de la base de datos de APOS.");
                }
                return;
            }

            const btn = document.getElementById('btnConfirmarVinculoAuditor');
            if (btn && cerrarAlFinalizar) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Notificando al Auditor APOS...';
            }

            fetch(`{{ route('pwa.docente.vincular_alumno') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    alumno_id: alumnoData.id || null,
                    titular_id: datosAsistidos.titular_id || (alumnoData.titular ? alumnoData.titular.id : null),
                    escuela_id: datosAsistidos.escuela_id || (alumnoData.escuela ? alumnoData.escuela.id : null),
                    alumno_nombre: alumnoNom,
                    titular_nombre: titularNom,
                    escuela_nombre: escuelaNom,
                    nro_resolucion: nroRes,
                    horarios_atencion: horarios,
                    patologia: patologia
                })
            })
            .then(r => r.json())
            .then(resp => {
                if (btn && cerrarAlFinalizar) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-bell me-2"></i> Vincular Alumno con Resolución y Notificar al Auditor';
                }
                if (resp && resp.ok) {
                    const banner = document.getElementById('bannerEstadoNotificacionAuditor');
                    if (banner) {
                        banner.style.background = 'rgba(16, 185, 129, 0.25)';
                        banner.style.borderColor = '#10b981';
                        banner.innerHTML = `<div class="text-warning fw-bold"><i class="fas fa-check-circle me-1"></i> ¡AUDITOR NOTIFICADO EN TIEMPO REAL!</div><div>${resp.message}</div>`;
                    }
                    if (cerrarAlFinalizar) {
                        cerrarModalNuevo();
                        if (typeof window.integraModalElegante === 'function') {
                            window.integraModalElegante({
                                tipo: 'success',
                                badge: 'VÍNCULO Y RESOLUCIÓN EN VIVO',
                                titulo: 'Auditor APOS Notificado Inmediatamente',
                                mensaje: resp.message,
                                linkUrl: "{{ route('auditor.docentes.index') }}",
                                linkTexto: 'Ver en Bandeja del Auditor',
                                autoCerrarMs: 5500
                            });
                        } else {
                            mostrarToastAuditorNotificado(resp.message);
                        }
                    } else {
                        mostrarToastAuditorNotificado(resp.message);
                    }
                }
            })
            .catch(err => {
                console.error('Error notificando al auditor:', err);
                if (btn && cerrarAlFinalizar) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-bell me-2"></i> Vincular Alumno con Resolución y Notificar al Auditor';
                }
            });
        }

        function mostrarRegistroVerificado(titular, alumno, escuela) {
            const card = document.getElementById('cardVerificadoBd');
            if (!card) return;
            
            document.getElementById('verificadoTitular').innerText = titular || 'Titular Verificado en BD';
            document.getElementById('verificadoAlumno').innerText = alumno || 'Alumno Verificado en BD';
            document.getElementById('verificadoEscuela').innerText = escuela || 'Institución Asignada';
            
            card.style.display = 'block';
            asistenteHabla(`¡Registro verificado en base de datos! Encontrado ${alumno}, hijo de ${titular}.`);
        }

        // 4. BÚSQUEDA POR N° DE RESOLUCIÓN (1° CAMPO) + TOMA DE RESOLUCIÓN EN CAMINO + BÚSQUEDAS FILTRABLES
        function tomarResolucionEnCamino(item) {
            abrirModalNuevo();
            if (document.getElementById('comunResolucionSearchInput')) {
                document.getElementById('comunResolucionSearchInput').value = item.nro_resolucion || '';
            }
            if (document.getElementById('alumnoResolucionInput')) {
                document.getElementById('alumnoResolucionInput').value = item.nro_resolucion || '';
            }
            if (document.getElementById('comunTitularInput')) {
                document.getElementById('comunTitularInput').value = item.titular_nombre || '';
            }
            if (document.getElementById('comunAlumnoInput')) {
                document.getElementById('comunAlumnoInput').value = item.alumno_nombre || '';
            }
            if (document.getElementById('comunEscuelaInput')) {
                document.getElementById('comunEscuelaInput').value = item.escuela_nombre || '';
            }
            if (document.getElementById('alumnoDni')) {
                document.getElementById('alumnoDni').value = item.alumno_dni || '';
            }
            if (document.getElementById('alumnoPatologia')) {
                document.getElementById('alumnoPatologia').value = item.diagnostico || 'CUD Activo';
            }
            if (document.getElementById('alumnoHorariosInput')) {
                document.getElementById('alumnoHorariosInput').value = item.horarios_atencion || 'Lunes a Viernes de 08:00 a 11:00 hs (3 hs/día)';
            }
            if (document.getElementById('alumnoHorasDiariasInput')) {
                document.getElementById('alumnoHorasDiariasInput').value = 3;
            }

            datosAsistidos.alumno = item.alumno_nombre;
            datosAsistidos.titular = item.titular_nombre;
            datosAsistidos.titular_id = item.titular_id;
            datosAsistidos.escuela = item.escuela_nombre;
            datosAsistidos.escuela_id = item.escuela_id;
            datosAsistidos.alumno_data = {
                id: item.alumno_id,
                nombre: item.alumno_nombre,
                dni: item.alumno_dni,
                nro_resolucion: item.nro_resolucion
            };

            mostrarRegistroVerificado(
                (item.titular_nombre || 'Titular APOS') + (item.titular_afiliado ? ` (Af. ${item.titular_afiliado})` : ''),
                `${item.alumno_nombre} • Res: ${item.nro_resolucion}`,
                item.escuela_nombre || 'Escuela Asignada'
            );
            habilitarFormularioDatos();

            if (typeof window.integraModalElegante === 'function') {
                window.integraModalElegante({
                    tipo: 'info',
                    badge: 'RESOLUCIÓN EN CAMINO TOMADA',
                    titulo: `Resolución ${item.nro_resolucion} Vinculada`,
                    mensaje: `Se cargaron automáticamente los datos del Titular (${item.titular_nombre}), Alumno (${item.alumno_nombre}) y Escuela (${item.escuela_nombre}). Completá la documentación faltante y tocá el botón verde para enviar al Visto Bueno del Auditor y Jefe de Área.`,
                    autoCerrarMs: 5200
                });
            }
        }

        function filtrarResolucionComun() {
            clearTimeout(timeoutComun);
            const input = document.getElementById('comunResolucionSearchInput');
            const term = input ? input.value.trim() : '';
            const list = document.getElementById('listaResolucionesComun');
            if (!list) return;
            list.innerHTML = "";
            if (!term || term.length < 1) {
                list.style.display = 'none';
                return;
            }

            // Sincronizar también con el input de resolución del formulario
            if (document.getElementById('alumnoResolucionInput')) {
                document.getElementById('alumnoResolucionInput').value = term;
            }

            timeoutComun = setTimeout(() => {
                fetch(`{{ route('pwa.docente.search') }}?type=resolucion&q=${encodeURIComponent(term)}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.length > 0) {
                            list.style.display = 'block';
                            data.forEach(a => {
                                const row = document.createElement('div');
                                row.className = "alumno-card";
                                row.style = "margin-bottom: 8px; padding: 12px; cursor: pointer; background: #1e293b; border-left: 4px solid #facc15;";

                                const nroRes = a.nro_resolucion || a.resolucion || ('RES-' + a.id);
                                const resH = resaltarCoincidencia(nroRes, term);
                                const nomH = resaltarCoincidencia(a.nombre || '', term);
                                const dniH = resaltarCoincidencia(a.dni || '', term);
                                const padreNom = a.titular ? a.titular.nombre : 'Titular APOS';
                                const padreH = resaltarCoincidencia(padreNom, term);
                                const padreAfil = a.titular && a.titular.nro_afiliado ? ` (Af: ${resaltarCoincidencia(a.titular.nro_afiliado, term)})` : '';
                                const escNom = a.escuela ? a.escuela.nombre : 'Escuela Provincial';

                                row.innerHTML = `<div style="width:100%;">
                                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                                        <span style="background:rgba(250,204,21,0.2); border:1px solid #facc15; color:#fde047; padding:2px 8px; border-radius:6px; font-size:0.75rem; font-weight:800;">
                                            <i class="fas fa-file-signature me-1"></i> ${resH}
                                        </span>
                                        <span class="badge bg-success" style="font-size:0.68rem;">Trae Titular + Hijo</span>
                                    </div>
                                    <div class="fw-bold text-white" style="font-size:0.9rem;"><i class="fas fa-child text-warning me-1"></i> Alumno: ${nomH} <small class="text-muted">(DNI: ${dniH})</small></div>
                                    <div class="small text-info mt-1"><i class="fas fa-user-shield me-1"></i> Padre/Titular: <strong>${padreH}</strong>${padreAfil}</div>
                                    <div class="small text-muted mt-1"><i class="fas fa-school text-warning me-1"></i> ${escNom} &bull; Máx. 3 hs/día</div>
                                </div>`;

                                row.onclick = () => {
                                    list.style.display = 'none';
                                    input.value = nroRes;
                                    if (document.getElementById('alumnoResolucionInput')) {
                                        document.getElementById('alumnoResolucionInput').value = nroRes;
                                    }
                                    document.getElementById('comunAlumnoInput').value = a.nombre;
                                    datosAsistidos.alumno = a.nombre;
                                    datosAsistidos.alumno_data = a;

                                    if (a.titular) {
                                        document.getElementById('comunTitularInput').value = a.titular.nombre;
                                        datosAsistidos.titular = a.titular.nombre;
                                        datosAsistidos.titular_id = a.titular.id;
                                    }

                                    document.getElementById('alumnoDni').value = a.dni || '';
                                    document.getElementById('alumnoPatologia').value = (a.diagnostico ? a.diagnostico.nombre : (a.diagnostico_detalle || 'CUD Activo'));

                                    if (a.escuela) {
                                        document.getElementById('comunEscuelaInput').value = escNom;
                                        datosAsistidos.escuela = escNom;
                                        datosAsistidos.escuela_id = a.escuela.id;
                                    }

                                    mostrarRegistroVerificado(`${padreNom}${a.titular && a.titular.nro_afiliado ? ' (Af. ' + a.titular.nro_afiliado + ')' : ''}`, `${a.nombre} (${nroRes})`, escNom);
                                    habilitarFormularioDatos();
                                    enviarVinculoAlAuditor(false);
                                };
                                list.appendChild(row);
                            });
                        } else {
                            list.style.display = 'block';
                            list.innerHTML = `<div style="padding:10px; font-size:0.8rem; color:#fde047;">
                                <i class="fas fa-info-circle me-1"></i> La Resolución <strong>${term}</strong> se guardará como nueva al seleccionar al Titular y Alumno debajo.
                            </div>`;
                        }
                    });
            }, 90);
        }

        function filtrarTitularComun() {
            clearTimeout(timeoutComun);
            const term = document.getElementById('comunTitularInput').value.trim();
            const list = document.getElementById('listaTitularesComun');
            list.innerHTML = "";
            if(!term || term.length < 1) { list.style.display = 'none'; return; }
            
            timeoutComun = setTimeout(() => {
                fetch(`{{ route('pwa.docente.search') }}?type=titulares&q=${encodeURIComponent(term)}`)
                    .then(res => res.json())
                    .then(data => {
                        if(data.length > 0) {
                            list.style.display = 'block';
                            data.forEach(e => {
                                const row = document.createElement('div');
                                row.className = "alumno-card";
                                row.style = "margin-bottom: 8px; padding: 12px; cursor: pointer; background: #1e293b; border-left: 3px solid #3b82f6;";
                                const nomH = resaltarCoincidencia(e.nombre, term);
                                const dniH = resaltarCoincidencia(e.dni || '', term);
                                const afilH = e.nro_afiliado ? ' | Af: ' + resaltarCoincidencia(e.nro_afiliado, term) : '';

                                let hijosHtml = '';
                                if (e.familiares && e.familiares.length > 0) {
                                    const nombresHijos = e.familiares.map(h => `<span class="badge bg-secondary bg-opacity-25 text-warning me-1"><i class="fas fa-child me-1"></i>${h.nombre}</span>`).join('');
                                    hijosHtml = `<div class="mt-1 small text-muted">${nombresHijos}</div>`;
                                } else {
                                    hijosHtml = `<div class="mt-1 small text-muted font-italic">Sin alumnos dependientes vinculados</div>`;
                                }

                                row.innerHTML = `<div>
                                    <div class="fw-bold text-white"><i class="fas fa-user-shield text-info me-2"></i> ${nomH} <small class="text-muted ms-1">(${dniH}${afilH})</small></div>
                                    ${hijosHtml}
                                </div>`;

                                row.onclick = () => {
                                    document.getElementById('comunTitularInput').value = e.nombre;
                                    datosAsistidos.titular = e.nombre;
                                    datosAsistidos.titular_id = e.id;
                                    list.style.display = 'none';
                                    
                                    const alumSelect = document.getElementById('comunAlumnoInput');
                                    const listAlum = document.getElementById('listaAlumnosComun');
                                    listAlum.style.display = 'block';
                                    listAlum.innerHTML = "";
                                    
                                    if (e.familiares && e.familiares.length > 0) {
                                        alumSelect.placeholder = `🔍 Seleccionar hijo de ${e.nombre}...`;
                                        e.familiares.forEach(a => {
                                            const rowAlum = document.createElement('div');
                                            rowAlum.className = "alumno-card";
                                            rowAlum.style = "margin-bottom: 5px; padding: 10px; cursor: pointer; background: #1e293b; border-left: 3px solid #f59e0b;";
                                            rowAlum.innerHTML = `<i class="fas fa-child text-warning me-2"></i> <strong>${a.nombre}</strong> <small class="text-muted ms-1">(DNI: ${a.dni || 'S/D'})</small>`;
                                            rowAlum.onclick = () => {
                                                alumSelect.value = a.nombre;
                                                datosAsistidos.alumno = a.nombre;
                                                datosAsistidos.alumno_data = a;
                                                listAlum.style.display = 'none';
                                                
                                                document.getElementById('alumnoDni').value = a.dni || '';
                                                document.getElementById('alumnoPatologia').value = (a.diagnostico ? a.diagnostico.nombre : (a.tiene_patologia ? 'Sí, pero sin especificar' : 'Sano'));
                                                
                                                if (document.getElementById('alumnoResolucionInput')) {
                                                    document.getElementById('alumnoResolucionInput').value = a.nro_resolucion || '';
                                                }
                                                const escNombre = a.escuela ? a.escuela.nombre : '';
                                                if (a.escuela) {
                                                    document.getElementById('comunEscuelaInput').value = escNombre;
                                                    datosAsistidos.escuela = escNombre;
                                                    datosAsistidos.escuela_id = a.escuela.id;
                                                }

                                                mostrarRegistroVerificado(e.nombre, a.nombre, escNombre);
                                                habilitarFormularioDatos();
                                                enviarVinculoAlAuditor(false);
                                            };
                                            listAlum.appendChild(rowAlum);
                                        });
                                    } else {
                                        alumSelect.placeholder = "⚠️ Este titular no tiene hijos registrados.";
                                    }
                                };
                                list.appendChild(row);
                            });
                        } else { list.style.display = 'none'; }
                    });
            }, 150);
        }

        function filtrarAlumnoComun() {
            clearTimeout(timeoutComun);
            const term = document.getElementById('comunAlumnoInput').value.trim();
            const list = document.getElementById('listaAlumnosComun');
            if(!term || term.length < 1) { return; }

            timeoutComun = setTimeout(() => {
                fetch(`{{ route('pwa.docente.search') }}?type=alumnos&q=${encodeURIComponent(term)}`)
                    .then(res => res.json())
                    .then(data => {
                        if(data.length > 0) {
                            list.style.display = 'block';
                            list.innerHTML = "";
                            data.forEach(a => {
                                const row = document.createElement('div');
                                row.className = "alumno-card";
                                row.style = "margin-bottom: 8px; padding: 12px; cursor: pointer; background: #1e293b; border-left: 3px solid #f59e0b;";
                                const nomH = resaltarCoincidencia(a.nombre, term);
                                const dniH = resaltarCoincidencia(a.dni || '', term);

                                const padreNombre = a.titular ? a.titular.nombre : 'Sin Titular Asignado';
                                const padreDni = a.titular && a.titular.dni ? ` (DNI: ${a.titular.dni})` : '';

                                row.innerHTML = `<div>
                                    <div class="fw-bold text-white"><i class="fas fa-child text-warning me-2"></i> ${nomH} <small class="text-muted ms-1">(DNI: ${dniH})</small></div>
                                    <div class="small text-info mt-1"><i class="fas fa-user-shield me-1"></i> Padre/Titular: ${padreNombre}${padreDni}</div>
                                </div>`;

                                row.onclick = () => {
                                    document.getElementById('comunAlumnoInput').value = a.nombre;
                                    datosAsistidos.alumno = a.nombre;
                                    datosAsistidos.alumno_data = a;
                                    list.style.display = 'none';

                                    if (a.titular) {
                                        document.getElementById('comunTitularInput').value = a.titular.nombre;
                                        datosAsistidos.titular = a.titular.nombre;
                                        datosAsistidos.titular_id = a.titular.id;
                                    }

                                    document.getElementById('alumnoDni').value = a.dni || '';
                                    document.getElementById('alumnoPatologia').value = (a.diagnostico ? a.diagnostico.nombre : (a.tiene_patologia ? 'Sí, pero sin especificar' : 'Sano'));

                                    if (document.getElementById('alumnoResolucionInput')) {
                                        document.getElementById('alumnoResolucionInput').value = a.nro_resolucion || '';
                                    }
                                    const escNombre = a.escuela ? a.escuela.nombre : '';
                                    if (a.escuela) {
                                        document.getElementById('comunEscuelaInput').value = escNombre;
                                        datosAsistidos.escuela = escNombre;
                                        datosAsistidos.escuela_id = a.escuela.id;
                                    }

                                    mostrarRegistroVerificado(padreNombre, a.nombre, escNombre);
                                    habilitarFormularioDatos();
                                    enviarVinculoAlAuditor(false);
                                };
                                list.appendChild(row);
                            });
                        }
                    });
            }, 80);
        }

        function filtrarEscuelaComun() {
            clearTimeout(timeoutComun);
            const term = document.getElementById('comunEscuelaInput').value.trim();
            const list = document.getElementById('listaEscuelasComun');
            list.innerHTML = "";
            if(!term || term.length < 1) { list.style.display = 'none'; return; }
            
            timeoutComun = setTimeout(() => {
                fetch(`{{ route('pwa.docente.search') }}?type=escuelas&q=${encodeURIComponent(term)}`)
                    .then(res => res.json())
                    .then(data => {
                        if(data.length > 0) {
                            list.style.display = 'block';
                            data.forEach(e => {
                                const row = document.createElement('div');
                                row.className = "alumno-card";
                                row.style = "margin-bottom: 5px; padding: 10px; cursor: pointer; background: #1e293b;";
                                const nomH = resaltarCoincidencia(e.nombre, term);
                                const cueH = e.cue ? ' (CUE: ' + resaltarCoincidencia(e.cue, term) + ')' : '';

                                row.innerHTML = `<i class="fas fa-school text-primary me-2"></i> ${nomH} <small class="text-muted ms-1">${cueH}</small>`;
                                row.onclick = () => {
                                    document.getElementById('comunEscuelaInput').value = e.nombre;
                                    datosAsistidos.escuela = e.nombre;
                                    datosAsistidos.escuela_id = e.id;
                                    list.style.display = 'none';
                                    habilitarFormularioDatos();
                                };
                                list.appendChild(row);
                            });
                        } else { list.style.display = 'none'; }
                    });
            }, 80);
        }

        function filtrarAlumnosActivos() {
            const rawTerm = document.getElementById('misAlumnosSearchInput').value.trim();
            const term = rawTerm.toLowerCase();
            const cards = document.querySelectorAll('.item-alumno-filtrable');
            let encontrados = 0;

            cards.forEach(card => {
                const rawNombre = card.getAttribute('data-raw-nombre') || '';
                const rawCurso = card.getAttribute('data-raw-curso') || '';
                const nombre = card.getAttribute('data-nombre') || '';
                const curso = card.getAttribute('data-curso') || '';
                
                if (!term || nombre.toLowerCase().includes(term) || curso.toLowerCase().includes(term)) {
                    card.style.display = 'flex';
                    encontrados++;
                    const elNom = card.querySelector('.js-hl-nombre');
                    const elCur = card.querySelector('.js-hl-curso');
                    if (elNom) elNom.innerHTML = resaltarCoincidencia(rawNombre, rawTerm);
                    if (elCur) elCur.innerHTML = resaltarCoincidencia(rawCurso, rawTerm);
                } else {
                    card.style.display = 'none';
                }
            });

            if(document.getElementById('noResultsMsg')) {
                document.getElementById('noResultsMsg').style.display = encontrados === 0 ? 'block' : 'none';
            }
        }

        function cerrarYCrear() {
            enviarVinculoAlAuditor(true);
        }

        // 5. SISTEMA INTELIGENTE DE HABLA Y ESCUCHA POR VOZ
        function asistenteHabla(texto, callback) {
            if ('speechSynthesis' in window) {
                window.speechSynthesis.cancel();
                let msg = new SpeechSynthesisUtterance();
                msg.text = texto;
                msg.lang = 'es-AR';
                msg.rate = 1.05;
                msg.pitch = 1.1;
                
                msg.onend = function() {
                    if (callback) setTimeout(callback, 300);
                };
                window.speechSynthesis.speak(msg);
            } else {
                console.log("Asistente Virtual (Texto):", texto);
                if(callback) callback();
            }
        }

        function asistenteEscucha(placeholder, callback) {
            const SpeechRec = window.SpeechRecognition || window.webkitSpeechRecognition;
            if (!SpeechRec) {
                alert("Tu navegador no soporta micrófono por voz. Puedes escribir en el cuadro.");
                return;
            }

            const reconocimiento = new SpeechRec();
            reconocimiento.lang = 'es-AR';
            reconocimiento.interimResults = false;
            reconocimiento.maxAlternatives = 1;

            const btnMic = document.getElementById('btnMicSearch');
            const inputSearch = document.getElementById('alumnoSearchInput');

            reconocimiento.onstart = function() {
                if (btnMic) btnMic.classList.add('listening');
                if (inputSearch) {
                    inputSearch.placeholder = placeholder || "Escuchando...";
                    inputSearch.value = "";
                }
            };

            reconocimiento.onresult = function(event) {
                const comando = event.results[0][0].transcript;
                if (inputSearch) inputSearch.value = comando;
                if (btnMic) btnMic.classList.remove('listening');
                if (callback) callback(comando);
            };

            reconocimiento.onerror = function(event) {
                console.error("Error Speech API:", event.error);
                if (inputSearch) inputSearch.placeholder = "No escuché. Toca el micrófono para hablar.";
                if (btnMic) btnMic.classList.remove('listening');
            };

            reconocimiento.onend = function() {
                if (btnMic) btnMic.classList.remove('listening');
            };

            try {
                reconocimiento.start();
            } catch(e) {
                console.warn("Speech recognition exception:", e);
            }
        }

        function iniciarDictadoVoz() {
            if (window.speechSynthesis) window.speechSynthesis.cancel();

            asistenteEscucha("Dicta el nombre del alumno, titular (ej: Abayay), DNI o afiliado...", (comando) => {
                const inputSearch = document.getElementById('alumnoSearchInput');
                if (inputSearch) inputSearch.value = comando;
                
                fetch(`{{ route('pwa.docente.search') }}?type=alumnos&q=${encodeURIComponent(comando)}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.length > 0) {
                            const a = data[0];
                            const padreNombre = a.titular ? a.titular.nombre : 'Titular Verificado';
                            const escNombre = a.escuela ? a.escuela.nombre : 'Institución Registrada';

                            document.getElementById('comunAlumnoInput').value = a.nombre;
                            if (a.titular) document.getElementById('comunTitularInput').value = a.titular.nombre;
                            if (a.escuela) document.getElementById('comunEscuelaInput').value = a.escuela.nombre;
                            if (document.getElementById('alumnoDni')) document.getElementById('alumnoDni').value = a.dni || '';
                            if (document.getElementById('alumnoPatologia')) document.getElementById('alumnoPatologia').value = (a.diagnostico ? a.diagnostico.nombre : 'Terapia Ocupacional');

                            datosAsistidos.alumno = a.nombre;
                            datosAsistidos.titular = padreNombre;
                            datosAsistidos.escuela = escNombre;

                            mostrarRegistroVerificado(padreNombre, a.nombre, escNombre);
                            habilitarFormularioDatos();
                        } else {
                            fetch(`{{ route('pwa.docente.search') }}?type=titulares&q=${encodeURIComponent(comando)}`)
                                .then(res => res.json())
                                .then(dataT => {
                                    if (dataT && dataT.length > 0) {
                                        const t = dataT[0];
                                        const a = (t.familiares && t.familiares.length > 0) ? t.familiares[0] : { nombre: 'Alumno Asociado', dni: '' };
                                        const escNombre = a.escuela ? a.escuela.nombre : 'Institución Registrada';

                                        document.getElementById('comunTitularInput').value = t.nombre;
                                        document.getElementById('comunAlumnoInput').value = a.nombre;
                                        if (a.escuela) document.getElementById('comunEscuelaInput').value = escNombre;
                                        if (document.getElementById('alumnoDni')) document.getElementById('alumnoDni').value = a.dni || '';

                                        datosAsistidos.titular = t.nombre;
                                        datosAsistidos.alumno = a.nombre;
                                        datosAsistidos.escuela = escNombre;

                                        mostrarRegistroVerificado(t.nombre, a.nombre, escNombre);
                                        habilitarFormularioDatos();
                                    } else {
                                        asistenteHabla(`No encontré registros para "${comando}". Intenta dictar el número de DNI o selecciona el nombre en la lista.`);
                                    }
                                });
                        }
                    });
            });
        }

        function iniciarAsistenteSecuencial() {
            asistenteHabla("Hola. Por favor presiona el botón del micrófono o dicta el nombre del alumno o su DNI.", () => {
                iniciarDictadoVoz();
            });
        }

        // 6. GESTIÓN DE ARCHIVOS Y COMPRESIÓN DE IMÁGENES CLIENT-SIDE
        function abrirCamaraUpload(tipo, inputId) {
            const input = document.getElementById(inputId);
            if(input) {
                input.setAttribute('data-tipo-doc', tipo);
                input.click();
            }
        }

        function comprimirImagen(file, maxWidth = 1600, maxHeight = 1600, quality = 0.8) {
            return new Promise((resolve) => {
                if (!file || !file.type.startsWith('image/')) {
                    resolve(file);
                    return;
                }

                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = (event) => {
                    const img = new Image();
                    img.src = event.target.result;
                    img.onload = () => {
                        let width = img.width;
                        let height = img.height;

                        if (width > maxWidth || height > maxHeight) {
                            if (width > height) {
                                height = Math.round((height * maxWidth) / width);
                                width = maxWidth;
                            } else {
                                width = Math.round((width * maxHeight) / height);
                                height = maxHeight;
                            }
                        }

                        const canvas = document.createElement('canvas');
                        canvas.width = width;
                        canvas.height = height;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);

                        canvas.toBlob((blob) => {
                            if (!blob) {
                                resolve(file);
                                return;
                            }
                            const compressedFile = new File([blob], file.name.replace(/\.[^/.]+$/, ".jpg"), {
                                type: 'image/jpeg',
                                lastModified: Date.now()
                            });
                            console.log(`Compresión completada: de ${(file.size/1024/1024).toFixed(2)}MB a ${(compressedFile.size/1024).toFixed(1)}KB`);
                            resolve(compressedFile);
                        }, 'image/jpeg', quality);
                    };
                    img.onerror = () => resolve(file);
                };
                reader.onerror = () => resolve(file);
            });
        }

        async function manejarSubidaArchivo(inputId) {
            const input = document.getElementById(inputId);
            const fileOriginal = input ? input.files[0] : null;
            if (!fileOriginal) return;

            const tipoDoc = input.getAttribute('data-tipo-doc') || 'Documento';
            const alumnoActual = datosAsistidos.alumno || 'Paciente Desconocido';
            
            let spanId = inputId === 'cameraPicker' ? 'spanScanCamera' : 'spanScanGallery';
            let btnEle = inputId === 'cameraPicker' ? document.getElementById('btnScanCamera') : document.getElementById('btnScanGallery');
            
            let originalText = document.getElementById(spanId) ? document.getElementById(spanId).innerText : 'Subir';
            if(document.getElementById(spanId)) document.getElementById(spanId).innerHTML = '<i class="fas fa-spinner fa-spin"></i> Optimizando y Subiendo...';
            if(btnEle) btnEle.style.opacity = "0.7";

            const file = await comprimirImagen(fileOriginal);

            const formData = new FormData();
            formData.append('documento', file);
            formData.append('tipo_documento', tipoDoc);
            formData.append('alumno_nombre', alumnoActual);
            
            const csrfTokenInput = document.querySelector('form#formSubidaDoc input[name="_token"]');
            if(csrfTokenInput) formData.append('_token', csrfTokenInput.value);

            try {
                const response = await fetch("{{ route('pwa.docente.upload') }}", {
                    method: 'POST',
                    body: formData,
                    headers: { 'Accept': 'application/json' }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    if(document.getElementById(spanId)) document.getElementById(spanId).innerHTML = '<i class="fas fa-check-circle"></i> ¡Subido!';
                    if(btnEle) btnEle.style.opacity = "1";
                    setTimeout(() => { if(document.getElementById(spanId)) document.getElementById(spanId).innerText = originalText; }, 1500);
                    if (typeof window.integraModalElegante === 'function') {
                        window.integraModalElegante({
                            tipo: 'success',
                            badge: 'DOCUMENTO ENVIADO A AUDITORÍA',
                            titulo: `${tipoDoc} Recibido`,
                            mensaje: result.message || `Tu documento (${tipoDoc}) se cargó perfectamente y el Auditor APOS ya fue notificado en tiempo real.`,
                            autoCerrarMs: 4200
                        });
                    }
                } else {
                    if(document.getElementById(spanId)) document.getElementById(spanId).innerText = originalText; 
                    if(btnEle) btnEle.style.opacity = "1";
                    alert("⚠️ Ocurrió un error: " + (result.message || "Fallo en servidor"));
                }
            } catch (error) {
                console.error("Error subiendo el archivo:", error);
                if(document.getElementById(spanId)) document.getElementById(spanId).innerText = originalText; 
                if(btnEle) btnEle.style.opacity = "1";
                alert("⚠️ Error de conexión al intentar subir el archivo.");
            }
            
            if(input) input.value = "";
        }

        // 7. SINCRONIZACIÓN AUTOMÁTICA EN TIEMPO REAL (SIN RECARGAR PÁGINA)
        let pwaLastSignature = null;
        let pwaLastEnCaminoCount = {{ isset($resolucionesEnCamino) ? count($resolucionesEnCamino) : 0 }};
        let pwaLastMontoCobrado = {{ (int)($montoCobrado ?? 0) }};

        setInterval(() => {
            fetch(`{{ route('api.pwa.docente.live_status') }}?docente_id={{ $docenteIdDemo }}`)
                .then(r => r.json())
                .then(data => {
                    if (!data || !data.ok) return;
                    if (pwaLastSignature === null) {
                        pwaLastSignature = data.updated_signature;
                        return;
                    }
                    if (data.updated_signature !== pwaLastSignature) {
                        pwaLastSignature = data.updated_signature;

                        const elCobrado = document.getElementById('lblMontoCobradoLive');
                        const elPretendido = document.getElementById('lblMontoPretendidoLive');
                        if (elCobrado) elCobrado.innerText = data.monto_cobrado_fmt;
                        if (elPretendido) elPretendido.innerText = data.monto_pretendido_fmt;

                        const badgeCamino = document.getElementById('badgeCountEnCamino');
                        if (badgeCamino) badgeCamino.innerText = `${data.en_camino_count} Disponible(s)`;

                        const contCamino = document.getElementById('contenedorResolucionesEnCamino');
                        if (contCamino && Array.isArray(data.en_camino)) {
                            if (data.en_camino.length > 0) {
                                contCamino.innerHTML = data.en_camino.map(item => {
                                    const safeJson = JSON.stringify(item).replace(/'/g, "&#39;");
                                    return `
                                    <div style="background:#0f172a; border:1px solid rgba(250,204,21,0.5); border-left:4px solid #facc15; border-radius:12px; padding:10px 12px; margin-bottom:8px; display:flex; justify-content:space-between; align-items:center; gap:10px;">
                                        <div style="min-width:0; flex:1;">
                                            <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
                                                <span style="background:#facc15; color:#0f172a; font-weight:800; font-size:0.7rem; padding:2px 7px; border-radius:6px;">
                                                    <i class="fas fa-file-signature me-1"></i>${item.nro_resolucion}
                                                </span>
                                                <span style="font-weight:700; color:#f8fafc; font-size:0.86rem;">${item.alumno_nombre}</span>
                                            </div>
                                            <div style="font-size:0.74rem; color:#94a3b8; margin-top:3px;">
                                                <i class="fas fa-user-shield text-info me-1"></i>Padre: <strong style="color:#e2e8f0;">${item.titular_nombre}</strong> &bull; <i class="fas fa-school text-warning ms-1 me-1"></i>${item.escuela_nombre}
                                            </div>
                                        </div>
                                        <button type="button" onclick='tomarResolucionEnCamino(${safeJson})' style="background:linear-gradient(135deg, #10b981, #059669); color:white; border:none; border-radius:10px; padding:8px 12px; font-size:0.74rem; font-weight:800; cursor:pointer; white-space:nowrap;">
                                            <i class="fas fa-hand-pointer me-1"></i> Tomar
                                        </button>
                                    </div>`;
                                }).join('');
                            } else {
                                contCamino.innerHTML = `<div style="font-size:0.78rem; color:#94a3b8; text-align:center; padding:6px;">No hay resoluciones pendientes de tomar.</div>`;
                            }
                        }

                        if (data.en_camino_count > pwaLastEnCaminoCount && data.en_camino.length > 0) {
                            const nueva = data.en_camino[0];
                            if (typeof window.integraModalElegante === 'function') {
                                window.integraModalElegante({
                                    tipo: 'info',
                                    badge: 'NUEVA RESOLUCIÓN EN CAMINO (APOS)',
                                    titulo: `Resolución ${nueva.nro_resolucion} Recibida en Vivo`,
                                    mensaje: `Administración APOS acaba de emitir la Resolución ${nueva.nro_resolucion} para el alumno ${nueva.alumno_nombre} (Padre: ${nueva.titular_nombre}). Ya apareció en tu pantalla lista para tomar.`,
                                    autoCerrarMs: 5500
                                });
                            }
                        } else if (data.monto_cobrado > pwaLastMontoCobrado) {
                            if (typeof window.integraModalElegante === 'function') {
                                window.integraModalElegante({
                                    tipo: 'success',
                                    badge: 'VISTO BUENO AUDITOR + JEFE DE ÁREA',
                                    titulo: '¡Trámite Aprobado y Acreditado en Vivo!',
                                    mensaje: `El Auditor y el Jefe de Área acaban de dar el Visto Bueno. Tu saldo aprobado subió automáticamente a $${data.monto_cobrado_fmt}.`,
                                    autoCerrarMs: 5500
                                });
                            }
                        }

                        pwaLastEnCaminoCount = data.en_camino_count;
                        pwaLastMontoCobrado = data.monto_cobrado;
                    }
                })
                .catch(() => {});
        }, 4000);
    </script>

    @include('partials.modal_elegante_integra')
    @include('partials.ai_assistant_widget')
</body>
</html>

