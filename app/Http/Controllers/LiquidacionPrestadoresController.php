<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class LiquidacionPrestadoresController extends Controller
{
    /**
     * Muestra el Tablero de Cierre de Liquidaciones y Billeteras de Prestadores
     */
    public function indexDemo(Request $request)
    {
        $periodoActual = "Septiembre 2026";
        
        $resumenGlobal = (object)[
            'total_a_liquidar' => 184500000.00,
            'liquidado_aprobado' => 142800000.00,
            'en_auditoria' => 41700000.00,
            'prestadores_count' => 840,
            'lotes_procesados' => 12
        ];

        $liquidacionesPrestadores = [
            (object)[
                'id' => 101,
                'prestador_nombre' => 'Sanatorio Central San Juan',
                'tipo' => 'Sanatorio / Alta Complejidad',
                'cuit' => '30-71234567-8',
                'cbu_alias' => 'SANATORIO.SANJUAN.MP',
                'banco' => 'Banco Santander',
                'monto_bruto' => 85200000.00,
                'retenciones' => 4260000.00,
                'monto_neto' => 80940000.00,
                'estado' => 'aprobado_para_pago',
                'fecha_cierre' => Carbon::now()->subDays(1)->format('d/m/Y')
            ],
            (object)[
                'id' => 102,
                'prestador_nombre' => 'Red de Farmacias Mutual Convenidas',
                'tipo' => 'Farmacia / Vademécum',
                'cuit' => '30-68912345-2',
                'cbu_alias' => 'FARMACIAS.MUTUAL.RIOJA',
                'banco' => 'Banco Macro',
                'monto_bruto' => 48900000.00,
                'retenciones' => 2445000.00,
                'monto_neto' => 46455000.00,
                'estado' => 'aprobado_para_pago',
                'fecha_cierre' => Carbon::now()->subDays(2)->format('d/m/Y')
            ],
            (object)[
                'id' => 103,
                'prestador_nombre' => 'Colegio de Terapeutas & Maestras Integradoras',
                'tipo' => 'Educación Especial / Discapacidad',
                'cuit' => '30-54123987-9',
                'cbu_alias' => 'HONORARIOS.DOCENTES.OSP',
                'banco' => 'Banco de la Nación Argentina',
                'monto_bruto' => 38400000.00,
                'retenciones' => 0.00,
                'monto_neto' => 38400000.00,
                'estado' => 'en_revision_auditoria',
                'fecha_cierre' => Carbon::now()->format('d/m/Y')
            ]
        ];

        return view('dashboards.liquidacion_prestadores', compact('periodoActual', 'resumenGlobal', 'liquidacionesPrestadores'));
    }

    /**
     * Procesa la firma y acreditación masiva del lote de liquidación mensual.
     */
    public function procesarCierreLiquidacion(Request $request): JsonResponse
    {
        $loteId = 'LOTE-PAY-' . Carbon::now()->format('Ym') . '-' . rand(100, 999);
        $fecha = Carbon::now()->format('d/m/Y H:i:s');
        $hashSeguridad = md5($loteId . $fecha);

        return response()->json([
            'success' => true,
            'lote_id' => $loteId,
            'monto_total' => '$142.800.000,00',
            'fecha_cierre' => $fecha,
            'hash_seguridad' => $hashSeguridad,
            'message' => "✅ Cierre de Liquidación Masivo procesado con éxito (Lote {$loteId}). En enviado al Home Banking Institucional para acreditación instantánea."
        ]);
    }

    /**
     * Genera y descarga el archivo oficial Interbanking CBU/Alias para Home Banking.
     */
    public function descargarInterbankingTxt(Request $request)
    {
        $fecha = Carbon::now()->format('Ymd');
        $filename = "INTERBANKING_INTEGRA_PAYOUT_{$fecha}.txt";
        
        $contenido = "072000000000000000000030712345678SANATORIO CENTRAL SAN JUAN       000008094000000ARS\r\n"
                   . "011000000000000000000030689123452RED DE FARMACIAS MUTUAL          000004645500000ARS\r\n"
                   . "007000000000000000000030541239879COLEGIO DE TERAPEUTAS DOCENTES    000003840000000ARS\r\n";

        return response($contenido, 200, [
            'Content-Type' => 'text/plain',
            'Content-Disposition' => "attachment; filename=\"{$filename}\""
        ]);
    }
}
