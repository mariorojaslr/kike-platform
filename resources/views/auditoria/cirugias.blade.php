@extends('layouts.app')

@section('title', 'Gestor de Cirugías & Prótesis Especiales (ANMAT)')

@section('content')
<div class="container-fluid py-4" style="background-color: #0f172a; min-height: 100vh; color: white;">
    <div class="container">
        
        <!-- Header Cirugías -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary">
            <div>
                <span class="badge bg-primary text-white px-3 py-1 rounded-pill fw-bold text-uppercase shadow-sm" style="font-size: 0.75rem;">
                    <i class="fas fa-bone me-1"></i> Auditoría Quirúrgica ANMAT
                </span>
                <h3 class="fw-bold mt-2 mb-0 text-white">
                    <i class="fas fa-procedures text-info me-2"></i> Solicitudes de Cirugías & Prótesis
                </h3>
                <small class="text-white-50"><i class="fas fa-shield-alt me-1 text-success"></i> Junta Evaluadora de Alta Complejidad</small>
            </div>
            <span class="badge bg-success text-white px-3 py-2 rounded-pill fw-bold">
                <i class="fas fa-check-circle me-1"></i> Trazabilidad Criptográfica MD5
            </span>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-lg rounded-3 mb-4 p-3 d-flex align-items-center gap-3">
                <i class="fas fa-check-circle fa-2x text-success"></i>
                <div>
                    <h6 class="mb-0 fw-bold">{{ session('success') }}</h6>
                    <small>Se emitió el certificado digital de prótesis con validez ANMAT.</small>
                </div>
            </div>
        @endif

        <div class="row g-3">
            @foreach($solicitudes as $s)
                <div class="col-md-6">
                    <div class="card border-0 bg-secondary bg-opacity-10 shadow-sm h-100" style="border-radius: 16px; border: 1px solid rgba(255,255,255,0.1);">
                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="fw-bold text-white mb-0"><i class="fas fa-user-injured text-warning me-2"></i> {{ $s->paciente }}</h5>
                                    @if($s->estado == 'aprobado_con_qr')
                                        <span class="badge bg-success text-white px-3 py-1 rounded-pill fw-bold"><i class="fas fa-qrcode me-1"></i> PRÓTESIS AUTORIZADA</span>
                                    @else
                                        <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold"><i class="fas fa-clock me-1"></i> EN EVALUACIÓN</span>
                                    @endif
                                </div>

                                <div class="bg-dark p-3 rounded-3 mb-3 border border-secondary border-opacity-25" style="font-size: 0.85rem;">
                                    <div class="mb-1"><strong class="text-info">Sanatorio:</strong> <span class="text-white">{{ $s->sanatorio }}</span></div>
                                    <div class="mb-1"><strong class="text-info">Intervención:</strong> <span class="text-white">{{ $s->cirugia }}</span></div>
                                    <div class="mb-1"><strong class="text-info">Prótesis ANMAT:</strong> <span class="text-warning">{{ $s->protesis }}</span></div>
                                    <div class="mb-1"><strong class="text-info">Proveedor:</strong> <span class="text-white">{{ $s->ortopedia }}</span></div>
                                    <div><strong class="text-info">Monto Cotizado:</strong> <span class="badge bg-success fs-6">${{ number_format($s->presupuesto_adjudicado, 0, ',', '.') }}</span></div>
                                </div>
                            </div>

                            <div>
                                @if($s->estado == 'aprobado_con_qr')
                                    <div class="p-2 bg-success text-white rounded text-center small fw-bold shadow-sm">
                                        <i class="fas fa-shield-alt me-1"></i> Hash MD5: {{ $s->hash_md5 }}
                                    </div>
                                @else
                                    <form action="{{ route('auditoria.cirugias.autorizar', $s->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2 shadow-sm">
                                            <i class="fas fa-signature me-1"></i> Autorizar Prótesis Quirúrgica
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</div>
@endsection
