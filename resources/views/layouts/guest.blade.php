<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'KIKE Ecosistema') }}</title>

    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
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
            --fondo: #f8fafc; /* Slate 50 */
            --surface: #ffffff;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--fondo);
            color: var(--primario);
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden;
        }

        /* Diseño de Pantalla Dividida */
        .auth-wrapper {
            display: flex;
            height: 100vh;
            width: 100vw;
        }

        .auth-cover {
            flex: 1;
            background: linear-gradient(135deg, rgba(15,23,42,0.9) 0%, rgba(59,130,246,0.85) 100%), url('https://images.unsplash.com/photo-1551288049-bebda4e38f71?q=80&w=2070&auto=format&fit=crop') center/cover;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            padding: 40px;
            text-align: center;
            position: relative;
        }
        
        .auth-cover::before {
            content:'';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCI+PHBhdGggZD0iTTAgMGg0MHY0MEgweiIgZmlsbD0ibm9uZSIvPjxwYXRoIGQ9Ik0wIDBMMCAwaDFsLTEgMXptMCAwTDAgMGgxbC0xIDF6IiBmaWxsPSJyZ2JhKDI1NSwyNTUsMjU1LDAuMDUpIi8+PC9zdmc+');
            opacity: 0.3;
        }

        .auth-cover h1 {
            font-weight: 700;
            font-size: 3.5rem;
            letter-spacing: -1px;
            text-shadow: 0 4px 15px rgba(0,0,0,0.3);
            z-index: 1;
        }

        .auth-cover p {
            font-size: 1.2rem;
            font-weight: 300;
            max-width: 500px;
            margin-top: 15px;
            opacity: 0.9;
            z-index: 1;
        }

        .auth-form-container {
            width: 500px;
            background: var(--surface);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 50px;
            box-shadow: -10px 0 30px rgba(0,0,0,0.05);
            z-index: 2;
        }

        @media (max-width: 991px) {
            .auth-cover { display: none; }
            .auth-form-container { width: 100%; padding: 30px; }
            body { overflow: auto; }
        }

        /* Estilos de Formularios */
        .form-control {
            border: 1px solid #e2e8f0;
            padding: 12px 15px;
            border-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--secundario);
            box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.15);
        }

        .form-label {
            font-weight: 500;
            font-size: 0.9rem;
            color: #475569;
            margin-bottom: 8px;
        }

        .btn-primary {
            background-color: var(--secundario);
            border: none;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background-color: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(59, 130, 246, 0.3);
        }
        
        .logo-mobile {
            display: none;
        }
        
        @media (max-width: 991px) {
            .logo-mobile {
                display: block;
                text-align: center;
                margin-bottom: 30px;
            }
            .logo-mobile h2 {
                font-weight: 700;
                color: var(--secundario);
            }
        }
    </style>
</head>
<body>

    <div class="auth-wrapper">
        <!-- Panel Izquierdo (Imagen/Branding) -->
        <div class="auth-cover">
            <h1>KIKE</h1>
            <p>Ecosistema Digital de Gestión en Salud y Educación Integradora. Eficiencia y Control Total en Tiempo Real.</p>
        </div>

        <!-- Panel Derecho (Formulario) -->
        <div class="auth-form-container">
            <div class="logo-mobile">
                <h2>KIKE</h2>
                <p class="text-muted small">Ecosistema de Gestión</p>
            </div>
            
            {{ $slot }}
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
