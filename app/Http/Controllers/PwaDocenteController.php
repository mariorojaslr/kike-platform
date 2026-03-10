<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Docente;
use App\Models\Familiar;

class PwaDocenteController extends Controller
{
    /**
     * Vista de simulación del Ecosistema del Docente/Terapeuta
     */
    public function demo(Request $request)
    {
        // En un entorno de producción, aquí se validaría un token único en la URL o sesión
        // $token = $request->query('token');
        // $docente = Docente::where('token_acceso', $token)->firstOrFail();
        
        // --- DATA DE DEMOSTRACIÓN PARA EL OWNER ---
        $docenteNombre = 'Prof. Andrea V. (Demo)';
        
        // Simulación financiera y gamificada
        $montoCobrado = 45000; // Dinero listo a cobrar
        $montoPretendido = 15000; // Dinero en auditoría (padre aceptó, falta admin)
        
        $alumnosDemo = [
            (object)['id' => 1, 'nombre' => 'Mateo Giménez', 'curso' => 'Terapia Ocupacional', 'estado' => 'aprobado'],
            (object)['id' => 2, 'nombre' => 'Sofía Cortez', 'curso' => 'Fonoaudiología', 'estado' => 'pendiente'],
            (object)['id' => 3, 'nombre' => 'Lucas Benítez', 'curso' => 'Psicología Infantil', 'estado' => 'sin_informar'],
        ];

        return view('pwa.docente.dashboard', compact(
            'docenteNombre', 
            'montoCobrado', 
            'montoPretendido', 
            'alumnosDemo'
        ));
    }

    /**
     * Procesador de Archivos Docentes (Vía Móvil)
     */
    public function uploadDocument(Request $request)
    {
        $request->validate([
            'documento' => 'required|file|max:10240', // Max 10MB
            'tipo_documento' => 'required|string',
            'alumno_nombre' => 'nullable|string'
        ]);

        try {
            $file = $request->file('documento');
            
            // Si tuviéramos BUNNY NET aquí usaríamos 'bunny', pero 
            // como la instrucción fue usar bunny.net cuando corresponda, 
            // el config filesystems.php tiene "bunny". 
            // No obstante, si no está configurado al 100% en el .env con sus keys reales, 
            // fallará en producción. Así que lo guardaremos en un disco específico preparado.
            // Para la maqueta, forzaremos public.
            
            $path = $file->store('documentos_docentes', 'public');

            // Retornamos JSON porque quien llama es la PWA vía fetch()
            return response()->json([
                'success' => true,
                'message' => 'El archivo se ha subido correctamente a los servidores de Kike.',
                'path' => $path
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al subir el archivo: ' . $e->getMessage()
            ], 500);
        }
    }
}
