<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <!-- Meta tags para PWA -->
    <meta name="theme-color" content="#000000">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>PWA Terapeutas | KIKE</title>
    
    <!-- Google Fonts & Bootstrap 5 -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --brand-primary: #3b82f6; /* Color fallback */
            --bg-dark: #000000;
            --card-dark: #121212;
            --text-light: #ffffff;
            --text-muted: #a1a1aa;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-light);
            padding-bottom: 80px; /* Espacio para el menú inferior */
            -webkit-tap-highlight-color: transparent; /* Evita destellos azules en móviles */
        }

        /* Título Superior */
        .app-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #222;
            position: sticky;
            top: 0;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            z-index: 100;
        }

        /* Escáner / Botón Gigante Central */
        .camera-button-area {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .btn-camera {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--brand-primary), #1d4ed8);
            border: 8px solid #222;
            color: white;
            font-size: 4rem;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.5);
            transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            cursor: pointer;
        }
        
        .btn-camera:active {
            transform: scale(0.9);
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.5);
        }

        /* Instrucciones animadas */
        .pulse-text {
            animation: text-pulse 2s infinite;
            margin-top: 25px;
            font-weight: 500;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        @keyframes text-pulse {
            0% { opacity: 0.6; }
            50% { opacity: 1; text-shadow: 0 0 10px rgba(255,255,255,0.3); }
            100% { opacity: 0.6; }
        }

        /* Historial de Facturas */
        .history-section {
            padding: 0 20px;
        }

        .history-card {
            background-color: var(--card-dark);
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 10px;
            border: 1px solid #333;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .status-badge {
            font-size: 0.75rem;
            padding: 4px 8px;
            border-radius: 20px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-pendiente { background-color: rgba(234, 179, 8, 0.2); color: #eab308; }
        .status-aprobada { background-color: rgba(34, 197, 94, 0.2); color: #22c55e; }
        .status-rechazada { background-color: rgba(239, 68, 68, 0.2); color: #ef4444; }

        /* Menú Inferior Fijo (Bottom Navigation) */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: #111;
            display: flex;
            justify-content: space-around;
            padding: 15px 0 25px 0; /* Padding extra para iOS home indicator */
            border-top: 1px solid #222;
            z-index: 1000;
        }

        .nav-item {
            color: #666;
            text-decoration: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: 0.8rem;
            transition: color 0.2s;
        }

        .nav-item.active {
            color: var(--brand-primary);
        }

        .nav-item i {
            font-size: 1.5rem;
            margin-bottom: 5px;
        }
        
        /* Contenedor del video escáner (oculto por defecto) */
        #scanner-container {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: #000;
            z-index: 2000;
            flex-direction: column;
        }
        
        #reader { width: 100%; height: 100%; }
        
        .close-scanner {
            position: absolute;
            top: 20px; right: 20px;
            background: rgba(0,0,0,0.5);
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px; height: 40px;
            z-index: 2010;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="app-header">
        <h5 class="mb-0 fw-bold">Modo Terapias <i class="fas fa-moon text-primary fs-6 ms-1"></i></h5>
        <div class="small text-muted mt-1">{{ Auth::user()->name ?? 'Docente' }}</div>
    </div>

    <!-- Zona Central: El Botón de Acción -->
    <div class="camera-button-area">
        <!-- Input file oculto que invoca la cámara nativa del teléfono -->
        <input type="file" id="facturaInput" accept="image/*" capture="environment" style="display: none;">
        
        <!-- Botón visual -->
        <div class="btn-camera" id="btnProcesarFactura" onclick="document.getElementById('facturaInput').click();">
            <i class="fas fa-camera"></i>
        </div>
        
        <div class="pulse-text text-primary">Capturar Factura</div>
        <p class="text-center text-muted small mt-2 px-4" style="line-height: 1.4;">
            El sistema leerá el Código QR de AFIP automáticamente para su auditoría.
        </p>
    </div>

    <!-- Zona Historial Reciente -->
    <div class="history-section">
        <h6 class="text-muted fw-bold mb-3 small text-uppercase">Subidas Recientes</h6>
        
        @forelse($facturas as $factura)
            <div class="history-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-dark rounded p-2 text-muted border border-secondary">
                        <i class="fas fa-receipt fa-lg"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-6">Factura #{{ $factura->id }}</div>
                        <div class="small text-muted">{{ $factura->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
                <div class="status-badge status-{{ $factura->estado }}">
                    {{ $factura->estado }}
                </div>
            </div>
        @empty
            <div class="text-center p-4 border border-dark rounded">
                <i class="fas fa-inbox text-muted fs-1 mb-2"></i>
                <p class="text-muted small mb-0">No has subido facturas recientemente.</p>
            </div>
        @endforelse
    </div>

    <!-- Navegación Móvil (Bottom Bar) -->
    <div class="bottom-nav">
        <a href="#" class="nav-item">
            <i class="fas fa-clock"></i>
            <span>Horas</span>
        </a>
        <a href="#" class="nav-item active">
            <i class="fas fa-camera"></i>
            <span>Facturar</span>
        </a>
        <a href="#" class="nav-item">
            <i class="fas fa-user-circle"></i>
            <span>Perfil</span>
        </a>
    </div>

    <!-- Toast UI para mensajes de éxito -->
    <div class="toast-container position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 3000;">
        <div id="appToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body fw-bold" id="toastMessage">
                    Factura escaneada correctamente.
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Librería OCR para QR (html5-qrcode) via CDN -->
    <script src="https://unpkg.com/html5-qrcode"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const fileInput = document.getElementById('facturaInput');
            const btntrigger = document.getElementById('btnProcesarFactura');
            const btntriggerIcon = btntrigger.querySelector('i');
            const html5QrCode = new Html5Qrcode("reader"); // Instancia del lector (aunque acá escaneemos imagen estática)

            // Cuando el usuario saca la foto física:
            fileInput.addEventListener('change', async (e) => {
                if(e.target.files.length == 0) return;
                
                const file = e.target.files[0];
                
                // UX: Cambiar UI a estado de carga
                btntrigger.style.background = '#444';
                btntrigger.style.border = '8px solid #555';
                btntriggerIcon.className = "fas fa-spinner fa-spin";
                document.querySelector('.pulse-text').innerText = "Procesando código QR...";

                try {
                    // 1. Usar html5-qrcode para escanear la foto que tomó el usuario
                    let qrData = null;
                    try {
                         // scanFile escanea archivos estáticos sin abrir stream de video
                         qrData = await html5QrCode.scanFile(file, true); 
                    } catch(err) {
                         console.warn("No se encontró código QR en la imagen.", err);
                         // Igualmente podemos mandar la factura para auditoría visual manual
                    }

                    // 2. Enviar a nuestro backend
                    const formData = new FormData();
                    formData.append('fotoFactura', file);
                    if(qrData) formData.append('qrData', qrData);
                    formData.append('_token', '{{ csrf_token() }}'); // Ojo, inyectar CSRF

                    const response = await fetch('{{ url("/mobile/facturas") }}', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const result = await response.json();

                    if(result.success) {
                        showToast(result.message, 'success');
                        setTimeout(() => location.reload(), 2000); // Recargar para ver historial
                    } else {
                        showToast(result.message, 'danger');
                        resetButton();
                    }

                } catch (error) {
                    console.error("Error catastrófico: ", error);
                    showToast("Error de conexión al subir la factura.", "danger");
                    resetButton();
                }
            });

            function showToast(message, type) {
                const toastEl = document.getElementById('appToast');
                const toastBody = document.getElementById('toastMessage');
                toastBody.innerText = message;
                
                toastEl.className = `toast align-items-center text-bg-${type} border-0`;
                const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
                toast.show();
            }

            function resetButton() {
                btntrigger.style.background = 'linear-gradient(135deg, var(--brand-primary), #1d4ed8)';
                btntrigger.style.border = '8px solid #222';
                btntriggerIcon.className = "fas fa-camera";
                document.querySelector('.pulse-text').innerText = "Capturar Factura";
                fileInput.value = '';
            }
        });
    </script>
</body>
</html>
