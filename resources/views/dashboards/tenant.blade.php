@extends('layouts.tenant')

@section('title', 'Inicio')

@section('content')
    <!-- Ecosistema Metrics -->
    <div class="row">
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-icon"><i class="fas fa-users"></i></div>
                <div class="stats-info">
                    <p>Titulares/Referentes</p>
                    <h3>{{ $totalAfiliados ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-icon"><i class="fas fa-child"></i></div>
                <div class="stats-info">
                    <p>Alumnos/Pacientes</p>
                    <h3>{{ $totalFamiliares ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                <div class="stats-info">
                    <p>Docentes/Terapeutas</p>
                    <h3>{{ $totalDocentes ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-icon"><i class="fas fa-school"></i></div>
                <div class="stats-info">
                    <p>Escuelas Vinculadas</p>
                    <h3>{{ $totalEscuelas ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Banner Reportar Pago por Transferencia sin Comisiones -->
    <div class="row mt-4 mb-4">
        <div class="col-12">
            <div class="content-card d-flex align-items-center justify-content-between flex-wrap gap-3" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(168, 85, 247, 0.1)); border: 1px solid rgba(59, 130, 246, 0.2);">
                <div>
                    <h5 class="fw-bold mb-1"><i class="fas fa-file-invoice-dollar text-primary me-2"></i> Reportar Pago de Abono (Transferencia Bancaria)</h5>
                    <p class="mb-0 text-muted small">Transfiere directamente a nuestras cuentas (Santander, Banco Provincia, ARQ/DollarApp) y sube tu comprobante para lectura automática con IA.</p>
                </div>
                <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#reportarPagoModal">
                    <i class="fas fa-upload me-2"></i> Subir Comprobante
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Reportar Pago -->
    <div class="modal fade" id="reportarPagoModal" tabindex="-1" aria-hidden="true" style="color: #0f172a;">
        <div class="modal-dialog">
            <div class="modal-content border-0" style="border-radius: 15px;">
                <div class="modal-header bg-primary text-white border-0" style="border-radius: 15px 15px 0 0;">
                    <h5 class="modal-title fw-bold"><i class="fas fa-receipt me-2"></i> Subir Comprobante de Pago</h5>
                    <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('pagos.reportar') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="empresa_id" value="{{ Auth::user()->empresa_id ?? session('impersonated_tenant_id') ?? 1 }}">
                    <div class="modal-body p-4">
                        <div class="alert alert-info py-2 small mb-3">
                            <i class="fas fa-robot text-primary me-1"></i> Nuestra IA (Gemini Vision) extraerá automáticamente el N° de Operación, Monto y Banco de la captura.
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Foto / Captura del Comprobante (JPG, PNG, PDF)</label>
                            <input type="file" name="comprobante" class="form-control" accept="image/*,application/pdf" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pb-4 pe-4">
                        <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Procesar y Enviar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Setup Marca Blanca -->
    <div class="modal fade" id="setupModal" tabindex="-1" aria-hidden="true" style="color: #0f172a;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0" style="border-radius: 15px;">
                <div class="modal-header bg-light border-0" style="border-radius: 15px 15px 0 0;">
                    <h5 class="modal-title fw-bold"><i class="fas fa-paint-roller text-primary me-2"></i> Setup de Marca (Whitelabel)</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <!-- IMPORTANTE: El backend confía que Empresa existe mediante $empresa pasada por el Controlador TenantDashboardController -->
                <form action="{{ route('tenant.setup.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label fw-bold small text-muted">Nombre Público de la Institución</label>
                                <input type="text" name="nombre" class="form-control bg-light border-0" value="{{ $empresa->nombre ?? '' }}" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label class="form-label fw-bold small text-muted">A qué Provincia pertenece esta institución</label>
                                <select name="provincia_id" id="setupProvincia" class="form-select bg-light border-0">
                                    <option value="">- Seleccionar Provincia -</option>
                                    @foreach($provincias as $prov)
                                        <option value="{{ $prov->id }}" {{ ($empresa->provincia_id == $prov->id) ? 'selected' : '' }}>{{ $prov->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Localidad Principal</label>
                                <select name="localidad_id" id="setupLocalidad" class="form-select bg-light border-0" {{ $empresa->provincia_id ? '' : 'disabled' }}>
                                    <option value="">- Elija Provincia primero -</option>
                                    @if(isset($localidadesEmpresa) && count($localidadesEmpresa)>0)
                                        @foreach($localidadesEmpresa as $loc)
                                            <option value="{{ $loc->id }}" {{ ($empresa->localidad_id == $loc->id) ? 'selected' : '' }}>{{ $loc->nombre }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Color Primario</label>
                                <input type="color" name="color_primario" class="form-control form-control-color w-100 border-0" value="{{ $empresa->color_primario ?? '#3b82f6' }}">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Color Secundario (Menú)</label>
                                <input type="color" name="color_secundario" class="form-control form-control-color w-100 border-0" value="{{ $empresa->color_secundario ?? '#1e293b' }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Subir Logotipo Vectorial</label>
                            <input type="file" name="logo" class="form-control bg-light border-0" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer border-0 pb-4 pe-4">
                        <button type="submit" class="btn btn-primary rounded-pill px-4" style="background-color: var(--brand-primary); border: none;">Guardar Apariencia</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Lógica JS dependiente para Combos Provincia -> Localidad en Tenant
        const selProvincia = document.getElementById('setupProvincia');
        const selLocalidad = document.getElementById('setupLocalidad');

        if(selProvincia) {
            selProvincia.addEventListener('change', async function() {
                const provId = this.value;
                selLocalidad.innerHTML = '<option value="">- Cargando... -</option>';
                selLocalidad.disabled = true;

                if(!provId) {
                    selLocalidad.innerHTML = '<option value="">- Elija Provincia primero -</option>';
                    return;
                }

                try {
                    // Llamamos a la API interna
                    const r = await fetch('/api/localidades/' + provId);
                    const data = await r.json();
                    
                    selLocalidad.innerHTML = '<option value="">- Seleccione una Localidad -</option>';
                    data.forEach(l => { 
                        selLocalidad.innerHTML += `<option value="${l.id}">${l.nombre}</option>`; 
                    });
                    selLocalidad.disabled = false;
                } catch (e) {
                    console.error("Error obteniendo localidades", e);
                    selLocalidad.innerHTML = '<option value="">Error de conexión</option>';
                }
            });
        }
    });
</script>
@endpush
