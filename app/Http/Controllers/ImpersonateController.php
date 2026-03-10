<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ImpersonateController extends Controller
{
    /**
     * Inicia el Modo Omnisciente (Suplantación)
     */
    public function enter($userId)
    {
        // 1. Solo un verdadero 'owner' (dueño de KIKE) puede usar el God Mode.
        $originalUser = Auth::user();
        if ($originalUser->role !== 'owner') {
            return redirect()->back()->with('error', 'Acceso denegado: God Mode es exclusivo para Owners.');
        }

        // 2. Buscar a la "víctima" (el usuario que vamos a suplantar)
        $userToImpersonate = User::findOrFail($userId);

        // 3. Guardamos el ID del Owner original en su propia sesión para no perder el mapa de retorno
        session()->put('impersonated_by', $originalUser->id);

        // 4. Hacemos login mágico forzoso sin contraseña
        Auth::login($userToImpersonate);

        // 5. Redirigimos al Tenant Dashboard (o donde corresponda) con éxito
        return redirect()->route('tenant.dashboard')->with('success', "Modo Omnisciente: Estás viendo todo como {$userToImpersonate->name}.");
    }

    /**
     * Detiene el Modo y regresa al Owner
     */
    public function leave()
    {
        // 1. Verificamos si realmente estábamos en modo suplantación
        if (!session()->has('impersonated_by')) {
            return redirect()->back();
        }

        // 2. Recuperamos el ID del Owner original desde el salvavidas
        $originalUserId = session()->pull('impersonated_by');
        $originalUser = User::findOrFail($originalUserId);

        // 3. Volvemos a iniciar sesión con nuestro super usuario
        Auth::login($originalUser);

        // 4. Regresamos al Centro de Comando Owner
        return redirect()->route('dashboard')->with('success', 'Modo Omnisciente finalizado. Estás de vuelta en tu cuenta Owner.');
    }
}
