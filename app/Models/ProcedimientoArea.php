<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcedimientoArea extends Model
{
    protected $table = 'procedimientos_area';
    protected $guarded = ['id'];

    protected $casts = [
        'es_obligatorio' => 'boolean',
        'activo' => 'boolean',
    ];

    public function cumplimientos()
    {
        return $this->hasMany(CumplimientoProcedimientoExpediente::class, 'procedimiento_area_id');
    }
}
