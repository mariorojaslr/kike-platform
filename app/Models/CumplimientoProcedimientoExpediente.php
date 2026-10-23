<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CumplimientoProcedimientoExpediente extends Model
{
    protected $table = 'cumplimientos_procedimiento_expediente';
    protected $guarded = ['id'];

    protected $casts = [
        'cumplido' => 'boolean',
        'fecha_cumplimiento' => 'datetime',
    ];

    public function expediente()
    {
        return $this->belongsTo(ExpedienteAlumno::class, 'expediente_id');
    }

    public function procedimiento()
    {
        return $this->belongsTo(ProcedimientoArea::class, 'procedimiento_area_id');
    }
}
