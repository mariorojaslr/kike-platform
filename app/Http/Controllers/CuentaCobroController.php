<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CuentaCobro;

class CuentaCobroController extends Controller
{
    /**
     * Crear una nueva cuenta o billetera bancaria
     */
    public function store(Request $request)
    {
        $request->validate([
            'banco_nombre' => 'required|string|max:255',
            'titular' => 'nullable|string|max:255',
            'cbu_cvu' => 'nullable|string|max:255',
            'alias' => 'nullable|string|max:255',
            'instrucciones' => 'nullable|string|max:1000',
        ]);

        CuentaCobro::create([
            'banco_nombre' => $request->banco_nombre,
            'titular' => $request->titular,
            'cbu_cvu' => $request->cbu_cvu,
            'alias' => $request->alias,
            'instrucciones' => $request->instrucciones,
            'activo' => true,
        ]);

        return back()->with('success', 'Cuenta o billetera agregada exitosamente.');
    }

    /**
     * Actualizar una cuenta o billetera existente
     */
    public function update(Request $request, $id)
    {
        $cuenta = CuentaCobro::findOrFail($id);

        $request->validate([
            'banco_nombre' => 'required|string|max:255',
            'titular' => 'nullable|string|max:255',
            'cbu_cvu' => 'nullable|string|max:255',
            'alias' => 'nullable|string|max:255',
            'instrucciones' => 'nullable|string|max:1000',
        ]);

        $cuenta->update([
            'banco_nombre' => $request->banco_nombre,
            'titular' => $request->titular,
            'cbu_cvu' => $request->cbu_cvu,
            'alias' => $request->alias,
            'instrucciones' => $request->instrucciones,
        ]);

        return back()->with('success', 'Cuenta bancaria o billetera actualizada correctamente.');
    }

    /**
     * Alternar estado (activo / inactivo)
     */
    public function toggleStatus($id)
    {
        $cuenta = CuentaCobro::findOrFail($id);
        $cuenta->activo = !$cuenta->activo;
        $cuenta->save();

        return back()->with('success', 'Estado de la cuenta actualizado.');
    }

    /**
     * Eliminar cuenta de cobro
     */
    public function destroy($id)
    {
        $cuenta = CuentaCobro::findOrFail($id);
        $cuenta->delete();

        return back()->with('success', 'Cuenta bancaria eliminada.');
    }
}
