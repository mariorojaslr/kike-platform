<?php

namespace App\Exports\Tenant;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FamiliaresTemplateExport implements WithHeadings, ShouldAutoSize, WithStyles
{
    public function headings(): array
    {
        return [
            'DNI_TITULAR_VINCULADO',
            'NOMBRE_COMPLETO_PACIENTE',
            'DNI_PACIENTE',
            'PARENTESCO',
            'CODIGO_DIAGNOSTICO_OFICIAL'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 'fill' => ['fillType' => 'solid', 'color' => ['argb' => 'FF10B981']]],
        ];
    }
}
