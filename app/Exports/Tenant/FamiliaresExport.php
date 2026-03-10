<?php

namespace App\Exports\Tenant;

use App\Models\Familiar;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FamiliaresExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $empresaId;

    public function __construct($empresaId)
    {
        $this->empresaId = $empresaId;
    }

    public function collection()
    {
        return Familiar::with(['titular', 'diagnostico'])->where('empresa_id', $this->empresaId)->orderBy('apellido')->get();
    }

    public function headings(): array
    {
        return [
            'Nro. Afiliado Social',
            'DNI Paciente',
            'Apellido Paciente',
            'Nombre Paciente',
            'Titular Vinculado',
            'DNI Titular',
            'Diagnóstico / Patología',
        ];
    }

    public function map($familiar): array
    {
        return [
            $familiar->numero_afiliado,
            $familiar->dni,
            $familiar->apellido,
            $familiar->nombre,
            $familiar->titular ? $familiar->titular->nombre : 'Sin Titular',
            $familiar->titular ? $familiar->titular->dni : '',
            $familiar->diagnostico ? $familiar->diagnostico->nombre : 'S/D',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'color' => ['rgb' => '0f172a']]],
        ];
    }
}
