<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExpedienteAlumno;
use App\Models\FacturaDocente;
use App\Models\NovedadAuditoria;
use App\Models\Docente;
use App\Services\GeminiService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ExpedienteAlumnoController extends Controller
{
    protected GeminiService $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * Crea o actualiza un expediente de alumno para el docente activo.
     */
    public function store(Request $request)
    {
        $request->validate([
            'alumno_id' => 'required|exists:familiars,id',
            'escuela_id' => 'nullable|exists:escuelas,id',
            'horarios_atencion' => 'nullable|string|max:255',
            'horas_mensuales_asignadas' => 'nullable|integer|min:1|max:10',
            'resolucion_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'certificado_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        $user = Auth::user();
        // Buscar o asumir docente del usuario
        $docente = Docente::where('email', $user->email ?? '')->first() ?? Docente::first();

        if (!$docente) {
            return back()->with('error', 'No se encontró el registro del docente.');
        }

        $resolucionUrl = null;
        $datosIa = null;
        $tieneResolucion = false;

        if ($request->hasFile('resolucion_file')) {
            $path = $request->file('resolucion_file')->store('resoluciones', 'public');
            $resolucionUrl = $path;
            $tieneResolucion = true;
            
            // Analizar con Gemini Vision
            $fullPath = Storage::disk('public')->path($path);
            $iaResult = $this->geminiService->analizarResolucionInstituto($fullPath);
            if ($iaResult['success']) {
                $datosIa = $iaResult['data'];
            }
        }

        $certificadoUrl = null;
        if ($request->hasFile('certificado_file')) {
            $certificadoUrl = $request->file('certificado_file')->store('certificados', 'public');
        }

        $nroRes = $datosIa['nro_resolucion'] ?? $request->nro_resolucion ?? ($tieneResolucion ? ('RES-' . date('Y') . '-' . rand(100, 999)) : null);

        $expediente = ExpedienteAlumno::create([
            'docente_id' => $docente->id,
            'empresa_id' => $docente->empresa_id ?? 1,
            'alumno_id' => $request->alumno_id,
            'escuela_id' => $request->escuela_id,
            'horarios_atencion' => $request->horarios_atencion,
            'horas_mensuales_asignadas' => $request->horas_mensuales_asignadas ?? 3, // Default 3hs
            'origen_resolucion' => $tieneResolucion ? 'externa_papel' : 'pendiente_auditoria',
            'nro_resolucion' => $nroRes,
            'resolucion_url' => $resolucionUrl,
            'resolucion_datos_ia' => $datosIa,
            'certificado_medico_url' => $certificadoUrl,
            'diagnostico' => $datosIa['diagnostico_resumido'] ?? $request->diagnostico ?? null,
            'estado_auditoria' => 'pendiente',
        ]);

        // Registrar Novedad en tiempo real para el auditor
        $tipoNov = $tieneResolucion ? 'nueva_resolucion' : 'solicitud_resolucion_auditoria';
        $descNov = $tieneResolucion 
            ? "El docente {$docente->nombre} cargó la Resolución N° {$nroRes} para el expediente del alumno."
            : "El docente {$docente->nombre} inició expediente del alumno sin resolución. Requiere que Auditoría le consigna/refiera una resolución válida.";

        NovedadAuditoria::create([
            'empresa_id' => $docente->empresa_id ?? 1,
            'docente_id' => $docente->id,
            'expediente_id' => $expediente->id,
            'tipo_novedad' => $tipoNov,
            'descripcion' => $descNov,
            'estado' => 'pendiente',
        ]);

        $mensajeExito = $tieneResolucion 
            ? "Expediente del alumno iniciado con Resolución (analizado con IA). Alerta enviada a Auditoría." 
            : "Expediente iniciado (Vía B). Se solicitó a Auditoría la asignación de Resolución correspondiente.";

        return back()->with('success', $mensajeExito);
    }

    /**
     * Subir Factura ARCA / AFIP para un expediente/alumno.
     */
    public function subirFacturaArca(Request $request)
    {
        $request->validate([
            'expediente_id' => 'required|exists:expedientes_alumno,id',
            'factura_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        $expediente = ExpedienteAlumno::findOrFail($request->expediente_id);
        $path = $request->file('factura_file')->store('facturas_arca', 'public');
        $fullPath = Storage::disk('public')->path($path);

        // Procesar Factura ARCA con Gemini Vision OCR
        $iaResult = $this->geminiService->analizarFacturaArca($fullPath);
        $dataIa = $iaResult['success'] ? $iaResult['data'] : [];

        $factura = FacturaDocente::create([
            'docente_id' => $expediente->docente_id,
            'expediente_id' => $expediente->id,
            'alumno_id' => $expediente->alumno_id,
            'empresa_id' => $expediente->empresa_id,
            'nro_factura' => $dataIa['nro_factura'] ?? null,
            'punto_venta' => $dataIa['punto_venta'] ?? null,
            'cuit_emisor' => $dataIa['cuit_emisor'] ?? null,
            'razon_social_emisor' => $dataIa['razon_social_emisor'] ?? null,
            'domicilio_emisor' => $dataIa['domicilio_emisor'] ?? null,
            'cae' => $dataIa['cae'] ?? null,
            'vencimiento_cae' => $dataIa['vencimiento_cae'] ?? null,
            'monto_total' => $dataIa['monto_total'] ?? 0.00,
            'periodo_mes' => now()->month,
            'periodo_anio' => now()->year,
            'comprobante_url' => $path,
            'qr_raw_data' => $dataIa['qr_raw_data'] ?? null,
            'estado_auditoria' => 'pendiente',
        ]);

        // Registrar Novedad en tiempo real
        NovedadAuditoria::create([
            'empresa_id' => $expediente->empresa_id,
            'docente_id' => $expediente->docente_id,
            'expediente_id' => $expediente->id,
            'factura_id' => $factura->id,
            'tipo_novedad' => 'nueva_factura_arca',
            'descripcion' => "Factura ARCA subida por " . ($dataIa['nro_factura'] ?? 'Docente') . " por $" . number_format($dataIa['monto_total'] ?? 0, 2),
            'estado' => 'pendiente',
        ]);

        return back()->with('success', 'Factura ARCA subida y analizada por IA correctamente. Enviada a Auditoría.');
    }
}
