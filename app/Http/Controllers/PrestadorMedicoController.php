<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class PrestadorMedicoController extends Controller
{
    /**
     * Portal Demo para Médicos Particulares, Especialistas y Clínicas
     */
    public function indexDemo()
    {
        $prestadorNombre = "Sanatorio Central San Juan & Dr. Marcelo Páez";
        $cápitasActivas = "130.000 Abonados Mutual";

        $ordenesSolicitadas = [
            (object)[
                'id' => 501,
                'afiliado_nombre' => 'Abayay Ramón Martín',
                'afiliado_nro' => 'MUT-32456789/00',
                'practica_codigo' => '42.01.01 - Resonancia Magnética Nuclear de Cerebro',
                'tipo' => 'Ambulatoria Especializada',
                'monto' => 38000,
                'estado' => 'aprobado',
                'fecha_solicitud' => Carbon::now()->subHours(3)->format('d/m/Y H:i') . ' hs'
            ],
            (object)[
                'id' => 502,
                'afiliado_nombre' => 'Giménez Mateo',
                'afiliado_nro' => 'MUT-52345678/01',
                'practica_codigo' => '34.02.05 - Internación en Sala General (3 Días de Cama)',
                'tipo' => 'Sanatorio / Internación',
                'monto' => 125000,
                'estado' => 'en_auditoria',
                'fecha_solicitud' => Carbon::now()->subMinutes(45)->format('d/m/Y H:i') . ' hs'
            ],
            (object)[
                'id' => 503,
                'afiliado_nombre' => 'Cortez Sofía',
                'afiliado_nro' => 'MUT-54123987/01',
                'practica_codigo' => '18.01.02 - Consulta Médica en Consultorio',
                'tipo' => 'Consulta Médica',
                'monto' => 8500,
                'estado' => 'aprobado',
                'fecha_solicitud' => Carbon::now()->subDays(1)->format('d/m/Y H:i') . ' hs'
            ]
        ];

        return view('prestadores.dashboard', compact('prestadorNombre', 'cápitasActivas', 'ordenesSolicitadas'));
    }

    /**
     * Panel de Auditoría Médica Central de la Mutual
     */
    public function auditoriaIndex()
    {
        $auditorMedico = "Dr. Roberto E. Ferrero (Jefe de Auditoría Médica Mutual)";

        $solicitudesAuditoria = [
            (object)[
                'id' => 502,
                'afiliado_nombre' => 'Giménez Mateo',
                'afiliado_nro' => 'MUT-52345678/01',
                'prestador' => 'Sanatorio Central San Juan',
                'practica' => 'Internación en Sala General (3 Días de Cama) + Laboratorio Completo',
                'diagnostico_cie10' => 'J18.9 - Neumonía no especificada',
                'monto_presupuestado' => 125000,
                'estado' => 'pendiente',
                'fecha' => Carbon::now()->subMinutes(45)->format('d/m/Y H:i')
            ],
            (object)[
                'id' => 504,
                'afiliado_nombre' => 'Pereyra Noelia',
                'afiliado_nro' => 'MUT-28896170/00',
                'prestador' => 'Instituto de Traumatología',
                'practica' => 'Cirugía Artroscópica de Rodilla con Prótesis',
                'diagnostico_cie10' => 'M23.2 - Trastorno de menisco por desgarro',
                'monto_presupuestado' => 480000,
                'estado' => 'pendiente',
                'fecha' => Carbon::now()->subHours(2)->format('d/m/Y H:i')
            ]
        ];

        return view('auditoria.prestaciones_medicas', compact('auditorMedico', 'solicitudesAuditoria'));
    }

    /**
     * Aprueba la orden médica o internación desde auditoría
     */
    public function autorizarPractica(Request $request, $id)
    {
        $fechaHora = Carbon::now()->format('d/m/Y H:i:s');
        return back()->with('success', "✅ Práctica u Orden N° {$id} AUTORIZADA en Tiempo Real por Auditoría Médica el {$fechaHora}. Orden emitida para cobro del prestador.");
    }
}
