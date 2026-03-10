<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Gestión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; padding-top: 20px; }
        .card { border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border: none; }
        .navbar-custom { background-color: #1e40af; color: white; border-radius: 10px; margin-bottom: 20px; padding: 15px; }
    </style>
</head>
<body>

<div class="container">
    <div class="navbar-custom d-flex justify-content-between align-items-center">
        <h4 class="mb-0">SISTEMA DE AUDITORÍA</h4>
        <span>{{ Auth::user()->name }}</span>
    </div>

    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="fw-bold text-dark">{{ $titulo }}</h2>
        </div>
        <div class="col-md-4 text-end">
            <button class="btn btn-primary shadow-sm">+ Nuevo Registro</button>
        </div>
    </div>

    <div class="card p-4">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        @foreach($columnas as $col)
                            <th>{{ $col }}</th>
                        @endforeach
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($datos as $registro)
                    <tr>
                        @if(request()->is('docentes'))
                            <td class="fw-bold">{{ $registro->nombre }}</td>
                            <td>{{ $registro->dni }}</td>
                            <td>
                                <span class="badge {{ $registro->validado_auditoria ? 'bg-success' : 'bg-warning text-dark' }}">
                                    {{ $registro->validado_auditoria ? 'Auditado' : 'Pendiente' }}
                                </span>
                            </td>
                        @elseif(request()->is('escuelas'))
                            <td class="fw-bold">{{ $registro->nombre }}</td>
                            <td>{{ $registro->cue }}</td>
                            <td>{{ $registro->direccion }}</td>
                        @elseif(request()->is('beneficiarios'))
                            <td class="fw-bold">{{ $registro->nombre }}</td>
                            <td>{{ $registro->dni }}</td>
                            <td class="text-primary fw-italic">{{ $registro->diagnostico }}</td>
                        @endif
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-primary">Editar</button>
                            <button class="btn btn-sm btn-outline-danger">Borrar</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>

