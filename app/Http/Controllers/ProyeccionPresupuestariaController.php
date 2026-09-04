<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class ProyeccionPresupuestariaController extends Controller
{
    /**
     * Muestra el Tablero de Proyección Epidemiológica y Presupuesto Futuro con IA
     */
    public function indexDemo(Request $request)
    {
        $periodoProyeccion = "2026 / 2027 (Próximos 12 Meses)";
        
        $proyeccionEpidemiologica = [
            (object)[
                'patologia' => 'Trastorno del Espectro Autista (TEA) & Apoyo Escolar',
                'casos_actuales' => 420,
                'casos_proyectados' => 468,
                'gasto_anual_actual' => 990000000.00,
                'gasto_anual_proyectado' => 1103000000.00,
                'variacion' => '+11.4%',
                'nivel_riesgo' => 'MEDIO'
            ],
            (object)[
                'patologia' => 'Oncología & Alta Complejidad Sanatorial',
                'casos_actuales' => 185,
                'casos_proyectados' => 198,
                'gasto_anual_actual' => 1704000000.00,
                'gasto_anual_proyectado' => 1823000000.00,
                'variacion' => '+7.0%',
                'nivel_riesgo' => 'ALTO'
            ],
            (object)[
                'patologia' => 'Cirugías Traumatológicas & Próthesis',
                'casos_actuales' => 310,
                'casos_proyectados' => 315,
                'gasto_anual_actual' => 1060000000.00,
                'gasto_anual_proyectado' => 1077000000.00,
                'variacion' => '+1.6%',
                'nivel_riesgo' => 'ESTABLE'
            ],
            (object)[
                'patologia' => 'Tratamientos Crónicos & Diabetes (Vademécum)',
                'casos_actuales' => 3270,
                'casos_proyectados' => 3450,
                'gasto_anual_actual' => 1357000000.00,
                'gasto_anual_proyectado' => 1431000000.00,
                'variacion' => '+5.5%',
                'nivel_riesgo' => 'CONTROLADO'
            ]
        ];

        return view('dashboards.proyeccion_presupuesto', compact('periodoProyeccion', 'proyeccionEpidemiologica'));
    }
}
