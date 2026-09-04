<!-- WIDGET FLOTANTE ASISTENTE IA & MANUAL INTERACTIVO INTEGRA -->
<div id="integra-ai-widget-container" style="position: fixed; bottom: 25px; right: 25px; z-index: 999999; font-family: 'Poppins', sans-serif;">
    
    <!-- Botón Flotante Principal Mágico (Desplazable) -->
    <button id="integra-ai-toggle-btn" type="button" 
            style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #6366f1 0%, #a855f7 50%, #ec4899 100%); border: none; color: #ffffff; box-shadow: 0 10px 25px rgba(168, 85, 247, 0.4); cursor: grab; display: flex; align-items: center; justify-content: center; font-size: 26px; transition: transform 0.2s ease, box-shadow 0.3s ease; position: relative; user-select: none; touch-action: none;"
            title="Manual Interactivo & Asistente IA (Mantén presionado y arrastra para mover)">
        <i class="fa-solid fa-wand-magic-sparkles" id="integra-ai-icon-open"></i>
        <i class="fa-solid fa-xmark" id="integra-ai-icon-close" style="display: none;"></i>
        <!-- Glowing Pulse Effect -->
        <span style="position: absolute; width: 100%; height: 100%; border-radius: 50%; background: inherit; opacity: 0.5; z-index: -1; animation: integra-pulse 2s infinite;"></span>
    </button>

    <!-- Ventana de Chat e Instrucciones Interactivas (Desplazable y Redimensionable) -->
    <div id="integra-ai-chat-window" 
         style="display: none; position: fixed; bottom: 95px; right: 25px; width: 390px; height: 550px; min-width: 290px; min-height: 320px; max-width: calc(100vw - 20px); max-height: calc(100vh - 40px); background: rgba(15, 23, 42, 0.96); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 24px; box-shadow: 0 20px 50px rgba(0,0,0,0.6); flex-direction: column; overflow: hidden; resize: both; z-index: 999999; animation: integra-pop-in 0.3s ease-out;">
        
        <!-- Header con Degradado (Handle de Arrastre) -->
        <div id="integra-ai-chat-header" style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.35) 0%, rgba(168, 85, 247, 0.35) 100%); padding: 14px 18px; border-bottom: 1px solid rgba(255, 255, 255, 0.1); display: flex; align-items: center; justify-content: space-between; cursor: move; user-select: none; touch-action: none; flex-shrink: 0;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="color: rgba(255, 255, 255, 0.4); font-size: 14px; margin-right: 2px;" title="Mantén presionado para mover la ventana">
                    <i class="fa-solid fa-grip-vertical"></i>
                </div>
                <div style="width: 38px; height: 38px; border-radius: 12px; background: linear-gradient(135deg, #6366f1, #a855f7); display: flex; align-items: center; justify-content: center; color: white; font-size: 18px; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);">
                    <i class="fa-solid fa-robot"></i>
                </div>
                <div>
                    <h6 style="margin: 0; color: #ffffff; font-weight: 700; font-size: 14px; letter-spacing: 0.3px;">Asistente Virtual & Manual IA</h6>
                    <span style="font-size: 11px; color: #a1a1aa; display: flex; align-items: center; gap: 5px;" id="integra-ai-context-lbl">
                        <span style="width: 7px; height: 7px; border-radius: 50%; background-color: #22c55e; display: inline-block;"></span>
                        Manual Interactivo Activo
                    </span>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 6px;">
                <button id="integra-ai-tts-btn" type="button" style="background: transparent; border: none; color: #a855f7; font-size: 16px; cursor: pointer; padding: 4px 8px; border-radius: 8px; transition: color 0.2s;" title="Activar/Desactivar Respuesta Hablada por Voz">
                    <i class="fa-solid fa-volume-high" id="integra-ai-tts-icon"></i>
                </button>
                <button id="integra-ai-minimize-btn" type="button" style="background: transparent; border: none; color: #9ca3af; font-size: 16px; cursor: pointer; padding: 4px 8px; border-radius: 8px; transition: color 0.2s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#9ca3af'" title="Minimizar">
                    <i class="fa-solid fa-minus"></i>
                </button>
            </div>
        </div>

        <!-- Área de Mensajes del Chat -->
        <div id="integra-ai-messages" style="flex: 1; padding: 18px; overflow-y: auto; display: flex; flex-direction: column; gap: 12px; scroll-behavior: smooth;">
            
            <!-- Mensaje de Bienvenida -->
            <div style="display: flex; gap: 10px; align-items: flex-start; max-width: 90%;">
                <div style="width: 28px; height: 28px; border-radius: 8px; background: rgba(168, 85, 247, 0.2); color: #c084fc; display: flex; align-items: center; justify-content: center; font-size: 13px; flex-shrink: 0; margin-top: 2px;">
                    <i class="fa-solid fa-sparkles"></i>
                </div>
                <div style="background: rgba(255, 255, 255, 0.08); color: #f4f4f5; padding: 12px 15px; border-radius: 16px; border-top-left-radius: 4px; font-size: 13px; line-height: 1.5; border: 1px solid rgba(255, 255, 255, 0.08);">
                    ¡Hola! 👋 Soy tu **Asistente e Instrucciones IA**. Puedes escribirme o **tocar el micrófono 🎙️ y hablarme en voz alta** para consultar lo que necesites sobre esta pantalla.
                </div>
            </div>

            <!-- Sugerencias Rápidas Dinámicas por Pantalla -->
            <div id="integra-ai-suggestions" style="display: flex; flex-wrap: wrap; gap: 6px; margin-top: 5px;">
                <!-- Se cargan por JavaScript según el rol / pantalla -->
            </div>

        </div>

        <!-- Indicador de Carga / Escribiendo -->
        <div id="integra-ai-typing" style="display: none; padding: 8px 18px; align-items: center; gap: 8px; color: #a1a1aa; font-size: 12px; flex-shrink: 0;">
            <div style="display: flex; gap: 4px;">
                <span class="integra-dot" style="width: 6px; height: 6px; background: #a855f7; border-radius: 50%; animation: integra-bounce 1.4s infinite ease-in-out both;"></span>
                <span class="integra-dot" style="width: 6px; height: 6px; background: #a855f7; border-radius: 50%; animation: integra-bounce 1.4s infinite ease-in-out both 0.2s;"></span>
                <span class="integra-dot" style="width: 6px; height: 6px; background: #a855f7; border-radius: 50%; animation: integra-bounce 1.4s infinite ease-in-out both 0.4s;"></span>
            </div>
            <span>El Asistente Virtual está consultando el manual...</span>
        </div>

        <!-- Footer / Input Form con Micrófono de Voz -->
        <form id="integra-ai-form" style="padding: 12px 14px; background: rgba(0, 0, 0, 0.4); border-top: 1px solid rgba(255, 255, 255, 0.08); display: flex; gap: 8px; align-items: center; flex-shrink: 0;">
            <button type="button" id="integra-ai-mic-btn"
                    style="width: 40px; height: 40px; border-radius: 12px; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.15); color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 16px; cursor: pointer; transition: all 0.2s;"
                    title="Toca para hablar por micrófono (Dictado por Voz)">
                <i class="fa-solid fa-microphone" id="integra-ai-mic-icon"></i>
            </button>
            <input type="text" id="integra-ai-input" placeholder="Pregunta o habla al micrófono..." autocomplete="off"
                   style="flex: 1; background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 14px; padding: 10px 14px; color: #ffffff; font-size: 13px; outline: none; transition: border-color 0.2s;"
                   onfocus="this.style.borderColor='#a855f7'" onblur="this.style.borderColor='rgba(255, 255, 255, 0.15)'">
            <button type="submit" id="integra-ai-send-btn" 
                    style="width: 40px; height: 40px; border-radius: 12px; background: linear-gradient(135deg, #6366f1, #a855f7); border: none; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 15px; cursor: pointer; transition: transform 0.2s;"
                    title="Enviar consulta">
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </form>
    </div>
