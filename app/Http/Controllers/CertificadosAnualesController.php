<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class CertificadosAnualesController extends Controller
{
    /**
     * Muestra el Certificado Oficial de Cobertura y Aportes Anuales para Deducir Impuestos (ARCA)
     */
    public function indexDemo(Request $request)
    {
        $anioFiscal = "2026";
        $afiliado = (object)[
            'nombre' => 'ABAYAY RAMON MARTIN',
            'dni' => '32.456.789',
            'nro_afiliado' => 'MUT-32456789/00',
            'cuit' => '20-32456789-7',
            'plan' => 'PLAN GLOBAL INTEGRAL OSP',
            'fecha_alta' => '15/03/2018'
        ];

        $resumenAportes = (object)[
            'total_aportes_periodo' => 480000.00,
            'meses_cobertura' => 12,
            'grupo_familiar_count' => 3,
            'estado_cuenta' => 'SIN DEUDA - REGULARIZADO',
            'hash_certificado' => md5('CERT_ARCA_' . $afiliado->cuit . '_' . $anioFiscal)
        ];

        return view('pwa.afiliado.certificado_anual', compact('anioFiscal', 'afiliado', 'resumenAportes'));
    }
}
