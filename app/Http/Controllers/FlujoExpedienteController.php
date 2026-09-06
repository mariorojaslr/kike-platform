<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class FlujoExpedienteController extends Controller
{
    /**
     * Muestra el Gestor del Flujo de Expedientes (Workflow & Pipeline de Estados)
     */
    public function indexDemo(Request $request)
    {
        // 1. Definición del Flujo Estándar de Estados (Pipeline 7 Etapas)
        $etapasWorkflow = [
            (object)[
                'orden' => 1,
                'codigo' => 'iniciado_docente',
                'nombre' => '1. Iniciado por Docente',
                'color' => 'info',
                'icono' => 'fas fa-folder-plus',
                'descripcion' => 'Docente/Terapeuta ingresa legajo del alumno y horario de atención.',
                'requiere' => ['Legajo Alumno', 'Horarios (Máx 3hs/día)']
            ],
            (object)[
                'orden' => 2,
                'codigo' => 'resolucion_osp',
                'nombre' => '2. Resolución OSP Aprobada',
                'color' => 'primary',
                'icono' => 'fas fa-file-contract',
                'descripcion' => 'Verificación de vigencia y dictamen de horas por OSP.',
                'requiere' => ['Resolución OSP en PDF/Foto', 'Dictamen IA']
            ],
            (object)[
                'orden' => 3,
                'codigo' => 'aval_directora',
                'nombre' => '3. Certificación Directora',
                'color' => 'warning',
                'icono' => 'fas fa-school',
                'descripcion' => 'Firma digital de la autoridad escolar que avala la asistencia.',
                'requiere' => ['Firma Directora', 'Control 3hs/día']
            ],
            (object)[
                'orden' => 4,
                'codigo' => 'conformidad_padre',
                'nombre' => '4. Conformidad Padre/Titular',
                'color' => 'secondary',
                'icono' => 'fas fa-user-shield',
                'descripcion' => 'Rúbrica de la familia confirmando la prestación recibida.',
                'requiere' => ['Aval Padre por App/WhatsApp']
            ],
            (object)[
                'orden' => 5,
                'codigo' => 'auditoria_medica',
                'nombre' => '5. Auditoría Médica MD5',
                'color' => 'danger',
                'icono' => 'fas fa-shield-alt',
                'descripcion' => 'Junta médica audita factura ARCA, asistencia y emite sello MD5.',
                'requiere' => ['Factura ARCA Valida', 'Sello MD5']
            ],
            (object)[
                'orden' => 6,
                'codigo' => 'listo_liquidacion',
                'nombre' => '6. Aprobado para Tesorería',
                'color' => 'success',
                'icono' => 'fas fa-wallet',
                'descripcion' => 'Lote enviado a Tesorería para archivo Interbanking CBU.',
                'requiere' => ['CBU/Alias Verificado']
            ],
            (object)[
                'orden' => 7,
                'codigo' => 'transferido_cbu',
                'nombre' => '7. Liquidado / Transferido',
                'color' => 'dark',
                'icono' => 'fas fa-check-double',
                'descripcion' => 'Honorarios acreditados en cuenta bancaria con comprobante.',
                'requiere' => ['Comprobante Home Banking']
            ]
        ];

        // 2. Expedientes Activos en el Pipeline
        $expedientesEnFlujo = [
            (object)[
                'id' => 'EXP-2026-901',
                'alumno' => 'Mateo Giménez',
                'docente' => 'Prof. Andrea V. (Fonoaudióloga)',
                'escuela' => 'Escuela N° 102 - Manuel Belgrano',
                'etapa_actual' => 'auditoria_medica',
                'etapa_nombre' => '5. Auditoría Médica MD5',
                'etapa_orden' => 5,
                'monto' => 145000.00,
                'horas_mes' => 36,
                'resolucion' => 'RES-OSP-4819',
                'aval_directora' => true,
                'aval_padre' => true,
                'factura_arca' => true,
                'fecha_actualizacion' => Carbon::now()->subHours(2)->format('d/m/Y H:i')
            ],
            (object)[
                'id' => 'EXP-2026-902',
                'alumno' => 'Sofía Cortez',
                'docente' => 'Prof. Andrea V. (Terapeuta Ocupacional)',
                'escuela' => 'Escuela N° 102 - Manuel Belgrano',
                'etapa_actual' => 'aval_directora',
                'etapa_nombre' => '3. Certificación Directora',
                'etapa_orden' => 3,
                'monto' => 120000.00,
                'horas_mes' => 24,
                'resolucion' => 'RES-OSP-4820',
                'aval_directora' => false,
                'aval_padre' => true,
                'factura_arca' => true,
                'fecha_actualizacion' => Carbon::now()->subHours(5)->format('d/m/Y H:i')
            ],
            (object)[
                'id' => 'EXP-2026-903',
                'alumno' => 'Lucas Benítez',
                'docente' => 'Prof. Carlos R. (Psicopedagogo)',
                'escuela' => 'Colegio San Martin',
                'etapa_actual' => 'listo_liquidacion',
                'etapa_nombre' => '6. Aprobado para Tesorería',
                'etapa_orden' => 6,
                'monto' => 180000.00,
                'horas_mes' => 60,
                'resolucion' => 'RES-OSP-4799',
                'aval_directora' => true,
                'aval_padre' => true,
                'factura_arca' => true,
                'fecha_actualizacion' => Carbon::now()->subDay()->format('d/m/Y H:i')
            ]
        ];

        return view('expedientes.flujo_workflow', compact('etapasWorkflow', 'expedientesEnFlujo'));
    }

    /**
     * Avanza de etapa un expediente en el flujo de trabajo
     */
    public function avanzarEstado(Request $request, $id)
    {
        $nuevoEstado = $request->input('nuevo_estado', 'Siguiente Etapa');
        return redirect()->back()->with('success', "🚀 Expediente $id avanzado exitosamente a la etapa '$nuevoEstado'.");
    }

    /**
     * Guarda una regla o requisito para una etapa del flujo
     */
    public function guardarEtapaConfig(Request $request)
    {
        return redirect()->back()->with('success', "⚙️ Configuración del Flujo de Expedientes actualizada correctamente.");
    }
}
