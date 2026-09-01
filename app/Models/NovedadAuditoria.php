<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NovedadAuditoria extends Model
{
    protected $table = 'novedades_auditoria';
    protected $guarded = ['id'];

    public function docente()
    {
        return $this->belongsTo(Docente::class, 'docente_id');
    }

    public function expediente()
    {
        return $this->belongsTo(ExpedienteAlumno::class, 'expediente_id');
    }

    public function factura()
    {
        return $this->belongsTo(FacturaDocente::class, 'factura_id');
    }
}
