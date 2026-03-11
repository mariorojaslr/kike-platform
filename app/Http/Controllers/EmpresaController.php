<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmpresaController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validar los datos de entrada del formulario Modal
        $request->validate([
            'nombre' => 'required|string|max:255',
            'limite_usuarios' => 'required|integer|min:1',
            'limite_mb' => 'required|numeric|min:1',
            'admin_email' => 'required|email|unique:users,email',
        ]);

        // 2. Crear la Institución/Empresa en la Base de Datos
        $empresa = Empresa::create([
            'nombre' => $request->nombre,
            'limite_usuarios' => $request->limite_usuarios,
            'limite_mb' => $request->limite_mb,
            'consumo_actual_mb' => 0.00, // Siempre inicia en 0 MB consumidos
            'estado_cuenta' => 'al_dia', // Siempre inicia al día
            'proximo_vencimiento' => now()->addMonth(), // Se cobra a mes adelantado según regla de negocio
            'deuda_actual' => 0.00,
            'meses_adeudados' => 0,
        ]);

        // 3. Crear el Usuario Administrador Global para esta nueva empresa
        // Generamos una contraseña temporal aleatoria de 8 caracteres
        $password = Str::random(8);

        $user = User::create([
            'name' => 'Admin ' . $request->nombre,
            'email' => $request->admin_email,
            'password' => Hash::make($password),
            'role' => 'empresa',
            'empresa_id' => $empresa->id,
        ]);

        // En un entorno real, enviaríamos un correo electrónico con sus credenciales aquí

        // 4. Retornar Feedback (Toast)
        return redirect()->route('dashboard')->with('success', 'Empresa "' . $request->nombre . '" creada exitosamente. Pass temporal del admin: ' . $password);
    }

    public function toggleStatus(Request $request, Empresa $empresa)
    {
        // Alternar el estado entre al_dia y suspendida
        $nuevoEstado = $empresa->estado_cuenta === 'al_dia' ? 'suspendida' : 'al_dia';
        $empresa->estado_cuenta = $nuevoEstado;
        $empresa->save();

        $mensaje = $nuevoEstado === 'suspendida' ? 'Empresa suspendida.' : 'Empresa reactivada exitosamente.';

        return back()->with('success', $mensaje);
    }

    public function resetPassword(Request $request, Empresa $empresa)
    {
        $request->validate([
            'new_password' => 'required|string|min:8',
        ]);

        $adminUser = User::where('empresa_id', $empresa->id)
                         ->whereIn('role', ['empresa', 'tenant'])
                         ->first();

        if ($adminUser) {
            $adminUser->password = Hash::make($request->new_password);
            $adminUser->save();
            return back()->with('success', 'Contraseña del administrador actualizada exitosamente.');
        }

        return back()->with('error', 'No se encontró un usuario administrador para esta empresa.');
    }

    public function crearAdminPorDefecto(Request $request, Empresa $empresa)
    {
        // Genera un correo en base al nombre de la empresa (simplificado)
        $cleanName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $empresa->nombre));
        $email = "admin@{$cleanName}.com";
        $password = '12345678';

        // Intenta crear el usuario
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Administrador ' . $empresa->nombre,
                'password' => Hash::make($password),
                'role' => 'empresa', // o tenant
                'empresa_id' => $empresa->id
            ]
        );

        return back()->with('success', "Administrador creado. Correo: {$email} / Clave: {$password}");
    }
}
