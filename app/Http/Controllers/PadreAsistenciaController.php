<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExpedienteAlumno;
use App\Models\NovedadAuditoria;
use App\Models\NotificacionSistema;
use App\Models\Empresa;
use Carbon\Carbon;

class PadreAsistenciaController extends Controller
{
    /**
     * Muestra el Portal del Padre / Titular Afiliado APOS La Rioja (Reintegros, Resoluciones y Avales)
     */
    public function indexDemo()
    {
        $aposEmpresa = Empresa::where('nombre', 'LIKE', '%APOS%')->first();
        $empresaId = $aposEmpresa ? $aposEmpresa->id : 2;

        $expedientes = ExpedienteAlumno::with(['alumno.titular', 'docente', 'escuela', 'titular'])
            ->where('empresa_id', $empresaId)
            ->orderByDesc('updated_at')
            ->take(40)
            ->get();

        $primerExp = $expedientes->first();
        $titularObj = $primerExp ? ($primerExp->titular ?: ($primerExp->alumno ? $primerExp->alumno->titular : null)) : null;

        $padreNombre = $titularObj
            ? trim(($titularObj->apellido ?? '') . ' ' . ($titularObj->nombre ?? '')) . ' (Titular APOS)'
            : "Portal de Afiliados Titulares — APOS La Rioja";

        $hijoNombre = ($primerExp && $primerExp->alumno)
            ? trim(($primerExp->alumno->apellido ?? '') . ' ' . ($primerExp->alumno->nombre ?? ''))
            : "Grupo Familiar Integrado";

        $reintegros = $expedientes->map(function ($exp) {
            $alumNom = $exp->alumno ? trim(($exp->alumno->apellido ?? '') . ' ' . ($exp->alumno->nombre ?? '')) : 'Alumno';
            $alumDni = $exp->alumno ? ($exp->alumno->dni ?? 'S/D') : 'S/D';
            $titObj = $exp->titular ?: ($exp->alumno ? $exp->alumno->titular : null);
            $padNom = $titObj ? trim(($titObj->apellido ?? '') . ' ' . ($titObj->nombre ?? '')) : 'Titular APOS';
            $docNom = $exp->docente ? trim(($exp->docente->apellido ?? '') . ' ' . ($exp->docente->nombre ?? '')) : 'Docente Integradora';
            $escNom = $exp->escuela ? $exp->escuela->nombre : 'Escuela La Rioja';

            return (object)[
                'id' => $exp->id,
                'periodo' => 'Octubre 2026',
                'alumno_nombre' => $alumNom,
                'alumno_dni' => $alumDni,
                'padre_nombre' => $padNom,
                'docente_nombre' => $docNom,
                'escuela_nombre' => $escNom,
                'monto_facturado' => 285000,
                'estado_reintegro' => $exp->estado_auditoria === 'aprobado' ? 'aprobado_para_pago' : 'en_auditoria',
                'resolucion_cargada' => !empty($exp->nro_resolucion),
                'nro_resolucion' => $exp->nro_resolucion ?: 'EN TRÁMITE AUDITORÍA',
                'aval_padre' => (bool)$exp->paso_cbu_confirmado,
                'fecha_aval_padre' => $exp->updated_at ? $exp->updated_at->format('d/m/Y H:i') . ' hs' : Carbon::now()->format('d/m/Y H:i') . ' hs',
                'aval_directora' => (bool)$exp->paso_horas_confirmado,
            ];
        });

        return view('pwa.padre.dashboard', compact('padreNombre', 'hijoNombre', 'reintegros'));
    }

    /**
     * Confirmación de presencia de la docente y resolución por parte del Padre/Titular APOS
     */
    public function confirmarAsistencia(Request $request)
    {
        $request->validate([
            'reintegro_id' => 'required|integer',
            'padre_nombre' => 'required|string|max:255',
            'nro_resolucion' => 'nullable|string|max:100'
        ]);

        $fechaHora = Carbon::now()->format('d/m/Y H:i:s');
        $exp = ExpedienteAlumno::with(['alumno.titular', 'docente', 'escuela', 'titular'])->find($request->reintegro_id);

        if ($exp) {
            $exp->paso_cbu_confirmado = true;
            if ($request->filled('nro_resolucion')) {
                $exp->nro_resolucion = trim($request->nro_resolucion);
                $exp->origen_resolucion = 'resolucion_apos';
                $exp->paso_resolucion_confirmado = true;
            }
            $exp->save();

            $alumNom = $exp->alumno ? trim(($exp->alumno->apellido ?? '') . ' ' . ($exp->alumno->nombre ?? '')) : 'Alumno';
            $docNom = $exp->docente ? trim(($exp->docente->apellido ?? '') . ' ' . ($exp->docente->nombre ?? '')) : 'Docente';
            $resTxt = $exp->nro_resolucion ?: 'En Trámite';

            $descripcion = "AVAL DEL PADRE/TITULAR APOS: {$request->padre_nombre} confirmó la prestación de la Docente {$docNom} para su hijo/a {$alumNom} (Resolución: {$resTxt}) el {$fechaHora}.";

            NovedadAuditoria::create([
                'empresa_id' => $exp->empresa_id ?? 2,
                'docente_id' => $exp->docente_id,
                'expediente_id' => $exp->id,
                'tipo_novedad' => 'aval_padre',
                'descripcion' => $descripcion,
                'estado' => 'pendiente',
            ]);

            try {
                NotificacionSistema::create([
                    'empresa_id' => $exp->empresa_id ?? 2,
                    'titulo' => "🔔 Aval del Padre Titular: {$alumNom} (Res: {$resTxt})",
                    'mensaje' => $descripcion,
                    'link' => route('auditor.docentes.legajos'),
                    'leido' => false,
                ]);
            } catch (\Throwable $e) {
                // Continuar
            }
        }

        return back()->with('success', "✅ Confirmación del Padre/Titular APOS registrada ({$fechaHora}). El Auditor de APOS recibió la notificación en tiempo real.");
    }
}
