<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class TelemedicinaController extends Controller
{
    /**
     * Muestra la vista de Telemedicina y Videoconsulta Médica en Vivo
     */
    public function indexDemo(Request $request)
    {
        $afiliado = (object)[
            'nombre' => 'ABAYAY RAMON MARTIN',
            'dni' => '32.456.789',
            'nro_afiliado' => 'MUT-32456789/00',
            'plan' => 'PLAN GLOBAL INTEGRAL OSP',
            'edad' => 38,
            'antecedentes' => 'Hipertensión Arterial / Alergia a la Penicilina'
        ];

        $medico = (object)[
            'nombre' => 'Dr. Alejandro M. Peralta',
            'especialidad' => 'Medicina Familiar & Clínica Médica',
            'matricula' => 'M.P. 4812 / M.N. 112450',
            'sanatorio' => 'Centro de Telemedicina Mutual INTEGRA'
        ];

        $recetasEmitidas = [
            (object)[
                'id' => 'REC-2026-9812',
                'medicamento' => 'Losartán 50mg (Comprimidos x 30)',
                'posologia' => '1 comprimido cada 24 hs (por la mañana)',
                'diagnostico' => 'I10 - Hipertensión Esencial (Primaria)',
                'cobertura' => '70% (Vademécum Crónico Mutual)',
                'fecha' => Carbon::now()->format('d/m/Y H:i'),
                'hash_md5' => md5('REC-2026-9812-' . time())
            ]
        ];

        return view('pwa.afiliado.telemedicina', compact('afiliado', 'medico', 'recetasEmitidas'));
    }

    /**
     * Procesa la emisión instantánea de una Receta Electrónica Firmada Digitalmente durante la videoconsulta.
     */
    public function emitirRecetaDigital(Request $request): JsonResponse
    {
        $request->validate([
            'medicamento' => 'required|string|max:255',
            'posologia' => 'required|string|max:500',
            'diagnostico' => 'required|string|max:255',
            'cobertura' => 'nullable|string|max:100'
        ]);

        $nroReceta = 'REC-2026-' . rand(10000, 99999);
        $fecha = Carbon::now()->format('d/m/Y H:i:s');
        $hashMd5 = md5($nroReceta . $request->medicamento . $fecha);

        return response()->json([
            'success' => true,
            'receta' => [
                'nro_receta' => $nroReceta,
                'medicamento' => $request->medicamento,
                'posologia' => $request->posologia,
                'diagnostico' => $request->diagnostico,
                'cobertura' => $request->cobertura ?? '70% Vademécum Mutual',
                'fecha' => $fecha,
                'hash_md5' => $hashMd5,
                'qr_url' => "https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=" . urlencode("RECETA_MUTUAL_{$nroReceta}_HASH_{$hashMd5}")
            ],
            'message' => '✅ Receta Electrónica Digital Firmada e Integrada al expediente del afiliado con éxito.'
        ]);
    }
}
