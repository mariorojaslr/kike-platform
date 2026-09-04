<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class WhatsAppBotSimulatorController extends Controller
{
    /**
     * Muestra la interfaz interactiva del Simulador de Bot de WhatsApp Oficial de INTEGRA Mutual
     */
    public function indexDemo(Request $request)
    {
        $botNumero = "+54 9 3825 55-1234";
        $botNombre = "INTEGRA Mutual Bot Oficial";
        $afiliado = (object)[
            'nombre' => 'Ramon Martin Abayay',
            'nro_afiliado' => 'MUT-32456789/00',
            'telefono' => '+54 9 3825 41-9812'
        ];

        return view('pwa.simulador_whatsapp', compact('botNumero', 'botNombre', 'afiliado'));
    }

    /**
     * Procesa la respuesta automática inteligente enviada por el bot de WhatsApp.
     */
    public function procesarMensajeWhatsapp(Request $request): JsonResponse
    {
        $request->validate([
            'mensaje' => 'required|string|max:1000'
        ]);

        $mensajeLower = mb_strtolower(trim($request->input('mensaje')));
        $hora = Carbon::now()->format('H:i');

        if (str_contains($mensajeLower, 'hola') || str_contains($mensajeLower, 'buenas')) {
            $reply = "¡Hola! 👋 Te damos la bienvenida al *Bot Oficial de INTEGRA Mutual* (Obra Social 130.000 cápitas).\n\n" .
                     "¿Qué tramite deseas realizar hoy?\n" .
                     "1️⃣ Enviar foto de Receta para Validar\n" .
                     "2️⃣ Consultar mi Credencial / Token QR\n" .
                     "3️⃣ Pedir un Turno Médico\n" .
                     "4️⃣ Hablar con un Operador";
        } elseif (str_contains($mensajeLower, 'receta') || str_contains($mensajeLower, 'foto') || str_contains($mensajeLower, '1')) {
            $reply = "📸 *Validación por Foto de Receta ARCA / AFIP*\n\n" .
                     "Por favor envía la foto o PDF de la receta. Nuestro motor *Vision AI* extraerá los datos y emitirá tu Bono Digital con Vademécum Mutual (40%, 70%, 100% de cobertura).";
        } elseif (str_contains($mensajeLower, 'credencial') || str_contains($mensajeLower, 'token') || str_contains($mensajeLower, '2')) {
            $token = rand(100000, 999999);
            $reply = "💳 *Tu Credencial Digital & Token Dinámico*\n\n" .
                     "Afiliado: *ABAYAY RAMON MARTIN*\n" .
                     "Nº Afiliado: `MUT-32456789/00`\n" .
                     "🔑 *Token de Validación:* `{$token}` (Válido por 15 min)\n\n" .
                     "Poder ver el QR completo ingresando aquí:\n👉 " . url("/app-afiliado/credencial");
        } else {
            $reply = "Entendido. 😊 He derivado tu consulta al área de Atención al Afiliado. Un operador te contactará en breve o puedes ingresar al portal en: " . url("/demo");
        }

        return response()->json([
            'success' => true,
            'response' => $reply,
            'hora' => $hora
        ]);
    }
}
