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

        // 0. INTERCEPTOR CONVERSACIONAL DE SALUDOS Y CORTESÍA (Máxima prioridad para respuestas humanas y empáticas)
        if (preg_match('/^(hola|hola\s+c[oó]mo\s+est[aá]s|c[oó]mo\s+est[aá]s|c[oó]mo\s+est[aá]|c[oó]mo\s+va|que\s+tal|qu[eé]\s+tal|buenos\s+d[ií]as|buenas\s+tardes|buenas\s+noches|buenas|saludos)[\s\?!.]*$/i', $promptLower) || 
            (str_contains($promptLower, 'hola') && str_contains($promptLower, 'est')) ) {
            return response()->json([
                'success' => true,
                'response' => "¡Hola! 👋 Estoy muy bien, con muchas ganas de ayudarte hoy. 😊 ¿Y vos, cómo estás? ¿Cómo te ha ido en tu día?\n\nCuéntame, ¿en qué te puedo colaborar o guiar hoy en la plataforma INTEGRA? Puedes preguntarme por trámites, expedientes, turnos, credenciales, facturas o lo que necesites.",
                'source' => 'conversacional_empatico'
            ]);
        }

        if (preg_match('/^(gracias|muchas\s+gracias|genial|excelente|buen[íi]simo|crack|capo|perfecto|joya)[\s\?!.]*$/i', $promptLower)) {
            return response()->json([
                'success' => true,
                'response' => "¡De nada! Es un verdadero placer ayudarte. 😊\n\nSi tienes alguna otra consulta o quieres realizar cualquier trámite en el sistema, aquí estaré a tu entera disposición. ¡Que tengas un excelente día! 🌟",
                'source' => 'conversacional_empatico'
            ]);
        }

        if (preg_match('/(qui[eé]n\s+sos|quien\s+eres|qu[eé]\s+sos|c[oó]mo\s+te\s+llam[aá]s|quien\s+te\s+creo)/i', $promptLower)) {
            return response()->json([
                'success' => true,
                'response' => "¡Hola! 👋 Soy **INTEGRA Bot**, tu Asistente Virtual Inteligente y Manual Interactivo Oficial de la plataforma INTEGRA.\n\nEstoy programado con Inteligencia Artificial para acompañarte, resolver tus dudas sobre cualquier expediente o pantalla del sistema y asistirte tanto por escrito como por voz. ¿En qué te puedo ayudar hoy?",
                'source' => 'conversacional_empatico'
            ]);
        }

        // 1. Intentar responder vía API Gemini 1.5 Flash si está disponible
        $systemContext = "Eres INTEGRA Bot, el Asistente Virtual Inteligente y Manual Interactivo Oficial de la plataforma INTEGRA (Obra Social / Mutual con 130.000 abonados).\n" .
                         "El usuario está en la pantalla: {$path} ({$contexto}).\n\n" .
                         "REGLAS CONVERSACIONALES IMPORTANTES:\n" .
                         "1. Si el usuario te saluda ('hola', 'cómo estás', 'qué tal'), responde siempre con mucha calidez, empatía y humanidad (ej: '¡Hola! 👋 Estoy muy bien, ¿y vos cómo estás? ¿Cómo va tu día? 😊 ¿En qué te puedo ayudar hoy?').\n" .
                         "2. Si te da las gracias, responde amable y ateto.\n" .
                         "3. Si pregunta por un trámite o expediente, guíalo paso a paso con viñetas y negritas claras.\n\n" .
                         "CONOCIMIENTO CLAVE DEL SISTEMA Y EXPEDIENTES:\n" .
                         "- INICIAR EXPEDIENTE DOCENTE: /app-docente/demo -> Mis Alumnos -> + Nuevo Alumno/Legajo, subir Factura ARCA y enviar firma a Directora por WhatsApp.\n" .
                         "- INICIAR EXPEDIENTE REINTEGRO (PADRE): /app-padre/demo -> Solicitar Reintegro, subir Factura + Resolución OSP y firmar conformidad.\n" .
                         "- INICIAR EXPEDIENTE MÉDICO / INTERNACIÓN (PRESTADOR): /prestadores/demo -> Nueva Solicitud de Autorización Médica / Internación, ingresar código nomenclador y CIE-10.\n" .
                         "- DISPENSA EN FARMACIA: /farmacia/demo, validar DNI/QR, aplicar Vademécum (40%, 70%, 100%) y emitir Bono Digital con Hash MD5.\n" .
                         "- TOTALIZADORES Y SUCURSALES: /owner/mutual-dashboard, filtrar por Chilecito, Córdoba, La Rioja o BsAs.";

        $geminiResult = $this->geminiService->ask($prompt, $systemContext);

        if ($geminiResult['success']) {
            return response()->json([
                'success' => true,
                'response' => $geminiResult['response'],
                'source' => 'gemini_ia'
            ]);
        }

        // 2. Fallback: Base de Conocimiento Interactiva Integrada (Manual Inteligente por Pantalla)
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

        // --- CONSULTA ESPECÍFICA: INICIO DE EXPEDIENTE / TRÁMITE / LEGAJO ---
        if (str_contains($promptLower, 'expediente') || str_contains($promptLower, 'tramite') || str_contains($promptLower, 'trámite') || str_contains($promptLower, 'inici') || str_contains($promptLower, 'ingres') || str_contains($promptLower, 'abrir') || str_contains($promptLower, 'crear') || str_contains($promptLower, 'solicitud')) {
            return "📋 **Manual Oficial: Cómo Iniciar un Expediente o Trámite en INTEGRA**\n\n" .
                   "El inicio de un **Expediente Digital** depende de tu rol en la plataforma:\n\n" .
                   "1. 👨‍🏫 **Si eres Docente / Terapeuta (Educación Especial):**\n" .
                   "   - Ve a la **App del Docente** (`/app-docente/demo`).\n" .
                   "   - Ingresa a **Mis Alumnos** ➔ Presiona **+ Nuevo Alumno / Cargar Legajo**.\n" .
                   "   - Sube la **Factura ARCA** y envía la solicitud de aval por WhatsApp a la **Directora de Escuela**.\n" .
                   "   - Se generará automáticamente el número de expediente (`EXP-2026-XXXX`) con firma digital y **Hash MD5**.\n\n" .
                   "2. 👨‍👩‍👦 **Si eres Padre / Titular (Reintegros de Obra Social):**\n" .
                   "   - Ingresa a la **App del Titular** (`/app-padre/demo`).\n" .
                   "   - Toca en **Solicitar Reintegro**.\n" .
                   "   - Adjunta la foto de la factura abonada + **Resolución OSP** y firma la conformidad del servicio.\n\n" .
                   "3. 🏥 **Si eres Clínica / Prestador Médico (Autorizaciones / Internaciones):**\n" .
                   "   - Ve a la **Red de Prestadores** (`/prestadores/demo`).\n" .
                   "   - Presiona el botón verde **Nueva Solicitud de Autorización Médica / Internación**.\n" .
                   "   - Ingresa el código nomenclador, diagnóstico CIE-10 y adjunta la orden médica para que pase a **Auditoría Médica Central**.\n\n" .
                   "4. 💊 **Si eres Farmacia Convenida (Validador de Medicamentos):**\n" .
                   "   - Ve a `/farmacia/demo`, ingresa el DNI o escanea el QR del afiliado y presiona **Validar Receta** para emitir el Bono Digital.";
        }

        // --- ROL: AFILIADO (Credencial y Turnos) ---
        if (str_contains($promptLower, 'credencial') || str_contains($promptLower, 'qr') || str_contains($promptLower, 'token')) {
            return "💳 **Manual del Afiliado: Credencial Digital y Token QR**\n\n" .
                   "1. **Acceso:** Tu credencial digital está disponible en `/app-afiliado/credencial`.\n" .
                   "2. **Código QR Dinámico:** Se actualiza en tiempo real con un token de seguridad para presentar en la clínica o farmacia.\n" .
                   "3. **Grupo Familiar:** Puedes alternar pestañas para ver las credenciales de tus familiares a cargo.";
        }

        if (str_contains($promptLower, 'turno') || str_contains($promptLower, 'cartilla') || str_contains($promptLower, 'medico')) {
            return "🩺 **Manual del Afiliado: Cartilla Médica y Turnos**\n\n" .
                   "1. **Buscador de Cartilla:** Ingresa a `/app-afiliado/turnos` y busca por especialidad.\n" .
                   "2. **Reserva de Turno:** Elige el sanatorio en convenio y confirma tu turno de manera instantánea.";
        }

        // --- ROL: DOCENTE / TERAPEUTA (PWA) ---
        if (str_contains($promptLower, 'parlante') || str_contains($promptLower, 'voz') || str_contains($promptLower, 'dicta')) {
            return "🎙️ **Manual Docente: Modo Parlante / Búsqueda por Voz**\n\n" .
                   "1. **Micrófono:** En la pantalla del docente, toca el botón de micrófono.\n" .
                   "2. **Dictado:** Di el nombre o DNI del alumno.\n" .
                   "3. **Comprobación:** El sistema busca y completa automáticamente los datos del legajo.";
        }

        if (str_contains($promptLower, 'factura') || str_contains($promptLower, 'arca') || str_contains($promptLower, 'afip')) {
            return "📄 **Manual Docente: Carga de Factura ARCA**\n\n" .
                   "1. **Mis Alumnos:** Selecciona al alumno y presiona **Subir Factura ARCA**.\n" .
                   "2. **Compresión:** El sistema optimiza el PDF/imagen en tu celular.\n" .
                   "3. **Liquidación:** Una vez aprobada, actualiza el monto en la billetera virtual.";
        }

        // --- BÚSQUEDAS POR PANTALLA (SOLO SI PREGUNTA POR LA PANTALLA ACTIVA) ---
        if (str_contains($promptLower, 'pantalla') || str_contains($promptLower, 'funci') || str_contains($promptLower, 'quien') || str_contains($promptLower, 'que hay') || str_contains($promptLower, 'manual') || str_contains($promptLower, 'ayuda')) {
            if (str_contains($path, 'afiliado')) {
                return "👤 **Manual de la App del Afiliado**\n\n" .
                       "En esta pantalla puedes presentar tu **Credencial Digital QR**, consultar la **Cartilla Médica** y reservar **Turnos Médicos**.";
            }

            if (str_contains($path, 'docente')) {
                return "👨‍🏫 **Manual del Portal de Terapeutas y Docentes**\n\n" .
                       "Aquí puedes dar de alta **Legajos de Alumnos**, cargar **Facturas ARCA** y enviar solicitudes de aval a las Directoras.";
            }

            if (str_contains($path, 'directora')) {
                return "🏫 **Manual del Portal de Directora de Escuela**\n\n" .
                       "Desde aquí certificas digitalmente en 1-Clic la asistencia de los docentes que atienden alumnos en tu colegio.";
            }

            if (str_contains($path, 'padre')) {
                return "👨‍👩‍👦 **Manual del Titular: Módulo de Reintegros**\n\n" .
                       "Carga solicitudes de reintegro adjuntando la Factura abonada y la Resolución OSP.";
            }

            if (str_contains($path, 'farmacia')) {
                return "💊 **Manual del Validador de Farmacias Convenidas**\n\n" .
                       "Valida el DNI/QR del afiliado, aplica el Vademécum Mutual y emite el Bono Digital de Dispensa.";
            }

            if (str_contains($path, 'prestadores')) {
                return "🏥 **Manual de la Red de Prestadores Médicos**\n\n" .
                       "Solicita autorizaciones de internación y visualiza/imprime el Bono Digital Oficial con QR.";
            }

            if (str_contains($path, 'owner') || str_contains($path, 'dashboard')) {
                return "👑 **Manual del Cuadro de Mando Ejecutivo (Alta Dirección)**\n\n" .
                       "Filtra totalizadores por sucursal (**Chilecito, La Rioja, Córdoba, BsAs**) y monitorea costos por patologías prevalentes.";
            }
        }

        // --- RESPUESTA CONVERSACIONAL DE ORIENTACIÓN GENERAL ---
        return "😊 **Asistente Virtual INTEGRA**\n\n" .
               "Entendido. ¿En qué tema te gustaría que te oriente en este momento?\n\n" .
               "- 📋 **Expedientes y Trámites:** Cómo iniciar y seguir legajos digitales.\n" .
               "- 💳 **Afiliados:** Credenciales QR, token y turnos.\n" .
               "- 👨‍🏫 **Docentes:** Facturas ARCA y legajos.\n" .
               "- 🏥 **Sanatorios y Farmacias:** Autorizaciones y bonos digitales.\n" .
               "- 👑 **Ejecutivo:** Totalizadores por sucursal (Chilecito, Córdoba, etc.).\n\n" .
               "Pregúntame libremente o háblame al micrófono con tus palabras.";
    }
}

