<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppNotificationService
{
    protected string $apiToken;
    protected string $phoneNumberId;
    protected string $apiVersion;

    public function __construct()
    {
        $this->apiToken = config('services.whatsapp.token', 'EAAG_DEMO_TOKEN_INTEGRA_MUTUAL_2026');
        $this->phoneNumberId = config('services.whatsapp.phone_number_id', '109283928172');
        $this->apiVersion = 'v18.0';
    }

    /**
     * Envia notificación oficial de comprobante o aval por WhatsApp Cloud API.
     */
    public function enviarNotificacionAval(string $telefono, string $nombreDestinatario, string $modulo, string $enlaceUrl): array
    {
        $mensaje = "👋 Hola *{$nombreDestinatario}*,\n\n"
            . "Le informamos desde *INTEGRA Mutual & Obra Social* que tiene un nuevo aval/documento listo en la plataforma:\n\n"
            . "📌 *Módulo:* {$modulo}\n"
            . "🌐 *Acceso Directo:* {$enlaceUrl}\n\n"
            . "📱 Podés abrir el enlace en tu celular e instalar el acceso directo en tu pantalla de inicio.";

        return $this->enviarMensajeTexto($telefono, $mensaje);
    }

    /**
     * Envia mensaje de texto plano o plantilla vía Meta Graph API.
     */
    public function enviarMensajeTexto(string $telefono, string $mensaje): array
    {
        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $this->limpiarTelefono($telefono),
            'type' => 'text',
            'text' => [
                'preview_url' => true,
                'body' => $mensaje
            ]
        ];

        try {
            // Simulación o llamada real si hay token de producción
            if (str_contains($this->apiToken, 'DEMO')) {
                return [
                    'status' => 'simulated',
                    'message_id' => 'wamid.HBgLMzgwNDEyMzQ1NhUCAB4IDXNpbXVsYXRlZF9pZAA=',
                    'payload' => $payload
                ];
            }

            $response = Http::withToken($this->apiToken)
                ->post("https://graph.facebook.com/{$this->apiVersion}/{$this->phoneNumberId}/messages", $payload);

            return [
                'status' => $response->successful() ? 'sent' : 'failed',
                'response' => $response->json()
            ];
        } catch (\Exception $e) {
            Log::error("Error enviando WhatsApp Cloud API: " . $e->getMessage());
            return [
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Formatea el número telefónico a estándar internacional E.164 Argentina (+549...).
     */
    protected function limpiarTelefono(string $telefono): string
    {
        $num = preg_replace('/[^0-9]/', '', $telefono);
        if (str_starts_with($num, '549')) return $num;
        if (str_starts_with($num, '54')) return '549' . substr($num, 2);
        if (str_starts_with($num, '0')) return '549' . substr($num, 1);
        return '549' . $num;
    }
}
