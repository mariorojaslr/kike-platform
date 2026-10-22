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
        <script>
            // Motor Universal INTEGRA de Búsqueda en Tiempo Real con Resaltado Amarillo (<mark>)
            window.integraLimpiarResaltado = function(root) {
                if (!root) return;
                root.querySelectorAll('mark.integra-hl').forEach(function(mark) {
                    const parent = mark.parentNode;
                    if (parent) {
                        parent.replaceChild(document.createTextNode(mark.textContent), mark);
                        parent.normalize();
                    }
                });
            };

            window.integraResaltarEnElemento = function(root, rawTerm) {
                if (!root) return;
                window.integraLimpiarResaltado(root);
                if (!rawTerm) return;

                const escaped = rawTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                const regex = new RegExp('(' + escaped + ')', 'gi');
                const walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT, {
                    acceptNode: function(node) {
                        if (!node.nodeValue || !node.nodeValue.trim()) return NodeFilter.FILTER_REJECT;
                        const tag = node.parentNode ? node.parentNode.tagName : '';
                        if (['SCRIPT', 'STYLE', 'TEXTAREA', 'INPUT', 'SELECT', 'OPTION', 'MARK', 'BUTTON'].includes(tag)) {
                            return NodeFilter.FILTER_REJECT;
                        }
                        return regex.test(node.nodeValue) ? NodeFilter.FILTER_ACCEPT : NodeFilter.FILTER_REJECT;
                    }
                });

                const nodesToReplace = [];
                let currentNode;
                while ((currentNode = walker.nextNode())) {
                    nodesToReplace.push(currentNode);
                }

                nodesToReplace.forEach(function(textNode) {
                    const frag = document.createDocumentFragment();
                    const parts = textNode.nodeValue.split(regex);
                    parts.forEach(function(part) {
                        if (part.toLowerCase() === rawTerm.toLowerCase()) {
                            const mark = document.createElement('mark');
                            mark.className = 'integra-hl';
                            mark.style.cssText = 'background:#facc15; color:#0f172a; font-weight:800; padding:0 2px; border-radius:3px;';
                            mark.textContent = part;
                            frag.appendChild(mark);
                        } else if (part) {
                            frag.appendChild(document.createTextNode(part));
                        }
                    });
                    if (textNode.parentNode) {
                        textNode.parentNode.replaceChild(frag, textNode);
                    }
                });
            };

            document.addEventListener('DOMContentLoaded', function() {
                // 1. Buscador de Nómina de Docentes (Panel Auditor)
                const inputNomina = document.getElementById('buscadorNominaDocentes');
                const tablaNomina = document.getElementById('tablaNominaDocentesAuditor');
                if (inputNomina && tablaNomina) {
                    inputNomina.addEventListener('input', function() {
                        const raw = this.value.trim();
                        const q = raw.toLowerCase();
                        tablaNomina.querySelectorAll('tbody tr').forEach(function(tr) {
                            window.integraLimpiarResaltado(tr);
                            const txt = tr.innerText.toLowerCase();
                            if (!q || txt.includes(q)) {
                                tr.style.display = '';
                                if (raw) window.integraResaltarEnElemento(tr, raw);
                            } else {
                                tr.style.display = 'none';
                            }
                        });
                    });
                }

                // 2. Buscador de Bandeja de Vínculos y Resoluciones (Panel Auditor)
                const inputBandeja = document.getElementById('buscadorBandejaResoluciones');
                if (inputBandeja) {
                    inputBandeja.addEventListener('input', function() {
                        const raw = this.value.trim();
                        const q = raw.toLowerCase();
                        document.querySelectorAll('.item-vinculo-auditor').forEach(function(card) {
                            window.integraLimpiarResaltado(card);
                            const txt = card.innerText.toLowerCase();
                            if (!q || txt.includes(q)) {
                                card.style.display = '';
                                if (raw) window.integraResaltarEnElemento(card, raw);
                            } else {
                                card.style.display = 'none';
                            }
                        });
                    });
                }

                // 3. Buscador de Planilla de Liquidación e-SIDIF
                const inputLiq = document.getElementById('buscadorLiquidacionDocente');
                if (inputLiq) {
                    inputLiq.addEventListener('input', function() {
                        const raw = this.value.trim();
                        const q = raw.toLowerCase();
                        document.querySelectorAll('.fila-liquidacion-docente').forEach(function(tr) {
                            window.integraLimpiarResaltado(tr);
                            const txt = tr.innerText.toLowerCase();
                            if (!q || txt.includes(q)) {
                                tr.style.display = '';
                                if (raw) window.integraResaltarEnElemento(tr, raw);
                            } else {
                                tr.style.display = 'none';
                            }
                        });
                    });
                }
            });
        </script>
        @stack('scripts')
        @include('partials.modal_elegante_integra')
        @include('partials.ai_assistant_widget')
    </body>
</html>
