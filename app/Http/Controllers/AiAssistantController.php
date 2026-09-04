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
            'prompt' => 'required|string|min:2|max:1500',
            'contexto' => 'nullable|string|max:500',
            'path' => 'nullable|string|max:255'
        ]);

        $prompt = trim($request->input('prompt'));
        $contexto = $request->input('contexto', '');
        $path = $request->input('path', '');

        // 1. Intentar responder vía API Gemini 1.5 Flash si está disponible
        $systemContext = "Eres INTEGRA Bot, el Asistente Virtual Inteligente y Manual Interactivo Oficial de la plataforma INTEGRA (Obra Social / Mutual con 130.000 abonados).\n" .
                         "El usuario está en la pantalla: {$path} ({$contexto}).\n" .
                         "Guíalo paso a paso como un manual de uso amable, claro, amigable y profesional. Usa viñetas y negritas.";

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

        // --- ROL: AFILIADO (Credencial y Turnos) ---
        if (str_contains($path, 'afiliado') || str_contains($promptLower, 'credencial') || str_contains($promptLower, 'token') || str_contains($promptLower, 'turno')) {
            if (str_contains($promptLower, 'credencial') || str_contains($promptLower, 'qr') || str_contains($promptLower, 'token')) {
                return "💳 **Manual del Afiliado: Credencial Digital y Token QR**\n\n" .
                       "1. **Acceso:** Tu credencial digital está disponible en la pantalla principal `/app-afiliado/credencial`.\n" .
                       "2. **Código QR Dinámico:** El código QR se actualiza en tiempo real con un token de seguridad para presentar en mostrador de clínicas o farmacias.\n" .
                       "3. **Grupo Familiar:** Puedes cambiar de pestaña en la credencial para mostrar la tarjeta de tus hijos o cónyuge.\n" .
                       "4. **Estado Prestacional:** La franja verde indica que tu cápita está **ACTIVA Y AL DÍA**.";
            }

            if (str_contains($promptLower, 'turno') || str_contains($promptLower, 'cartilla') || str_contains($promptLower, 'medico')) {
                return "🩺 **Manual del Afiliado: Cartilla Médica y Turnos**\n\n" .
                       "1. **Buscador de Cartilla:** Ingresa a `/app-afiliado/turnos` para buscar por especialidad (ej: Pediatría, Traumatología, Cardiología).\n" .
                       "2. **Seleccionar Sanatorio:** Elige la clínica o sanatorio en convenio (ej: Sanatorio Central, Instituto de Traumatología).\n" .
                       "3. **Reserva de Turno:** Presiona **Solicitar Turno** o **Telemedicina** para recibir la confirmación instantánea en tu celular.";
            }

            return "👤 **Manual de la App del Afiliado**\n\n" .
                   "Desde esta aplicación puedes:\n" .
                   "- Presentar tu **Credencial Digital QR** en clínicas y farmacias.\n" .
                   "- Reservar **Turnos Médicos** y Telemedicina.\n" .
                   "- Consultar la **Cartilla Médica** oficial de la mutual.";
        }

        // --- ROL: DOCENTE / TERAPEUTA (PWA) ---
        if (str_contains($path, 'docente') || str_contains($promptLower, 'factura') || str_contains($promptLower, 'parlante') || str_contains($promptLower, 'alumno')) {
            if (str_contains($promptLower, 'parlante') || str_contains($promptLower, 'voz') || str_contains($promptLower, 'dicta')) {
                return "🎙️ **Manual Docente: Modo Parlante / Búsqueda por Voz**\n\n" .
                       "1. **Tocar el Micrófono:** En la pantalla del docente, toca el botón de micrófono o selecciona la pestaña **Modo Parlante**.\n" .
                       "2. **Dictado:** Dicta el nombre del alumno, del titular o el DNI (ej: *Abayay Ramón Martín*).\n" .
                       "3. **Verificación Automática:** El asistente verificará la coincidencia en la BD de la Mutual y completará los datos del legajo automáticamente.";
            }

            if (str_contains($promptLower, 'factura') || str_contains($promptLower, 'arca') || str_contains($promptLower, 'afip')) {
                return "📄 **Manual Docente: Carga de Factura ARCA**\n\n" .
                       "1. **Acceso:** Ve al botón **Mis Alumnos** o selecciona al estudiante en la lista.\n" .
                       "2. **Subir Comprobante:** Toca el botón **Subir Factura ARCA**.\n" .
                       "3. **Compresión Inteligente:** El sistema optimiza la imagen/PDF automáticamente en tu celular de 10MB a 300KB.\n" .
                       "4. **Validación:** Una vez subida, la billetera actualizará el monto hacia la pestaña **Lo que vas a cobrar**.";
            }

            return "👨‍🏫 **Manual del Portal de Terapeutas y Docentes**\n\n" .
                   "En este portal puedes:\n" .
                   "- Dar de alta a un **Alumno Nuevo** por voz o método manual.\n" .
                   "- Subir la **Factura ARCA** y enviar el enlace de firma por WhatsApp a la **Directora de Escuela**.\n" .
                   "- Consultar tus **Billeteras Gemelas** (*Lo que vas a cobrar* vs *Lo que deberías cobrar*).";
        }

        // --- ROL: DIRECTORA DE ESCUELA ---
        if (str_contains($path, 'directora') || str_contains($promptLower, 'directora') || str_contains($promptLower, 'asistencia')) {
            return "🏫 **Manual del Portal de Directora de Escuela**\n\n" .
                   "1. **Revisión de Alumnos:** En `/directora/asistencias` verás a las docentes que atienden alumnos en tu institución.\n" .
                   "2. **Límite Prestacional:** El sistema controla el tope máximo de 3hs/día por alumno (60hs/mes).\n" .
                   "3. **Firma Digital 1-Clic:** Presiona **Certificar Asistencias** para firmar digitalmente.\n" .
                   "4. **Trazabilidad Institucional:** Cada firma registra `[Nombre Directora + Escuela + Fecha + Hora + IP]`.";
        }

        // --- ROL: PADRE / TITULAR (REINTEGROS) ---
        if (str_contains($path, 'padre') || str_contains($promptLower, 'padre') || str_contains($promptLower, 'reintegro')) {
            return "👨‍👩‍👦 **Manual del Titular: Módulo de Reintegros**\n\n" .
                   "1. **Solicitud:** En `/app-padre/demo`, carga la factura de la docente abonada a cuenta.\n" .
                   "2. **Resolución OSP:** Adjunta la foto de la Resolución del Instituto/Obra Social.\n" .
                   "3. **Conformidad:** Firma digitalmente la conformidad del servicio para habilitar el reintegro en tu cbu/billetera.";
        }

        // --- ROL: FARMACIA CONVENIDA ---
        if (str_contains($path, 'farmacia') || str_contains($promptLower, 'farmacia') || str_contains($promptLower, 'vademecum') || str_contains($promptLower, 'dispensa')) {
            return "💊 **Manual del Validador de Farmacias Convenidas**\n\n" .
                   "1. **Validar Afiliado:** Ingresa el DNI o escanea el QR de la Credencial Digital.\n" .
                   "2. **Buscador Vademécum:** Selecciona el medicamento para aplicar la cobertura automática (Ambulatorio 40%, Crónicos 70%, Discapacidad/PMI 100%).\n" .
                   "3. **Bono Digital:** Presiona **Validar Receta** para emitir el bono con código de autorización en tiempo real y QR para la liquidación quincenal.";
        }

        // --- ROL: PRESTADORES Y CLÍNICAS ---
        if (str_contains($path, 'prestadores') || str_contains($promptLower, 'bono') || str_contains($promptLower, 'internacion') || str_contains($promptLower, 'orden')) {
            return "🏥 **Manual de la Red de Prestadores Médicos**\n\n" .
                   "1. **Solicitar Autorización:** Toca el botón verde **Solicitar Autorización Médica** para pedir internaciones, resonancias o cirugías.\n" .
                   "2. **Ver e Imprimir Bono:** Toca **Imprimir Bono** en cualquier orden autorizada para desplegar el documento oficial con código QR y sello de Auditoría Médica.\n" .
                   "3. **Exportación:** Presiona el botón de impresión para guardar el comprobante en PDF.";
        }

        // --- ROL: DUEÑO / EJECUTIVO ---
        if (str_contains($path, 'owner') || str_contains($promptLower, 'sucursal') || str_contains($promptLower, 'patologia') || str_contains($promptLower, 'chilecito')) {
            return "👑 **Manual del Cuadro de Mando Ejecutivo (Alta Dirección)**\n\n" .
                   "1. **Filtro por Sucursal:** Utiliza el selector en el encabezado para alternar entre la **Visión Global** o las sedes de **Chilecito, La Rioja, Córdoba o Buenos Aires**.\n" .
                   "2. **Matriz Epidemiológica:** Revisa el ranking de enfermedades prevalentes, casos activos y costo promedio por paciente.\n" .
                   "3. **Control de Siniestralidad:** Monitorea la siniestralidad de salud (37.3%) y el ahorro acumulado por la auditoría con IA ($92.5M).";
        }

        // --- RESPUESTA GENÉRICA POR DEFECTO ---
        return "🤖 **Asistente Virtual e Instrucciones de la Plataforma INTEGRA**\n\n" .
               "¡Hola! Puedo orientarte en el uso de cualquier pantalla del sistema:\n\n" .
               "- 💳 **Afiliados:** Credencial digital QR y reserva de turnos.\n" .
               "- 👨‍🏫 **Docentes:** Carga de facturas, modo parlante y legajos.\n" .
               "- 🏫 **Directoras:** Certificación digital de asistencias.\n" .
               "- 💊 **Farmacias:** Validador online de vademécum y dispensa.\n" .
               "- 🏥 **Prestadores:** Emisión e impresión de bonos digitales.\n" .
               "- 👑 **Ejecutivo:** Totalizadores por sucursal y patologías.\n\n" .
               "Escribe tu consulta o selecciona uno de los botones sugeridos.";
    }
}
