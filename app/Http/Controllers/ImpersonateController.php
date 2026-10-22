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
        // 1. Un 'owner' (dueño de Integra) o el administrador de una Subagencia Provincial puede entrar a sus clientes hijos.
        $originalUser = Auth::user();
        $userToImpersonate = User::findOrFail($userId);

        $esSubagenciaDelCliente = false;
        if ($originalUser->empresa_id && $userToImpersonate->empresa_id) {
            $empresaDestino = \App\Models\Empresa::find($userToImpersonate->empresa_id);
            if ($empresaDestino && $empresaDestino->parent_id == $originalUser->empresa_id) {
                $esSubagenciaDelCliente = true;
            }
        }

        if ($originalUser->role !== 'owner' && !$esSubagenciaDelCliente && !session()->has('impersonated_by')) {
            return redirect()->back()->with('error', 'Acceso denegado: No tienes permisos para administrar esta entidad.');
        }

        // 3. Guardamos el ID del usuario original en su propia sesión si aún no estaba guardado
        if (!session()->has('impersonated_by')) {
            session()->put('impersonated_by', $originalUser->id);
        }

        // 4. Hacemos login mágico forzoso sin contraseña
        Auth::login($userToImpersonate);

        // 5. Redirigimos al Tenant Dashboard o a la Auditoria según corresponda
        if (request()->query('redirect') === 'auditor') {
            return redirect()->route('auditor.facturas')->with('success', "Modo Auditor: Revisando facturas pendientes de la empresa.");
        }
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
