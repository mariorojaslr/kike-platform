@extends('layouts.app')

@section('title', 'APOS La Rioja | Portal del Padre / Titular - Resoluciones y Reintegros')

@section('content')
<div class="container-fluid py-4" style="background-color: #0f172a; min-height: 100vh; color: white;">
    <div class="container" style="max-width: 780px;">
        
        <!-- Header Institucional del Padre con Logo Oficial APOS -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-white p-2 rounded-3 shadow" style="border: 2px solid #38bdf8;">
                    <img src="{{ asset('images/apos_logo.svg') }}" alt="APOS La Rioja" style="height: 46px; width: auto; display: block;">
                </div>
                <div>
                    <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold text-uppercase" style="font-size: 0.72rem;">
                        <i class="fas fa-hand-holding-usd me-1"></i> Portal del Afiliado Titular • APOS La Rioja
                    </span>
                    <h4 class="fw-bold mt-2 mb-0 text-white">
                        <i class="fas fa-user-shield text-info me-2"></i> {{ $padreNombre }}
                    </h4>
                    <small style="color: #cbd5e1;"><i class="fas fa-child me-1 text-warning"></i> Hijo/a Vinculado/a: <strong class="text-white">{{ $hijoNombre }}</strong></small>
                </div>
            </div>
            <div>
                <a href="{{ route('auditor.docentes.index') }}" class="btn btn-outline-info btn-sm rounded-pill fw-bold px-3">
                    <i class="fas fa-user-check me-1"></i> Ver Auditor APOS
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert border-0 shadow-lg rounded-3 mb-4 p-3 d-flex align-items-center justify-content-between gap-3" style="background: linear-gradient(135deg, #065f46, #047857); color: white; border: 1px solid #34d399 !important;">
                <div class="d-flex align-items-center gap-3">
                    <i class="fas fa-check-circle fa-2x text-warning"></i>
                    <div>
                        <h6 class="mb-0 fw-bold text-white">{{ session('success') }}</h6>
                        <small style="color: #a7f3d0;">El cambio de estado ya figura en tiempo real en la Bandeja del Auditor APOS.</small>
                    </div>
                </div>
                <a href="{{ route('auditor.docentes.index') }}" class="btn btn-warning btn-sm rounded-pill fw-bold text-dark px-3">
                    Ver Auditor
                </a>
            </div>
        @endif

        <!-- Card Explicativa de Resolución APOS y Reintegro -->
        <div class="card border-0 bg-dark shadow-sm mb-4" style="border-left: 4px solid #38bdf8 !important; border-radius: 14px;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ asset('images/apos_logo.svg') }}" alt="APOS" style="height: 34px; background: white; padding: 3px 6px; border-radius: 6px;">
                    <div>
                        <h6 class="fw-bold mb-1 text-white">Circuito de Resolución APOS y Vinculación Familiar</h6>
                        <p class="small text-light mb-0">
                            Cuando el Padre/Titular aporta la <b>Resolución emitida por APOS</b> o confirma la prestación de la Docente Integradora, el sistema vincula automáticamente <b>Alumno + Padre + Docente + Resolución</b> y notifica de inmediato al Auditor.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botón para Cargar Resolución directamente el Padre -->
        <button type="button" class="btn btn-warning text-dark w-100 rounded-pill fw-bold py-3 mb-4 d-flex align-items-center justify-content-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalSubirResolucionPadre">
            <i class="fas fa-file-signature fa-lg"></i> Cargar Resolución Emitida por APOS (Vía Padre/Titular)
        </button>

        <!-- Buscador Manual en Tiempo Real con Marcado Amarillo -->
        <div class="card border-0 shadow-sm mb-4" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155 !important;">
            <div class="card-body p-3">
                <label class="form-label small fw-bold text-warning mb-1">
                    <i class="fas fa-bolt me-1"></i> Buscar en Tiempo Real (Marca coincidencias en amarillo):
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-dark border-secondary text-warning"><i class="fas fa-search"></i></span>
                    <input type="text" id="buscadorPadreTiempoReal" class="form-control bg-dark text-white border-secondary fw-semibold" placeholder="Buscar por Alumno, DNI, Padre, Docente, Escuela o N° de Resolución..." oninput="filtrarPadreTiempoReal()">
                </div>
            </div>
        </div>

        <!-- Histórico de Expedientes, Resoluciones y Estados -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold text-white mb-0"><i class="fas fa-list-alt me-2 text-info"></i> Expedientes Familiares y Resoluciones APOS</h5>
            <span class="badge bg-info text-dark fw-bold px-3 py-1 rounded-pill" id="contadorPadreResultados">{{ $reintegros->count() }} registros</span>
        </div>

        @foreach($reintegros as $r)
            @php
                $searchPadre = strtolower($r->alumno_nombre . ' ' . $r->alumno_dni . ' ' . $r->padre_nombre . ' ' . $r->docente_nombre . ' ' . $r->escuela_nombre . ' ' . $r->nro_resolucion);
            @endphp
            <div class="card border-0 shadow-sm mb-3 card-padre-filtrable"
                 data-search="{{ $searchPadre }}"
                 data-raw-alumno="{{ $r->alumno_nombre }}"
                 data-raw-dni="{{ $r->alumno_dni }}"
                 data-raw-padre="{{ $r->padre_nombre }}"
                 data-raw-docente="{{ $r->docente_nombre }}"
                 data-raw-escuela="{{ $r->escuela_nombre }}"
                 data-raw-resolucion="{{ $r->nro_resolucion }}"
                 style="background: #1e293b; border-radius: 16px; border: 1px solid rgba(255,255,255,0.12) !important;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-2 flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('images/apos_logo.svg') }}" alt="APOS" style="height: 22px; background: white; padding: 2px 5px; border-radius: 4px;">
                            <h5 class="fw-bold text-white mb-0">
                                <i class="fas fa-child text-warning me-1"></i> <span class="hl-alumno">{{ $r->alumno_nombre }}</span>
                                <small class="text-info ms-1">(DNI: <span class="hl-dni">{{ $r->alumno_dni }}</span>)</small>
                            </h5>
                        </div>
                        @if($r->estado_reintegro == 'aprobado_para_pago')
                            <span class="badge bg-success text-white rounded-pill px-3 py-1 fw-bold shadow-sm"><i class="fas fa-check-circle me-1"></i> RESOLUCIÓN ACTIVA APOS</span>
                        @else
                            <span class="badge bg-warning text-dark rounded-pill px-3 py-1 fw-bold shadow-sm"><i class="fas fa-hourglass-half me-1"></i> EN AUDITORÍA APOS</span>
                        @endif
                    </div>

                    <p class="small text-light mb-2">
                        <i class="fas fa-user-shield text-primary me-1"></i> Padre/Titular: <strong class="text-white hl-padre">{{ $r->padre_nombre }}</strong>
                        <span class="mx-1">|</span>
                        <i class="fas fa-chalkboard-teacher text-info me-1"></i> Docente: <strong class="text-info hl-docente">{{ $r->docente_nombre }}</strong>
                    </p>
                    <p class="small text-warning mb-3">
                        <i class="fas fa-school me-1"></i> Escuela: <span class="hl-escuela">{{ $r->escuela_nombre }}</span>
                    </p>

                    <!-- Triple Trazabilidad (Resolución APOS, Directora, Padre) -->
                    <div class="bg-dark p-3 rounded-3 mb-3 border border-secondary" style="font-size: 0.82rem;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-light"><i class="fas fa-file-contract text-warning me-2"></i> Resolución APOS:</span>
                            <span class="badge bg-warning text-dark fw-bold"><i class="fas fa-check me-1"></i> <span class="hl-resolucion">{{ $r->nro_resolucion }}</span></span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-light"><i class="fas fa-school text-info me-2"></i> Aval Directora Escuela:</span>
                            @if($r->aval_directora)
                                <span class="badge bg-success"><i class="fas fa-check me-1"></i> Certificado</span>
                            @else
                                <span class="badge bg-warning text-dark">Pendiente</span>
                            @endif
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="text-light"><i class="fas fa-user-shield text-success me-2"></i> Confirmación del Padre/Titular:</span>
                            @if($r->aval_padre)
                                <span class="badge bg-success" title="{{ $r->fecha_aval_padre }}"><i class="fas fa-check me-1"></i> Avalado</span>
                            @else
                                <span class="badge bg-warning text-dark">Pendiente de Firma</span>
                            @endif
                        </div>
                    </div>

                    <form action="{{ route('padre.asistencia.confirmar') }}" method="POST" class="row g-2 align-items-center">
                        @csrf
                        <input type="hidden" name="reintegro_id" value="{{ $r->id }}">
                        <input type="hidden" name="padre_nombre" value="{{ $r->padre_nombre }}">
                        <div class="col-sm-6">
                            <input type="text" name="nro_resolucion" class="form-control form-control-sm bg-dark text-warning border-secondary fw-bold" value="{{ $r->nro_resolucion !== 'EN TRÁMITE AUDITORÍA' ? $r->nro_resolucion : '' }}" placeholder="Informar N° Resolución APOS (opcional)">
                        </div>
                        <div class="col-sm-6">
                            <button type="submit" class="btn btn-success btn-sm w-100 rounded-pill fw-bold py-2 shadow-sm">
                                <i class="fas fa-bell me-1"></i> Confirmar Vínculo y Notificar Auditor
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach

    </div>
</div>

<!-- Modal Cargar Resolución Padre -->
<div class="modal fade" id="modalSubirResolucionPadre" tabindex="-1" aria-hidden="true" style="color: #0f172a;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header bg-dark text-white border-0 d-flex align-items-center justify-content-between" style="border-radius: 16px 16px 0 0;">
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ asset('images/apos_logo.svg') }}" alt="APOS" style="height: 26px; background: white; padding: 2px 6px; border-radius: 6px;">
                    <h5 class="modal-title fw-bold mb-0"><i class="fas fa-file-upload text-warning me-2"></i> Cargar Resolución de APOS</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('pwa.expediente.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4 text-dark">
                    <p class="small text-muted mb-3">Si usted tramitó la Resolución directamente en APOS La Rioja, ingrese el número o adjunte el comprobante para vincularlo a la Docente y notificar al Auditor.</p>
                    <div class="mb-3">
                        <label class="form-label fw-bold">N° de Resolución APOS *</label>
                        <input type="text" name="nro_resolucion" class="form-control fw-bold" placeholder="Ej: 505/25 o RES-APOS-2026" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Adjuntar Imagen o PDF de la Resolución (Opcional)</label>
                        <input type="file" name="resolucion_file" class="form-control" accept="image/*,.pdf">
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 pe-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 text-secondary fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold shadow-sm">
                        <i class="fas fa-bell me-1"></i> Subir y Notificar al Auditor APOS
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function resaltarAmarilloPadre(texto, termino) {
        if (!texto) return '';
        if (!termino) return texto;
        const escapado = termino.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        const regex = new RegExp(`(${escapado})`, 'gi');
        return texto.toString().replace(regex, '<mark style="background:#facc15; color:#0f172a; font-weight:800; padding:0 3px; border-radius:3px;">$1</mark>');
    }

    function filtrarPadreTiempoReal() {
        const input = document.getElementById('buscadorPadreTiempoReal');
        const rawTerm = input ? input.value.trim() : '';
        const term = rawTerm.toLowerCase();
        let visibles = 0;

        document.querySelectorAll('.card-padre-filtrable').forEach(card => {
            const search = card.getAttribute('data-search') || '';
            if (!term || search.includes(term)) {
                card.style.display = '';
                visibles++;
                ['alumno', 'dni', 'padre', 'docente', 'escuela', 'resolucion'].forEach(campo => {
                    const el = card.querySelector('.hl-' + campo);
                    const raw = card.getAttribute('data-raw-' + campo) || '';
                    if (el) el.innerHTML = resaltarAmarilloPadre(raw, rawTerm);
                });
            } else {
                card.style.display = 'none';
            }
        });

        const badge = document.getElementById('contadorPadreResultados');
        if (badge) badge.textContent = visibles + ' registros';
    }
</script>
@endsection
