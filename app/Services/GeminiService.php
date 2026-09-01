<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key') ?? env('GEMINI_API_KEY', '');
        $this->model = config('services.gemini.model') ?? env('GEMINI_MODEL', 'gemini-1.5-flash');
    }

    /**
     * Envía una consulta a la API de Gemini 1.5 Flash.
     *
     * @param string $prompt Mensaje del usuario
     * @param string|null $systemContext Contexto del sistema/rol
     * @return array ['success' => bool, 'response' => string]
     */
    public function ask(string $prompt, ?string $systemContext = null): array
    {
        if (empty($this->apiKey)) {
            return [
                'success' => false,
                'response' => 'La API Key de Gemini no está configurada en el servidor. Por favor verifica la variable GEMINI_API_KEY en el archivo .env.'
            ];
        }

        $systemPrompt = $systemContext ?? $this->getDefaultSystemPrompt();
        $fullPrompt = "Instrucciones de Sistema: " . $systemPrompt . "\n\nConsulta del Usuario: " . $prompt;

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($url, [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $fullPrompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 1000,
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

                if ($text) {
                    return [
                        'success' => true,
                        'response' => trim($text)
                    ];
                }

                return [
                    'success' => false,
                    'response' => 'No se obtuvo una respuesta válida del asistente de IA.'
                ];
            }

            Log::error('Gemini API Error: ' . $response->body());
            
            $errorMessage = 'Ocurrió un inconveniente al consultar con el Asistente Virtual.';
            $errorData = $response->json();
            if (isset($errorData['error']['message'])) {
                $errorMessage .= ' (' . $errorData['error']['message'] . ')';
            }

            return [
                'success' => false,
                'response' => $errorMessage
            ];

        } catch (\Throwable $e) {
            Log::error('Gemini Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'response' => 'Error de conexión con la IA: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Analiza una imagen/PDF de comprobante de transferencia bancaria usando Gemini Vision.
     *
     * @param string $filePath Ruta local del archivo de imagen
     * @return array ['success' => bool, 'data' => array]
     */
    public function analizarComprobantePago(string $filePath): array
    {
        if (empty($this->apiKey)) {
            return [
                'success' => false,
                'data' => [],
                'error' => 'API Key de Gemini no configurada.'
            ];
        }

        if (!file_exists($filePath)) {
            return [
                'success' => false,
                'data' => [],
                'error' => 'Archivo de comprobante no encontrado.'
            ];
        }

        $fileData = file_get_contents($filePath);
        $base64Data = base64_encode($fileData);
        $mimeType = mime_content_type($filePath) ?: 'image/jpeg';

        $prompt = "Analiza este comprobante de transferencia bancaria o pago (de entidades como Banco Santander, Banco Provincia, MercadoPago, DollarApp, Ualá, etc.) y extrae exactamente la información en formato JSON válido plano.\n" .
                  "Campos a extraer (si no está presente algún dato, pon null):\n" .
                  "- nro_comprobante: (string) número de comprobante, transacción, operación o referencia\n" .
                  "- monto: (float/number) el monto exacto dinero transferido\n" .
                  "- fecha_pago: (string) fecha del comprobante en formato YYYY-MM-DD\n" .
                  "- banco_origen: (string) banco o billetera emisora (ej: Santander, Banco Provincia, DollarApp, MercadoPago, etc.)\n" .
                  "- titular_origen: (string) nombre del pagador o titular emisor\n\n" .
                  "Responde ÚNICAMENTE con el objeto JSON plano sin comillas tipográficas, sin Markdown ```json y sin texto previo o posterior.";

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->timeout(35)->post($url, [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $prompt],
                            [
                                'inlineData' => [
                                    'mimeType' => $mimeType,
                                    'data' => $base64Data
                                ]
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.1,
                    'maxOutputTokens' => 800,
                ]
            ]);

            if ($response->successful()) {
                $rawText = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '';
                $cleanJson = preg_replace('/^```(?:json)?\s*|\s*```$/i', '', trim($rawText));
                $parsed = json_decode($cleanJson, true);

                if (is_array($parsed)) {
                    return [
                        'success' => true,
                        'data' => $parsed
                    ];
                }
            }

            Log::error('Gemini Vision Error: ' . $response->body());
            return [
                'success' => false,
                'data' => [],
                'error' => 'No se pudo decodificar el formato del comprobante.'
            ];

        } catch (\Throwable $e) {
            Log::error('Gemini Vision Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'data' => [],
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Analiza una Factura Electrónica de ARCA / AFIP (imagen o PDF) y extrae datos de código QR y texto.
     *
     * @param string $filePath Ruta local del archivo
     * @return array ['success' => bool, 'data' => array]
     */
    public function analizarFacturaArca(string $filePath): array
    {
        if (empty($this->apiKey) || !file_exists($filePath)) {
            return ['success' => false, 'data' => [], 'error' => 'Archivo no encontrado o API Key faltante.'];
        }

        $fileData = file_get_contents($filePath);
        $base64Data = base64_encode($fileData);
        $mimeType = mime_content_type($filePath) ?: 'image/jpeg';

        $prompt = "Analiza esta Factura Electrónica ARCA / AFIP (Argentina). Lee el código QR y los campos del texto en la factura y extrae exclusivamente un objeto JSON plano con la siguiente información:\n" .
                  "- nro_factura: (string) número completo de comprobante (ej: 00001-00000123)\n" .
                  "- punto_venta: (string) código de punto de venta (ej: 00001)\n" .
                  "- cuit_emisor: (string) CUIT del docente/profesional emisor sin guiones\n" .
                  "- razon_social_emisor: (string) nombre o razón social del docente/emisor\n" .
                  "- domicilio_emisor: (string) domicilio fiscal\n" .
                  "- cae: (string) Código de Autorización Electrónico CAE (ej: 74251890234512)\n" .
                  "- vencimiento_cae: (string) fecha de vencimiento CAE en formato YYYY-MM-DD\n" .
                  "- monto_total: (float) monto total exacto de la factura\n" .
                  "- qr_raw_data: (string) texto embebido en el código QR de AFIP si es visible\n\n" .
                  "Responde ÚNICAMENTE con el objeto JSON plano sin Markdown ```json y sin comentarios.";

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";

        try {
            $response = Http::withHeaders(['Content-Type' => 'application/json'])->timeout(35)->post($url, [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $prompt],
                            ['inlineData' => ['mimeType' => $mimeType, 'data' => $base64Data]]
                        ]
                    ]
                ],
                'generationConfig' => ['temperature' => 0.1, 'maxOutputTokens' => 800]
            ]);

            if ($response->successful()) {
                $rawText = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '';
                $cleanJson = preg_replace('/^```(?:json)?\s*|\s*```$/i', '', trim($rawText));
                $parsed = json_decode($cleanJson, true);

                if (is_array($parsed)) {
                    return ['success' => true, 'data' => $parsed];
                }
            }

            return ['success' => false, 'data' => [], 'error' => 'No se pudo decodificar la factura ARCA.'];
        } catch (\Throwable $e) {
            return ['success' => false, 'data' => [], 'error' => $e->getMessage()];
        }
    }

    /**
     * Analiza un documento en papel / imagen de Resolución del Instituto usando Gemini Vision.
     */
    public function analizarResolucionInstituto(string $filePath): array
    {
        if (empty($this->apiKey) || !file_exists($filePath)) {
            return ['success' => false, 'data' => [], 'error' => 'Archivo no encontrado o API Key faltante.'];
        }

        $fileData = file_get_contents($filePath);
        $base64Data = base64_encode($fileData);
        $mimeType = mime_content_type($filePath) ?: 'image/jpeg';

        $prompt = "Analiza este documento oficial en papel / foto de Resolución del Instituto o Ministerio para Educación Especial. Extrae la información en JSON plano con los siguientes campos:\n" .
                  "- nro_resolucion: (string) número o código de resolución\n" .
                  "- nombre_alumno: (string) nombre del alumno/beneficiario mencionado\n" .
                  "- dni_alumno: (string) DNI del alumno si figura\n" .
                  "- fecha_resolucion: (string) fecha de emisión YYYY-MM-DD\n" .
                  "- fecha_vigencia_hasta: (string) fecha de vencimiento o vigencia YYYY-MM-DD\n" .
                  "- diagnostico_resumido: (string) resumen del diagnóstico o cobertura aprobada\n" .
                  "- horas_aprobadas: (int) cantidad de horas de atención aprobadas\n\n" .
                  "Responde ÚNICAMENTE con el objeto JSON plano sin Markdown ```json.";

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";

        try {
            $response = Http::withHeaders(['Content-Type' => 'application/json'])->timeout(35)->post($url, [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $prompt],
                            ['inlineData' => ['mimeType' => $mimeType, 'data' => $base64Data]]
                        ]
                    ]
                ],
                'generationConfig' => ['temperature' => 0.1, 'maxOutputTokens' => 800]
            ]);

            if ($response->successful()) {
                $rawText = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '';
                $cleanJson = preg_replace('/^```(?:json)?\s*|\s*```$/i', '', trim($rawText));
                $parsed = json_decode($cleanJson, true);

                if (is_array($parsed)) {
                    return ['success' => true, 'data' => $parsed];
                }
            }

            return ['success' => false, 'data' => [], 'error' => 'No se pudo decodificar la resolución.'];
        } catch (\Throwable $e) {
            return ['success' => false, 'data' => [], 'error' => $e->getMessage()];
        }
    }

    /**
     * Contexto de sistema predeterminado para INTEGRA Platform.
     */
    protected function getDefaultSystemPrompt(): string
    {
        return "Eres INTEGRA Bot, un asistente virtual de IA experto y afable integrado en la plataforma INTEGRA (Sistema de Gestión y Auditoría de Facturación, Alumnos, Docentes, Escuelas y Titulares para obras sociales y entidades educativas/asistenciales en Argentina).\n" .
               "Tu objetivo es ayudar a los usuarios (Administradores, Auditores y Docentes) de forma concisa, clara, profesional y amable.\n" .
               "Responde en español de Argentina de forma estructurada, usando listas y negritas cuando sea conveniente.\n" .
               "Si el usuario pregunta cómo realizar una tarea (importar alumnos, subir documentación, auditar facturas), explícaselo paso a paso con amabilidad.";
    }
}
