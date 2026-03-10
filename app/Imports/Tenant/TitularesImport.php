<?php

namespace App\Imports\Tenant;

use App\Models\Titular;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;

class TitularesImport implements ToModel, WithHeadingRow, WithValidation
{
    protected $empresaId;

    public function __construct($empresaId)
    {
        $this->empresaId = $empresaId;
    }

    public function model(array $row)
    {
        return new Titular([
            'empresa_id' => $this->empresaId,
            'nombre'     => $row['nombre_completo'],
            'dni'        => $row['dni'],
            'cuil'       => $row['cuil'] ?? null,
            'telefono'   => $row['telefono'] ?? null,
            'email'      => $row['email'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nombre_completo' => 'required|string|max:255',
            'dni'             => [
                'required', 
                'string', 
                'max:20',
                // Evitamos DNI duplicados dentro del mismo tenant (por seguridad, aunque el sistema general los permita unique global)
                Rule::unique('titulars', 'dni') // Ojo: si la migración DNI tiene unique global, esto saltará si existe en otro Tenant.
            ],
            'email'           => 'nullable|email'
        ];
    }
}
