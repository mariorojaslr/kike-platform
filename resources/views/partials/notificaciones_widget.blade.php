<!-- WIDGET NOTIFICACIONES NATIVAS -->
<div class="dropdown me-3" id="integra-notif-container" style="position: relative;">
    <button class="btn btn-link text-white position-relative p-2" type="button" id="dropdownNotifBtn" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 1.2rem; text-decoration: none;">
        <i class="fa-solid fa-bell"></i>
        <span id="notifBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none; font-size: 0.65rem;">
            0
        </span>
    </button>
    <ul class="dropdown-menu dropdown-menu-end p-0 shadow-lg border-0" aria-labelledby="dropdownNotifBtn" style="width: 340px; max-width: 90vw; background: rgba(17, 24, 39, 0.95); backdrop-filter: blur(12px); border-radius: 16px; overflow: hidden; z-index: 99999;">
        <li class="p-3 border-bottom border-secondary d-flex justify-content-between align-items-center">
            <h6 class="m-0 text-white fw-bold"><i class="fa-solid fa-bell me-2 text-warning"></i> Notificaciones</h6>
            <button class="btn btn-sm text-muted p-0" onclick="integraMarcarTodasLeidas()" style="font-size: 11px;" title="Marcar todas como leídas">
                Marcar leídas
            </button>
        </li>
        <div id="notifListContainer" style="max-height: 350px; overflow-y: auto;">
            <div class="text-center p-3 text-muted small">Cargando avisos...</div>
        </div>
    </ul>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    integraCargarNotificaciones();
    // Re-check cada 60 segundos
    setInterval(integraCargarNotificaciones, 60000);
});

function integraCargarNotificaciones() {
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
                container.innerHTML = `<div class="text-center p-4 text-muted small"><i class="fa-solid fa-check-circle me-1"></i> No tienes notificaciones pendientes</div>`;
                return;
            }

            let html = "";
            data.items.forEach(item => {
                const bgStyle = item.leido ? "opacity: 0.6;" : "background: rgba(255, 255, 255, 0.05); font-weight: 500;";
                const linkAttr = item.link ? `href="${item.link}"` : `href="#"`;
                
                html += `
                    <a ${linkAttr} onclick="integraMarcarLeida(${item.id})" class="dropdown-item text-wrap p-3 border-bottom border-secondary d-block" style="${bgStyle} color: #e2e8f0; transition: background 0.2s;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small fw-bold text-info">${escapeHtmlNotif(item.titulo)}</span>
                            <span class="small text-muted" style="font-size: 10px;">${new Date(item.created_at).toLocaleDateString()}</span>
                        </div>
                        <div class="small text-light" style="font-size: 12px; line-height: 1.4;">${escapeHtmlNotif(item.mensaje)}</div>
                    </a>
                `;
            });

            container.innerHTML = html;
        })
        .catch(err => console.error("Error al cargar notificaciones", err));
}

function integraMarcarLeida(id) {
    fetch(`/api/notificaciones/${id}/leer`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Content-Type": "application/json"
        }
    }).then(() => integraCargarNotificaciones());
}

function integraMarcarTodasLeidas() {
    fetch("{{ route('notificaciones.leer_todas') }}", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Content-Type": "application/json"
        }
    }).then(() => integraCargarNotificaciones());
}

function escapeHtmlNotif(text) {
    if (!text) return "";
    const div = document.createElement("div");
    div.innerText = text;
    return div.innerHTML;
}
</script>
