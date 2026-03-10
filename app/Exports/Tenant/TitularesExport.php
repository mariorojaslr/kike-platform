<?php

namespace App\Exports\Tenant;

use App\Models\Titular;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TitularesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $empresaId;

    public function __construct($empresaId)
    {
        $this->empresaId = $empresaId;
    }

    public function collection()
    {
        return Titular::where('empresa_id', $this->empresaId)->orderBy('nombre')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombre Completo',
            'DNI',
            'Teléfono',
            'Email',
            'Fecha Registro',
        ];
    }

    public function map($titular): array
    {
        return [
            $titular->id,
            $titular->nombre,
            $titular->dni,
            $titular->telefono,
            $titular->email,
            $titular->created_at ? $titular->created_at->format('d/m/Y') : ''
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'color' => ['rgb' => '0f172a']]],
        ];
    }
}
