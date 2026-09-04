<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GeminiService;
use Illuminate\Http\JsonResponse;

class AiAssistantController extends Controller
{
    protected GeminiService $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * Procesa consultas enviadas por el widget de Asistente IA / Manual Interactivo.
     */
    public function query(Request $request): JsonResponse
    {
        $request->validate([
            'prompt' => 'required|string|min:1|max:1500',
            'contexto' => 'nullable|string|max:500',
            'path' => 'nullable|string|max:255'
        ]);

        $prompt = trim($request->input('prompt'));
        $contexto = $request->input('contexto', '');
        $path = $request->input('path', '');
        $promptLower = mb_strtolower($prompt);

        // 0. INTERCEPTOR CONVERSACIONAL DE SALUDOS Y CORTESÍA (Respuesta ultra concisa y empática)
        if (preg_match('/^(hola|hola\s+c[oó]mo\s+est[aá]s|c[oó]mo\s+est[aá]s|c[oó]mo\s+est[aá]|c[oó]mo\s+va|que\s+tal|qu[eé]\s+tal|buenos\s+d[ií]as|buenas\s+tardes|buenas\s+noches|buenas|saludos)[\s\?!.]*$/i', $promptLower) || 
            (str_contains($promptLower, 'hola') && str_contains($promptLower, 'est')) ) {
            return response()->json([
                'success' => true,
                'response' => "¡Hola! 👋 Estoy muy bien, listo para ayudarte. 😊 ¿A qué pantalla o trámite te gustaría que te lleve hoy?",
                'source' => 'conversacional_empatico'
            ]);
        }

        if (preg_match('/^(gracias|muchas\s+gracias|genial|excelente|buen[íi]simo|crack|capo|perfecto|joya)[\s\?!.]*$/i', $promptLower)) {
            return response()->json([
                'success' => true,
                'response' => "¡Un gusto ayudarte! 😊 Si necesitas ir a otra pantalla o realizar un trámite, dime y te llevo directo.",
                'source' => 'conversacional_empatico'
            ]);
        }

        if (preg_match('/(qui[eé]n\s+sos|quien\s+eres|qu[eé]\s+sos|c[oó]mo\s+te\s+llam[aá]s|quien\s+te\s+creo)/i', $promptLower)) {
            return response()->json([
                'success' => true,
                'response' => "¡Hola! 👋 Soy **INTEGRA Bot**, tu Asistente Virtual y Guía Rápida. Dime qué necesitas consultar o hacer y te llevo de inmediato a la sección correspondiente.",
                'source' => 'conversacional_empatico'
            ]);
        }

        // Interceptor para usuarios nuevos o desorientados
        if (preg_match('/(no\s+s[eé]|es\s+nuevo|qu[eé]\s+hago|tengo\s+miedo|como\s+empiezo|guiame|guíame|enseñame|enseñame)/i', $promptLower) && strlen($promptLower) < 45) {
            return response()->json([
                'success' => true,
                'response' => "¡No te preocupes! 🤗 Dime qué quieres hacer (expedientes, credenciales, reintegros o facturas) y te llevo directamente ahí.",
                'source' => 'mentor_conversacional'
            ]);
        }

        // 1. Intentar responder vía API Gemini 1.5 Flash si está disponible
        $systemContext = "Eres INTEGRA Bot, el Asistente Virtual de Inteligencia Artificial de la plataforma INTEGRA (Obra Social / Mutual).\n" .
                         "El usuario está en la pantalla: {$path} ({$contexto}).\n\n" .
                         "REGLAS DE ORO DE CONCISIÓN Y ACCIÓN:\n" .
                         "1. SÉ EXTREMADAMENTE CONCISO Y DIRECTO (Máximo 1 a 3 oraciones cortas). NUNCA generes respuestas largas, discursos extensos ni listas interminables de opciones.\n" .
                         "2. SI EL USUARIO PIDE IR O VER UNA SECCIÓN (ej: 'llévame a maestras integradoras', 'dónde están los turnos'), dale INMEDIATAMENTE el enlace/botón [🚀 Ir a X](/url) y una pregunta breve ofreciendo explicación.\n" .
                         "3. Si el usuario pide explicación ('cómo se hace', 'explicame'), da máximo 3 puntos ultracortos.\n" .
                         "4. NUNCA respondas con menús genéricos de opciones si el usuario hizo una pregunta concreta.\n\n" .
                         "RUTAS DEL SISTEMA:\n" .
                         "- Maestras Integradoras / Docentes: /app-docente/demo\n" .
                         "- Directora de Escuela: /app-directora/demo\n" .
                         "- Titulares / Reintegros: /app-padre/demo\n" .
                         "- Farmacia Convenida: /farmacia/demo\n" .
                         "- Prestadores Médicos: /prestadores/demo\n" .
                         "- Credencial QR Afiliado: /app-afiliado/credencial\n" .
                         "- Turnos y Cartilla: /app-afiliado/turnos\n" .
                         "- Telemedicina & Videoconsulta: /app-afiliado/telemedicina\n" .
                         "- Cierre de Liquidaciones CBU: /owner/liquidaciones\n" .
                         "- Derivaciones & Viáticos: /app-afiliado/derivaciones\n" .
                         "- Bot Oficial WhatsApp: /simulador/whatsapp\n" .
                         "- Panel Ejecutivo / Balances: /owner/mutual-dashboard";

        $geminiResult = $this->geminiService->ask($prompt, $systemContext);

        if ($geminiResult['success']) {
            return response()->json([
                'success' => true,
                'response' => $geminiResult['response'],
                'source' => 'gemini_ia'
            ]);
        }

        // 2. Fallback: Base de Conocimiento Interactiva Integrada (Respuesta Directa y Concisa)
        $respuestaManual = $this->obtenerRespuestaManual($prompt, $path, $contexto);

        return response()->json([
            'success' => true,
            'response' => $respuestaManual,
            'source' => 'manual_interactivo'
        ]);
    }

