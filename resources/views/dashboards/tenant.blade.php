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
