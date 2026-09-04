<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmergenciasPortalController extends Controller
{
    public function indexDemo()
    {
        $afiliado = (object)[
            'nombre' => 'Carlos Mendoza',
            'dni' => '28.491.022',
            'nro_credencial' => 'INT-88392-01',
            'plan' => 'INTEGRA GLOBAL 100%',
            'direccion_registrada' => 'Av. San Martín 450, Piso 3 A, La Rioja',
            'contacto_emergencia' => 'María (Esposa) - 3804-123456'
        ];

        $emergenciaActiva = (object)[
            'id' => 'SOS-9921',
            'estado' => 'unidad_en_camino',
            'tipo' => 'Código Rojo - Dolor Torácico',
            'unidad' => 'Ambulancia UTI #04 - UMED',
            'chofer_medico' => 'Dr. Fernando Páez / Paramédico J. Gómez',
            'eta_minutos' => 7,
            'fecha_solicitud' => now()->format('H:i:s')
        ];

        return view('afiliado.emergencias', compact('afiliado', 'emergenciaActiva'));
    }

    public function pedirAmbulancia(Request $request)
    {
        return redirect()->back()->with('success', '🚨 ALERTA SOS ENVIADA. La Unidad UTI #04 ha sido despachada hacia su ubicación GPS.');
    }
}
