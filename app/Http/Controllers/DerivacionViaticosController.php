<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class DerivacionViaticosController extends Controller
{
    /**
     * Muestra el módulo de Derivaciones y Viáticos de Alta Complejidad
     */
    public function indexDemo(Request $request)
    {
        $derivacionesActivas = [
            (object)[
                'id' => 'DER-2026-4412',
                'afiliado_nombre' => 'GUZMÁN LUCÍA VALENTINA',
                'afiliado_dni' => '41.890.123',
                'nro_afiliado' => 'MUT-41890123/00',
                'destino' => 'Sede Córdoba (Alta Complejidad)',
                'centro_medico' => 'Sanatorio Allende - Nueva Córdoba',
                'diagnostico' => 'Cirugía Cardíaca de Alta Complejidad',
                'cobertura_alojamiento' => 'Hotel Convenio Mutual Córdoba (7 días)',
                'monto_viaticos' => 145000.00,
                'estado' => 'autorizado',
                'fecha_salida' => Carbon::now()->addDays(2)->format('d/m/Y')
            ],
            (object)[
                'id' => 'DER-2026-3891',
                'afiliado_nombre' => 'PÁEZ HÉCTOR ROBERTO',
                'afiliado_dni' => '28.341.654',
                'nro_afiliado' => 'MUT-28341654/00',
                'destino' => 'Sede Buenos Aires (Derivación Nacional)',
                'centro_medico' => 'Hospital Italiano de Buenos Aires',
                'diagnostico' => 'Evaluación Oncológica Especializada',
                'cobertura_alojamiento' => 'Residencia Sanatorial Central (5 días)',
                'monto_viaticos' => 195000.00,
                'estado' => 'autorizado',
                'fecha_salida' => Carbon::now()->addDays(5)->format('d/m/Y')
            ]
        ];

        return view('pwa.afiliado.derivaciones', compact('derivacionesActivas'));
    }

    /**
     * Emite una Credencial Digital Provisoria de Tránsito para Derivación Sanatorial.
     */
    public function emitirVoucherTransito(Request $request): JsonResponse
    {
        $request->validate([
            'afiliado_nombre' => 'required|string|max:255',
            'destino' => 'required|string|max:255',
            'dias_alojamiento' => 'required|integer'
        ]);

        $voucherCode = 'TRANSITO-' . rand(10000, 99999);
        $hashMd5 = md5($voucherCode . time());

        return response()->json([
            'success' => true,
            'voucher' => [
                'codigo' => $voucherCode,
                'afiliado' => $request->afiliado_nombre,
                'destino' => $request->destino,
                'cobertura_alojamiento' => "Hotel Convenio Mutual ({$request->dias_alojamiento} días autorizados)",
                'monto_viaticos' => '$145.000,00',
                'hash_md5' => $hashMd5,
                'qr_url' => "https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=" . urlencode("VOUCHER_TRANSITO_{$voucherCode}_HASH_{$hashMd5}")
            ],
            'message' => '✅ Credencial Provisoria de Tránsito y Vales de Viático generados con éxito.'
        ]);
    }
}
