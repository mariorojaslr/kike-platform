<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Empresa;
use App\Models\Provincia;
use App\Models\Localidad;

class TenantDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Verificar si el usuario logueado realmente pertenece a una Empresa (Tenant)
        // Esto evita que un usuario suelto ("huerfano") intente entrar a ver datos de instituciones
        if (!$user->empresa_id) {
            return redirect('/')->with('error', 'Su cuenta no está vinculada a ninguna Clínica/Institución.');
        }

        // 2. Cargar los datos maestros de la Empresa 
        // Esta variable $empresa viajará a la vista para dictar el Nombre, Logo y los Colores (Marca Blanca)
        $empresa = Empresa::findOrFail($user->empresa_id);

        // 3. Obtener Provincias para el Setup Geográfico
        $provincias = Provincia::orderBy('nombre', 'asc')->get();
        // Obtener Localidades si la empresa ya tiene una provincia seleccionada previamente
        $localidadesEmpresa = [];
        if ($empresa->provincia_id) {
            $localidadesEmpresa = Localidad::where('provincia_id', $empresa->provincia_id)->orderBy('nombre', 'asc')->get();
        }

        // 4. Preparar variables del Ecosistema para poblar los medidores (KPIs de la vista)
        $totalEscuelas = \App\Models\Escuela::where('empresa_id', $empresa->id)->count(); 
        $totalAfiliados = \App\Models\Titular::where('empresa_id', $empresa->id)->count();
        $totalDocentes = \App\Models\Docente::where('empresa_id', $empresa->id)->count();
        $totalFamiliares = \App\Models\Familiar::where('empresa_id', $empresa->id)->count();

        // 5. Retornar vista "tenant" (El Cockpit del administrador de la clínica)
        return view('dashboards.tenant', compact('empresa', 'totalEscuelas', 'totalAfiliados', 'totalDocentes', 'totalFamiliares', 'provincias', 'localidadesEmpresa'));
    }

    /**
     * Guarda la personalización de la Empresa (Colores, Logo, Ubicación)
     */
    public function updateSetup(Request $request)
    {
        $user = Auth::user();
        $empresa = Empresa::findOrFail($user->empresa_id);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'color_primario' => 'nullable|string|max:7',
            'color_secundario' => 'nullable|string|max:7',
            'provincia_id' => 'nullable|exists:provincias,id',
            'localidad_id' => 'nullable|exists:localidades,id',
            'logo' => 'nullable|image|max:2048' // Máx 2MB
        ]);

        $empresa->nombre = $request->nombre;
        $empresa->color_primario = $request->color_primario;
        $empresa->color_secundario = $request->color_secundario;
        $empresa->provincia_id = $request->provincia_id;
        $empresa->localidad_id = $request->localidad_id;

        if ($request->hasFile('logo')) {
            // Guardar en Storage local por ahora, preparado para BunnyCDN a futuro
            $path = $request->file('logo')->store('logos_empresas', 'public');
            $empresa->logo = $path;
        }

        $empresa->save();

        return redirect()->back()->with('success', 'Configuración de Marca y Ubicación actualizada guardada correctamente.');
    }
}
