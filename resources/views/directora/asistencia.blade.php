@extends('layouts.app')

@section('title', 'Certificación Digital de Asistencias - Directora de Escuela')

@section('content')
<div class="container-fluid py-4" style="background-color: #0f172a; min-height: 100vh; color: white;">
    <div class="container">
        
        <!-- Header del Portal de la Directora -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary">
            <div>
                <span class="badge bg-primary px-3 py-2 rounded-pill fw-bold text-uppercase" style="letter-spacing: 1px;">
                    <i class="fas fa-school me-1"></i> Portal Institucional Escolar
                </span>
                <h2 class="fw-bold mt-2 mb-1 text-white">
                    <i class="fas fa-signature text-info me-2"></i> Certificación Digital de Asistencias Docentes
                </h2>
                <p class="text-muted mb-0"><i class="fas fa-building me-1 text-primary"></i> <strong>{{ $escuelaNombre }}</strong> | {{ $directoraNombre }}</p>
            </div>
            <div>
                <span class="badge bg-success bg-opacity-20 text-success border border-success px-3 py-2 rounded-pill">
                    <i class="fas fa-shield-alt me-1"></i> Trazabilidad Expresa Activa
                </span>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-lg rounded-3 mb-4 p-3 d-flex align-items-center gap-3">
                <i class="fas fa-check-circle fa-2x text-success"></i>
                <div>
                    <h6 class="mb-0 fw-bold">{{ session('success') }}</h6>
                    <small>Este aval queda asentado con firma digital inalterable para auditoría de la mutual.</small>
                </div>
            </div>
        @endif

        <!-- Banner Explicativo de Normativa y Límites -->
        <div class="card border-0 bg-dark shadow-sm mb-4" style="border-left: 4px solid #f59e0b !important; border-radius: 12px;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                    <div>
                        <h6 class="fw-bold mb-1 text-warning">Normativa de Certificación de Asistencia por Resolución</h6>
                        <p class="small text-muted mb-0">
                            Por disposición oficial, el límite máximo permitido por estudiante es de <b>3 horas diarias</b>. 
                            Al presionar <b>"Avalar Asistencia Oficial"</b>, usted confirma formalmente que la docente concurrió en los días y horarios declarados a atender al alumno en la institución.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Listado de Alumnos Atendidos -->
        <h5 class="fw-bold text-white mb-3"><i class="fas fa-users me-2 text-info"></i> Alumnos Atendidos y Declaración de Horarios</h5>

        <div class="row g-3">
            @foreach($alumnosAtendidos as $item)
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 bg-secondary bg-opacity-10 shadow-sm h-100" style="border-radius: 16px; border: 1px solid rgba(255,255,255,0.1);">
                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 class="fw-bold text-white mb-0"><i class="fas fa-child text-warning me-2"></i> {{ $item->alumno_nombre }}</h5>
                                    @if($item->estado_aval == 'firmado')
                                        <span class="badge bg-success rounded-pill px-3 py-1"><i class="fas fa-check-double me-1"></i> AVALADO</span>
                                    @else
                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-1"><i class="fas fa-clock me-1"></i> PENDIENTE</span>
                                    @endif
                                </div>
                                <p class="small text-info mb-3"><i class="fas fa-user-md me-1"></i> {{ $item->docente_nombre }}</p>

                                <div class="bg-dark p-3 rounded-3 mb-3 border border-secondary border-opacity-25">
                                    <div class="small text-muted mb-1"><strong>Días Declarados:</strong> {{ $item->dias_asistencia }}</div>
                                    <div class="small text-muted mb-1"><strong>Horario Diario:</strong> {{ $item->horario }}</div>
                                    <div class="small text-muted"><strong>Total Horas Mes:</strong> <span class="badge bg-primary">{{ $item->horas_mes }} hs</span></div>
                                </div>
                            </div>

                            <div>
                                @if($item->estado_aval == 'firmado')
                                    <div class="p-2 bg-success bg-opacity-10 border border-success rounded text-success small text-center">
                                        <i class="fas fa-signature me-1"></i> Avalado el {{ $item->fecha_firma }}
                                    </div>
                                @else
                                    <button type="button" class="btn btn-primary w-100 rounded-pill fw-bold py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalFirmar{{ $item->id }}">
                                        <i class="fas fa-signature me-1"></i> Avalar Asistencia Oficial
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
                            <div class="modal-header bg-dark text-white border-0" style="border-radius: 16px 16px 0 0;">
                                <h5 class="modal-title fw-bold"><i class="fas fa-pen-alt text-info me-2"></i> Certificación Digital de Asistencia</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('directora.asistencia.firmar') }}" method="POST">
                                @csrf
                                <input type="hidden" name="alumno_id" value="{{ $item->id }}">
                                <div class="modal-body p-4 text-dark">
                                    <div class="alert alert-info p-3 small mb-3">
                                        <strong>Alumno:</strong> {{ $item->alumno_nombre }}<br>
                                        <strong>Docente:</strong> {{ $item->docente_nombre }}<br>
                                        <strong>Horario Avalado:</strong> {{ $item->horario }} ({{ $item->dias_asistencia }})
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Nombre Completo de la Directora / Autoridad *</label>
                                        <input type="text" name="directora_nombre" class="form-control" value="{{ $directoraNombre }}" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Observaciones de Asistencia (Opcional)</label>
                                        <textarea name="observaciones" class="form-control" rows="2" placeholder="Ej: Cumplió su cronograma en conformidad en el aula."></textarea>
                                    </div>

                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="chkCertifica{{ $item->id }}" required checked>
                                        <label class="form-check-label small fw-bold" for="chkCertifica{{ $item->id }}">Certifico bajo responsabilidad que la docente asistió en los días y horarios indicados.</label>
                                    </div>
                                </div>
                                <div class="modal-footer border-0 pb-4 pe-4">
                                    <button type="button" class="btn btn-light rounded-pill px-4 text-secondary fw-bold" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                                        <i class="fas fa-check-circle me-1"></i> Firmar y Asentar Aval
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</div>
@endsection
