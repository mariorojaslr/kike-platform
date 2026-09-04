<!-- WIDGET FLOTANTE ASISTENTE IA & MANUAL INTERACTIVO INTEGRA -->
<div id="integra-ai-widget-container" style="position: fixed; bottom: 25px; right: 25px; z-index: 999999; font-family: 'Poppins', sans-serif;">
    
    <!-- Botón Flotante Principal Mágico -->
    <button id="integra-ai-toggle-btn" type="button" 
            style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #6366f1 0%, #a855f7 50%, #ec4899 100%); border: none; color: #ffffff; box-shadow: 0 10px 25px rgba(168, 85, 247, 0.4); cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 26px; transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease; position: relative;"
            title="Manual Interactivo & Asistente IA">
        <i class="fa-solid fa-wand-magic-sparkles" id="integra-ai-icon-open"></i>
        <i class="fa-solid fa-xmark" id="integra-ai-icon-close" style="display: none;"></i>
        <!-- Glowing Pulse Effect -->
        <span style="position: absolute; width: 100%; height: 100%; border-radius: 50%; background: inherit; opacity: 0.5; z-index: -1; animation: integra-pulse 2s infinite;"></span>
    </button>

    <!-- Ventana de Chat e Instrucciones Interactivas -->
    <div id="integra-ai-chat-window" 
         style="display: none; position: absolute; bottom: 75px; right: 0; width: 390px; max-width: calc(100vw - 30px); height: 550px; max-height: calc(100vh - 110px); background: rgba(15, 23, 42, 0.95); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 24px; box-shadow: 0 20px 50px rgba(0,0,0,0.6); flex-direction: column; overflow: hidden; transform-origin: bottom right; animation: integra-pop-in 0.3s ease-out;">
        
        <!-- Header con Degradado -->
        <div style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.3) 0%, rgba(168, 85, 247, 0.3) 100%); padding: 16px 20px; border-bottom: 1px solid rgba(255, 255, 255, 0.1); display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 42px; height: 42px; border-radius: 12px; background: linear-gradient(135deg, #6366f1, #a855f7); display: flex; align-items: center; justify-content: center; color: white; font-size: 20px; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);">
                    <i class="fa-solid fa-robot"></i>
                </div>
                <div>
                    <h6 style="margin: 0; color: #ffffff; font-weight: 700; font-size: 15px; letter-spacing: 0.3px;">Asistente Virtual & Manual IA</h6>
                    <span style="font-size: 11px; color: #a1a1aa; display: flex; align-items: center; gap: 5px;" id="integra-ai-context-lbl">
                        <span style="width: 7px; height: 7px; border-radius: 50%; background-color: #22c55e; display: inline-block;"></span>
                        Manual Interactivo Activo
                    </span>
                </div>
            </div>
            <button id="integra-ai-minimize-btn" style="background: transparent; border: none; color: #9ca3af; font-size: 18px; cursor: pointer; padding: 4px 8px; border-radius: 8px; transition: color 0.2s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#9ca3af'">
                <i class="fa-solid fa-minus"></i>
            </button>
        </div>

        <!-- Área de Mensajes del Chat -->
        <div id="integra-ai-messages" style="flex: 1; padding: 18px; overflow-y: auto; display: flex; flex-direction: column; gap: 12px; scroll-behavior: smooth;">
            
            <!-- Mensaje de Bienvenida -->
            <div style="display: flex; gap: 10px; align-items: flex-start; max-width: 90%;">
                <div style="width: 28px; height: 28px; border-radius: 8px; background: rgba(168, 85, 247, 0.2); color: #c084fc; display: flex; align-items: center; justify-content: center; font-size: 13px; flex-shrink: 0; margin-top: 2px;">
                    <i class="fa-solid fa-sparkles"></i>
                </div>
                <div style="background: rgba(255, 255, 255, 0.08); color: #f4f4f5; padding: 12px 15px; border-radius: 16px; border-top-left-radius: 4px; font-size: 13px; line-height: 1.5; border: 1px solid rgba(255, 255, 255, 0.08);">
                    ¡Hola! 👋 Soy tu **Asistente e Instrucciones IA**. ¿Qué necesitas consultar o realizar en esta pantalla?
                </div>
            </div>

            <!-- Sugerencias Rápidas Dinámicas por Pantalla -->
            <div id="integra-ai-suggestions" style="display: flex; flex-wrap: wrap; gap: 6px; margin-top: 5px;">
                <!-- Se cargan por JavaScript según el rol / pantalla -->
            </div>

        </div>

        <!-- Indicador de Carga / Escribiendo -->
        <div id="integra-ai-typing" style="display: none; padding: 8px 18px; align-items: center; gap: 8px; color: #a1a1aa; font-size: 12px;">
            <div style="display: flex; gap: 4px;">
                <span class="integra-dot" style="width: 6px; height: 6px; background: #a855f7; border-radius: 50%; animation: integra-bounce 1.4s infinite ease-in-out both;"></span>
                <span class="integra-dot" style="width: 6px; height: 6px; background: #a855f7; border-radius: 50%; animation: integra-bounce 1.4s infinite ease-in-out both 0.2s;"></span>
                <span class="integra-dot" style="width: 6px; height: 6px; background: #a855f7; border-radius: 50%; animation: integra-bounce 1.4s infinite ease-in-out both 0.4s;"></span>
            </div>
            <span>El Asistente Virtual está consultando el manual...</span>
        </div>

        <!-- Footer / Input Form -->
        <form id="integra-ai-form" style="padding: 12px 14px; background: rgba(0, 0, 0, 0.4); border-top: 1px solid rgba(255, 255, 255, 0.08); display: flex; gap: 8px; align-items: center;">
            <input type="text" id="integra-ai-input" placeholder="Pregunta cómo usar esta pantalla..." autocomplete="off"
                   style="flex: 1; background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 14px; padding: 10px 14px; color: #ffffff; font-size: 13px; outline: none; transition: border-color 0.2s;"
                   onfocus="this.style.borderColor='#a855f7'" onblur="this.style.borderColor='rgba(255, 255, 255, 0.15)'">
            <button type="submit" id="integra-ai-send-btn" 
                    style="width: 40px; height: 40px; border-radius: 12px; background: linear-gradient(135deg, #6366f1, #a855f7); border: none; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 15px; cursor: pointer; transition: transform 0.2s;">
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
</style>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.getElementById("integra-ai-toggle-btn");
    const chatWindow = document.getElementById("integra-ai-chat-window");
    const minimizeBtn = document.getElementById("integra-ai-minimize-btn");
    const iconOpen = document.getElementById("integra-ai-icon-open");
    const iconClose = document.getElementById("integra-ai-icon-close");
    const form = document.getElementById("integra-ai-form");
    const input = document.getElementById("integra-ai-input");
    const messagesContainer = document.getElementById("integra-ai-messages");
    const typingIndicator = document.getElementById("integra-ai-typing");
    const sendBtn = document.getElementById("integra-ai-send-btn");
    const suggestionsContainer = document.getElementById("integra-ai-suggestions");

    const endpointUrl = "{{ route('ai.assistant.query') }}";
    const csrfToken = "{{ csrf_token() }}";

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
                "🚀 ¿Qué funciones tiene esta pantalla?",
                "📋 ¿Cómo ingresar un trámite?",
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
        } else {
            chatWindow.style.display = "flex";
            iconOpen.style.display = "none";
            iconClose.style.display = "block";
            toggleBtn.style.transform = "scale(0.95)";
            setTimeout(() => input.focus(), 150);
        }
    }

    toggleBtn.addEventListener("click", toggleChat);
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
