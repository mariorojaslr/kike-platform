<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\TipoDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TipoDocumentoController extends Controller
{
    private function getEmpresaId()
    {
        return Auth::user()->empresa_id;
    }

    public function index()
    {
        $tipos = TipoDocumento::where('empresa_id', $this->getEmpresaId())->get();
        return view('dashboards.tenant.config.tipo_documentos.index', compact('tipos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'entidad_tipo' => 'required|in:docente,alumno',
            'nombre' => 'required|string|max:255',
            'es_obligatorio' => 'boolean',
            'vencimiento_dias' => 'nullable|integer|min:0'
        ]);

        TipoDocumento::create([
            'empresa_id' => $this->getEmpresaId(),
            'entidad_tipo' => $request->entidad_tipo,
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'es_obligatorio' => $request->has('es_obligatorio'),
            'vencimiento_dias' => $request->vencimiento_dias
        ]);

        return redirect()->back()->with('success', 'Tipo de documento agregado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $requisito = TipoDocumento::where('empresa_id', $this->getEmpresaId())->findOrFail($id);

        $request->validate([
            'entidad_tipo' => 'required|in:docente,alumno',
            'nombre' => 'required|string|max:255',
            'vencimiento_dias' => 'nullable|integer|min:0'
        ]);

        $requisito->update([
            'entidad_tipo' => $request->entidad_tipo,
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'es_obligatorio' => $request->has('es_obligatorio'),
            'vencimiento_dias' => $request->vencimiento_dias
        ]);

        return redirect()->back()->with('success', 'Tipo de documento actualizado correctamente.');
    }

    public function destroy($id)
    {
        $requisito = TipoDocumento::where('empresa_id', $this->getEmpresaId())->findOrFail($id);
        $requisito->delete();

        return redirect()->back()->with('success', 'Tipo de documento eliminado correctamente.');
    }
}