    /**
     * Motor de Manual Interactivo por pantalla para INTEGRA Platform.
     */
    protected function obtenerRespuestaManual(string $prompt, string $path, string $contexto): string
    {
        $promptLower = mb_strtolower($prompt);

        // --- DIRECTORIO DE ENLACES Y ACCESOS OFICIALES ---
        if (str_contains($promptLower, 'link') || str_contains($promptLower, 'enlace') || str_contains($promptLower, 'directorio') || str_contains($promptLower, 'accesos') || str_contains($promptLower, 'compartir')) {
            return "¡Con gusto! Podés consultar y copiar todos los enlaces organizados por sector (Maestras, Directoras, Padres, Afiliados, Sanatorios y Farmacias) desde aquí:\n\n" .
                   "👉 [🌐 Abrir Directorio de Enlaces Oficiales](/demo)\n\n" .
                   "¿Querés que te pase el enlace directo de algún sector en particular?";
        }

        // --- TELEMEDICINA / VIDEOCONSULTA EN VIVO ---
        if (str_contains($promptLower, 'telemedicina') || str_contains($promptLower, 'video') || str_contains($promptLower, 'virtual') || str_contains($promptLower, 'videoconsulta')) {
            return "¡Por supuesto! Podés acceder a la **Sala de Videoconsulta Médica en Vivo (WebRTC)** desde aquí:\n\n" .
                   "👉 [🩺 Ir a Sala de Telemedicina](/app-afiliado/telemedicina)\n\n" .
                   "¿Querés que te explique cómo emitir una receta digital durante la videollamada?";
        }

        // --- LIQUIDACIONES & BILLETERAS CBU ---
        if (str_contains($promptLower, 'liquidacion') || str_contains($promptLower, 'liquidación') || str_contains($promptLower, 'transferencia') || str_contains($promptLower, 'cbu') || str_contains($promptLower, 'tesoreria') || str_contains($promptLower, 'tesorería')) {
            return "¡Con gusto! Podés consultar el **Tablero de Cierre de Liquidaciones CBU a Prestadores** aquí:\n\n" .
                   "👉 [💰 Ir al Tablero de Liquidaciones](/owner/liquidaciones)\n\n" .
                   "¿Deseas autorizar la acreditación masiva de lotes?";
        }

        // --- DERIVACIONES & VIÁTICOS ---
        if (str_contains($promptLower, 'derivac') || str_contains($promptLower, 'viatico') || str_contains($promptLower, 'viático') || str_contains($promptLower, 'cordoba') || str_contains($promptLower, 'córdoba') || str_contains($promptLower, 'transito') || str_contains($promptLower, 'tránsito')) {
            return "¡Por supuesto! Podés gestionar las **Derivaciones de Alta Complejidad y Vales de Viáticos** aquí:\n\n" .
                   "👉 [✈️ Ir a Derivaciones & Viáticos](/app-afiliado/derivaciones)\n\n" .
                   "¿Necesitas emitir una Credencial Provisoria de Tránsito?";
        }

        // --- SIMULADOR BOT DE WHATSAPP ---
        if (str_contains($promptLower, 'whatsapp') || str_contains($promptLower, 'bot') || str_contains($promptLower, 'chat')) {
            return "¡Claro que sí! Podés probar la interacción con el **Bot Oficial de WhatsApp de la Mutual** aquí:\n\n" .
                   "👉 [📲 Abrir Simulador de WhatsApp](/simulador/whatsapp)\n\n" .
                   "¿Querés probar la validación por foto de receta?";
        }

        // --- MAESTRAS INTEGRADORAS / EDUCACIÓN ESPECIAL / DOCENTES ---
        if (str_contains($promptLower, 'maestra') || str_contains($promptLower, 'integradora') || str_contains($promptLower, 'terapeuta') || str_contains($promptLower, 'educacion especial') || str_contains($promptLower, 'educación especial')) {
            return "¡Por supuesto! Aquí tienes el acceso directo a la sección de **Maestras Integradoras / Educación Especial**:\n\n" .
                   "👉 [👨‍🏫 Ir a Maestras Integradoras](/app-docente/demo)\n\n" .
                   "¿Quieres que te explique cómo dar de alta un alumno o subir la factura ARCA?";
        }

        // --- DIRECTORA DE ESCUELA / AVALES ---
        if (str_contains($promptLower, 'directora') || str_contains($promptLower, 'aval') || str_contains($promptLower, 'colegio')) {
            return "¡Con gusto! Podés acceder al Portal de la **Directora de Escuela** desde aquí:\n\n" .
                   "👉 [🏫 Ir al Portal de Directora](/app-directora/demo)\n\n" .
                   "¿Te gustaría que te explique cómo certificar la asistencia con 1-Clic?";
        }

        // --- REINTEGROS / TITULARES / PADRES ---
        if (str_contains($promptLower, 'reintegro') || str_contains($promptLower, 'titular') || str_contains($promptLower, 'padre')) {
            return "¡Por supuesto! Podés gestionar las solicitudes de **Reintegro de Obra Social** aquí:\n\n" .
                   "👉 [👨‍👩‍👦 Ir a Solicitud de Reintegros](/app-padre/demo)\n\n" .
                   "¿Querés que te indique qué documentación y facturas adjuntar?";
        }

        // --- CREDENCIAL DIGITAL / QR / TOKEN AFILIADO ---
        if (str_contains($promptLower, 'credencial') || str_contains($promptLower, 'qr') || str_contains($promptLower, 'token') || str_contains($promptLower, 'afiliado') || str_contains($promptLower, 'carnet')) {
            return "¡Claro que sí! Podés ver la **Credencial Digital QR y Token** de tu grupo familiar aquí:\n\n" .
                   "👉 [💳 Ir a Credencial Digital](/app-afiliado/credencial)\n\n" .
                   "¿Necesitas ayuda para generar el token de validación?";
        }

        // --- TURNOS / CARTILLA MÉDICA ---
        if (str_contains($promptLower, 'turno') || str_contains($promptLower, 'cartilla') || str_contains($promptLower, 'medico') || str_contains($promptLower, 'médico')) {
            return "¡Con gusto! Podés consultar la **Cartilla Médica** y reservar turnos aquí:\n\n" .
                   "👉 [🩺 Ir a Cartilla y Turnos Médicos](/app-afiliado/turnos)\n\n" .
                   "¿Quieres que te oriente en cómo buscar por especialidad o sanatorio?";
        }

        // --- FARMACIA / MEDICAMENTOS / VADEMECUM / RECETAS ---
        if (str_contains($promptLower, 'farmacia') || str_contains($promptLower, 'medicamento') || str_contains($promptLower, 'remedio') || str_contains($promptLower, 'receta') || str_contains($promptLower, 'validador')) {
            return "¡Por supuesto! Podés acceder al **Validador de Farmacias Convenidas** desde aquí:\n\n" .
                   "👉 [💊 Ir al Validador de Farmacia](/farmacia/demo)\n\n" .
                   "¿Querés saber cómo validar la receta electrónica del afiliado?";
        }

        // --- PRESTADORES / CLÍNICAS / AUTORIZACIONES ---
        if (str_contains($promptLower, 'prestador') || str_contains($promptLower, 'clinica') || str_contains($promptLower, 'clínica') || str_contains($promptLower, 'sanatorio') || str_contains($promptLower, 'internaci') || str_contains($promptLower, 'autorizac') || str_contains($promptLower, 'bono')) {
            return "¡Con gusto! Podés gestionar **Autorizaciones Médicas e imprimir el Bono Digital** aquí:\n\n" .
                   "👉 [🏥 Ir a Red de Prestadores](/prestadores/demo)\n\n" .
                   "¿Querés que te explique los pasos para solicitar una autorización?";
        }

        // --- BALANCE / RENTABILIDAD / EJECUTIVO / CONSUMO DE PAPEL ---
        if (str_contains($promptLower, 'balance') || str_contains($promptLower, 'rentabilid') || str_contains($promptLower, 'ingreso') || str_contains($promptLower, 'gasto') || str_contains($promptLower, 'sucursal') || str_contains($promptLower, 'papel')) {
            return "📊 **Cuadro de Mando Ejecutivo (Alta Dirección)**\n\n" .
                   "- 💰 Ingresos Cápitas (130k abonados): **$485.2M** | Superávit: **+$332.2M**\n" .
                   "- 🌿 Reducción consumo de papel: **84.7% de ahorro** en Chilecito, La Rioja, Cba y BsAs.\n\n" .
                   "👉 [📊 Ver Panel Ejecutivo en Vivo](/owner/mutual-dashboard)";
        }

        // --- CONSULTA SOBRE EXPLICACIÓN / "CÓMO HAGO" / "EXPLICAME" / "NO ENTIENDO" ---
        if (str_contains($promptLower, 'explic') || str_contains($promptLower, 'paso') || str_contains($promptLower, 'como') || str_contains($promptLower, 'cómo') || str_contains($promptLower, 'no entiendo') || str_contains($promptLower, 'enseña')) {
            if (str_contains($path, 'docente') || str_contains($promptLower, 'maestra') || str_contains($promptLower, 'alumno')) {
                return "✍️ **Pasos para Maestras Integradoras:**\n" .
                       "1. Ingresa a **Mis Alumnos** ➔ Presiona **+ Nuevo Alumno**.\n" .
                       "2. Sube la **Factura ARCA** en formato PDF/imagen.\n" .
                       "3. Toca **Enviar Aval** para notificar a la Directora por WhatsApp.\n\n" .
                       "¿Te quedó claro o quieres repasar algún paso?";
            }

            if (str_contains($path, 'padre') || str_contains($promptLower, 'reintegro')) {
                return "✍️ **Pasos para Reintegros:**\n" .
                       "1. Toca en **Solicitar Reintegro**.\n" .
                       "2. Adjunta la factura abonada + la **Resolución OSP**.\n" .
                       "3. Firma la conformidad del servicio. ¡Listo!\n\n" .
                       "¿Querés hacer alguna prueba?";
            }

            if (str_contains($path, 'prestadores') || str_contains($promptLower, 'autorizac')) {
                return "✍️ **Pasos para Autorizaciones Médicas:**\n" .
                       "1. Presiona **Nueva Autorización**.\n" .
                       "2. Ingresa DNI del afiliado y código de prestación (CIE-10).\n" .
                       "3. Sube la orden médica y emite el Bono con QR.\n\n" .
                       "¿Deseas realizar una autorización ahora?";
            }
        }

        // --- INICIO DE EXPEDIENTE / TRÁMITE ---
        if (str_contains($promptLower, 'expediente') || str_contains($promptLower, 'tramite') || str_contains($promptLower, 'trámite') || str_contains($promptLower, 'inici') || str_contains($promptLower, 'abrir') || str_contains($promptLower, 'crear')) {
            return "📋 Para iniciar un expediente, elige el rol correspondiente:\n\n" .
                   "- 👉 [👨‍🏫 Maestras Integradoras / Educación Especial](/app-docente/demo)\n" .
                   "- 👉 [👨‍👩‍👦 Reintegros de Titulares](/app-padre/demo)\n" .
                   "- 👉 [🏥 Autorizaciones Médicas Sanatoriales](/prestadores/demo)\n\n" .
                   "¿De cuál de estos roles querés que te explique los pasos?";
        }

        // --- FALLBACK ULTRA CONCISO (SIN MENÚS EXTENSOS NI DISCURSOS) ---
        return "Entendido. 😊 Dime a qué pantalla o trámite te gustaría ir o qué consulta específica tienes y te llevo directamente ahí.";
    }
}
