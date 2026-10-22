<!-- MOTOR DE MODALES ELEGANTES Y BLOQUEO ACTIVO DE TOPE 3 HORAS DIARIAS (APOS LA RIOJA) -->
<style>
    /* Estética Premium Global para todos los Modales Bootstrap de la Plataforma */
    .modal-backdrop.show {
        backdrop-filter: blur(7px);
        background-color: rgba(2, 6, 23, 0.75) !important;
        opacity: 1 !important;
    }

    .modal.fade .modal-dialog {
        transform: scale(0.92) translateY(15px);
        transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.25s ease;
    }

    .modal.show .modal-dialog {
        transform: scale(1) translateY(0);
    }

    .modal-content {
        background: linear-gradient(155deg, #0f172a 0%, #1e293b 100%) !important;
        color: #f8fafc !important;
        border: 1px solid rgba(56, 189, 248, 0.38) !important;
        border-radius: 22px !important;
        box-shadow: 0 28px 70px rgba(0, 0, 0, 0.82), 0 0 35px rgba(14, 165, 233, 0.16) !important;
        overflow: hidden;
    }

    .modal-header {
        background: rgba(15, 23, 42, 0.85) !important;
        border-bottom: 1px solid rgba(148, 163, 184, 0.18) !important;
        padding: 18px 24px !important;
        color: #ffffff !important;
    }

    .modal-body {
        color: #e2e8f0 !important;
    }

    .modal-body .text-dark {
        color: #f8fafc !important;
    }

    .modal-body .text-muted {
        color: #94a3b8 !important;
    }

    .modal-body .bg-light,
    .modal-body .bg-white {
        background: rgba(15, 23, 42, 0.65) !important;
        color: #f8fafc !important;
        border-color: rgba(148, 163, 184, 0.22) !important;
    }

    .modal-body .form-control,
    .modal-body .form-select {
        background-color: #0f172a !important;
        color: #ffffff !important;
        border: 1px solid #334155 !important;
        border-radius: 12px !important;
    }

    .modal-body .form-control:focus,
    .modal-body .form-select:focus {
        border-color: #38bdf8 !important;
        box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.22) !important;
    }

    .modal-footer {
        background: rgba(15, 23, 42, 0.6) !important;
        border-top: 1px solid rgba(148, 163, 184, 0.15) !important;
    }

    /* Modal Flotante Auto-Retirable (Dream Modal INTEGRA) */
    #integraDreamModalOverlay {
        position: fixed;
        inset: 0;
        z-index: 999999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(2, 6, 23, 0.72);
        backdrop-filter: blur(8px);
        opacity: 0;
        transition: opacity 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }

    #integraDreamModalOverlay.active {
        display: flex;
        opacity: 1;
    }

    .integra-dream-card {
        width: 100%;
        max-width: 470px;
        background: linear-gradient(155deg, #0f172a 0%, #1e293b 65%, #0f172a 100%);
        border: 1px solid rgba(56, 189, 248, 0.45);
        border-radius: 24px;
        box-shadow: 0 30px 80px rgba(0, 0, 0, 0.85), 0 0 40px rgba(56, 189, 248, 0.2);
        padding: 28px 26px 22px 26px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        transform: scale(0.88) translateY(20px);
        transition: transform 0.34s cubic-bezier(0.16, 1, 0.3, 1);
        text-align: center;
    }

    #integraDreamModalOverlay.active .integra-dream-card {
        transform: scale(1) translateY(0);
    }

    .integra-dream-icon-ring {
        width: 68px;
        height: 68px;
        border-radius: 50%;
        margin: 0 auto 14px auto;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.85rem;
        box-shadow: 0 0 25px rgba(245, 158, 11, 0.35);
    }

    .integra-dream-progress {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 4px;
        width: 100%;
        background: rgba(255, 255, 255, 0.08);
    }

    .integra-dream-progress-bar {
        height: 100%;
        width: 100%;
        background: linear-gradient(90deg, #38bdf8, #f59e0b, #10b981);
        transform-origin: left center;
        transition: transform linear;
    }
</style>

<div id="integraDreamModalOverlay" onclick="if(event.target === this) window.cerrarModalEleganteIntegra()">
    <div class="integra-dream-card" id="integraDreamCard">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
            <div style="display:flex; align-items:center; gap:8px;">
                <img src="{{ asset('images/apos_logo.svg') }}" alt="APOS La Rioja" style="height:26px; background:#fff; padding:2px 6px; border-radius:6px;">
                <span style="font-size:0.7rem; font-weight:700; color:#38bdf8; letter-spacing:0.8px; text-transform:uppercase;">APOS La Rioja • Control en Vivo</span>
            </div>
            <button type="button" onclick="window.cerrarModalEleganteIntegra()" style="background:rgba(255,255,255,0.08); border:none; color:#94a3b8; width:30px; height:30px; border-radius:50%; cursor:pointer; font-size:0.9rem;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="integra-dream-icon-ring" id="integraDreamIconRing" style="background: rgba(245, 158, 11, 0.16); border: 2px solid #f59e0b; color: #fbbf24;">
            <i class="fas fa-shield-alt" id="integraDreamIcon"></i>
        </div>

        <div id="integraDreamBadge" style="display:inline-block; font-size:0.68rem; font-weight:800; text-transform:uppercase; letter-spacing:1px; padding:4px 12px; border-radius:20px; background:rgba(245,158,11,0.2); color:#fbbf24; margin-bottom:8px;">
            CONTROL AUTOMÁTICO APOS
        </div>

        <h4 id="integraDreamTitle" style="font-weight:800; font-size:1.2rem; margin:0 0 8px 0; color:#ffffff; line-height:1.3;">
            Límite Operativo: Máximo 3 Horas Diarias
        </h4>

        <p id="integraDreamMessage" style="font-size:0.9rem; color:#cbd5e1; margin:0 0 20px 0; line-height:1.55;">
            No se permite cargar más de 3 horas por día para un mismo alumno.
        </p>

        <div style="display:flex; justify-content:center; gap:10px;" id="integraDreamActions">
            <button type="button" onclick="window.cerrarModalEleganteIntegra()" style="background:linear-gradient(135deg, #0284c7, #2563eb); color:#ffffff; border:none; border-radius:50px; padding:10px 28px; font-weight:700; font-size:0.88rem; cursor:pointer; box-shadow:0 8px 20px rgba(37,99,235,0.4);">
                <i class="fas fa-check me-1"></i> Entendido
            </button>
        </div>

        <div class="integra-dream-progress">
            <div class="integra-dream-progress-bar" id="integraDreamProgressBar"></div>
        </div>
    </div>
</div>

<script>
    window._integraDreamTimer = null;

    window.cerrarModalEleganteIntegra = function() {
        const overlay = document.getElementById('integraDreamModalOverlay');
        if (!overlay) return;
        overlay.style.opacity = '0';
        const card = document.getElementById('integraDreamCard');
        if (card) card.style.transform = 'scale(0.9) translateY(15px)';
        if (window._integraDreamTimer) clearTimeout(window._integraDreamTimer);
        setTimeout(() => {
            overlay.classList.remove('active');
        }, 280);
    };

    window.integraModalElegante = function(opts) {
        opts = opts || {};
        const overlay = document.getElementById('integraDreamModalOverlay');
        const card = document.getElementById('integraDreamCard');
        const ring = document.getElementById('integraDreamIconRing');
        const icon = document.getElementById('integraDreamIcon');
        const badge = document.getElementById('integraDreamBadge');
        const title = document.getElementById('integraDreamTitle');
        const msg = document.getElementById('integraDreamMessage');
        const actions = document.getElementById('integraDreamActions');
        const bar = document.getElementById('integraDreamProgressBar');

        if (!overlay) return;

        const tipo = opts.tipo || 'warning'; // 'warning', 'success', 'danger', 'info'
        const paletas = {
            warning: { bg: 'rgba(245, 158, 11, 0.16)', border: '#f59e0b', color: '#fbbf24', icon: 'fa-hourglass-half', badge: 'TOPE REGLAMENTARIO APOS' },
            danger:  { bg: 'rgba(239, 68, 68, 0.18)',  border: '#ef4444', color: '#f87171', icon: 'fa-hand-paper',     badge: 'LÍMITE MÁXIMO POR ALUMNO' },
            success: { bg: 'rgba(16, 185, 129, 0.18)', border: '#10b981', color: '#34d399', icon: 'fa-check-circle',   badge: 'SINCRONIZADO EN TIEMPO REAL' },
            info:    { bg: 'rgba(56, 189, 248, 0.18)', border: '#38bdf8', color: '#38bdf8', icon: 'fa-bell',           badge: 'NOTIFICACIÓN APOS LA RIOJA' }
        };
        const p = paletas[tipo] || paletas.warning;

        ring.style.background = p.bg;
        ring.style.borderColor = p.border;
        ring.style.color = p.color;
        ring.style.boxShadow = `0 0 28px ${p.bg}`;
        icon.className = 'fas ' + (opts.icono || p.icon);

        badge.style.background = p.bg;
        badge.style.color = p.color;
        badge.textContent = opts.badge || p.badge;

        title.textContent = opts.titulo || 'Aviso del Sistema APOS';
        msg.innerHTML = opts.mensaje || '';
        card.style.borderColor = p.border;

        let btnsHtml = '';
        if (opts.linkUrl) {
            btnsHtml += `<a href="${opts.linkUrl}" style="background:#facc15; color:#0f172a; text-decoration:none; border-radius:50px; padding:10px 22px; font-weight:800; font-size:0.85rem; box-shadow:0 6px 18px rgba(250,204,21,0.35);">
                ${opts.linkTexto || 'Ver en Auditor'} <i class="fas fa-arrow-right ms-1"></i>
            </a>`;
        }
        btnsHtml += `<button type="button" onclick="window.cerrarModalEleganteIntegra()" style="background:linear-gradient(135deg, #0284c7, #2563eb); color:#ffffff; border:none; border-radius:50px; padding:10px 26px; font-weight:700; font-size:0.85rem; cursor:pointer; box-shadow:0 8px 20px rgba(37,99,235,0.4);">
            <i class="fas fa-check me-1"></i> ${opts.botonTexto || 'Entendido'}
        </button>`;
        actions.innerHTML = btnsHtml;

        const duracion = opts.autoCerrarMs !== undefined ? opts.autoCerrarMs : 4200;

        if (bar) {
            bar.style.transition = 'none';
            bar.style.transform = 'scaleX(1)';
        }

        overlay.classList.add('active');
        requestAnimationFrame(() => {
            overlay.style.opacity = '1';
            if (card) card.style.transform = 'scale(1) translateY(0)';
            if (bar && duracion > 0) {
                requestAnimationFrame(() => {
                    bar.style.transition = `transform ${duracion}ms linear`;
                    bar.style.transform = 'scaleX(0)';
                });
            }
        });

        if (window._integraDreamTimer) clearTimeout(window._integraDreamTimer);
        if (duracion > 0) {
            window._integraDreamTimer = setTimeout(() => {
                window.cerrarModalEleganteIntegra();
            }, duracion);
        }
    };

    // Reemplazar window.alert nativo por el Modal Elegante Auto-Retirable
    window.alert = function(texto) {
        window.integraModalElegante({
            tipo: 'info',
            titulo: 'Aviso Institucional APOS',
            mensaje: String(texto).replace(/\n/g, '<br>'),
            autoCerrarMs: 4500
        });
    };

    // Validador Activo de Tope de 3 Horas Diarias por Alumno
    window.validarTopeTresHorasDiarias = function(inputEl, alumnoNombre) {
        if (!inputEl) return true;
        const val = parseFloat(inputEl.value);
        if (isNaN(val)) return true;

        if (val > 3) {
            inputEl.value = 3;
            const nom = alumnoNombre ? ` para <strong>${alumnoNombre}</strong>` : '';
            window.integraModalElegante({
                tipo: 'danger',
                badge: 'TOPE MÁXIMO: 3 HS POR DÍA',
                titulo: 'No podés cargar más de 3 horas diarias',
                mensaje: `Intentaste cargar <strong>${val} horas por día</strong>${nom}.<br>El límite máximo permitido por alumno es de <strong>3 horas diarias</strong> (60 hs mensuales). Se ajustó automáticamente a <strong>3 horas</strong>.`,
                autoCerrarMs: 4500
            });
            return false;
        }
        if (val < 1 && inputEl.value !== '') {
            inputEl.value = 1;
        }
        return true;
    };

    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('input', function(e) {
            if (e.target && e.target.classList && e.target.classList.contains('js-input-horas-diarias')) {
                const alumno = e.target.getAttribute('data-alumno') || '';
                window.validarTopeTresHorasDiarias(e.target, alumno);
            }
        });
    });
</script>
