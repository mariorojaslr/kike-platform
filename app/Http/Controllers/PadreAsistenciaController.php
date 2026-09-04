<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExpedienteAlumno;
use App\Models\NovedadAuditoria;
use Carbon\Carbon;

class PadreAsistenciaController extends Controller
{
    /**
     * Muestra la vista demo del Portal del Padre / Titular (Reintegros y Aval)
     */
    public function indexDemo()
    {
        $padreNombre = "Abayay Ramón Martín (Titular)";
        $hijoNombre = "Abayay Mateo Martín (Alumno)";
        
        $reintegros = [
            (object)[
                'id' => 101,
                'periodo' => 'Septiembre 2026',
                'docente_nombre' => 'Prof. Andrea V.',
                'monto_facturado' => 45000,
                'estado_reintegro' => 'en_auditoria',
                'resolucion_cargada' => true,
                'nro_resolucion' => 'RES-2024-889',
                'aval_padre' => true,
                'fecha_aval_padre' => Carbon::now()->subDays(1)->format('d/m/Y H:i') . ' hs',
                'aval_directora' => true
            ],
            (object)[
                'id' => 102,
                'periodo' => 'Agosto 2026',
                'docente_nombre' => 'Prof. Andrea V.',
                'monto_facturado' => 45000,
                'estado_reintegro' => 'aprobado_para_pago',
                'resolucion_cargada' => true,
                'nro_resolucion' => 'RES-2024-889',
                'aval_padre' => true,
                'fecha_aval_padre' => Carbon::now()->subDays(20)->format('d/m/Y H:i') . ' hs',
                'aval_directora' => true
            ]
        ];

        return view('pwa.padre.dashboard', compact('padreNombre', 'hijoNombre', 'reintegros'));
    }

    /**
     * Confirmación de presencia de la docente por parte del Padre
     */
    public function confirmarAsistencia(Request $request)
    {
        $request->validate([
            'reintegro_id' => 'required|integer',
            'padre_nombre' => 'required|string|max:255'
        ]);

        $fechaHora = Carbon::now()->format('d/m/Y H:i:s');
        $ipClient = $request->ip();

        NovedadAuditoria::create([
            'empresa_id' => 1,
            'docente_id' => 1,
            'expediente_id' => 1,
            'tipo_novedad' => 'aval_padre',
            'descripcion' => "Confirmación de Asistencia Firmada por el Padre/Titular {$request->padre_nombre} el {$fechaHora} (IP: {$ipClient}). El padre avala el servicio prestado.",
            'estado' => 'aprobado',
        ]);

        return back()->with('success', "✅ Confirmación del Padre asentada correctamente ({$fechaHora}). Garantía de cumplimiento registrada.");
    }
}
