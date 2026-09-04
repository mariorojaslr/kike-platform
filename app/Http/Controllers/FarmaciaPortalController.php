<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FarmaciaPortalController extends Controller
{
    public function validadorDemo()
    {
        $afiliadoDefault = [
            'nombre' => 'ABAYAY RAMON MARTIN',
            'dni' => '28.452.109',
            'nro_afiliado' => 'MUT-849201-01',
            'plan' => 'Plan Especial Discapacidad & Integración',
            'cobertura_discapacidad' => 100,
            'estado' => 'ACTIVO - AL DÍA',
            'grupo_familiar' => [
                ['nombre' => 'ABAYAY RAMON MARTIN', 'relacion' => 'TITULAR', 'dni' => '28.452.109'],
                ['nombre' => 'ABAYAY MATEO MARTIN', 'relacion' => 'HIJO (DISCAPACIDAD)', 'dni' => '52.104.992'],
                ['nombre' => 'GOMEZ MARIA LAURA', 'relacion' => 'CÓNYUGE', 'dni' => '30.122.540']
            ]
        ];

        $vademecum = [
            [
                'id' => 1,
                'droga' => 'Amoxicilina 500mg (x 16 comp)',
                'laboratorio' => 'Roemmers',
                'pvp' => 12500,
                'cobertura_pct' => 40,
                'categoria' => 'Ambulatorio General'
            ],
            [
                'id' => 2,
                'droga' => 'Losartán 50mg (x 30 comp)',
                'laboratorio' => 'Bagó',
                'pvp' => 18400,
                'cobertura_pct' => 70,
                'categoria' => 'Crónico (Hipertensión)'
            ],
            [
                'id' => 3,
                'droga' => 'Metformina 850mg (x 60 comp)',
                'laboratorio' => 'Gador',
                'pvp' => 22100,
                'cobertura_pct' => 70,
                'categoria' => 'Crónico (Diabetes)'
            ],
            [
                'id' => 4,
                'droga' => 'Insulina Glargina 100 UI/ml (Lapicera prellenada)',
                'laboratorio' => 'Sanofi',
                'pvp' => 64500,
                'cobertura_pct' => 100,
                'categoria' => 'Plan Crónico Prioritario / 100%'
            ],
            [
                'id' => 5,
                'droga' => 'Risperidona 2mg (x 30 comp)',
                'laboratorio' => 'Baliarda',
                'pvp' => 34200,
                'cobertura_pct' => 100,
                'categoria' => 'Plan Especial Discapacidad / CUD'
            ],
            [
                'id' => 6,
                'droga' => 'Paracetamol 1g (x 20 comp)',
                'laboratorio' => 'Raffo',
                'pvp' => 8900,
                'cobertura_pct' => 40,
                'categoria' => 'Ambulatorio General'
            ]
        ];

        return view('pwa.farmacia.validador', compact('afiliadoDefault', 'vademecum'));
    }

    public function validarReceta(Request $request)
    {
        $items = $request->input('items', []);
        $afiliadoDni = $request->input('afiliado_dni', '28.452.109');
        $pacienteNombre = $request->input('paciente_nombre', 'ABAYAY MATEO MARTIN');
        
        $totalPvp = 0;
        $totalMutual = 0;
        $totalAfiliado = 0;
        
        $itemsProcesados = [];
        
        foreach ($items as $item) {
            $pvp = (float)($item['pvp'] ?? 0);
            $pct = (float)($item['cobertura_pct'] ?? 40);
            
            if ($request->input('es_discapacidad') == '1') {
                $pct = 100;
            }
            
            $montoMutual = round($pvp * ($pct / 100), 2);
            $montoAfiliado = round($pvp - $montoMutual, 2);
            
            $totalPvp += $pvp;
            $totalMutual += $montoMutual;
            $totalAfiliado += $montoAfiliado;
            
            $itemsProcesados[] = [
                'droga' => $item['droga'] ?? 'Medicamento',
                'pvp' => $pvp,
                'cobertura_pct' => $pct,
                'monto_mutual' => $montoMutual,
                'monto_afiliado' => $montoAfiliado
            ];
        }
        
        $nroAutorizacion = 'AUT-FAR-' . rand(100000, 999999);
        $fechaHora = date('d/m/Y H:i:s');
        
        return response()->json([
            'success' => true,
            'nro_autorizacion' => $nroAutorizacion,
            'fecha_hora' => $fechaHora,
            'afiliado_dni' => $afiliadoDni,
            'paciente_nombre' => $pacienteNombre,
            'total_pvp' => $totalPvp,
            'total_mutual' => $totalMutual,
            'total_afiliado' => $totalAfiliado,
            'items' => $itemsProcesados
        ]);
    }
}
