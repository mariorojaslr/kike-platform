<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class AfiliadoPortalController extends Controller
{
    /**
     * Vista de la Credencial Digital Oficial del Afiliado con QR Dinámico
     */
    public function credencialDemo()
    {
        $afiliado = (object)[
            'nombre' => 'ABAYAY RAMON MARTIN',
            'dni' => '32.456.789',
            'nro_afiliado' => 'MUT-32456789/00',
            'plan' => 'PLAN GLOBAL INTEGRAL OSP',
            'estado' => 'ACTIVO VIGENTE',
            'categoria' => 'TITULAR OBLIGATORIO',
            'fecha_vencimiento' => Carbon::now()->addYear()->format('m/Y'),
            'token_dinamico' => rand(100000, 999999),
            'grupo_familiar' => [
                (object)['nombre' => 'ABAYAY MATEO MARTIN', 'dni' => '54.321.987', 'parentesco' => 'Hijo / Adherente'],
                (object)['nombre' => 'CORTEZ MARIA ELENA', 'dni' => '33.123.456', 'parentesco' => 'Cónyuge']
            ]
        ];

        return view('pwa.afiliado.credencial', compact('afiliado'));
    }

    /**
     * Vista de Cartilla Médica y Reserva de Turnos
     */
    public function cartillaTurnos()
    {
        $especialidades = [
            (object)['nombre' => 'Pediatría y Salud Infantil', 'icon' => 'fa-child', 'prestadores_count' => 42],
            (object)['nombre' => 'Traumatología y Ortopedia', 'icon' => 'fa-bone', 'prestadores_count' => 28],
            (object)['nombre' => 'Cardiología e Imágenes', 'icon' => 'fa-heartbeat', 'prestadores_count' => 35],
            (object)['nombre' => 'Fonoaudiología y Terapia Ocupacional', 'icon' => 'fa-hands-helping', 'prestadores_count' => 64],
            (object)['nombre' => 'Clínica Médica General', 'icon' => 'fa-stethoscope', 'prestadores_count' => 110],
        ];

        $turnosProximos = [
            (object)[
                'id' => 801,
                'medico' => 'Dr. Marcelo Páez (Traumatólogo)',
                'sanatorio' => 'Sanatorio Central San Juan',
                'fecha_hora' => Carbon::now()->addDays(3)->format('d/m/Y') . ' a las 10:30 hs',
                'estado' => 'confirmado'
            ]
        ];

        return view('pwa.afiliado.turnos', compact('especialidades', 'turnosProximos'));
    }
}
