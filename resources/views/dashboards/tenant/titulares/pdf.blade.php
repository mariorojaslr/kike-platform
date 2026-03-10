<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Padrón de Titulares</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #0f172a; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #0f172a; font-size: 24px; }
        .header p { margin: 5px 0 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background-color: #0f172a; color: #fff; padding: 10px; text-align: left; font-size: 11px; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        tr:nth-child(even) { background-color: #f8fafc; }
        .footer { position: fixed; bottom: -20px; left: 0px; right: 0px; height: 30px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #ddd; padding-top: 5px;}
    </style>
</head>
<body>
    <div class="header">
        <h1>Padrón Oficial de Grupo Familiar (Titulares)</h1>
        <p>Documento Generado el: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre Completo</th>
                <th>DNI</th>
                <th>Contacto</th>
                <th>Fecha Ingreso</th>
            </tr>
        </thead>
        <tbody>
            @foreach($titulares as $titular)
                <tr>
                    <td>{{ $titular->id }}</td>
                    <td><strong>{{ $titular->nombre }}</strong></td>
                    <td>{{ $titular->dni }}</td>
                    <td>{{ $titular->telefono }}<br><small>{{ $titular->email }}</small></td>
                    <td>{{ $titular->created_at ? $titular->created_at->format('d/m/Y') : '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Software KIKE - {{ \Carbon\Carbon::now()->year }} | Documento Confidencial
    </div>
</body>
</html>
