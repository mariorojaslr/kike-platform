<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CirugiasProtesisController extends Controller
{
    public function indexDemo()
    {
        $solicitudes = [
            (object)[
                'id' => 'PR-4891',
                'paciente' => 'Elena Gómez',
                'dni' => '18.992.301',
                'sanatorio' => 'Sanatorio Mercado Luna',
                'cirugia' => 'Artroplastia Total de Cadera Izquierda',
                'protesis' => 'Prótesis de Cadera Híbrida Cónica Titanio ANMAT #39401',
                'medico_cirujano' => 'Dr. Gustavo Herrera (Traumatólogo)',
                'presupuesto_adjudicado' => 3850000,
                'ortopedia' => 'Ortopedia Quirúrgica Central S.A.',
                'estado' => 'aprobado_con_qr',
                'hash_md5' => md5('PR-4891-3850000-ANMAT'),
                'fecha' => '04/09/2026'
            ],
            (object)[
                'id' => 'PR-4892',
                'paciente' => 'Roberto Juárez',
                'dni' => '24.118.490',
                'sanatorio' => 'Clínica Pasteur',
                'cirugia' => 'Reconstrucción de Ligamento Cruzado Anterior',
                'protesis' => 'Kit de Anclajes de Titularización Biocompuesto',
                'medico_cirujano' => 'Dr. Marcelo Quiroga',
                'presupuesto_adjudicado' => 1420000,
                'ortopedia' => 'BioMed Argentina',
                'estado' => 'en_auditoria',
                'hash_md5' => md5('PR-4892-1420000-ANMAT'),
                'fecha' => '03/09/2026'
            ]
        ];

        return view('auditoria.cirugias', compact('solicitudes'));
    }

    public function autorizarProtesis(Request $request, $id)
    {
        return redirect()->back()->with('success', "✅ Solicitud de Prótesis $id autorizada con Sello de Auditoría Criptográfica MD5.");
    }
}
