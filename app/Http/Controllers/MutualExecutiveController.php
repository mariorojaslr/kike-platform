<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class MutualExecutiveController extends Controller
{
    /**
     * Dashboard Ejecutivo de la Mutual para la Dirección General y El Dueño
     */
    public function indexDashboard()
    {
        $periodo = "Septiembre 2026";
        $capitasTotal = 130000;
        
        $kpis = (object)[
            'recaudacion_total' => 1820000000, // $1.820M
            'gasto_prestaciones' => 680000000,  // $680M
            'gasto_discapacidad' => 145000000,  // $145M
            'ahorro_auditoria_ia' => 92500000,  // $92.5M
            'ratio_siniestralidad' => 37.3,     // % Siniestralidad
            'prestadores_activos' => 840,
            'sanatorios_convenio' => 14,
        ];

        $topSanatorios = [
            (object)['nombre' => 'Sanatorio Central San Juan', 'internaciones' => 84, 'monto' => 185000000, 'satisfaccion' => 98],
            (object)['nombre' => 'Instituto de Traumatología y Quirófano', 'internaciones' => 45, 'monto' => 124000000, 'satisfaccion' => 96],
            (object)['nombre' => 'Clínica de la Especialidad & Maternidad', 'internaciones' => 62, 'monto' => 142000000, 'satisfaccion' => 99],
            (object)['nombre' => 'Centro de Diagnóstico por Imagen RMN/TAC', 'internaciones' => 0, 'monto' => 78000000, 'satisfaccion' => 97],
        ];

        $departamentosGasto = [
            (object)['nombre' => 'Internaciones y Sanatorios', 'monto' => 451000000, 'porcentaje' => 54.6],
            (object)['nombre' => 'Discapacidad y Apoyo Escolar', 'monto' => 145000000, 'porcentaje' => 17.5],
            (object)['nombre' => 'Consultas Médicas y Especialistas', 'monto' => 128000000, 'porcentaje' => 15.5],
            (object)['nombre' => 'Farmacia y Medicamentos Especiales', 'monto' => 101000000, 'porcentaje' => 12.4],
        ];

        return view('dashboards.mutual_executive', compact('periodo', 'capitasTotal', 'kpis', 'topSanatorios', 'departamentosGasto'));
    }
}
