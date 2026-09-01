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

        // LECTURA DE REQUISITOS (Simulando Empresa 1 y Docente Demo 1)
        $empresaIdDemo = 1;
        $docenteIdDemo = 1;
        
        $tiposDocumentos = \App\Models\TipoDocumento::where('empresa_id', $empresaIdDemo)
            ->where('entidad_tipo', 'docente')
            ->get();
            
        // Le adjuntamos la subida actual
        foreach ($tiposDocumentos as $tipo) {
            $subido = \App\Models\DocumentoSubido::where('tipo_documento_id', $tipo->id)
                ->where('entidad_id', $docenteIdDemo)
                ->where('entidad_tipo', 'docente')
                ->latest()
                ->first();
                
            $tipo->estado_subida = $subido ? $subido->estado : 'sin_entregar';
            $tipo->comentarios = $subido ? $subido->comentarios_auditor : null;
        }

        return view('pwa.docente.dashboard', compact(
            'docenteNombre', 
            'montoCobrado', 
            'montoPretendido', 
            'alumnosDemo',
            'tiposDocumentos'
        ));
    }

    /**
     * Procesador de Archivos Docentes (Vía Móvil)
     */
    public function uploadDocument(Request $request)
    {
        $request->validate([
            'documento' => 'required|file|max:15240', // Max 15MB
            'tipo_documento' => 'required|string',
            'alumno_nombre' => 'nullable|string'
        ]);

        try {
            $file = $request->file('documento');
            
            // Forzamos "public" o "bunny" de acuerdo a la configuracion.
            $path = $file->store('documentos_docentes', 'public');

            // --- Búsqueda del Tipo Documento Real ---
            // Como esto es demo, buscaremos el primero que coincida por nombre. En prod, pásalo por ID real.
            $tipoDoc = \App\Models\TipoDocumento::where('nombre', $request->tipo_documento)->first();

            if ($tipoDoc) {
                \App\Models\DocumentoSubido::create([
                    'empresa_id' => 1, // Demo ID
                    'tipo_documento_id' => $tipoDoc->id,
                    'entidad_tipo' => 'docente',
                    'entidad_id' => 1, // Docente de prueba ID
                    'ruta_archivo' => $path,
                    'estado' => 'pendiente',
                    'fecha_vencimiento' => $tipoDoc->vencimiento_dias ? \Carbon\Carbon::now()->addDays($tipoDoc->vencimiento_dias) : null
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'El archivo se ha subido correctamente y está pendiente de Auditoría.',
                'path' => $path
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al subir el archivo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Búsqueda en Vivo (AJAX) para PWA
     */
    /**
     * Búsqueda en Vivo (AJAX) para PWA
     */
    public function search(Request $request)
    {
        $term = trim($request->query('q', ''));
        $type = $request->query('type');

        if (!$term && $type !== 'alumnos_by_titular') {
            return response()->json([]);
        }

        // Auto-seed sample alumnos if familiars table is empty
        if (\App\Models\Familiar::count() === 0) {
            $titularPadre = \App\Models\Titular::first();
            if ($titularPadre) {
                \App\Models\Familiar::create([
                    'titular_id' => $titularPadre->id,
                    'nombre' => 'Sofía Cortez',
                    'dni' => '54123987',
                    'parentesco' => 'Hijo',
                    'tiene_patologia' => true,
                    'diagnostico' => 'Fonoaudiología',
                ]);
                \App\Models\Familiar::create([
                    'titular_id' => $titularPadre->id,
                    'nombre' => 'Lucas Corso',
                    'dni' => '55987123',
                    'parentesco' => 'Hijo',
                    'tiene_patologia' => true,
                    'diagnostico' => 'Terapia Ocupacional',
                ]);
                \App\Models\Familiar::create([
                    'titular_id' => $titularPadre->id,
                    'nombre' => 'Mateo Giménez',
                    'dni' => '52345678',
                    'parentesco' => 'Hijo',
                    'tiene_patologia' => false,
                ]);
            }
        }

        if ($type === 'titulares') {
            $titulares = \App\Models\Titular::with(['familiares.escuela', 'familiares.diagnostico'])
                ->where(function($q) use ($term) {
                    $q->where('nombre', 'LIKE', "%{$term}%")
                      ->orWhere('dni', 'LIKE', "%{$term}%");
                })
                ->take(50)
                ->get();

            return response()->json($titulares);
        }

        if ($type === 'alumnos') {
            $query = \App\Models\Familiar::with(['escuela', 'titular', 'diagnostico']);

            if ($term) {
                $query->where(function($q) use ($term) {
                    $q->where('nombre', 'LIKE', "%{$term}%")
                      ->orWhere('dni', 'LIKE', "%{$term}%");
                });
            }

            if ($request->has('titular_id') && $request->query('titular_id')) {
                $query->where('titular_id', $request->query('titular_id'));
            }

            $alumnos = $query->take(50)->get();
            return response()->json($alumnos);
        }
        
        if ($type === 'alumnos_by_titular') {
            $query = \App\Models\Familiar::with(['escuela', 'titular', 'diagnostico'])
                ->where('titular_id', $request->query('titular_id'));

            $alumnos = $query->take(50)->get();
            return response()->json($alumnos);
        }

        if ($type === 'escuelas') {
            $escuelas = \App\Models\Escuela::where(function($q) use ($term) {
                    $q->where('nombre', 'LIKE', "%{$term}%");
                    if (\Illuminate\Support\Facades\Schema::hasColumn('escuelas', 'cue')) {
                        $q->orWhere('cue', 'LIKE', "%{$term}%");
                    }
                })
                ->take(50)
                ->get();

            return response()->json($escuelas);
        }

        return response()->json([]);
    }
}
