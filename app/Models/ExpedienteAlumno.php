<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpedienteAlumno extends Model
{
    protected $table = 'expedientes_alumno';
    protected $guarded = ['id'];

    protected $casts = [
        'resolucion_datos_ia' => 'array',
    ];

    public function docente()
    {
        return $this->belongsTo(Docente::class, 'docente_id');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function titular()
    {
        return $this->belongsTo(Titular::class, 'titular_id');
    }

    public function alumno()
    {
        return $this->belongsTo(Familiar::class, 'alumno_id');
    }

    public function escuela()
    {
        return $this->belongsTo(Escuela::class, 'escuela_id');
    }

    public function facturas()
    {
        return $this->hasMany(FacturaDocente::class, 'expediente_id');
    }
}
