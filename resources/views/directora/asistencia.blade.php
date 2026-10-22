@extends('layouts.app')

@section('title', 'APOS La Rioja | Certificación Digital de Asistencias - Dirección Escolar')

@section('content')
<div class="container-fluid py-4" style="background-color: #0f172a; min-height: 100vh; color: white;">
    <div class="container">
        
        <!-- Header Institucional con Logo Oficial APOS La Rioja -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-white p-2 rounded-3 shadow" style="border: 2px solid #38bdf8;">
                    <img src="{{ asset('images/apos_logo.svg') }}" alt="APOS La Rioja" style="height: 54px; width: auto; display: block;">
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="badge bg-primary px-3 py-1 rounded-pill fw-bold text-uppercase" style="letter-spacing: 0.8px; font-size: 0.72rem;">
                            <i class="fas fa-school me-1"></i> Portal Institucional Escolar • APOS La Rioja
                        </span>
                        <span class="badge bg-info text-dark px-3 py-1 rounded-pill fw-bold" style="font-size: 0.72rem;">
                            <i class="fas fa-link me-1"></i> Vínculo Alumno • Padre • Docente • Resolución
                        </span>
                    </div>
                    <h2 class="fw-bold mt-2 mb-1 text-white" style="font-size: 1.55rem;">
                        <i class="fas fa-signature text-info me-2"></i> Certificación Digital de Asistencias Docentes
                    </h2>
                    <p class="mb-0" style="color: #cbd5e1; font-size: 0.9rem;">
                        <i class="fas fa-building me-1 text-info"></i>
                        <strong class="text-warning" id="lblEscuelaActivaHeader">{{ $escuelaNombre }}</strong>
                        <span class="text-white-50 mx-2">|</span>
                        <span class="text-white">{{ $directoraNombre }}</span>
                    </p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('auditor.docentes.index') }}" class="btn btn-outline-info rounded-pill px-3 py-2 fw-bold btn-sm shadow-sm">
                    <i class="fas fa-user-check me-1"></i> Ver Bandeja del Auditor APOS
                </a>
                <span class="badge bg-success text-white border border-light px-3 py-2 rounded-pill shadow-sm fw-bold">
                    <i class="fas fa-shield-alt me-1"></i> Trazabilidad APOS Activa
                </span>
            </div>
        </div>

        @if(session('success'))
            <div class="alert border-0 shadow-lg rounded-3 mb-4 p-3 d-flex align-items-center justify-content-between gap-3" style="background: linear-gradient(135deg, #065f46, #047857); color: white; border: 1px solid #34d399 !important;">
                <div class="d-flex align-items-center gap-3">
                    <i class="fas fa-check-circle fa-2x text-warning"></i>
                    <div>
                        <h6 class="mb-0 fw-bold text-white">{{ session('success') }}</h6>
                        <small style="color: #a7f3d0;">Este aval quedó asentado con firma digital inalterable y ya se encuentra visible en la Bandeja Inmediata del Auditor de APOS.</small>
                    </div>
                </div>
                <a href="{{ route('auditor.docentes.index') }}" class="btn btn-warning btn-sm rounded-pill fw-bold text-dark px-3 whitespace-nowrap">
                    Ir al Auditor <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        @endif

        <!-- Banner Explicativo de Normativa APOS -->
        <div class="card border-0 bg-dark shadow-sm mb-4" style="border-left: 4px solid #f59e0b !important; border-radius: 12px;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ asset('images/apos_logo.svg') }}" alt="APOS" style="height: 36px; background: white; padding: 4px 8px; border-radius: 6px;">
                    <div>
                        <h6 class="fw-bold mb-1 text-warning">Normativa APOS de Certificación de Asistencia por Resolución</h6>
                        <p class="small text-light mb-0">
                            Por disposición oficial de <b>APOS La Rioja</b>, el límite máximo permitido por estudiante es de <b>3 horas diarias</b>.
                            Al presionar <b>"Avalar Asistencia Oficial"</b>, la Dirección Escolar certifica que el docente concurrió a atender al alumno vinculado a su Padre/Titular y Resolución, disparando una <b>alerta inmediata al Auditor</b>.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- BARRA DE BÚSQUEDA EN TIEMPO REAL CON MARCADO AMARILLO + FILTRO DE ESCUELA -->
        <div class="card border-0 shadow-lg mb-4" style="background: #1e293b; border-radius: 16px; border: 1px solid #334155 !important;">
            <div class="card-body p-3">
                <div class="row g-3 align-items-center">
                    <div class="col-lg-6">
                        <label class="form-label small fw-bold text-warning mb-1">
                            <i class="fas fa-bolt me-1"></i> Búsqueda Manual en Tiempo Real (Marca coincidencias en amarillo):
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-secondary text-warning">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" id="buscadorDirectoraTiempoReal" class="form-control bg-dark text-white border-secondary fw-semibold" placeholder="Escribí Alumno, DNI, Padre/Titular, Docente, Resolución o Escuela..." oninput="filtrarDirectoraTiempoReal()">
                            <button class="btn btn-outline-secondary text-light" type="button" onclick="limpiarBusquedaDirectora()" title="Limpiar búsqueda">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <label class="form-label small fw-bold text-info mb-1">
                            <i class="fas fa-school me-1"></i> Filtrar por Institución Escolar (APOS La Rioja):
                        </label>
                        <select id="filtroEscuelaDirectora" class="form-select bg-dark text-white border-secondary" onchange="filtrarDirectoraTiempoReal()">
                            <option value="">Todas las Escuelas del Padrón APOS ({{ $escuelas->count() }} instituciones)</option>
                            @foreach($escuelas as $esc)
                                <option value="{{ strtolower($esc->nombre) }}">{{ $esc->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-2 text-end">
                        <label class="form-label small fw-bold text-light mb-1 d-block">Resultados:</label>
                        <span class="badge bg-info text-dark fs-6 px-3 py-2 rounded-pill fw-bold" id="contadorResultadosDirectora">
                            {{ $alumnosAtendidos->count() }} legajos
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABLA INTERACTIVA EN TIEMPO REAL DE ALUMNOS, PADRES, DOCENTES Y RESOLUCIONES -->
        <div class="card border-0 shadow-lg mb-4" style="background: #1e293b; border-radius: 16px; border: 1px solid rgba(255,255,255,0.1);">
            <div class="card-header bg-dark border-bottom border-secondary p-3 d-flex justify-content-between align-items-center" style="border-radius: 16px 16px 0 0;">
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ asset('images/apos_logo.svg') }}" alt="APOS" style="height: 24px; background: white; padding: 2px 5px; border-radius: 4px;">
                    <h6 class="fw-bold text-white mb-0">
                        <i class="fas fa-table text-info me-1"></i> Padrón Escolar de Alumnos Integrados, Padres, Docentes y Resoluciones APOS
                    </h6>
                </div>
                <small class="text-warning fw-bold"><i class="fas fa-highlighter me-1"></i> Filtrado instantáneo y resaltado amarillo activo</small>
            </div>
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0" id="tablaDirectoraAlumnos">
                    <thead style="background: #0f172a; font-size: 0.78rem; text-transform: uppercase; color: #94a3b8;">
                        <tr>
                            <th class="ps-3">Alumno / DNI</th>
                            <th>Padre / Titular APOS</th>
                            <th>Docente Integrador/a</th>
                            <th>Escuela / Institución</th>
                            <th>Resolución APOS</th>
                            <th>Horario / Mes</th>
                            <th>Estado Aval</th>
                            <th class="text-end pe-3">Acción Directora</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($alumnosAtendidos as $item)
                            @php
                                $searchString = strtolower($item->alumno_nombre . ' ' . $item->alumno_dni . ' ' . $item->padre_nombre . ' ' . $item->docente_nombre . ' ' . $item->escuela_nombre . ' ' . $item->nro_resolucion . ' ' . $item->diagnostico);
                            @endphp
                            <tr class="fila-directora-filtrable"
                                data-search="{{ $searchString }}"
                                data-escuela="{{ strtolower($item->escuela_nombre) }}"
                                data-raw-alumno="{{ $item->alumno_nombre }}"
                                data-raw-dni="{{ $item->alumno_dni }}"
                                data-raw-padre="{{ $item->padre_nombre }}"
                                data-raw-docente="{{ $item->docente_nombre }}"
                                data-raw-escuela="{{ $item->escuela_nombre }}"
                                data-raw-resolucion="{{ $item->nro_resolucion }}">
                                <td class="ps-3">
                                    <div class="fw-bold text-white"><i class="fas fa-child text-warning me-1"></i> <span class="hl-alumno">{{ $item->alumno_nombre }}</span></div>
                                    <small class="text-info">DNI: <span class="hl-dni">{{ $item->alumno_dni }}</span></small>
                                </td>
                                <td>
                                    <div class="fw-semibold text-light"><i class="fas fa-user-shield text-primary me-1"></i> <span class="hl-padre">{{ $item->padre_nombre }}</span></div>
                                    @if($item->padre_afiliado)
                                        <small style="color:#94a3b8;">Af: {{ $item->padre_afiliado }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-info fw-semibold"><i class="fas fa-chalkboard-teacher me-1"></i> <span class="hl-docente">{{ $item->docente_nombre }}</span></span>
                                </td>
                                <td>
                                    <span class="small text-light"><i class="fas fa-school text-warning me-1"></i> <span class="hl-escuela">{{ $item->escuela_nombre }}</span></span>
                                </td>
                                <td>
                                    <span class="badge bg-warning text-dark fw-bold px-2 py-1">
                                        <i class="fas fa-file-signature me-1"></i> <span class="hl-resolucion">{{ $item->nro_resolucion }}</span>
                                    </span>
                                </td>
                                <td>
                                    <div class="small text-white">{{ $item->horario }}</div>
                                    <span class="badge bg-primary">{{ $item->horas_mes }} hs/mes</span>
                                </td>
                                <td>
                                    @if($item->estado_aval == 'firmado')
                                        <span class="badge bg-success text-white rounded-pill px-3 py-1 fw-bold"><i class="fas fa-check-double me-1"></i> AVALADO</span>
                                    @else
                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-1 fw-bold"><i class="fas fa-clock me-1"></i> PENDIENTE</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    @if($item->estado_aval == 'firmado')
                                        <span class="small text-success fw-bold"><i class="fas fa-check-circle me-1"></i> Firmado</span>
                                    @else
                                        <button type="button" class="btn btn-sm btn-primary rounded-pill fw-bold px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalFirmar{{ $item->id }}">
                                            <i class="fas fa-signature me-1"></i> Avalar Asistencia
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TARJETAS DETALLADAS DE ALUMNOS ATENDIDOS -->
        <h5 class="fw-bold text-white mb-3">
            <i class="fas fa-id-card-alt me-2 text-info"></i> Fichas de Certificación Escolar Individual (APOS La Rioja)
        </h5>

        <div class="row g-3" id="contenedorTarjetasDirectora">
            @foreach($alumnosAtendidos as $item)
                @php
                    $searchString = strtolower($item->alumno_nombre . ' ' . $item->alumno_dni . ' ' . $item->padre_nombre . ' ' . $item->docente_nombre . ' ' . $item->escuela_nombre . ' ' . $item->nro_resolucion . ' ' . $item->diagnostico);
                @endphp
                <div class="col-md-6 col-lg-4 tarjeta-directora-filtrable"
                     data-search="{{ $searchString }}"
                     data-escuela="{{ strtolower($item->escuela_nombre) }}"
                     data-raw-alumno="{{ $item->alumno_nombre }}"
                     data-raw-dni="{{ $item->alumno_dni }}"
                     data-raw-padre="{{ $item->padre_nombre }}"
                     data-raw-docente="{{ $item->docente_nombre }}"
                     data-raw-escuela="{{ $item->escuela_nombre }}"
                     data-raw-resolucion="{{ $item->nro_resolucion }}">
                    <div class="card border-0 shadow-sm h-100" style="background: #1e293b; border-radius: 16px; border: 1px solid rgba(255,255,255,0.12) !important;">
                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ asset('images/apos_logo.svg') }}" alt="APOS" style="height: 20px; background: white; padding: 2px 4px; border-radius: 4px;">
                                        <span class="badge bg-warning text-dark fw-bold">
                                            Res: <span class="hl-resolucion">{{ $item->nro_resolucion }}</span>
                                        </span>
                                    </div>
                                    @if($item->estado_aval == 'firmado')
                                        <span class="badge bg-success text-white rounded-pill px-3 py-1 fw-bold shadow-sm"><i class="fas fa-check-double me-1"></i> AVALADO</span>
                                    @else
                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-1 fw-bold shadow-sm"><i class="fas fa-clock me-1"></i> PENDIENTE</span>
                                    @endif
                                </div>

                                <h5 class="fw-bold text-white mb-1 mt-2">
                                    <i class="fas fa-child text-warning me-1"></i> <span class="hl-alumno">{{ $item->alumno_nombre }}</span>
                                </h5>
                                <div class="small text-light mb-2">
                                    DNI: <strong class="hl-dni">{{ $item->alumno_dni }}</strong>
                                    <span class="mx-1">|</span>
                                    Padre/Titular: <strong class="text-info hl-padre">{{ $item->padre_nombre }}</strong>
                                </div>

                                <p class="small text-info mb-1">
                                    <i class="fas fa-chalkboard-teacher me-1"></i> Docente: <strong class="hl-docente">{{ $item->docente_nombre }}</strong>
                                </p>
                                <p class="small text-warning mb-3">
                                    <i class="fas fa-school me-1"></i> <span class="hl-escuela">{{ $item->escuela_nombre }}</span>
                                </p>

                                <div class="bg-dark p-3 rounded-3 mb-3 border border-secondary">
                                    <div class="small text-light mb-1"><strong>Diagnóstico CUD:</strong> <span class="text-info">{{ $item->diagnostico }}</span></div>
                                    <div class="small text-light mb-1"><strong>Horario Diario:</strong> <span class="text-white">{{ $item->horario }}</span></div>
                                    <div class="small text-light"><strong>Total Horas Mes:</strong> <span class="badge bg-primary">{{ $item->horas_mes }} hs</span></div>
                                </div>
                            </div>

                            <div>
                                @if($item->estado_aval == 'firmado')
                                    <div class="p-2 bg-success text-white rounded small text-center fw-bold shadow-sm">
                                        <i class="fas fa-check-circle me-1"></i> Avalado Digitalmente ({{ $item->fecha_firma }})
                                    </div>
                                @else
                                    <button type="button" class="btn btn-primary w-100 rounded-pill fw-bold py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalFirmar{{ $item->id }}">
                                        <i class="fas fa-signature me-1"></i> Avalar Asistencia Oficial APOS
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal de Firma Digital -->
                <div class="modal fade" id="modalFirmar{{ $item->id }}" tabindex="-1" aria-hidden="true" style="color: #0f172a;">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                            <div class="modal-header bg-dark text-white border-0 d-flex align-items-center justify-content-between" style="border-radius: 16px 16px 0 0;">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ asset('images/apos_logo.svg') }}" alt="APOS" style="height: 28px; background: white; padding: 2px 6px; border-radius: 6px;">
                                    <h5 class="modal-title fw-bold mb-0"><i class="fas fa-pen-alt text-info me-1"></i> Aval Oficial APOS</h5>
                                </div>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('directora.asistencia.firmar') }}" method="POST">
                                @csrf
                                <input type="hidden" name="alumno_id" value="{{ $item->id }}">
                                <div class="modal-body p-4 text-dark">
                                    <div class="alert alert-info p-3 small mb-3">
                                        <strong>Alumno:</strong> {{ $item->alumno_nombre }} (DNI: {{ $item->alumno_dni }})<br>
                                        <strong>Padre / Titular APOS:</strong> {{ $item->padre_nombre }}<br>
                                        <strong>Docente Integrador/a:</strong> {{ $item->docente_nombre }}<br>
                                        <strong>Resolución APOS:</strong> {{ $item->nro_resolucion }}<br>
                                        <strong>Institución:</strong> {{ $item->escuela_nombre }}
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Nombre Completo de la Directora / Autoridad *</label>
                                        <input type="text" name="directora_nombre" class="form-control" value="{{ $directoraNombre }}" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Observaciones de Asistencia (Opcional)</label>
                                        <textarea name="observaciones" class="form-control" rows="2" placeholder="Ej: Cumplió su cronograma en conformidad en el aula según Resolución APOS."></textarea>
                                    </div>

                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="chkCertifica{{ $item->id }}" required checked>
                                        <label class="form-check-label small fw-bold" for="chkCertifica{{ $item->id }}">
                                            Certifico bajo responsabilidad institucional que el/la docente asistió en los días y horarios indicados. Se notificará en vivo al Auditor APOS.
                                        </label>
                                    </div>
                                </div>
                                <div class="modal-footer border-0 pb-4 pe-4">
                                    <button type="button" class="btn btn-light rounded-pill px-4 text-secondary fw-bold" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                                        <i class="fas fa-bell me-1"></i> Firmar Aval y Notificar al Auditor
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div id="sinResultadosDirectora" class="text-center py-5" style="display: none;">
            <i class="fas fa-search fa-3x text-warning mb-3 opacity-75"></i>
            <h5 class="text-white fw-bold">No se encontraron registros con ese criterio</h5>
            <p class="text-light small">Probá buscando por otro nombre de alumno, padre, docente, escuela o número de resolución.</p>
        </div>

    </div>
