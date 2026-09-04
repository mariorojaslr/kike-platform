<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class AuditoriaCentralController extends Controller
{
    /**
     * Muestra el Hub de Auditoría Médica Central de Alta Complejidad
     */
    public function indexDemo(Request $request)
    {
        $periodoAuditoria = "Septiembre 2026";
        $auditorActual = (object)[
            'nombre' => 'Dr. Eduardo R. Benítez',
            'cargo' => 'Auditor Médico Central - Mutual INTEGRA',
            'matricula' => 'M.P. 5120 / M.N. 98120'
        ];

        $solicitudesAuditoria = [
            (object)[
                'id' => 'AUD-2026-7810',
                'afiliado_nombre' => 'DÍAZ ROBERTO CARLOS',
                'afiliado_dni' => '22.145.890',
                'nro_afiliado' => 'MUT-22145890/00',
                'prestador' => 'Sanatorio Allende Córdoba',
                'practica' => 'Reemplazo Total de Cadera (Próthesis Importada)',
                'codigo_cie10' => 'M16.1 - Artrosis Primaria de Cadera',
                'monto_estimado' => 4850000.00,
                'estado' => 'pendiente',
                'fecha_solicitud' => Carbon::now()->subDays(1)->format('d/m/Y H:i')
            ],
            (object)[
                'id' => 'AUD-2026-7809',
                'afiliado_nombre' => 'SILVA MARÍA INÉS',
                'afiliado_dni' => '31.542.109',
                'nro_afiliado' => 'MUT-31542109/00',
                'prestador' => 'Hospital Italiano Buenos Aires',
                'practica' => 'Tratamiento Oncológico de Alta Complejidad',
                'codigo_cie10' => 'C50 - Tumor Maligno de Mama',
                'monto_estimado' => 8900000.00,
                'estado' => 'aprobado',
                'fecha_solicitud' => Carbon::now()->subDays(3)->format('d/m/Y H:i')
            ]
        ];

        return view('auditoria.central', compact('periodoAuditoria', 'auditorActual', 'solicitudesAuditoria'));
    }

    /**
     * Procesa la resolución de auditoría médica (Aprobación o Rechazo con Firma Criptográfica MD5).
     */
    public function procesarAuditoria(Request $request): JsonResponse
    {
        $request->validate([
            'solicitud_id' => 'required|string',
            'dictamen' => 'required|in:aprobado,rechazado,observado',
            'observaciones' => 'nullable|string|max:500'
        ]);

        $fecha = Carbon::now()->format('d/m/Y H:i:s');
        $hashMd5 = md5($request->solicitud_id . $request->dictamen . $fecha);

        return response()->json([
            'success' => true,
            'solicitud_id' => $request->solicitud_id,
            'dictamen' => strtoupper($request->dictamen),
            'fecha' => $fecha,
            'hash_md5' => $hashMd5,
            'message' => "✅ Dictamen de Auditoría Médica asentado con éxito ({$request->dictamen}). Trazabilidad criptográfica asentada con Hash MD5: {$hashMd5}"
        ]);
    }
}