</div>

<style>
    @keyframes integra-pulse {
        0% { transform: scale(1); opacity: 0.5; }
        50% { transform: scale(1.25); opacity: 0; }
        100% { transform: scale(1); opacity: 0; }
    }
    @keyframes integra-pop-in {
        from { opacity: 0; transform: scale(0.85) translateY(20px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
    @keyframes integra-bounce {
        0%, 80%, 100% { transform: scale(0); }
        40% { transform: scale(1.0); }
    }
    #integra-ai-messages::-webkit-scrollbar { width: 4px; }
    #integra-ai-messages::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.2); border-radius: 4px; }
    
    /* Indicador visual de redimensionamiento en esquina inferior derecha */
    #integra-ai-chat-window::after {
        content: '';
        position: absolute;
        bottom: 3px;
        right: 3px;
        width: 10px;
        height: 10px;
        border-right: 2px solid rgba(255, 255, 255, 0.4);
        border-bottom: 2px solid rgba(255, 255, 255, 0.4);
        pointer-events: none;
        border-bottom-right-radius: 4px;
    }
</style>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const container = document.getElementById("integra-ai-widget-container");
    const toggleBtn = document.getElementById("integra-ai-toggle-btn");
    const chatWindow = document.getElementById("integra-ai-chat-window");
    const chatHeader = document.getElementById("integra-ai-chat-header");
    const minimizeBtn = document.getElementById("integra-ai-minimize-btn");
    const iconOpen = document.getElementById("integra-ai-icon-open");
    const iconClose = document.getElementById("integra-ai-icon-close");
    const form = document.getElementById("integra-ai-form");
    const input = document.getElementById("integra-ai-input");
    const messagesContainer = document.getElementById("integra-ai-messages");
    const typingIndicator = document.getElementById("integra-ai-typing");
    const sendBtn = document.getElementById("integra-ai-send-btn");
    const suggestionsContainer = document.getElementById("integra-ai-suggestions");
    const micBtn = document.getElementById("integra-ai-mic-btn");
    const micIcon = document.getElementById("integra-ai-mic-icon");
    const ttsBtn = document.getElementById("integra-ai-tts-btn");
    const ttsIcon = document.getElementById("integra-ai-tts-icon");

    const endpointUrl = "{{ route('ai.assistant.query') }}";
    const csrfToken = "{{ csrf_token() }}";

    let ttsEnabled = true;
    let recognition = null;
    let isListening = false;

    // --- RECONOCIMIENTO DE VOZ (MICROFONO / DICTADO) ---
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

    if (SpeechRecognition) {
        recognition = new SpeechRecognition();
        recognition.lang = 'es-AR';
        recognition.continuous = false;
        recognition.interimResults = true;

        recognition.onstart = function() {
            isListening = true;
            micBtn.style.background = "#ef4444";
            micBtn.style.boxShadow = "0 0 12px rgba(239, 68, 68, 0.6)";
            micIcon.className = "fa-solid fa-microphone-lines fa-bounce";
            input.placeholder = "Escuchando tu voz... habla ahora...";
        };

        recognition.onresult = function(event) {
            let transcript = "";
            for (let i = event.resultIndex; i < event.results.length; i++) {
                transcript += event.results[i][0].transcript;
            }
            input.value = transcript;
        };

        recognition.onerror = function(event) {
            console.warn("Error micrófono:", event.error);
            stopListening();
            if (event.error === 'not-allowed') {
                alert("⚠️ Permiso de micrófono denegado en el navegador.");
            }
        };

        recognition.onend = function() {
            stopListening();
            if (input.value.trim().length > 2) {
                form.dispatchEvent(new Event("submit"));
            }
        };

        function stopListening() {
            isListening = false;
            micBtn.style.background = "rgba(255, 255, 255, 0.1)";
            micBtn.style.boxShadow = "none";
            micIcon.className = "fa-solid fa-microphone";
            input.placeholder = "Pregunta o habla al micrófono...";
        }

        micBtn.addEventListener("click", function() {
            if (isListening) {
                recognition.stop();
            } else {
                try {
                    recognition.start();
                } catch (err) {
                    console.error("Error al iniciar mic:", err);
                }
            }
        });
    } else {
        micBtn.addEventListener("click", function() {
            alert("⚠️ Tu navegador no admite entrada directa de voz (Web Speech API). Utiliza Chrome, Edge o Safari.");
        });
    }

    // --- SÍNTESIS DE VOZ (RESPUESTA HABLADA) ---
    if (ttsBtn) {
        ttsBtn.addEventListener("click", function() {
            ttsEnabled = !ttsEnabled;
            if (ttsEnabled) {
                ttsBtn.style.color = "#a855f7";
                ttsIcon.className = "fa-solid fa-volume-high";
            } else {
                ttsBtn.style.color = "#6b7280";
                ttsIcon.className = "fa-solid fa-volume-xmark";
                if ('speechSynthesis' in window) window.speechSynthesis.cancel();
            }
        });
    }

    function speakText(text) {
        if (!ttsEnabled || !('speechSynthesis' in window)) return;
        window.speechSynthesis.cancel();
        
        let cleanText = text.replace(/[*#_`]/g, '').replace(/https?:\/\/\S+/g, '');
        const utterance = new SpeechSynthesisUtterance(cleanText);
        utterance.lang = 'es-AR';
        utterance.rate = 1.0;
        utterance.pitch = 1.0;

        window.speechSynthesis.speak(utterance);
    }

    // --- ARRASTRE DEL BOTÓN FLOTANTE (MOUSE Y TOUCH) ---
    (function initButtonDrag() {
        let isDragging = false;
        let hasDragged = false;
        let startX, startY, origLeft, origTop;

        function onPointerDown(e) {
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;
            
            isDragging = true;
            hasDragged = false;
            startX = clientX;
            startY = clientY;

            const rect = container.getBoundingClientRect();
            origLeft = rect.left;
            origTop = rect.top;

            toggleBtn.style.cursor = "grabbing";

            document.addEventListener("mousemove", onPointerMove);
            document.addEventListener("mouseup", onPointerUp);
            document.addEventListener("touchmove", onPointerMove, { passive: false });
            document.addEventListener("touchend", onPointerUp);
        }

        function onPointerMove(e) {
            if (!isDragging) return;

            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;

            const deltaX = clientX - startX;
            const deltaY = clientY - startY;

            if (Math.abs(deltaX) > 4 || Math.abs(deltaY) > 4) {
                hasDragged = true;
            }

            if (hasDragged) {
                if (e.cancelable) e.preventDefault();

                let newLeft = origLeft + deltaX;
                let newTop = origTop + deltaY;

                const maxLeft = window.innerWidth - container.offsetWidth - 10;
                const maxTop = window.innerHeight - container.offsetHeight - 10;
                newLeft = Math.max(10, Math.min(newLeft, maxLeft));
                newTop = Math.max(10, Math.min(newTop, maxTop));

                container.style.bottom = "auto";
                container.style.right = "auto";
                container.style.left = newLeft + "px";
                container.style.top = newTop + "px";
            }
        }

        function onPointerUp() {
            if (!isDragging) return;
            isDragging = false;
            toggleBtn.style.cursor = "grab";

            document.removeEventListener("mousemove", onPointerMove);
            document.removeEventListener("mouseup", onPointerUp);
            document.removeEventListener("touchmove", onPointerMove);
            document.removeEventListener("touchend", onPointerUp);
        }

        toggleBtn.addEventListener("mousedown", onPointerDown);
        toggleBtn.addEventListener("touchstart", onPointerDown, { passive: false });

        toggleBtn.addEventListener("click", function(e) {
            if (hasDragged) {
                e.stopImmediatePropagation();
                e.preventDefault();
                hasDragged = false;
            } else {
                toggleChat();
            }
        });
    })();

    // --- ARRASTRE DE LA VENTANA DE CHAT (MOUSE Y TOUCH) ---
    (function initWindowDrag() {
        let isDragging = false;
        let startX, startY, origLeft, origTop;

        function onPointerDown(e) {
            if (e.target.closest("button")) return; // Ignorar si toca botones en el header

            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;

            isDragging = true;
            startX = clientX;
            startY = clientY;

            const rect = chatWindow.getBoundingClientRect();
            origLeft = rect.left;
            origTop = rect.top;

            chatHeader.style.cursor = "grabbing";

            document.addEventListener("mousemove", onPointerMove);
            document.addEventListener("mouseup", onPointerUp);
            document.addEventListener("touchmove", onPointerMove, { passive: false });
            document.addEventListener("touchend", onPointerUp);
        }

        function onPointerMove(e) {
            if (!isDragging) return;
            if (e.cancelable) e.preventDefault();

            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;

            const deltaX = clientX - startX;
            const deltaY = clientY - startY;

            let newLeft = origLeft + deltaX;
            let newTop = origTop + deltaY;

            const maxLeft = window.innerWidth - chatWindow.offsetWidth - 10;
            const maxTop = window.innerHeight - chatWindow.offsetHeight - 10;
            newLeft = Math.max(10, Math.min(newLeft, maxLeft));
            newTop = Math.max(10, Math.min(newTop, maxTop));

            chatWindow.style.bottom = "auto";
            chatWindow.style.right = "auto";
            chatWindow.style.left = newLeft + "px";
            chatWindow.style.top = newTop + "px";
            chatWindow.dataset.userMoved = "true";
        }

        function onPointerUp() {
            if (!isDragging) return;
            isDragging = false;
            chatHeader.style.cursor = "move";

            document.removeEventListener("mousemove", onPointerMove);
            document.removeEventListener("mouseup", onPointerUp);
            document.removeEventListener("touchmove", onPointerMove);
            document.removeEventListener("touchend", onPointerUp);
        }

        chatHeader.addEventListener("mousedown", onPointerDown);
        chatHeader.addEventListener("touchstart", onPointerDown, { passive: false });
    })();

    // Cargar sugerencias dinámicas según la ruta actual
    function cargarSugerenciasPantalla() {
        const path = window.location.pathname;
        let sugerencias = [];

        if (path.includes('afiliado')) {
            sugerencias = [
                "💳 ¿Cómo ver mi Credencial Digital QR?",
                "🩺 ¿Cómo reservar un Turno o Cartilla?",
                "🔑 ¿Cómo funciona el Token de Seguridad?"
            ];
        } else if (path.includes('docente')) {
            sugerencias = [
                "🎙️ ¿Cómo usar el Modo Parlante / Voz?",
                "📄 ¿Cómo subir la Factura ARCA?",
                "🏫 ¿Cómo enviar la firma a la Directora?"
            ];
        } else if (path.includes('directora')) {
            sugerencias = [
                "✍️ ¿Cómo certificar las asistencias?",
                "⏱️ ¿Cuál es el límite de 3hs/día?",
                "📲 ¿Cómo pedir firma por WhatsApp?"
            ];
        } else if (path.includes('padre')) {
            sugerencias = [
                "💵 ¿Cómo solicitar un Reintegro?",
                "📄 ¿Cómo subir la Resolución OSP?",
                "✍️ ¿Cómo dar la conformidad diaria?"
            ];
        } else if (path.includes('farmacia')) {
            sugerencias = [
                "💊 ¿Cómo validar una receta con QR?",
                "📊 ¿Cómo funciona el Vademécum (40%, 70%, 100%)?",
                "🖨️ ¿Cómo emitir el Bono de Dispensa?"
            ];
        } else if (path.includes('prestadores')) {
            sugerencias = [
                "🖨️ ¿Cómo ver e imprimir el Bono Digital?",
                "🏥 ¿Cómo pedir una internación?",
                "📊 ¿Dónde veo la liquidación del mes?"
            ];
        } else if (path.includes('owner') || path.includes('dashboard')) {
            sugerencias = [
                "🗺️ ¿Cómo filtrar por Sucursal (Chilecito, Córdoba, etc.)?",
                "🔬 ¿Dónde ver el costo por Patología?",
                "🛡️ ¿Cuánto ahorró la auditoría con IA?"
            ];
        } else {
            sugerencias = [
                "📋 ¿Cómo inicio un expediente?",
                "🚀 ¿Qué funciones tiene esta pantalla?",
                "❓ Guía de uso rápido"
            ];
        }

        if (suggestionsContainer) {
            suggestionsContainer.innerHTML = sugerencias.map(s => `
                <button type="button" onclick="integraAiUsarSugerencia('${s.replace(/'/g, "\\'")}')" 
                        style="background: rgba(99, 102, 241, 0.15); border: 1px solid rgba(99, 102, 241, 0.3); color: #a5b4fc; border-radius: 20px; padding: 6px 12px; font-size: 11px; cursor: pointer; transition: all 0.2s;"
                        onmouseover="this.style.background='rgba(99, 102, 241, 0.3)'" onmouseout="this.style.background='rgba(99, 102, 241, 0.15)'">
                    ${s}
                </button>
            `).join('');
        }
    }

    cargarSugerenciasPantalla();

    function toggleChat() {
        const isOpen = chatWindow.style.display === "flex";
        if (isOpen) {
            chatWindow.style.display = "none";
            iconOpen.style.display = "block";
            iconClose.style.display = "none";
            toggleBtn.style.transform = "scale(1)";
            if ('speechSynthesis' in window) window.speechSynthesis.cancel();
        } else {
            chatWindow.style.display = "flex";
            iconOpen.style.display = "none";
            iconClose.style.display = "block";
            toggleBtn.style.transform = "scale(0.95)";

            // Posicionar ventana inteligentemente cerca del botón si no se ha movido manualmente
            if (!chatWindow.dataset.userMoved) {
                const containerRect = container.getBoundingClientRect();
                const windowWidth = Math.min(390, window.innerWidth - 30);
                let winLeft = containerRect.right - windowWidth;
                let winTop = containerRect.top - 565;

                if (window.innerWidth < 450) {
                    winLeft = 15;
                    winTop = 20;
                } else {
                    if (winLeft < 15) winLeft = 15;
                    if (winTop < 15) winTop = 15;
                }

                chatWindow.style.left = winLeft + "px";
                chatWindow.style.top = winTop + "px";
                chatWindow.style.bottom = "auto";
                chatWindow.style.right = "auto";
            }

            setTimeout(() => input.focus(), 150);
        }
    }

    minimizeBtn.addEventListener("click", toggleChat);

    window.integraAiUsarSugerencia = function(texto) {
        input.value = texto;
        form.dispatchEvent(new Event("submit"));
    };

    function appendUserMessage(text) {
        const msgDiv = document.createElement("div");
        msgDiv.style.cssText = "display: flex; justify-content: flex-end; width: 100%;";
        msgDiv.innerHTML = `
            <div style="background: linear-gradient(135deg, #6366f1, #a855f7); color: #ffffff; padding: 10px 14px; border-radius: 16px; border-top-right-radius: 4px; font-size: 13px; line-height: 1.4; max-width: 85%; word-break: break-word; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);">
                ${escapeHtml(text)}
            </div>
        `;
        messagesContainer.appendChild(msgDiv);
        scrollToBottom();
    }

    function appendBotMessage(text, isError = false) {
        const msgDiv = document.createElement("div");
        msgDiv.style.cssText = "display: flex; gap: 10px; align-items: flex-start; max-width: 90%;";
        
        const iconColor = isError ? "#ef4444" : "#c084fc";
        const iconBg = isError ? "rgba(239, 68, 68, 0.2)" : "rgba(168, 85, 247, 0.2)";
        const iconClass = isError ? "fa-circle-exclamation" : "fa-robot";

        msgDiv.innerHTML = `
            <div style="width: 28px; height: 28px; border-radius: 8px; background: ${iconBg}; color: ${iconColor}; display: flex; align-items: center; justify-content: center; font-size: 13px; flex-shrink: 0; margin-top: 2px;">
                <i class="fa-solid ${iconClass}"></i>
            </div>
            <div style="background: rgba(255, 255, 255, 0.08); color: #f4f4f5; padding: 12px 15px; border-radius: 16px; border-top-left-radius: 4px; font-size: 13px; line-height: 1.5; border: 1px solid rgba(255, 255, 255, 0.08); word-break: break-word;">
                ${formatMarkdown(text)}
            </div>
        `;
        messagesContainer.appendChild(msgDiv);
        scrollToBottom();

        // Hablar la respuesta si está activada la lectura en voz alta
        if (!isError) {
            speakText(text);
        }
    }

    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    function escapeHtml(text) {
        const div = document.createElement("div");
        div.innerText = text;
        return div.innerHTML;
    }

    function formatMarkdown(text) {
        let formatted = escapeHtml(text);
        formatted = formatted.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        formatted = formatted.replace(/\*(.*?)\*/g, '<em>$1</em>');
        formatted = formatted.replace(/\n/g, '<br>');
        return formatted;
    }

    form.addEventListener("submit", function (e) {
        e.preventDefault();
        const text = input.value.trim();
        if (!text) return;

        appendUserMessage(text);
        input.value = "";
        
        input.disabled = true;
        sendBtn.disabled = true;
        typingIndicator.style.display = "flex";
        scrollToBottom();

        const contextPage = document.title || window.location.pathname;

        fetch(endpointUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                "Accept": "application/json"
            },
            body: JSON.stringify({
                prompt: text,
                contexto: contextPage,
                path: window.location.pathname
            })
        })
        .then(res => res.json())
        .then(data => {
            typingIndicator.style.display = "none";
            input.disabled = false;
            sendBtn.disabled = false;
            input.focus();

            if (data.success) {
                appendBotMessage(data.response);
            } else {
                appendBotMessage(data.response || "No se pudo procesar la solicitud.", true);
            }
        })
        .catch(err => {
            typingIndicator.style.display = "none";
            input.disabled = false;
            sendBtn.disabled = false;
            input.focus();
            appendBotMessage("Ocurrió un error de conexión con el Asistente Virtual.", true);
        });
    });
});
</script>

