<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Empresa;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OwnerUserController extends Controller
{
    /**
     * Muestra la gestión centralizada de usuarios y credenciales para el Owner.
     */
    public function index(Request $request)
    {
        $query = User::with('empresa');

        // Filtro por Empresa
        if ($request->filled('empresa_id')) {
            $query->where('empresa_id', $request->empresa_id);
        }

        // Filtro por Rol
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Buscador por nombre o email
        if ($request->filled('q')) {
            $term = trim($request->q);
            $query->where(function ($q) use ($term) {
                $q->where('name', 'LIKE', "%{$term}%")
                  ->orWhere('email', 'LIKE', "%{$term}%");
            });
        }

        $usuarios = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();

        // Empresas para los combos desplegables
        $empresas = Empresa::orderBy('nombre', 'asc')->get();

        // Métricas e Indicadores KPI
        $totalUsuarios = User::count();
        $totalAdmins = User::whereIn('role', ['empresa', 'tenant', 'admin'])->count();
        $totalAuditores = User::where('role', 'auditor')->count();
        
        // Contar empresas que no tienen ningún usuario administrador
        $empresasConAdminIds = User::whereIn('role', ['empresa', 'tenant'])
            ->whereNotNull('empresa_id')
            ->pluck('empresa_id')
            ->unique();
            
        $empresasSinAdminCount = $empresas->whereNotIn('id', $empresasConAdminIds)->count();

        return view('dashboards.owner_usuarios', compact(
            'usuarios',
            'empresas',
            'totalUsuarios',
            'totalAdmins',
            'totalAuditores',
            'empresasSinAdminCount'
        ));
    }

    /**
     * Crea un nuevo usuario asignado a una empresa (Admin, Auditor o Staff).
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:empresa,auditor,docente',
            'empresa_id' => 'required|exists:empresas,id',
            'password' => 'nullable|string|min:6',
        ]);

        $plainPassword = $request->filled('password') ? $request->password : Str::random(8);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($plainPassword),
            'role' => $request->role,
            'empresa_id' => $request->empresa_id,
        ]);

        $roleLabel = match($user->role) {
            'empresa' => 'Administrador de Empresa',
            'auditor' => 'Auditor de Empresa',
            'docente' => 'Personal / Docente',
            default => $user->role
        };

        return back()->with('success', "Usuario '{$user->name}' ({$roleLabel}) creado exitosamente.")
                     ->with('credentials_created', [
                         'name' => $user->name,
                         'email' => $user->email,
                         'password' => $plainPassword,
                         'role' => $roleLabel,
                         'empresa' => $user->empresa->nombre ?? 'Sin empresa'
                     ]);
    }

    /**
     * Resetea la contraseña de cualquier usuario.
     */
    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'new_password' => 'nullable|string|min:6',
        ]);

        $plainPassword = $request->filled('new_password') ? $request->new_password : Str::random(8);

        $user->password = Hash::make($plainPassword);
        $user->save();

        return back()->with('success', "Contraseña de '{$user->name}' ({$user->email}) actualizada exitosamente.")
                     ->with('credentials_updated', [
                         'name' => $user->name,
                         'email' => $user->email,
                         'password' => $plainPassword,
                         'empresa' => $user->empresa->nombre ?? 'Global / Master'
                     ]);
    }

    /**
     * Elimina un usuario del sistema.
     */
    public function destroy(User $user)
    {
        // Evitar que el Owner se elimine a sí mismo
        if ($user->id === auth()->id() || $user->role === 'owner') {
            return back()->with('error', 'No puedes eliminar la cuenta de Propietario Master.');
        }

        $nombre = $user->name;
        $user->delete();

        return back()->with('success', "Usuario '{$nombre}' eliminado del sistema correctamente.");
    }
}
