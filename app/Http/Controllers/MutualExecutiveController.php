<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MutualExecutiveController extends Controller
{
    /**
     * Dashboard Ejecutivo de la Mutual para la Dirección General y El Dueño
     */
    public function indexDashboard(Request $request)
    {
        $periodo = "Septiembre 2026";
        $capitasTotal = 130000;
        $sucursalSeleccionada = $request->input('sucursal', 'todas');
        
        $sucursales = [
            'todas' => (object)[
                'nombre' => 'Todas las Sedes (Visión Global Mutual)',
                'capitas' => 130000,
                'recaudacion' => 1820000000,
                'gasto' => 680000000,
                'gasto_discapacidad' => 145000000,
                'ahorro_ia' => 92500000,
                'siniestralidad' => 37.3,
                'prestadores' => 840,
                'sanatorios' => 14
            ],
            'chilecito' => (object)[
                'nombre' => 'Sede Chilecito',
                'capitas' => 32500,
                'recaudacion' => 455000000,
                'gasto' => 162000000,
                'gasto_discapacidad' => 68000000,
                'ahorro_ia' => 24500000,
                'siniestralidad' => 35.6,
                'prestadores' => 210,
                'sanatorios' => 3
            ],
            'la_rioja' => (object)[
                'nombre' => 'Sede La Rioja Capital',
                'capitas' => 58000,
                'recaudacion' => 812000000,
                'gasto' => 310000000,
                'gasto_discapacidad' => 52000000,
                'ahorro_ia' => 41000000,
                'siniestralidad' => 38.1,
                'prestadores' => 420,
                'sanatorios' => 6
            ],
            'cordoba' => (object)[
                'nombre' => 'Sede Córdoba (Alta Complejidad)',
                'capitas' => 24000,
                'recaudacion' => 336000000,
                'gasto' => 142000000,
                'gasto_discapacidad' => 18000000,
                'ahorro_ia' => 19000000,
                'siniestralidad' => 42.2,
                'prestadores' => 130,
                'sanatorios' => 3
            ],
            'buenos_aires' => (object)[
                'nombre' => 'Sede Buenos Aires (Derivaciones)',
                'capitas' => 15500,
                'recaudacion' => 217000000,
                'gasto' => 66000000,
                'gasto_discapacidad' => 7000000,
                'ahorro_ia' => 8000000,
                'siniestralidad' => 30.4,
                'prestadores' => 80,
                'sanatorios' => 2
            ]
        ];

        $datosSedeActual = $sucursales[$sucursalSeleccionada] ?? $sucursales['todas'];

        $kpis = (object)[
            'recaudacion_total' => $datosSedeActual->recaudacion,
            'gasto_prestaciones' => $datosSedeActual->gasto,
            'gasto_discapacidad' => $datosSedeActual->gasto_discapacidad,
            'ahorro_auditoria_ia' => $datosSedeActual->ahorro_ia,
            'ratio_siniestralidad' => $datosSedeActual->siniestralidad,
            'prestadores_activos' => $datosSedeActual->prestadores,
            'sanatorios_convenio' => $datosSedeActual->sanatorios,
        ];

        // Totalizadores por Patologías y Costos (Matriz Epidemiológica)
        $patologiasPrevalentes = [
            (object)[
                'enfermedad' => 'Trastorno del Espectro Autista (TEA) & Apoyo Escolar',
                'categoria' => 'Discapacidad / Integración',
                'afiliados_casos' => 420,
                'gasto_total' => 82500000,
                'costo_promedio' => 196428,
                'sede_concentracion' => 'Chilecito (42%)',
                'tendencia' => '+5.2%'
            ],
            (object)[
                'enfermedad' => 'Oncología & Tratamientos de Alta Complejidad',
                'categoria' => 'Alta Complejidad Sanatorial',
                'afiliados_casos' => 185,
                'gasto_total' => 142000000,
                'costo_promedio' => 767567,
                'sede_concentracion' => 'Córdoba (55%)',
                'tendencia' => '+2.1%'
            ],
            (object)[
                'enfermedad' => 'Traumatología & Reemplazos Articulares',
                'categoria' => 'Cirugía Programada',
                'afiliados_casos' => 310,
                'gasto_total' => 88400000,
                'costo_promedio' => 285161,
                'sede_concentracion' => 'La Rioja Capital (46%)',
                'tendencia' => '-1.4%'
            ],
            (object)[
                'enfermedad' => 'Hipertensión Arterial & Riesgo Cardiovascular',
                'categoria' => 'Crónico Preventivo',
                'afiliados_casos' => 1850,
                'gasto_total' => 64200000,
                'costo_promedio' => 34702,
                'sede_concentracion' => 'La Rioja Capital (51%)',
                'tendencia' => '+0.8%'
            ],
            (object)[
                'enfermedad' => 'Diabetes Mellitus Tipo II (Vademécum 70%/100%)',
                'categoria' => 'Crónico / Vademécum',
                'afiliados_casos' => 1420,
                'gasto_total' => 48900000,
                'costo_promedio' => 34436,
                'sede_concentracion' => 'Chilecito (38%)',
                'tendencia' => '+3.0%'
            ]
        ];

        // Totalizadores por Sucursal / Departamento Geográfico
        $totalizadoresSucursales = [
            (object)[
                'codigo' => 'chilecito',
                'nombre' => 'Sede Chilecito',
                'capitas' => 32500,
                'recaudacion' => 455000000,
                'gasto' => 162000000,
                'siniestralidad' => 35.6,
                'prestador_top' => 'Escuela Esp. N° 5 & Clinica Chilecito',
                'patologia_prevalente' => 'TEA & Apoyo Escolar'
            ],
            (object)[
                'codigo' => 'la_rioja',
                'nombre' => 'Sede La Rioja Capital',
                'capitas' => 58000,
                'recaudacion' => 812000000,
                'gasto' => 310000000,
                'siniestralidad' => 38.1,
                'prestador_top' => 'Sanatorio Central San Juan',
                'patologia_prevalente' => 'Traumatología & Cirugías'
            ],
            (object)[
                'codigo' => 'cordoba',
                'nombre' => 'Sede Córdoba',
                'capitas' => 24000,
                'recaudacion' => 336000000,
                'gasto' => 142000000,
                'siniestralidad' => 42.2,
                'prestador_top' => 'Sanatorio Allende & Privado',
                'patologia_prevalente' => 'Oncología & Alta Complejidad'
            ],
            (object)[
                'codigo' => 'buenos_aires',
                'nombre' => 'Sede Buenos Aires',
                'capitas' => 15500,
                'recaudacion' => 217000000,
                'gasto' => 66000000,
                'siniestralidad' => 30.4,
                'prestador_top' => 'Hospital Italiano / Favaloro',
                'patologia_prevalente' => 'Consultas Especializadas'
            ]
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

        return view('dashboards.mutual_executive', compact(
            'periodo',
            'capitasTotal',
            'sucursalSeleccionada',
            'sucursales',
            'datosSedeActual',
            'kpis',
            'patologiasPrevalentes',
            'totalizadoresSucursales',
            'topSanatorios',
            'departamentosGasto'
        ));
    }
}
