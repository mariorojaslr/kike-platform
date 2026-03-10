<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Padrón de Profesionales</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #0f172a; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #0f172a; font-size: 22px; }
        .header p { margin: 5px 0 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background-color: #0f172a; color: #fff; padding: 10px; text-align: left; font-size: 11px; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        tr:nth-child(even) { background-color: #f8fafc; }
        .badge { background-color: #e2e8f0; padding: 3px 6px; border-radius: 4px; font-size: 9px; font-weight: bold; }
        .footer { position: fixed; bottom: -20px; left: 0px; right: 0px; height: 30px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #ddd; padding-top: 5px;}
        .status-danger { color: #dc3545; font-weight: bold; }
        .status-success { color: #198754; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Padrón Oficial de Docentes y Profesionales</h1>
        <p>Documento Generado el: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }} | Auditoría Laboral</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Profesional</th>
                <th>DNI</th>
                <th>Especialidad / Título</th>
                <th>Contacto</th>
                <th>Estado Documental</th>
            </tr>
        </thead>
        <tbody>
            @foreach($docentes as $docente)
                @php
                    $vencidos = $docente->documentos->filter(fn($doc) => $doc->esta_vencido)->count();
                    $totalDocs = $docente->documentos->count();
                @endphp
                <tr>
                    <td><strong>{{ $docente->nombre }}</strong></td>
                    <td>{{ $docente->dni }}</td>
                    <td><span class="badge">{{ $docente->formacion ? $docente->formacion->nombre : 'Sin Título' }}</span></td>
                    <td>{{ $docente->telefono }}<br><small>{{ $docente->email }}</small></td>
                    <td>
                        @if($totalDocs == 0)
                            <span class="status-danger">100% Incompleta</span>
                        @elseif($vencidos > 0)
                            <span class="status-danger">{{ $vencidos }} Doc(s). Vencidos</span>
                        @else
                            <span class="status-success">Al Día</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Software KIKE - {{ \Carbon\Carbon::now()->year }} | Registro Oficial Auditable
    </div>
</body>
</html>
