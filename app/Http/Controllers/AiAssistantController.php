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

        // Interceptor mentor para usuarios nuevos o desorientados
        if (preg_match('/(no\s+s[eé]|es\s+nuevo|qu[eé]\s+hago|tengo\s+miedo|como\s+empiezo|guiame|guíame|enseñame|enseñame)/i', $promptLower) && strlen($promptLower) < 45) {
            return response()->json([
                'success' => true,
                'response' => "¡No te preocupes en absoluto! 🤗 Es totalmente normal sentirse así al principio. Aquí estoy yo para acompañarte paso a paso y enseñarte lo fácil que es usar la plataforma INTEGRA.\n\n" .
                              "Dime con toda confianza, ¿con qué te gustaría empezar hoy?\n" .
                              "- 📋 Ver cómo iniciar un expediente o trámite.\n" .
                              "- 💳 Ver la credencial digital QR o reservar un turno.\n" .
                              "- 👨‍🏫 Cargar la factura ARCA de un alumno (si eres docente).\n" .
                              "- 🏥 Solicitar o ver un bono de prestación médica.\n\n" .
                              "¡Tú eliges y lo hacemos juntos paso a paso!",
                'source' => 'mentor_conversacional'
            ]);
        }

        // 1. Intentar responder vía API Gemini 1.5 Flash si está disponible
        $systemContext = "Eres INTEGRA Bot, el Asistente Virtual de Inteligencia Artificial, Compañero y Mentor Oficial de la plataforma INTEGRA (Obra Social / Mutual con 130.000 abonados).\n" .
                         "El usuario está en la pantalla: {$path} ({$contexto}).\n\n" .
                         "MISIÓN Y PERSONALIDAD:\n" .
                         "1. Sé extremadamente cálido, empático, afable y pedagógico. Tu meta es remover el miedo del usuario a usar el sistema y hacer que sienta que aprende fácilmente y que trabaja en equipo contigo.\n" .
                         "2. Si el usuario te saluda o expresa incertidumbre (ej: 'no sé qué hacer', 'soy nuevo', 'tengo miedo de tocar algo'), tranquilízalo con amabilidad: '¡No te preocupes! Aquí estoy para acompañarte paso a paso. Es súper fácil. ¿Con qué te gustaría empezar hoy? 😊'.\n" .
                         "3. Guíalo con diálogo fluido, cercano y estructurado en pasos sencillos con negritas y emojis amigables.\n" .
                         "4. Al final de cada explicación, hazle una pregunta cercana para mantener el diálogo activo y ayudarlo a seguir avanzando (ej: '¿Te gustaría que te muestre el siguiente paso?' o '¿Quieres que repasemos alguna otra parte?').\n\n" .
                         "CONOCIMIENTO DE PANTALLAS Y EXPEDIENTES:\n" .
                         "- DOCENTES / TERAPEUTAS (/app-docente/demo): Carga de alumnos, Facturas ARCA, aval de Directora por WhatsApp, billeteras virtuales.\n" .
                         "- REINTEGROS / TITULAR (/app-padre/demo): Solicitud de reintegro con Factura + Resolución OSP y conformidad diaria.\n" .
                         "- PRESTADORES MÉDICOS (/prestadores/demo): Solicitud de autorizaciones de internación/estudios, emisión e impresión de Bono Digital con QR y Hash MD5.\n" .
                         "- FARMACIAS CONVENIDAS (/farmacia/demo): Validador online con QR, Vademécum (40%, 70%, 100%) y Bono de dispensa.\n" .
                         "- AFILIADOS (/app-afiliado/credencial y /turnos): Credencial digital QR, token dinámico, cartilla y turnos.\n" .
                         "- ALTA DIRECCIÓN (/owner/mutual-dashboard): Totalizadores por sucursales (Chilecito, Córdoba, La Rioja, BsAs) y matriz por patologías.";

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

        // --- CONSULTA ESPECÍFICA: BALANCE / RENTABILIDAD / FINANZAS ---
        if (str_contains($promptLower, 'balance') || str_contains($promptLower, 'rentabilid') || str_contains($promptLower, 'grafic') || str_contains($promptLower, 'gráfic') || str_contains($promptLower, 'ingreso') || str_contains($promptLower, 'gasto') || str_contains($promptLower, 'costo')) {
            return "📊 **Informe Comercial & Balance Financiero de la Mutual (Mes en Curso)**\n\n" .
                   "¡Con gusto! Aquí tienes el reporte consolidado de rendimiento financiero y siniestralidad:\n\n" .
                   "- 💰 **Ingresos por Cápitas (130.000 Abonados):** **$485.200.000,00**\n" .
                   "- 🏥 **Costo Directo de Salud & Prestaciones:** **$181.000.000,00** *(Siniestralidad Salud: 37,3%)*\n" .
                   "- 🏢 **Costos Fijos Operativos & Estructura:** **$64.500.000,00**\n" .
                   "- 🛡️ **Ahorro Acumulado por Auditoría Médica con IA:** **+$92.500.000,00** *(Fraudes bloqueados & tope 3hs/día)*\n" .
                   "- 📈 **Superávit / Rentabilidad Neta:** **+$332.200.000,00** *(Margen Operativo Neto: +68.4%)*\n\n" .
                   "👉 [📊 Ver Cuadro de Mando Ejecutivo en Vivo](/owner/mutual-dashboard)";
        }

        // --- CONSULTA ESPECÍFICA: CONSUMO DE PAPEL POR ÁREA ---
        if (str_contains($promptLower, 'papel') || str_contains($promptLower, 'resma') || str_contains($promptLower, 'ecolog') || str_contains($promptLower, 'ecológ')) {
            return "🌿 **Informe de Auditoría Ecológica & Consumo de Papel por Área**\n\n" .
                   "Gracias a la implementación del **Bono Digital QR** y la **Credencial Digital**, el consumo de papelería se redujo un **84,7%**:\n\n" .
                   "- 📍 **Sucursal Chilecito:** 12 resmas ($45.200) ➔ *Ahorro digital: 88%*\n" .
                   "- 📍 **Sucursal La Rioja Central:** 18 resmas ($68.100) ➔ *Ahorro digital: 85%*\n" .
                   "- 📍 **Sucursal Córdoba:** 24 resmas ($89.400) ➔ *Ahorro digital: 82%*\n" .
                   "- 📍 **Sucursal Buenos Aires:** 30 resmas ($112.000) ➔ *Ahorro digital: 84%*\n\n" .
                   "💵 **Ahorro Neto en Papelería:** **-$1.450.000 / mes** (84 resmas actuales vs 550 resmas del periodo anterior en papel).\n\n" .
                   "👉 [📊 Abrir Panel de Control por Sucursal](/owner/mutual-dashboard)";
        }

        // --- CONSULTA ESPECÍFICA: INICIO DE EXPEDIENTE / TRÁMITE / LEGAJO ---
        if (str_contains($promptLower, 'expediente') || str_contains($promptLower, 'tramite') || str_contains($promptLower, 'trámite') || str_contains($promptLower, 'inici') || str_contains($promptLower, 'ingres') || str_contains($promptLower, 'abrir') || str_contains($promptLower, 'crear') || str_contains($promptLower, 'solicitud')) {
            return "📋 **Manual Oficial: Cómo Iniciar un Expediente o Trámite en INTEGRA**\n\n" .
                   "El inicio de un **Expediente Digital** depende de tu rol en la plataforma:\n\n" .
                   "1. 👨‍🏫 **Si eres Docente / Terapeuta (Educación Especial):**\n" .
                   "   - Ve a la **App del Docente**.\n" .
                   "   - Ingresa a **Mis Alumnos** ➔ Presiona **+ Nuevo Alumno / Cargar Legajo**.\n" .
                   "   - Sube la **Factura ARCA** y envía la solicitud de aval por WhatsApp a la **Directora de Escuela**.\n" .
                   "   - 👉 [👨‍🏫 Ir a la App del Docente para iniciar Expediente](/app-docente/demo)\n\n" .
                   "2. 👨‍👩‍👦 **Si eres Padre / Titular (Reintegros de Obra Social):**\n" .
                   "   - Ingresa a la **App del Titular** ➔ Toca en **Solicitar Reintegro**.\n" .
                   "   - Adjunta la factura abonada + **Resolución OSP** y firma la conformidad.\n" .
                   "   - 👉 [👨‍👩‍👦 Ir a la App del Titular para Solicitar Reintegro](/app-padre/demo)\n\n" .
                   "3. 🏥 **Si eres Clínica / Prestador Médico (Autorizaciones / Internaciones):**\n" .
                   "   - Ve a la **Red de Prestadores** ➔ Presiona **Nueva Solicitud de Autorización Médica / Internación**.\n" .
                   "   - Ingresa el código nomenclador, diagnóstico CIE-10 y adjunta la orden escaneada.\n" .
                   "   - 👉 [🏥 Ir a la Red de Prestadores para Autorización Médica](/prestadores/demo)\n\n" .
                   "4. 💊 **Si eres Farmacia Convenida (Validador de Medicamentos):**\n" .
                   "   - Valida el DNI o QR del afiliado y presiona **Validar Receta** para emitir el Bono Digital.\n" .
                   "   - 👉 [💊 Ir al Validador de Farmacias Convenidas](/farmacia/demo)";
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

