<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExpedienteAlumno;
use App\Models\Docente;
use Carbon\Carbon;

class ExpedientePdfController extends Controller
{
    /**
     * Muestra o descarga la Rendición Oficial de Expediente con QR de Trazabilidad Expresa
     */
    public function descargarPdf($id = 1)
    {
        $fechaEmision = Carbon::now()->format('d/m/Y H:i:s');

        // Datos de expediente demo para rendición oficial
        $expediente = (object)[
            'id' => $id,
            'periodo' => 'Septiembre 2026',
            'nro_resolucion' => 'RES-2024-889 (Aprobada OSP)',
            'docente_nombre' => 'Prof. Andrea V. (Fonoaudióloga)',
            'docente_dni' => '32.416.914',
            'docente_cuil' => '27-32416914-8',
            'alumno_nombre' => 'Abayay Mateo Martín',
            'alumno_dni' => '54.321.987',
            'titular_nombre' => 'Abayay Ramón Martín',
            'titular_dni' => '32.456.789',
            'escuela_nombre' => 'Escuela N° 102 - Manuel Belgrano',
            'escuela_cue' => '4600102-00',
            'horas_mensuales' => 36,
            'monto_total' => 45000,
            'factura_arca' => 'FAC-C 00001-00000452 (CAE: 74321987654321)',
            'directora_aval' => 'Prof. Marta E. González (Directora)',
            'fecha_aval_directora' => Carbon::now()->subDays(2)->format('d/m/Y H:i') . ' hs',
            'fecha_aval_padre' => Carbon::now()->subDays(1)->format('d/m/Y H:i') . ' hs',
            'auditor_aprobacion' => 'Dr. Enrique Iturralde (Auditoría Central)',
            'hash_trazabilidad' => strtoupper(md5($id . 'INTEGRA_SALT_2026_' . $fechaEmision)),
        ];

        return view('pdf.expediente_oficial', compact('expediente', 'fechaEmision'));
    }
}
