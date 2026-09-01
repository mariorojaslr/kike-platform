<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PagoEmpresa;
use App\Models\Empresa;
use App\Models\NotificacionSistema;
use App\Services\GeminiService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PagoEmpresaController extends Controller
{
    protected GeminiService $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * Permite a un Tenant/Empresa reportar una transferencia subiendo el comprobante.
     */
    public function reportarPago(Request $request)
    {
        $request->validate([
            'comprobante' => 'required|file|max:15360', // max 15MB
            'empresa_id' => 'required|exists:empresas,id'
        ]);

        $user = Auth::user();
        $empresaId = $request->empresa_id;
        $file = $request->file('comprobante');

        // Almacenamos el archivo localmente o en el disco configurado (Bunny/Public)
        $path = $file->store('comprobantes_pagos', 'public');
        $fullPath = storage_path('app/public/' . $path);

        // Análisis automático con Gemini Vision
        $iaResult = $this->geminiService->analizarComprobantePago($fullPath);
        $extracted = $iaResult['success'] ? $iaResult['data'] : [];

        $montoExtraido = $extracted['monto'] ?? 0.00;
        $nroComprobante = $extracted['nro_comprobante'] ?? ('REF-' . rand(100000, 999999));
        $bancoOrigen = $extracted['banco_origen'] ?? null;
        $fechaPago = !empty($extracted['fecha_pago']) ? $extracted['fecha_pago'] : now();

        $pago = PagoEmpresa::create([
            'empresa_id' => $empresaId,
            'user_id' => $user->id ?? null,
            'monto' => $montoExtraido,
            'nro_comprobante' => $nroComprobante,
            'banco_origen' => $bancoOrigen,
            'fecha_pago' => $fechaPago,
            'comprobante_url' => $path,
            'estado' => 'pendiente_verificacion',
            'datos_extraidos_ia' => $extracted,
        ]);

        // Notificación interna para el Owner
        NotificacionSistema::create([
            'empresa_id' => $empresaId,
            'titulo' => 'Nuevo Comprobante de Pago Recibido',
            'mensaje' => "La empresa #{$empresaId} ha subido un comprobante por $" . number_format($montoExtraido, 2) . " (Op N° {$nroComprobante}).",
            'link' => route('owner.billing')
        ]);

        $montoFormatted = number_format($montoExtraido, 2, ',', '.');
        $mensajeFeedback = "¡Gracias por enviar tu pago! Hemos registrado el comprobante N° {$nroComprobante} por \${$montoFormatted}. Procederemos a verificarlo en nuestras cuentas para dar por finalizada la cobranza.";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $mensajeFeedback,
                'pago' => $pago
            ]);
        }

        return redirect()->back()->with('success', $mensajeFeedback);
    }

    /**
     * El Owner aprueba la transferencia tras verificar en su homebanking.
     */
    public function aprobar(Request $request, PagoEmpresa $pago)
    {
        $pago->estado = 'aprobado';
        $pago->notas_owner = $request->input('notas_owner', 'Aprobado y auditado por el Owner.');
        $pago->save();

        // Actualizamos estado de la cuenta de la empresa a al_dia
        $empresa = $pago->empresa;
        if ($empresa) {
            $empresa->estado_cuenta = 'al_dia';
            $empresa->meses_adeudados = 0;
            $empresa->deuda_actual = 0.00;
            $empresa->save();
        }

        // Notificación interna para los administradores del Tenant
        NotificacionSistema::create([
            'empresa_id' => $pago->empresa_id,
            'titulo' => '✅ Pago Aprobado',
            'mensaje' => "Tu comprobante N° {$pago->nro_comprobante} por $" . number_format($pago->monto, 2) . " ha sido verificado. Tu cuenta se encuentra Al Día.",
            'link' => route('tenant.dashboard')
        ]);

        return redirect()->back()->with('success', "Pago N° {$pago->nro_comprobante} APROBADO exitosamente. La empresa ahora está AL DÍA.");
    }

    /**
     * El Owner rechaza un comprobante no válido.
     */
    public function rechazar(Request $request, PagoEmpresa $pago)
    {
        $pago->estado = 'rechazado';
        $pago->notas_owner = $request->input('notas_owner', 'Comprobante no verificado o inválido.');
        $pago->save();

        NotificacionSistema::create([
            'empresa_id' => $pago->empresa_id,
            'titulo' => '⚠️ Comprobante de Pago Observado',
            'mensaje' => "No pudimos verificar el comprobante N° {$pago->nro_comprobante}. Motivo: " . $pago->notas_owner,
            'link' => route('tenant.dashboard')
        ]);

        return redirect()->back()->with('warning', "Pago marcado como RECHAZADO.");
    }
}
