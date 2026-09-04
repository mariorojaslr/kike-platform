<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#0f172a">
        <link rel="manifest" href="{{ asset('manifest.json') }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'INTEGRA'))</title>

        <!-- Fonts & Icons -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')

        <style>
            @media print {
                /* Reset de página e impresión limpia */
                html, body {
                    background: #ffffff !important;
                    margin: 0 !important;
                    padding: 0 !important;
                    height: auto !important;
                    overflow: visible !important;
                    color: #000000 !important;
                }

                /* Ocultar interfaz del sistema y elementos no imprimibles */
                nav, header, footer, sidebar, .navbar, .sidebar, 
                .modal-backdrop, .modal-header, .modal-footer, .btn, .no-print,
                #integra-ai-widget-container, #integra-ai-chat-window,
                .btn-close, .print-options-bar {
                    display: none !important;
                }

                /* Forzar que el modal o documento activo se renderice sin sombras ni marcos oscuros */
                .modal {
                    position: static !important;
                    display: block !important;
                    overflow: visible !important;
                    background: transparent !important;
                    opacity: 1 !important;
                }

                .modal-dialog {
                    max-width: 100% !important;
                    margin: 0 !important;
                    padding: 0 !important;
                    box-shadow: none !important;
                    transform: none !important;
                }

                .modal-content {
                    border: none !important;
                    box-shadow: none !important;
                    background: transparent !important;
                    border-radius: 0 !important;
                }

                .modal-body {
                    padding: 0 !important;
                    background: #ffffff !important;
                }

                /* Forzar visualización limpia del área de comprobante */
                #bonoPrintArea, .printable-voucher {
                    display: block !important;
                    visibility: visible !important;
                    margin: 0 !important;
                    padding: 0 !important;
                    box-shadow: none !important;
                }
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @auth
                @include('layouts.navigation')
            @endauth

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                @yield('content')
                {{ $slot ?? '' }}
            </main>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        @stack('scripts')
        @include('partials.ai_assistant_widget')
    </body>
</html>
