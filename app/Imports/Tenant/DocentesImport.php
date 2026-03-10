<?php

namespace App\Imports\Tenant;

use App\Models\Docente;
use App\Models\Formacion;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class DocentesImport implements ToModel, WithHeadingRow, WithValidation
{
    protected $empresaId;

    public function __construct($empresaId)
    {
        $this->empresaId = $empresaId;
    }

    public function model(array $row)
    {
        // Buscamos el ID de la Formación Profesional si enviaron el código
        $formacionId = null;
        if (!empty($row['codigo_formacion_pro'])) {
            // Buscamos la formación por su nombre o código si implementaste código para formaciones
            // Asumo que Formacion tiene un campo "nombre" y usaremos el código como string para buscarla
            $formacion = Formacion::where('nombre', 'LIKE', '%' . $row['codigo_formacion_pro'] . '%')->first();
            $formacionId = $formacion ? $formacion->id : null;
        }

        return new Docente([
            'empresa_id' => $this->empresaId,
            'nombre'     => $row['nombre_completo'],
            'dni'        => $row['dni'] ?? null,
            'telefono'   => $row['telefono'] ?? null,
            'email'      => $row['email'] ?? null,
            'direccion'  => $row['direccion'] ?? null,
            'formacion_id' => $formacionId,
            'validado_auditoria' => 0,
        ]);
    }

    public function rules(): array
    {
        return [
            'nombre_completo' => 'required|string',
            'dni' => 'nullable|numeric',
            'email' => 'nullable|email'
        ];
    }
}
