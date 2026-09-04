<?php

namespace App\Services;

class WhatsAppNotificationService
{
    /**
     * Genera un enlace oficial de WhatsApp Web / App para enviar el aval de asistencia a la Directora de Escuela.
     */
    public static function generarLinkAvalWhatsApp(string $telefonoDirectora, string $nombreDocente, string $nombreAlumno, string $escuela, string $tokenAval): string
    {
        $telefonoLimpio = preg_replace('/[^0-9]/', '', $telefonoDirectora);
        if (!str_starts_with($telefonoLimpio, '549') && !str_starts_with($telefonoLimpio, '54')) {
            $telefonoLimpio = '549' . $telefonoLimpio;
        }

        $mensaje = "🏛️ *SOLICITUD DE AVAL DIGITAL - INTEGRA PLATFORM*\n\n" .
                   "Estimada Directora de *{$escuela}*,\n\n" .
                   "La docente/terapeuta *{$nombreDocente}* ha registrado la atención asistencial del alumno *{$nombreAlumno}*.\n\n" .
                   "🔑 *Token de Aval:* `{$tokenAval}`\n\n" .
                   "Por favor ingresa al siguiente enlace para certificar la asistencia en 1-Clic:\n" .
                   "👉 " . url("/app-directora/demo?token={$tokenAval}") . "\n\n" .
                   "_Obra Social / Mutual INTEGRA - Sistema de Gestión Escolar_";

        return "https://api.whatsapp.com/send?phone={$telefonoLimpio}&text=" . urlencode($mensaje);
    }

    /**
     * Genera notificación para informar al Titular (Padre) que la asistencia fue avalada.
     */
    public static function generarNotificacionPadreWhatsApp(string $telefonoPadre, string $nombreAlumno, int $horasAprobadas): string
    {
        $telefonoLimpio = preg_replace('/[^0-9]/', '', $telefonoPadre);
        if (!str_starts_with($telefonoLimpio, '549') && !str_starts_with($telefonoLimpio, '54')) {
            $telefonoLimpio = '549' . $telefonoLimpio;
        }

        $mensaje = "👨‍👩‍👦 *NOTIFICACIÓN DE REINTEGRO / ATENCIÓN AVALADA*\n\n" .
                   "Estimado Titular,\n\n" .
                   "Le informamos que la Directora de Escuela ha certificado exitosamente *{$horasAprobadas} hs* de atención asistencial para su hijo/a *{$nombreAlumno}*.\n\n" .
                   "Podés consultar el estado de tu reintegro y firmar la conformidad diaria ingresando aquí:\n" .
                   "👉 " . url("/app-padre/demo") . "\n\n" .
                   "_INTEGRA Mutual - Auditoría de Cobertura Salud & Educación_";

        return "https://api.whatsapp.com/send?phone={$telefonoLimpio}&text=" . urlencode($mensaje);
    }

    /**
     * Genera enlace de WhatsApp para enviar el Bono Digital de Dispensa o Autorización Sanatorial.
     */
    public static function generarBonoDigitalWhatsApp(string $telefonoAfiliado, string $nroBono, string $prestacion): string
    {
        $telefonoLimpio = preg_replace('/[^0-9]/', '', $telefonoAfiliado);
        if (!str_starts_with($telefonoLimpio, '549') && !str_starts_with($telefonoLimpio, '54')) {
            $telefonoLimpio = '549' . $telefonoLimpio;
        }

        $mensaje = "🏥 *BONO DIGITAL DE PRESTACIÓN MÉDICA - INTEGRA*\n\n" .
                   "Estimado Afiliado,\n\n" .
                   "Tu bono de *{$prestacion}* ha sido emitido e impreso digitalmente con éxito.\n\n" .
                   "📑 *Nº Comprobante:* `{$nroBono}`\n" .
                   "👉 Presenta el código QR desde tu credencial digital: " . url("/app-afiliado/credencial") . "\n\n" .
                   "_Mutual provincial INTEGRA - 130.000 Cápitas_";

        return "https://api.whatsapp.com/send?phone={$telefonoLimpio}&text=" . urlencode($mensaje);
    }
}
