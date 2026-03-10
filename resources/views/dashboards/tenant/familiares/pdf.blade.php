<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Padrón de Alumnos/Pacientes</title>
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
    </style>
</head>
<body>
    <div class="header">
        <h1>Padrón Oficial de Alumnos / Pacientes</h1>
        <p>Documento Generado el: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nro. Afiliado Social</th>
                <th>Paciente</th>
                <th>DNI</th>
                <th>Titular Responsable</th>
                <th>Diagnóstico</th>
            </tr>
        </thead>
        <tbody>
            @foreach($familiares as $familiar)
                <tr>
                    <td><span class="badge">{{ $familiar->numero_afiliado }}</span></td>
                    <td><strong>{{ $familiar->apellido }}</strong>, {{ $familiar->nombre }}</td>
                    <td>{{ $familiar->dni }}</td>
                    <td>{{ $familiar->titular ? $familiar->titular->apellido . ' ' . $familiar->titular->nombre : 'Sin Titular' }}</td>
                    <td>{{ $familiar->diagnostico ? $familiar->diagnostico->nombre : 'Sin Diagnóstico' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Software KIKE - {{ \Carbon\Carbon::now()->year }} | Documento Confidencial
    </div>
</body>
</html>
