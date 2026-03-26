@extends('layouts.tenant')

@section('title', 'Importador Masivo (Excel)')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold" style="color: var(--brand-primary);"><i class="fas fa-satellite-dish"></i> Sincronización Masiva (Excel)</h2>
        <p class="text-muted">Carga tus planillas generadas en Excel para poblar automáticamente el sistema con Titulares, Beneficiarios (Pacientes) y Docentes (Terapeutas).</p>
        
        <div class="alert alert-info mt-3 border-0 shadow-sm" style="border-left: 4px solid var(--brand-primary) !important;">
            <i class="fas fa-info-circle me-2"></i> <strong>Instrucciones Importantes:</strong><br>
            <ul>
                <li>Guarda tu archivo Excel usando la opción <b>"Guardar como -> CSV (delimitado por comas o punto y coma)"</b>.</li>
                <li>El sistema utiliza el <b>N° de Afiliado</b> para vincular directamente a los padres (Titulares, ej: terminado en 00) con sus hijos (Beneficiarios, ej: terminado en 01, 02).</li>
                <li>Los Diagnósticos y Escuelas Nuevas se crearán automáticamente en el catálogo si no existen.</li>
            </ul>
        </div>
    </div>
</div>

<div class="row">

    <!-- Carga de ARCHIVO RESUMEN -->
    <div class="col-md-6 mb-4">
        <div class="content-card h-100">
            <h5 class="fw-bold text-primary mb-3"><i class="fas fa-users-cog"></i> 1. Importar "Planilla Resume General"</h5>
            <p class="small text-muted mb-4">Sube el archivo que contiene la estructura principal vinculada: <b>Titular -> Beneficiario -> Docente</b> y sus horas auditadas.</p>
            
            <form action="{{ route('tenant.importador.procesar') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="tipo_importacion" value="resumen">
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Archivo CSV (Delimitado por punto y coma)</label>
                    <input type="file" name="archivo_csv" class="form-control" accept=".csv" required>
                </div>
                
                <button type="submit" class="btn btn-primary w-100 shadow-sm" onclick="this.innerHTML='<i class=\'fas fa-spinner fa-spin\'></i> Importando y Vinculando...'">
                    <i class="fas fa-cloud-upload-alt me-2"></i> Procesar Titulares y Docentes
                </button>
            </form>
            
            <div class="mt-4 pt-4 border-top">
                <h6 class="fw-bold text-muted" style="font-size: 0.8rem;">Estructura de Columnas Esperada:</h6>
                <div class="p-2 bg-light rounded" style="font-size: 0.75rem; white-space: nowrap; overflow-x: auto; color: #555;">
                    1. Apellido y Nombre Titular | 2. N° Afiliado | 3. Apellido/Nombre Beneficiario | 4. N° Afiliado Beneficiario | 5. Docente Nombre | 6. DNI | 7. Resolución
                </div>
            </div>
        </div>
    </div>

    <!-- Carga de ARCHIVO ALUMNOS -->
    <div class="col-md-6 mb-4">
        <div class="content-card h-100 border-warning" style="border-width: 2px;">
            <h5 class="fw-bold text-warning mb-3"><i class="fas fa-child"></i> 2. Importar "Planilla Datos del Alumno"</h5>
            <p class="small text-muted mb-4">Sube el archivo que nutre los detalles clínicos y educativos: <b>DNI, Diagnóstico CUD, Escuela, Turno y Grado</b>.</p>
            
            <form action="{{ route('tenant.importador.procesar') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="tipo_importacion" value="alumnos">
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Archivo CSV (Delimitado por punto y coma)</label>
                    <input type="file" name="archivo_csv" class="form-control" accept=".csv" required>
                </div>
                
                <button type="submit" class="btn btn-warning text-dark fw-bold w-100 shadow-sm" onclick="this.innerHTML='<i class=\'fas fa-spinner fa-spin\'></i> Nutriendo Pacientes...'">
                    <i class="fas fa-file-medical me-2"></i> Procesar Datos Educativos Reales
                </button>
            </form>

            <div class="mt-4 pt-4 border-top">
                <h6 class="fw-bold text-muted" style="font-size: 0.8rem;">Estructura de Columnas Esperada:</h6>
                <div class="p-2 bg-light rounded" style="font-size: 0.75rem; white-space: nowrap; overflow-x: auto; color: #555;">
                    1. Apellido/Nombre Alumno | 2. DNI | 3. N° Afiliado | 4. Diagnóstico (CUD) | 5. Nombre de Escuela | 6. Grado M/D | 7. Turno | 8. Horario
                </div>
                <p class="text-danger mt-2" style="font-size: 0.7rem;"><i class="fas fa-exclamation-triangle"></i> Sube esta planilla DESPUÉS de haber cargado el Resumen para que el N° de Afiliado logre atarlos a sus Padres y Terapeutas.</p>
            </div>
        </div>
    </div>
</div>
@endsection
