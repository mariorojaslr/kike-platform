<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\Docente;
use App\Models\Familiar;
use App\Models\SolicitudAuditoria;
use App\Models\Ticket;
use App\Models\User;

class OwnerDashboardController extends Controller
{
    public function index()
    {
        // Seguridad: Si el usuario autenticado no es owner, lo enviamos a su panel de cliente (SaaS tenant)
        if (auth()->check() && auth()->user()->role !== 'owner') {
            return redirect()->route('tenant.dashboard');
        }

        // Métricas Globales (Owner Snapshot)
        $totalEmpresas = Empresa::count();
        $ticketsAbiertos = Ticket::where('estado', 'abierto')->count();
        $totalUsuariosGlobal = User::count();
        
        // Sumatoria de volumen físico simulada o calculada:
        $volumenTotalMb = Empresa::sum('consumo_actual_mb');

        // Cargar todas las empresas con sus métricas relacionadas para la tabla principal
        // En una app real, Docentes y Familiares tendrían un empresa_id, simularemos el conteo aquí si no lo tienen.
        // Asumiremos que la relación está construida o listamos las empresas simplemente.
        $empresas = Empresa::withCount('users', 'tickets')->get();

        return view('dashboards.owner', compact(
            'totalEmpresas', 
            'ticketsAbiertos', 
            'totalUsuariosGlobal', 
            'volumenTotalMb',
            'empresas'
        ));
    }
}
