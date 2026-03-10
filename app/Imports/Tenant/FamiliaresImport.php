<?php

namespace App\Imports\Tenant;

use App\Models\Familiar;
use App\Models\Titular;
use App\Models\Diagnostico;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class FamiliaresImport implements ToModel, WithHeadingRow, WithValidation
{
    protected $empresaId;

    public function __construct($empresaId)
    {
        $this->empresaId = $empresaId;
    }

    public function model(array $row)
    {
        // 1. Validar que el dueño (Titular) exista usando su DNI
        $titular = Titular::where('empresa_id', $this->empresaId)
                          ->where('dni', $row['dni_titular_vinculado'])
                          ->firstOrFail();

        // 2. Buscamos el ID del Diagnóstico si enviaron el código
        $diagnosticoId = null;
        if (!empty($row['codigo_diagnostico_oficial'])) {
            $diagnostico = Diagnostico::where('codigo', $row['codigo_diagnostico_oficial'])->first();
            $diagnosticoId = $diagnostico ? $diagnostico->id : null;
        }

        return new Familiar([
            'empresa_id' => $this->empresaId,
            'titular_id' => $titular->id,
            'nombre'     => $row['nombre_completo_paciente'],
            'dni'        => $row['dni_paciente'] ?? null,
            'parentesco' => $row['parentesco'] ?? 'Hijo',
            'tiene_patologia' => $diagnosticoId ? 1 : 0,
            'diagnostico_id' => $diagnosticoId,
        ]);
    }

    public function rules(): array
    {
        return [
            'dni_titular_vinculado' => 'required', // Tiene que existir en la BD (se atrapa en model())
            'nombre_completo_paciente' => 'required|string',
            'dni_paciente' => 'nullable|numeric',
            'parentesco' => 'nullable|string'
        ];
    }
}
