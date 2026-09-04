<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BilleteraMutualController extends Controller
{
    public function indexDemo()
    {
        $billetera = (object)[
            'titular' => 'Guillermo Fernández',
            'cbu_alias' => 'INTEGRA.GUILLERMO.SALUD',
            'saldo_disponible' => 64500,
            'credito_preaprobado' => 300000,
            'cuotas_disponibles' => 12
        ];

        $movimientos = [
            (object)[
                'id' => 'MOV-9921',
                'concepto' => 'Reintegro Liquidación Copago Farmacia',
                'monto' => 18400,
                'tipo' => 'credito',
                'fecha' => '04/09/2026 14:30'
            ],
            (object)[
                'id' => 'MOV-9918',
                'concepto' => 'Pago Copago Telemedicina Dr. Páez',
                'monto' => -3500,
                'tipo' => 'debito',
                'fecha' => '02/09/2026 10:15'
            ],
            (object)[
                'id' => 'MOV-9840',
                'concepto' => 'Acreditación Viáticos Tránsito Córdoba',
                'monto' => 45600,
                'tipo' => 'credito',
                'fecha' => '28/08/2026 18:00'
            ]
        ];

        return view('afiliado.billetera', compact('billetera', 'movimientos'));
    }

    public function solicitarCredito(Request $request)
    {
        $monto = $request->input('monto', 50000);
        return redirect()->back()->with('success', "💳 Préstamo Salud de $$monto acreditado al instante en su Billetera Virtual INTEGRA.");
    }
}
