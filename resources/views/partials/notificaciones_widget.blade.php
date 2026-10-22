<!-- WIDGET NOTIFICACIONES EN TIEMPO REAL (AUDITOR & ADMIN APOS) -->
<div class="dropdown me-2" id="integra-notif-container" style="position: relative;">
    <button class="btn btn-link position-relative p-2 rounded-circle border" type="button" id="dropdownNotifBtn" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 1.15rem; text-decoration: none; color: var(--text-main, #0f172a); width: 42px; height: 42px; display: inline-flex; align-items: center; justify-content: center;">
        <i class="fa-solid fa-bell text-warning"></i>
        <span id="notifBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none; font-size: 0.68rem;">
            0
        </span>
    </button>
    <ul class="dropdown-menu dropdown-menu-end p-0 shadow-lg border-0" aria-labelledby="dropdownNotifBtn" style="width: 370px; max-width: 92vw; background: #0f172a; border-radius: 16px; overflow: hidden; z-index: 99999; border: 1px solid rgba(56,189,248,0.3) !important;">
        <li class="p-3 border-bottom border-secondary border-opacity-25 d-flex justify-content-between align-items-center" style="background: #1e293b;">
            <h6 class="m-0 text-white fw-bold" style="font-size: 0.9rem;">
                <i class="fa-solid fa-bell me-2 text-warning"></i> Avisos en Vivo (Auditoría APOS)
            </h6>
            <button class="btn btn-sm btn-outline-light rounded-pill px-2 py-0" onclick="integraMarcarTodasLeidas()" style="font-size: 11px;">
                Marcar leídas
            </button>
        </li>
        <div id="notifListContainer" style="max-height: 360px; overflow-y: auto;">
            <div class="text-center p-3 text-white-50 small">Cargando avisos...</div>
        </div>
        <li class="p-2 text-center border-top border-secondary border-opacity-25" style="background: #1e293b;">
            <a href="{{ route('auditor.docentes.legajos') }}" class="text-info small fw-bold text-decoration-none">
                <i class="fas fa-bolt me-1"></i> Ir a Bandeja Inmediata del Auditor
            </a>
        </li>
    </ul>
</div>

<!-- Toast Flotante de Alerta Instantánea cuando entra una Nueva Vinculación / Resolución -->
<div id="integraLiveToastAlert" style="display:none; position:fixed; top:20px; right:20px; z-index:999999; width:380px; max-width:92vw; background:linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color:#fff; border-left:5px solid #f59e0b; border-radius:14px; box-shadow:0 15px 35px rgba(0,0,0,0.45); padding:16px;">
    <div class="d-flex justify-content-between align-items-start mb-1">
        <span class="badge bg-warning text-dark fw-bold"><i class="fas fa-bell me-1"></i> NUEVA NOVEDAD PARA AUDITORÍA</span>
        <button type="button" class="btn-close btn-close-white btn-sm" onclick="document.getElementById('integraLiveToastAlert').style.display='none'"></button>
    </div>
    <h6 class="fw-bold text-info mb-1 mt-2" id="integraLiveToastTitle" style="font-size:0.92rem;"></h6>
    <p class="small text-light mb-2" id="integraLiveToastMsg" style="line-height:1.4;"></p>
    <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('auditor.docentes.legajos') }}" class="btn btn-sm btn-warning text-dark fw-bold rounded-pill px-3">
            <i class="fas fa-eye me-1"></i> Ver Ahora en Bandeja del Auditor
        </a>
    </div>
</div>

<script>
let integraUltimoIdNotif = null;

document.addEventListener("DOMContentLoaded", function () {
    integraCargarNotificaciones(true);
    // Polling en tiempo real cada 6 segundos para alertar de inmediato al Auditor
    setInterval(() => integraCargarNotificaciones(false), 6000);
});

function integraSonarCampana() {
    try {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.type = "sine";
        osc.frequency.setValueAtTime(880, ctx.currentTime);
        osc.frequency.setValueAtTime(1174.66, ctx.currentTime + 0.12);
        gain.gain.setValueAtTime(0.15, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.45);
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.start();
        osc.stop(ctx.currentTime + 0.45);
    } catch (e) {}
}

function integraCargarNotificaciones(esCargaInicial) {
    fetch("{{ route('notificaciones.get') }}")
        .then(res => res.json())
        .then(data => {
            const badge = document.getElementById("notifBadge");
            const container = document.getElementById("notifListContainer");

            if (data.unread_count > 0) {
                badge.innerText = data.unread_count;
                badge.style.display = "inline-block";
            } else {
                badge.style.display = "none";
            }

            if (!data.items || data.items.length === 0) {
                container.innerHTML = `<div class="text-center p-4 text-white-50 small"><i class="fa-solid fa-check-circle me-1 text-success"></i> No tenés notificaciones pendientes</div>`;
                return;
            }

            const topItem = data.items[0];
            if (!esCargaInicial && topItem && integraUltimoIdNotif !== null && topItem.id > integraUltimoIdNotif && !topItem.leido) {
                integraSonarCampana();
                const toastEl = document.getElementById("integraLiveToastAlert");
                if (toastEl) {
                    document.getElementById("integraLiveToastTitle").innerText = topItem.titulo || "Nueva Vinculación Docente-Alumno";
                    document.getElementById("integraLiveToastMsg").innerText = topItem.mensaje || "";
                    toastEl.style.display = "block";
                }
            }
            if (topItem) {
                integraUltimoIdNotif = topItem.id;
            }

            let html = "";
            data.items.forEach(item => {
                const bgStyle = item.leido ? "opacity: 0.65;" : "background: rgba(56, 189, 248, 0.12); border-left: 3px solid #f59e0b;";
                const linkAttr = item.link ? `href="${item.link}"` : `href="{{ route('auditor.docentes.legajos') }}"`;

                html += `
                    <a ${linkAttr} onclick="integraMarcarLeida(${item.id})" class="dropdown-item text-wrap p-3 border-bottom border-secondary border-opacity-25 d-block" style="${bgStyle} color: #f8fafc; transition: background 0.2s;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small fw-bold text-warning">${escapeHtmlNotif(item.titulo)}</span>
                            <span class="small text-white-50" style="font-size: 10px;">${new Date(item.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>
                        </div>
                        <div class="small text-light" style="font-size: 12px; line-height: 1.4;">${escapeHtmlNotif(item.mensaje)}</div>
                    </a>
                `;
            });

            container.innerHTML = html;
        })
        .catch(() => {});
}

function integraMarcarLeida(id) {
    fetch(`/api/notificaciones/${id}/leer`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Content-Type": "application/json"
        }
    }).then(() => integraCargarNotificaciones(true));
}

function integraMarcarTodasLeidas() {
    fetch("{{ route('notificaciones.leer_todas') }}", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Content-Type": "application/json"
        }
    }).then(() => integraCargarNotificaciones(true));
}

function escapeHtmlNotif(text) {
    if (!text) return "";
    const div = document.createElement("div");
    div.innerText = text;
    return div.innerHTML;
}
</script>
