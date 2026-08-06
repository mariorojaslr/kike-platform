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
     * Procesa consultas enviadas por el widget de Asistente IA.
     */
    public function query(Request $request): JsonResponse
    {
        $request->validate([
            'prompt' => 'required|string|min:2|max:1500',
            'contexto' => 'nullable|string|max:500'
        ]);

        $systemContext = null;
        if ($request->filled('contexto')) {
            $systemContext = "Contexto actual del usuario en la pantalla: " . $request->contexto . "\n" .
                             "Ayuda al usuario a resolver su duda de manera específica sobre esta pantalla.";
        }

        $result = $this->geminiService->ask($request->input('prompt'), $systemContext);

        return response()->json($result);
    }
}