</div>

<script>
    function resaltarAmarilloDirectora(texto, termino) {
        if (!texto) return '';
        if (!termino) return texto;
        const escapado = termino.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        const regex = new RegExp(`(${escapado})`, 'gi');
        return texto.toString().replace(regex, '<mark style="background:#facc15; color:#0f172a; font-weight:800; padding:0 3px; border-radius:3px;">$1</mark>');
    }

    function aplicarResaltadoElemento(contenedor, rawTerm) {
        const campos = [
            { sel: '.hl-alumno', attr: 'data-raw-alumno' },
            { sel: '.hl-dni', attr: 'data-raw-dni' },
            { sel: '.hl-padre', attr: 'data-raw-padre' },
            { sel: '.hl-docente', attr: 'data-raw-docente' },
            { sel: '.hl-escuela', attr: 'data-raw-escuela' },
            { sel: '.hl-resolucion', attr: 'data-raw-resolucion' }
        ];
        campos.forEach(c => {
            const el = contenedor.querySelector(c.sel);
            const rawVal = contenedor.getAttribute(c.attr) || '';
            if (el) {
                el.innerHTML = resaltarAmarilloDirectora(rawVal, rawTerm);
            }
        });
    }

    function filtrarDirectoraTiempoReal() {
        const input = document.getElementById('buscadorDirectoraTiempoReal');
        const selectEsc = document.getElementById('filtroEscuelaDirectora');
        const rawTerm = input ? input.value.trim() : '';
        const term = rawTerm.toLowerCase();
        const escTerm = selectEsc ? selectEsc.value.trim().toLowerCase() : '';

        let visibles = 0;

        // 1. Filtrar y resaltar filas de la tabla
        document.querySelectorAll('.fila-directora-filtrable').forEach(fila => {
            const dataSearch = fila.getAttribute('data-search') || '';
            const dataEscuela = fila.getAttribute('data-escuela') || '';
            const coincideTexto = !term || dataSearch.includes(term);
            const coincideEscuela = !escTerm || dataEscuela.includes(escTerm);

            if (coincideTexto && coincideEscuela) {
                fila.style.display = '';
                visibles++;
                aplicarResaltadoElemento(fila, rawTerm);
            } else {
                fila.style.display = 'none';
            }
        });

        // 2. Filtrar y resaltar tarjetas
        document.querySelectorAll('.tarjeta-directora-filtrable').forEach(card => {
            const dataSearch = card.getAttribute('data-search') || '';
            const dataEscuela = card.getAttribute('data-escuela') || '';
            const coincideTexto = !term || dataSearch.includes(term);
            const coincideEscuela = !escTerm || dataEscuela.includes(escTerm);

            if (coincideTexto && coincideEscuela) {
                card.style.display = '';
                aplicarResaltadoElemento(card, rawTerm);
            } else {
                card.style.display = 'none';
            }
        });

        // Actualizar contador y cartel sin resultados
        const badge = document.getElementById('contadorResultadosDirectora');
        if (badge) badge.textContent = visibles + ' legajos';

        const emptyMsg = document.getElementById('sinResultadosDirectora');
        if (emptyMsg) emptyMsg.style.display = visibles === 0 ? 'block' : 'none';
    }

    function limpiarBusquedaDirectora() {
        const input = document.getElementById('buscadorDirectoraTiempoReal');
        const selectEsc = document.getElementById('filtroEscuelaDirectora');
        if (input) input.value = '';
        if (selectEsc) selectEsc.value = '';
        filtrarDirectoraTiempoReal();
    }
</script>
@endsection
