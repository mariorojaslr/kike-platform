<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Factura;

class TerapeutaController extends Controller
{
    /**
     * Devuelve la pantalla de inicio de la App Móvil (PWA) para el docente
     */
    public function index()
    {
        $user = Auth::user();
        // Cargamos todas las facturas pasadas de este usuario para el historial inferior
        $facturas = Factura::where('user_id', $user->id)->orderBy('created_at', 'desc')->take(5)->get();
        
        return view('mobile.terapeuta_dashboard', compact('user', 'facturas'));
    }

    /**
     * Recibe la foto y los datos del QR desde el celular
     * Etapa de Staging (Se guarda en disco local primero)
     */
    public function storeFactura(Request $request)
    {
        $request->validate([
            'fotoFactura' => 'required|image|max:10240', // Max 10MB
            'qrData' => 'nullable|string'
        ]);

        try {
            $user = Auth::user();
            
            // 1. Guardar localmente en storage/app/public/facturas_staging (luego pasará a Bunny.net)
            $path = $request->file('fotoFactura')->store("facturas_staging/{$user->empresa_id}/{$user->id}", 'public');

            // 2. Crear registro en Base de Datos
            $factura = Factura::create([
                'empresa_id' => $user->empresa_id,
                'user_id' => $user->id,
                'qr_data' => $request->qrData,
                'imagen_url' => $path,
                'storage_disk' => 'local',
                'estado' => 'pendiente' // Queda a la espera del Auditor
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Factura subida exitosamente y enviada a Auditoría.',
                'factura_id' => $factura->id
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la factura: ' . $e->getMessage()
            ], 500);
        }
    }
}
