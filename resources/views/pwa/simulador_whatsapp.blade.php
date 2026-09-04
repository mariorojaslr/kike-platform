@extends('layouts.app')

@section('title', 'Simulador de Bot Oficial WhatsApp - Mutual INTEGRA')

@section('content')
<div class="container-fluid py-4" style="background-color: #0b141a; min-height: 100vh; color: white;">
    <div class="container" style="max-width: 600px;">
        
        <!-- Header de la Interfaz Web WhatsApp -->
        <div class="d-flex align-items-center justify-content-between p-3 rounded-top-4 shadow-sm" style="background-color: #202c33; border-bottom: 1px solid rgba(255,255,255,0.08);">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-4" style="width: 45px; height: 45px; background-color: #00a884 !important;">
                    <i class="fab fa-whatsapp"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold text-white">{{ $botNombre }}</h6>
                    <small class="text-success" style="font-size: 0.7rem;"><i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i> {{ $botNumero }} &bull; En línea oficial</small>
                </div>
            </div>
            <a href="{{ url('/demo') }}" class="btn btn-sm btn-outline-light rounded-pill px-3">
                <i class="fas fa-times"></i>
            </a>
        </div>

        <!-- Cuerpo del Chat de WhatsApp -->
        <div id="whatsapp-chat-body" style="height: 480px; background-color: #0b141a; background-image: radial-gradient(#1f2c34 1px, transparent 0); background-size: 16px 16px; overflow-y: auto; padding: 20px; display: flex; flex-direction: column; gap: 12px; border-left: 1px solid #202c33; border-right: 1px solid #202c33;">
            
            <!-- Mensaje Inicial del Bot -->
            <div class="d-flex justify-content-start">
                <div class="p-3 rounded-3 text-white shadow-sm position-relative" style="background-color: #202c33; max-width: 85%; font-size: 0.85rem; border-top-left-radius: 0 !important; line-height: 1.5;">
                    ¡Hola! 👋 Te damos la bienvenida al <strong>Bot Oficial de INTEGRA Mutual</strong> (Obra Social 130.000 cápitas).<br><br>
                    ¿Qué trámite deseas realizar hoy?<br>
                    1️⃣ Enviar foto de Receta para Validar<br>
                    2️⃣ Consultar mi Credencial / Token QR<br>
                    3️⃣ Pedir un Turno Médico<br>
                    4️⃣ Hablar con un Operador
                    <div class="text-end text-muted mt-1" style="font-size: 0.65rem;">16:30</div>
                </div>
            </div>

        </div>

        <!-- Formulario de Entrada de Mensaje estilo WhatsApp -->
        <form id="whatsapp-form" class="d-flex align-items-center gap-2 p-3 rounded-bottom-4" style="background-color: #202c33;">
            <button type="button" class="btn btn-link text-secondary p-1" onclick="alert('Simulador de adjunto de foto de receta / PDF')">
                <i class="fas fa-paperclip fa-lg"></i>
            </button>
            <input type="text" id="whatsapp-input" class="form-control text-white border-0 rounded-pill px-3 py-2" style="background-color: #2a3942; font-size: 0.9rem;" placeholder="Escribe un mensaje por WhatsApp..." autocomplete="off">
            <button type="submit" class="btn btn-success rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background-color: #00a884; border: none;">
                <i class="fas fa-paper-plane text-white"></i>
            </button>
        </form>

    </div>
</div>

<script>
document.getElementById("whatsapp-form").addEventListener("submit", function(e) {
    e.preventDefault();
    const input = document.getElementById("whatsapp-input");
    const text = input.value.trim();
    if (!text) return;

    const chatBody = document.getElementById("whatsapp-chat-body");
    const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

    // Mensaje del Usuario (Derecha)
    const userBubble = `
        <div class="d-flex justify-content-end">
            <div class="p-3 rounded-3 text-white shadow-sm position-relative" style="background-color: #005c4b; max-width: 85%; font-size: 0.85rem; border-top-right-radius: 0 !important; line-height: 1.5;">
                ${escapeHtml(text)}
                <div class="text-end text-white-50 mt-1" style="font-size: 0.65rem;">${time} <i class="fas fa-check-double text-info ms-1"></i></div>
            </div>
        </div>
    `;
    chatBody.insertAdjacentHTML('beforeend', userBubble);
    input.value = "";
    chatBody.scrollTop = chatBody.scrollHeight;

    // Respuesta Inteligente del Bot (Izquierda)
    fetch("{{ route('whatsapp.simulador.procesar') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Accept": "application/json"
        },
        body: JSON.stringify({ mensaje: text })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const formattedResponse = data.response.replace(/\n/g, '<br>');
            const botBubble = `
                <div class="d-flex justify-content-start">
                    <div class="p-3 rounded-3 text-white shadow-sm position-relative" style="background-color: #202c33; max-width: 85%; font-size: 0.85rem; border-top-left-radius: 0 !important; line-height: 1.5;">
                        ${formattedResponse}
                        <div class="text-end text-muted mt-1" style="font-size: 0.65rem;">${data.hora}</div>
                    </div>
                </div>
            `;
            setTimeout(() => {
                chatBody.insertAdjacentHTML('beforeend', botBubble);
                chatBody.scrollTop = chatBody.scrollHeight;
            }, 600);
        }
    })
    .catch(err => console.error("Error al procesar mensaje en bot de WhatsApp"));
});

function escapeHtml(str) {
    const div = document.createElement("div");
    div.innerText = str;
    return div.innerHTML;
}
</script>
@endsection
