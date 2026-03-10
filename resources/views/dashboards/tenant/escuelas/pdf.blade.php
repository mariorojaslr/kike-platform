<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Padrón Oficial de Escuelas</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #1e293b;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            color: #1e293b;
            text-transform: uppercase;
            font-size: 16px;
        }
        .header p {
            margin: 5px 0 0;
            color: #64748b;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
        }
        tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
            border-top: 1px solid #cbd5e1;
            padding-top: 10px;
        }
        .badge {
            background-color: #dcfce7;
            color: #166534;
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>Padrón Oficial de Instituciones / Escuelas</h2>
        <p>Documento generado el {{ date('d/m/Y') }} a las {{ date('H:i') }} hs</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nombre Institución</th>
                <th>CUE</th>
                <th>Dirección</th>
                <th>Teléfono</th>
                <th>Email Inst.</th>
                <th>Director / Contacto</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($escuelas as $escuela)
            <tr>
                <td style="font-weight: bold; color: #1e293b;">{{ $escuela->nombre }}</td>
                <td>{{ $escuela->cue ?? '-' }}</td>
                <td>{{ $escuela->direccion ?? '-' }}</td>
                <td>{{ $escuela->telefono ?? '-' }}</td>
                <td>{{ $escuela->email ?? '-' }}</td>
                <td>{{ $escuela->contacto_principal ?? '-' }}</td>
                <td>
                    @if($escuela->activo)
                        <span class="badge">Operativa</span>
                    @else
                        <span class="badge badge-danger">Inactiva</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center;">No hay escuelas listadas en la base de datos de esta entidad.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Este documento es información confidencial y uso exclusivo corporativo.<br>
        KIKE SaaS Platform - Módulo de Reportes Institucionales.
    </div>

</body>
</html>
