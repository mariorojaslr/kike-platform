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

        // Cargar estructura jerárquica: Nivel 2 (Sucursales / Marcas Blancas Provinciales) -> Nivel 3 (Clientes / Obras Sociales dentro de cada Sucursal)
        $sucursales = Empresa::whereNull('parent_id')
            ->with(['provincia', 'clientes' => function ($q) {
                $q->with('provincia')->withCount('users', 'tickets');
            }])
            ->withCount('users', 'tickets')
            ->get();

        $empresas = Empresa::with('parent', 'provincia')->withCount('users', 'tickets')->get();
        $provincias = \App\Models\Provincia::orderBy('nombre', 'asc')->get();

        return view('dashboards.owner', compact(
            'totalEmpresas', 
            'ticketsAbiertos', 
            'totalUsuariosGlobal', 
            'volumenTotalMb',
            'sucursales',
            'empresas',
            'provincias'
        ));
    }
}
