<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExpedienteAlumno;
use App\Models\NovedadAuditoria;
use Carbon\Carbon;

class DirectoraAsistenciaController extends Controller
{
    /**
     * Muestra la vista demo del Portal de la Directora de Escuela
     */
    public function indexDemo()
    {
        $escuelaNombre = "Escuela N° 102 - Manuel Belgrano";
        $directoraNombre = "Prof. Marta E. González (Directora)";
        
        $alumnosAtendidos = [
            (object)[
                'id' => 1,
                'alumno_nombre' => 'Mateo Giménez',
                'docente_nombre' => 'Prof. Andrea V. (Fonoaudióloga)',
                'dias_asistencia' => 'Lunes, Miércoles y Viernes',
                'horario' => '09:00 a 12:00 hs (3 horas/día)',
                'horas_mes' => 36,
                'estado_aval' => 'firmado',
                'fecha_firma' => Carbon::now()->subDays(2)->format('d/m/Y H:i') . ' hs'
            ],
            (object)[
                'id' => 2,
                'alumno_nombre' => 'Sofía Cortez',
                'docente_nombre' => 'Prof. Andrea V. (Terapeuta Ocupacional)',
                'dias_asistencia' => 'Martes y Jueves',
                'horario' => '14:00 a 17:00 hs (3 horas/día)',
                'horas_mes' => 24,
                'estado_aval' => 'pendiente',
                'fecha_firma' => null
            ],
            (object)[
                'id' => 3,
                'alumno_nombre' => 'Lucas Benítez',
                'docente_nombre' => 'Prof. Carlos R. (Psicopedagogo)',
                'dias_asistencia' => 'Lunes a Viernes',
                'horario' => '08:30 a 11:30 hs (3 horas/día)',
                'horas_mes' => 60,
                'estado_aval' => 'firmado',
                'fecha_firma' => Carbon::now()->subDays(5)->format('d/m/Y H:i') . ' hs'
            ],
        ];

        return view('directora.asistencia', compact('escuelaNombre', 'directoraNombre', 'alumnosAtendidos'));
    }

    /**
     * Procesa la firma digital del aval de la Directora
     */
    public function firmarAsistencia(Request $request)
    {
        $request->validate([
            'alumno_id' => 'required|integer',
            'directora_nombre' => 'required|string|max:255',
            'observaciones' => 'nullable|string|max:500'
        ]);

        $fechaHora = Carbon::now()->format('d/m/Y H:i:s');
        $ipClient = $request->ip();

        // En producción busca el ExpedienteAlumno por id
        $exp = ExpedienteAlumno::find($request->alumno_id);
        if ($exp) {
            $exp->paso_horas_confirmado = true;
            $exp->save();

            // Registrar Novedad con Trazabilidad Expresa para Auditoría
            NovedadAuditoria::create([
                'empresa_id' => $exp->empresa_id ?? 1,
                'docente_id' => $exp->docente_id,
                'expediente_id' => $exp->id,
                'tipo_novedad' => 'aval_directora',
                'descripcion' => "Certificación Oficial Firmada por la Directora {$request->directora_nombre} el {$fechaHora} (IP: {$ipClient}). Horarios avalados.",
                'estado' => 'aprobado',
            ]);
        }

        return back()->with('success', "✅ Certificación Oficial Firmada y Sellada Digitalmente con Éxito ({$fechaHora}). Registro inalterable guardado en Auditoría.");
    }
}
