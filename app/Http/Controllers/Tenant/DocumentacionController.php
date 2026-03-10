<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\DocenteDocumento;
use App\Models\Docente;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class DocumentacionController extends Controller
{
    private function getEmpresaId()
    {
        return Auth::user()->empresa_id ?? session('impersonated_tenant_id');
    }

    /**
     * Sube un documento vinculado a un docente (Cert. Buena conducta, Titulo, etc)
     */
    public function store(Request $request, $docente_id)
    {
        $docente = Docente::where('empresa_id', $this->getEmpresaId())->findOrFail($docente_id);

        $request->validate([
            'tipo_documento' => 'required|string|max:255',
            'archivo' => 'required|file|mimes:pdf,jpg,jpeg,png|max:4096', // Max 4MB
            'fecha_vencimiento' => 'nullable|date'
        ]);

        // Guardado físico del archivo en storage/app/public/documentos_docentes
        // Se aísla por Tenant (EmpresaId) y Docente para un Storage seguro multitenant
        $empresaId = $this->getEmpresaId();
        $path = $request->file('archivo')->store("tenant/{$empresaId}/docentes/{$docente_id}", 'public');

        DocenteDocumento::create([
            'docente_id' => $docente->id,
            'tipo_documento' => $request->tipo_documento,
            'ruta_archivo' => $path,
            'fecha_vencimiento' => $request->fecha_vencimiento,
            'estado' => 'pendiente' // Pendiente de auditoría por defecto
        ]);

        return redirect()->back()->with('success', 'Documento adjuntado exitosamente a la ficha.');
    }

    /**
     * Descarga segura (Validando que el archivo realmente le pertenezca a la institucion)
     */
    public function download($id)
    {
        $documento = DocenteDocumento::with('docente')->findOrFail($id);

        // Seguridad estricta Tenant
        if ($documento->docente->empresa_id !== $this->getEmpresaId()) {
            abort(403, 'Acceso Denegado a este recurso.');
        }

        return Storage::disk('public')->download($documento->ruta_archivo, basename($documento->ruta_archivo));
    }

    /**
     * Elimina el documento físico y de Base de Datos
     */
    public function destroy($id)
    {
        $documento = DocenteDocumento::with('docente')->findOrFail($id);

        if ($documento->docente->empresa_id !== $this->getEmpresaId()) {
            abort(403);
        }

        // Eliminar del Storage
        if (Storage::disk('public')->exists($documento->ruta_archivo)) {
            Storage::disk('public')->delete($documento->ruta_archivo);
        }

        $documento->delete();

        return redirect()->back()->with('success', 'Certificado o archivo eliminado correctamente.');
    }
}
