<?php

namespace App\Exports\Tenant;

use App\Models\Docente;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DocentesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $empresaId;

    public function __construct($empresaId)
    {
        $this->empresaId = $empresaId;
    }

    public function collection()
    {
        return Docente::with('formacion')->where('empresa_id', $this->empresaId)->orderBy('nombre')->get();
    }

    public function headings(): array
    {
        return [
            'ID Docente',
            'Nombre Completo',
            'DNI',
            'Teléfono',
            'Email',
            'Título / Formación',
        ];
    }

    public function map($docente): array
    {
        return [
            $docente->id,
            $docente->nombre,
            $docente->dni,
            $docente->telefono,
            $docente->email,
            $docente->formacion ? $docente->formacion->nombre : 'Sin Especialidad',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'color' => ['rgb' => '0f172a']]],
        ];
    }
}
