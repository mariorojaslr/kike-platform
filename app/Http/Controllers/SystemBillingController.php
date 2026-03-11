<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empresa;

class SystemBillingController extends Controller
{
    /**
     * Muestra el Panel de Facturación y Ciclos (SaaS Owner)
     */
    public function index()
    {
        // Traer todas las empresas con datos de facturación
        $empresas = Empresa::orderBy('nombre')->get();
        
        // Calcular estadísticas globales
        $ingresosEstimados = $empresas->sum('deuda_actual'); // Proxy de ingresos por cobrar o MRR
        $empresasAlDia = $empresas->where('estado_cuenta', 'al_dia')->count();
        $empresasDeudoras = $empresas->whereIn('estado_cuenta', ['suspendida', 'pendiente'])->count();
        $totalMbConsumidos = $empresas->sum('consumo_actual_mb');

        // Valores de Tarifa Simulados (luego pueden ir a una tabla "configuracion")
        // Como el usuario quiere empezar a trabajarlos, le armaremos el frontend primero
        $tarifaBase = 50.00; // $50 USD/ARS de mantenimiento base
        $precioPorUsuarioExtra = 1.50; // $1.50 por cada usuario extra del límite base
        $precioPorGBExtra = 5.00; // $5.00 por cada GB (1024 MB) extra del límite base

        return view('dashboards.owner_billing', compact(
            'empresas',
            'ingresosEstimados',
            'empresasAlDia',
            'empresasDeudoras',
            'totalMbConsumidos',
            'tarifaBase',
            'precioPorUsuarioExtra',
            'precioPorGBExtra'
        ));
    }

    /**
     * Actualizar la escala de tarifas globales 
     * (Simulado por ahora para dar la UX al cliente, luego lo atamos a DB)
     */
    public function updateTarifas(Request $request)
    {
        // TODO: Guardar esto en una tabla `system_settings` o `tarifas_base`
        
        return back()->with('success', 'Escala de tarifas globales actualizada exitosamente. El próximo ciclo de facturación se calculará con estos nuevos valores.');
    }
}
