<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NotificacionSistema;
use Illuminate\Support\Facades\Auth;

class NotificacionController extends Controller
{
    /**
     * Devuelve las notificaciones no leídas para la campanita del header (JSON)
     */
    public function getNotificaciones()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['unread_count' => 0, 'items' => []]);
        }

        $empresaId = $user->empresa_id ?? session('impersonated_tenant_id');

        $query = NotificacionSistema::query();

        if ($user->role === 'owner') {
            // El owner ve notificaciones dirigidas a él o globales de empresas
            $query->orderBy('created_at', 'desc');
        } else {
            $query->where('empresa_id', $empresaId)
                  ->orderBy('created_at', 'desc');
        }

        $items = $query->limit(10)->get();
        $unreadCount = $query->where('leido', false)->count();

        return response()->json([
            'unread_count' => $unreadCount,
            'items' => $items
        ]);
    }

    /**
     * Marca una notificación como leída
     */
    public function marcarLeida($id)
    {
        $notif = NotificacionSistema::find($id);
        if ($notif) {
            $notif->leido = true;
            $notif->save();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Marca todas las notificaciones como leídas
     */
    public function marcarTodasLeidas()
    {
        $user = Auth::user();
        if ($user) {
            $empresaId = $user->empresa_id ?? session('impersonated_tenant_id');
            if ($user->role === 'owner') {
                NotificacionSistema::where('leido', false)->update(['leido' => true]);
            } else {
                NotificacionSistema::where('empresa_id', $empresaId)->where('leido', false)->update(['leido' => true]);
            }
        }

        return response()->json(['success' => true]);
    }
}
