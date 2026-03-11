<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <!-- Tema Nocturno Obligatorio App Móvil -->
    <meta name="theme-color" content="#0f172a">
    <title>KIKE | Portal de Terapeutas</title>
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
            user-select: none; /* Prevenir selecciones accidenciales */
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

        /* ----- LISTADO DE ALUMNOS A CARGO (EL JUEGO) ----- */
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
        .estado.pendiente { background: rgba(245, 158, 11, 0.15); color: var(--dinero-espera); border: 2px solid var(--dinero-espera); } /* Padre ya firmó */
        .estado.aprobado { background: rgba(16, 185, 129, 0.15); color: var(--dinero-ok); border: 2px solid var(--dinero-ok); } /* Auditoría firmó -> Plata al bolsillo */

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
            padding: 12px 0 25px 0; /* Padding inferior mayor para iPhone con Home Bar */
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
            color: #3b82f6; /* Azul brillante si está activo */
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

        /* ----- BOTTOM SHEET MODAL (PSEUDO-APP) ----- */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.7);
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
            padding: 25px 20px 40px 20px;
            z-index: 2001;
            transition: bottom 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.1);
            border-top: 1px solid var(--tarjeta-borde);
            box-shadow: 0 -10px 40px rgba(0,0,0,0.5);
        }
        .bottom-sheet.active { bottom: 0; }

        .sheet-pill {
            width: 40px; height: 5px;
            background: #475569;
            border-radius: 5px;
            margin: 0 auto 20px auto;
        }

        .sheet-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .sheet-header h3 { margin: 0; font-size: 1.2rem; font-weight: 700; }
        .sheet-close { background: none; border: none; color: var(--gris-texto); font-size: 1.5rem; }

        /* Input y Micrófono */
        .input-group-voice {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .input-dark {
            flex-grow: 1;
            background: #0f172a;
            border: 1px solid var(--tarjeta-borde);
            border-radius: 12px;
            padding: 15px;
            color: white;
            font-family: inherit;
            font-size: 0.95rem;
        }
        .input-dark::placeholder { color: #64748b; }
        .input-dark:focus { outline: none; border-color: #3b82f6; }

        .btn-mic {
            background: linear-gradient(135deg, #8b5cf6, #6d28d9);
            border: none;
            border-radius: 12px;
            width: 55px;
            color: white;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(139, 92, 246, 0.4);
            cursor: pointer;
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
            gap: 15px;
            margin-bottom: 20px;
        }
        .scan-btn {
            background: #0f172a;
            border: 1px solid var(--tarjeta-borde);
            border-radius: 16px;
            padding: 20px 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            color: white;
            text-align: center;
            cursor: pointer;
        }
        .scan-btn i { font-size: 2rem; color: #3b82f6; }
        .scan-btn span { font-size: 0.8rem; font-weight: 600; color: var(--gris-texto); }
        .scan-btn:active { background: #1e293b; border-color: #3b82f6; }

        .btn-primary-dark {
            background: var(--dinero-ok);
            color: #064e3b;
            font-weight: 700;
            width: 100%;
            padding: 15px;
            border-radius: 12px;
            border: none;
            font-size: 1rem;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <!-- CABECERA -->
    <header class="header-pwa">
        <div class="docente-perfil">
            <div class="avatar">
                {{ substr(explode(' ', $docenteNombre)[1] ?? 'A', 0, 1) }}
            </div>
            <div class="saludo">
                <p>Hola, Terapeuta</p>
                <h1>{{ explode(' ', $docenteNombre)[0] ?? 'Andrea' }}</h1>
            </div>
        </div>
        <div>
            <i class="fas fa-bell text-white" style="font-size: 1.3rem; position: relative;">
                <span style="position:absolute; top:-4px; right:-2px; background:var(--dinero-espera); width:10px; height:10px; border-radius:50%; border:2px solid var(--bg-oscuro);"></span>
            </i>
        </div>
    </header>

    <div class="container">
        
        <!-- BILLETERAS GEMELAS -->
        <div class="billeteras-grid">
            <!-- Izquierda: Lo que ya es suyo, aprobado -->
            <div class="billetera-card billetera-ok">
                <div class="billetera-titulo ok-text">Cobro Listo</div>
                <div class="billetera-monto"><span class="moneda">$</span>{{ number_format($montoCobrado, 0, ',', '.') }}</div>
                <div class="billetera-subtexto"><i class="fas fa-check-double me-1"></i>Aprobado Total</div>
            </div>

            <!-- Derecha: Lo que proyecta si termina sus horas + validaciones padre/auditor en curso -->
            <div class="billetera-card billetera-pretendido">
                <div class="billetera-titulo espera-text">Proyectado</div>
                <div class="billetera-monto"><span class="moneda">$</span>{{ number_format($montoPretendido, 0, ',', '.') }}</div>
                <div class="billetera-subtexto"><i class="fas fa-hourglass-half me-1"></i>En Validación</div>
            </div>
        </div>

        <!-- EL VÚMETRO (ECUALIZADOR DE RENDIMIENTO) -->
        <div class="vumetro-container">
            <div class="vumetro-header">
                <span>Rendimiento Mensual</span>
                <span class="ok-text">75% Óptimo</span>
            </div>
            <div class="ecualizador" id="ecualizadorPuas">
                <!-- Se crearán por JS para darles el efecto exacto descrito: Puas bajas en los extremos, grandes al centro (Fuego/Rojo a la derecha) o secuencial -->
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

    <script>
        // --- SCRIPT GENERADOR DEL VÚMETRO MUSICAL (ECUALIZADOR) ---
        // Generaremos 18 barritas visuales simulando sonido, el % de progreso activará/iluminará de izquierda a derecha.
        const ecualizadorContenedor = document.getElementById('ecualizadorPuas');
        const totalBarras = 18;
        const rendimientoActivo = Math.floor(totalBarras * 0.75); // 75% activado

        // Creamos un patrón de vúmetro: Empezando bajo, cresta al centro/der
        const alturasPattern = [20, 30, 45, 60, 80, 50, 40, 65, 90, 100, 85, 75, 55, 65, 80, 95, 85, 75];

        for (let i = 0; i < totalBarras; i++) {
            const barra = document.createElement('div');
            barra.classList.add('barra');
            
            // Asignamos su altura en base al patrón vúmetro (%)
            barra.style.height = alturasPattern[i] + '%';
            
            // Asignamos color de acuerdo a su posición (Niveles del vúmetro)
            if (i < 8) {
                barra.classList.add('lvl-bajo'); // Verdes
            } else if (i < 13) {
                barra.classList.add('lvl-medio'); // Amarillas
            } else {
                barra.classList.add('lvl-alto');  // Rojas
            }

            // Encendemos (Activamos) las barras hasta donde llegó su rendimiento (75% en el demo)
            if (i < rendimientoActivo) {
                // Pequeño delay para que "suban" o se enciendan fluidamente cuando se carga la app
                setTimeout(() => {
                    barra.classList.add('activa');
                }, i * 50); // Cascada de luz
            }

            ecualizadorContenedor.appendChild(barra);
        }
    </script>

    <!-- ESTRUCTURA DEL MODAL BOTTOM SHEET (NUEVO ALUMNO) -->
    <div class="modal-overlay" id="overlayNuevo" onclick="cerrarModalNuevo()"></div>
    <div class="bottom-sheet" id="sheetNuevo">
        <div class="sheet-pill"></div>
        <div class="sheet-header">
            <h3>Nuevo Alumno / Ingreso</h3>
            <button class="sheet-close" onclick="cerrarModalNuevo()"><i class="fas fa-times-circle"></i></button>
        </div>

        <p style="font-size: 0.85rem; color: var(--gris-texto); margin-bottom: 20px;">
            Busque un alumno dictando su nombre, o escanee códigos de documentación requeridos internamente. 
        </p>

        <!-- Ingreso / Dictado de Voz -->
        <div class="input-group-voice">
            <input type="text" id="alumnoSearchInput" class="input-dark" placeholder="Ej: Nombre del alumno...">
            <button class="btn-mic" id="btnMicSearch" onclick="iniciarDictadoVoz()" title="Búsqueda por Voz">
                <i class="fas fa-microphone"></i>
            </button>
        </div>

        <!-- Escáneres Especiales con Subida de Archivos -->
        <form id="formSubidaDoc" style="display: none;">
            @csrf
            <!-- Input para Cámara Directa (Trasera) -->
            <input type="file" id="cameraPicker" name="documento" accept="image/*" capture="environment" onchange="manejarSubidaArchivo('cameraPicker')">
            <!-- Input para Galería / PDFs / Archivos Libres -->
            <input type="file" id="galleryPicker" name="documento" accept="image/*,.pdf" onchange="manejarSubidaArchivo('galleryPicker')">
        </form>

        <div class="scan-grid" style="grid-template-columns: 1fr 1fr; gap: 10px;">
            <!-- Opción 1: Cámara Directa -->
            <div class="scan-btn" id="btnScanCamera" onclick="abrirCamaraUpload('DNI / Documento Frontal', 'cameraPicker')">
                <i class="fas fa-camera text-success"></i>
                <span id="spanScanCamera" style="font-size: 0.75rem;">Tomar Foto Directa</span>
            </div>
            <!-- Opción 2: Subir de la Galería/PDF -->
            <div class="scan-btn" id="btnScanGallery" onclick="abrirCamaraUpload('Archivo Adjunto / PDF', 'galleryPicker')">
                <i class="fas fa-file-upload text-primary"></i>
                <span id="spanScanGallery" style="font-size: 0.75rem;">Subir de Galería/PDF</span>
            </div>
            
            <!-- Opción 3: Lector QR Nativo -->
            <div class="scan-btn" onclick="abrirCamaraUpload('Código QR / Factura', 'cameraPicker')" style="grid-column: span 2;">
                <i class="fas fa-qrcode text-warning"></i>
                <span style="font-size: 0.8rem;">Escanear Código QR en Vivo</span>
            </div>
        </div>

        <button class="btn-primary-dark mt-3" onclick="cerrarYCrear()">
            <i class="fas fa-arrow-right me-2" style="font-size: 0.8rem;"></i> Procesar Datos
        </button>
    </div>

    <!-- ESTRUCTURA DEL MODAL BOTTOM SHEET (MIS ALUMNOS) -->
    <div class="bottom-sheet" id="sheetMisAlumnos" style="height: 85vh; overflow-y: auto;">
        <div class="sheet-pill"></div>
        <div class="sheet-header">
            <h3>Mis Alumnos</h3>
            <button class="sheet-close" onclick="cerrarModalMisAlumnos()"><i class="fas fa-times-circle"></i></button>
        </div>

        <p style="font-size: 0.85rem; color: var(--gris-texto); margin-bottom: 20px;">
            Busque o seleccione un paciente recurrente para informar asistencia o novedades.
        </p>

        <!-- Barra de búsqueda rápida -->
        <div class="input-group-voice" style="margin-bottom: 25px;">
            <input type="text" id="misAlumnosSearchInput" class="input-dark shadow-sm" placeholder="🔍 Escribe un nombre o patología..." oninput="filtrarAlumnosActivos()">
        </div>

        <!-- Lista Integrada -->
        <div id="listaMisAlumnosContenedor">
            @foreach($alumnosDemo as $alumno)
                <div class="alumno-card item-alumno-filtrable" data-nombre="{{ strtolower($alumno->nombre) }}" data-curso="{{ strtolower($alumno->curso) }}" onclick="alert('Abriendo herramientas para: {{ $alumno->nombre }}...\nAquí el docente podrá cargar novedades, validar asistencias y actualizar reportes en tiempo real.');">
                    <div class="alumno-info">
                        <h4 style="font-size: 1rem;">{{ $alumno->nombre }}</h4>
                        <p style="font-size: 0.8rem;"><i class="fas fa-briefcase-medical me-1 text-primary"></i> {{ $alumno->curso }}</p>
                    </div>
                    
                    @if($alumno->estado == 'aprobado')
                        <div class="estado aprobado" style="width: 40px; height: 40px; font-size: 1rem;"><i class="fas fa-check"></i></div>
                    @elseif($alumno->estado == 'pendiente')
                        <div class="estado pendiente" style="width: 40px; height: 40px; font-size: 1rem;"><i class="fas fa-fingerprint"></i></div>
                    @else
                        <div class="estado sin_informar text-center" style="width: 40px; height: 40px; font-size: 1rem;"><i class="fas fa-plus"></i></div>
                    @endif
                </div>
            @endforeach
            <div id="noResultsMsg" style="display: none; text-align: center; color: var(--gris-texto); margin-top: 30px;">
                <i class="fas fa-search mb-2" style="font-size: 2rem; opacity: 0.5;"></i>
                <p>No se encontraron alumnos con ese criterio.</p>
            </div>
        </div>
    </div>

    <!-- ESTRUCTURA DEL MODAL BOTTOM SHEET (MIS REQUISITOS) -->
    <div class="bottom-sheet" id="sheetRequisitos" style="height: 85vh; overflow-y: auto;">
        <div class="sheet-pill"></div>
        <div class="sheet-header">
            <h3>Documentación Docente</h3>
            <button class="sheet-close" onclick="cerrarModalRequisitos()"><i class="fas fa-times-circle"></i></button>
        </div>

        <p style="font-size: 0.85rem; color: var(--gris-texto); margin-bottom: 20px;">
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
                <div style="text-align: center; color: var(--gris-texto); margin-top: 30px;">
                    <i class="fas fa-check-circle mb-2 text-success" style="font-size: 2rem;"></i>
                    <p>No tienes requisitos pendientes por entregar.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- SCRIPT DE LÓGICA DE VOZ, SÍNTESIS Y MODALES -->
    <script>
        const overlay = document.getElementById('overlayNuevo');
        const sheet = document.getElementById('sheetNuevo');
        const sheetMis = document.getElementById('sheetMisAlumnos');
        const sheetReq = document.getElementById('sheetRequisitos');
        const btnMic = document.getElementById('btnMicSearch');
        const inputSearch = document.getElementById('alumnoSearchInput');

        let pasoActual = 0;
        let datosAsistidos = {};

        function abrirModalNuevo() {
            sheetMis.classList.remove('active');
            overlay.classList.add('active');
            sheet.classList.add('active');
            
            // Iniciar flujo asistido secuencial
            iniciarAsistenteSecuencial();
        }

        function cerrarModalNuevo() {
            overlay.classList.remove('active');
            sheet.classList.remove('active');
            if(window.speechSynthesis) window.speechSynthesis.cancel();
            pasoActual = 0;
            
            // Reset de UI (quitar brishos)
            if(document.getElementById('btnScanCamera')) {
                document.getElementById('btnScanCamera').style.border = "1px solid var(--tarjeta-borde)";
                document.getElementById('btnScanCamera').style.boxShadow = "none";
                document.getElementById('spanScanCamera').innerText = "Tomar Foto Directa";
            }
        }

        function abrirModalMisAlumnos() {
            sheet.classList.remove('active');
            if(sheetReq) sheetReq.classList.remove('active');
            overlay.classList.add('active');
            sheetMis.classList.add('active');
            
            // Detener cualquier asistente activo si se salta de ventana
            if(window.speechSynthesis) window.speechSynthesis.cancel();
        }

        function cerrarModalMisAlumnos() {
            overlay.classList.remove('active');
            sheetMis.classList.remove('active');
        }

        function abrirModalRequisitos() {
            sheet.classList.remove('active');
            sheetMis.classList.remove('active');
            overlay.classList.add('active');
            if(sheetReq) sheetReq.classList.add('active');
            if(window.speechSynthesis) window.speechSynthesis.cancel();
        }

        function cerrarModalRequisitos() {
            overlay.classList.remove('active');
            if(sheetReq) sheetReq.classList.remove('active');
        }

        // --- CERRAR MODALES CLICKANDO EN EL OVERLAY NEGRO ---
        document.getElementById('overlayNuevo').addEventListener('click', function() {
            cerrarModalNuevo();
            cerrarModalMisAlumnos();
            cerrarModalRequisitos();
        });

        // --- FILTRO LIVE SEARCH (MIS ALUMNOS) ---
        function filtrarAlumnosActivos() {
            const term = document.getElementById('misAlumnosSearchInput').value.toLowerCase();
            const cards = document.querySelectorAll('.item-alumno-filtrable');
            let encontrados = 0;

            cards.forEach(card => {
                const nombre = card.getAttribute('data-nombre');
                const curso = card.getAttribute('data-curso');
                
                if (nombre.includes(term) || curso.includes(term)) {
                    card.style.display = 'flex';
                    encontrados++;
                } else {
                    card.style.display = 'none';
                }
            });

            document.getElementById('noResultsMsg').style.display = encontrados === 0 ? 'block' : 'none';
        }

        function cerrarYCrear() {
            alert("Simulación de Plataforma:\nProcesando carga rápida de informacion para: " + (inputSearch.value || "Documento Escaneado") + "...");
            cerrarModalNuevo();
        }

        // --- SISTEMA INTELIGENTE DE HABLA Y ESCUCHA ---

        function kikeHabla(texto, callback) {
            if ('speechSynthesis' in window) {
                // Cancelamos audios anteriores por si acaso
                window.speechSynthesis.cancel();
                let msg = new SpeechSynthesisUtterance();
                msg.text = texto;
                msg.lang = 'es-AR'; // Español latino/argentino
                msg.rate = 1.05; // Un pelito más rápido
                msg.pitch = 1.1; // Tono amigable
                
                msg.onend = function() {
                    if (callback) setTimeout(callback, 300); // 300ms de gracia al terminar de hablar
                };
                window.speechSynthesis.speak(msg);
            } else {
                console.log("Asistente KIKE (Texto):", texto);
                alert(texto);
                if(callback) callback();
            }
        }

        function kikeEscucha(placeholder, callback) {
            if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
                alert("Navegador sin soporte de micrófono. Escribe la info abajo.");
                return;
            }

            const reconClass = window.SpeechRecognition || window.webkitSpeechRecognition;
            const reconocimiento = new reconClass();

            reconocimiento.lang = 'es-AR';
            reconocimiento.interimResults = false;
            reconocimiento.maxAlternatives = 1;

            reconocimiento.onstart = function() {
                // UI Animación Pulse
                btnMic.classList.add('listening');
                inputSearch.placeholder = placeholder;
                inputSearch.value = "";
            };

            reconocimiento.onresult = function(event) {
                const comando = event.results[0][0].transcript;
                inputSearch.value = comando;
                btnMic.classList.remove('listening');
                if(callback) callback(comando);
            };

            reconocimiento.onerror = function(event) {
                console.error("Error Speech API:", event.error);
                inputSearch.placeholder = "No escuché. Presiona el micro para reintentar.";
                btnMic.classList.remove('listening');
            };

            reconocimiento.onend = function() {
                btnMic.classList.remove('listening');
            };

            try {
                reconocimiento.start();
            } catch(e) {
                console.warn(e);
            }
        }

        // Reemplazo del Dictado manual por si el usuario lo oprime manual
        function iniciarDictadoVoz() {
            kikeEscucha("Te escucho...", (comando) => {
                inputSearch.value = comando;
                if(pasoActual === 1) { datosAsistidos.escuela = comando; pasoActual=2; flujoAsistente(); return; }
                if(pasoActual === 2) { datosAsistidos.alumno = comando; pasoActual=3; flujoAsistente(); return; }
            });
        }

        function abrirCamaraUpload(tipo, inputId) {
            // Guardamos temporalmente qué estamos subiendo para el post-procesado
            document.getElementById(inputId).setAttribute('data-tipo-doc', tipo);
            // Ejecutamos el click en el input type=file oculto dinámico
            document.getElementById(inputId).click();
        }

        async function manejarSubidaArchivo(inputId) {
            const input = document.getElementById(inputId);
            const file = input.files[0];
            
            if (!file) return;

            const tipoDoc = input.getAttribute('data-tipo-doc');
            const alumnoActual = datosAsistidos.alumno || inputSearch.value || 'Paciente Desconocido';
            
            // UI Update: Mostrar cargando...
            let spanId = inputId === 'cameraPicker' ? 'spanScanCamera' : 'spanScanGallery';
            let btnEle = inputId === 'cameraPicker' ? document.getElementById('btnScanCamera') : document.getElementById('btnScanGallery');
            
            let originalText = document.getElementById(spanId).innerText;
            document.getElementById(spanId).innerHTML = '<i class="fas fa-spinner fa-spin"></i> Subiendo...';
            btnEle.style.opacity = "0.7";

            const formData = new FormData();
            formData.append('documento', file);
            formData.append('tipo_documento', tipoDoc);
            formData.append('alumno_nombre', alumnoActual);
            
            // Añadir CSRF token si es necesario
            const csrfToken = document.querySelector('form#formSubidaDoc input[name="_token"]').value;
            formData.append('_token', csrfToken);

            try {
                const response = await fetch("{{ route('pwa.docente.upload') }}", {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    console.log("Archivo subido. Ruta: ", result.path);
                    
                    document.getElementById(spanId).innerHTML = '<i class="fas fa-check-circle"></i> ¡Subido!';
                    btnEle.style.opacity = "1";
                    setTimeout(() => { document.getElementById(spanId).innerText = originalText; }, 1500);

                    // Magia: Avanzar en el flujo asistente si estamos en él
                    if (pasoActual === 3) {
                        pasoActual = 4;
                        setTimeout(flujoAsistente, 500);
                    } else if (pasoActual === 4) {
                        pasoActual = 5;
                        setTimeout(flujoAsistente, 500);
                    } else {
                        alert(`✅ ¡Tu documento (${tipoDoc}) se cargó perfectamente en la plataforma!`);
                    }

                } else {
                    document.getElementById(spanId).innerText = originalText; btnEle.style.opacity = "1";
                    alert("⚠️ Ocurrió un error: " + (result.message || "Fallo en servidor"));
                }
            } catch (error) {
                console.error("Error subiendo el archivo:", error);
                document.getElementById(spanId).innerText = originalText; btnEle.style.opacity = "1";
                alert("⚠️ Error de conexión al intentar subir el archivo.");
            }
            
            // Limpiamos el input
            input.value = "";
        }

        // MÁQUINA DE ESTADOS DEL ASISTENTE KIKE (FLUJO SECUENCIAL Y AUDIBLE)
        function iniciarAsistenteSecuencial() {
            pasoActual = 1;
            datosAsistidos = {};
            inputSearch.value = "";
            
            // Espera medio segundo a que el modal suba
            setTimeout(() => {
                flujoAsistente();
            }, 600);
        }

        function flujoAsistente() {
            if (pasoActual === 1) {
                kikeHabla("Hola. Para comenzar rápido tu carga, indícame. ¿En qué escuela estás trabajando hoy?", () => {
                    kikeEscucha("Habla el nombre de la escuela...", (respuesta) => {
                        datosAsistidos.escuela = respuesta;
                        pasoActual = 2;
                        flujoAsistente();
                    });
                });
            } 
            else if (pasoActual === 2) {
                kikeHabla(`Excelente, identificamos ${datosAsistidos.escuela}. Ahora, ¿Cuál es el nombre de tu alumno paciente?`, () => {
                    kikeEscucha("Dicta el nombre del alumno...", (respuesta) => {
                        datosAsistidos.alumno = respuesta;
                        pasoActual = 3;
                        flujoAsistente();
                    });
                });
            } 
            else if (pasoActual === 3) {
                kikeHabla(`Encontré a ${datosAsistidos.alumno}. Para confirmar tu asistencia y facturarlo a caja, toca la cámara y sácale una foto al documento de identidad.`, () => {
                    inputSearch.value = `Esperando foto DNI de ${datosAsistidos.alumno}...`;
                    
                    // Resalta el botón de cámara para indicarle donde tocar
                    document.getElementById('btnScanCamera').style.border = "2px solid #10b981";
                    document.getElementById('btnScanCamera').style.boxShadow = "0 0 15px rgba(16, 185, 129, 0.4)";
                });
            }
            else if (pasoActual === 4) {
                // El usuario ya tomó la foto del DNI
                // Restablecemos diseño del botón original
                document.getElementById('btnScanCamera').style.border = "1px solid var(--tarjeta-borde)";
                document.getElementById('btnScanCamera').style.boxShadow = "none";
                
                kikeHabla(`Fotocopia del documento recibida en el servidor. Un aviso administrativo: Tu certificado de buena conducta expiró. Por favor escanea uno nuevo para habilitar la liquidación.`, () => {
                    inputSearch.value = "Esperando Certificado de Conducta...";
                    document.getElementById('btnScanGallery').style.border = "2px solid #f59e0b";
                    document.getElementById('btnScanGallery').style.boxShadow = "0 0 15px rgba(245, 158, 11, 0.4)";
                });
            }
            else if (pasoActual === 5) {
                // Terminó todos los requerimientos
                kikeHabla("Todos los documentos están herméticos y al día. He guardado todo en el servidor con éxito.", () => {
                    document.getElementById('btnScanGallery').style.border = "1px solid var(--tarjeta-borde)";
                    document.getElementById('btnScanGallery').style.boxShadow = "none";
                    cerrarModalNuevo();
                    alert("✅ ¡Éxito! Demo Secuencial Finalizada y Documentos Subidos Realmente a la Nube.");
                });
            }
        }
    </script>
</body>
</html>
