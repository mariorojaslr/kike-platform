<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacturaDocente extends Model
{
    protected $table = 'facturas_docente';
    protected $guarded = ['id'];

    public function docente()
    {
        return $this->belongsTo(Docente::class, 'docente_id');
    }

    public function expediente()
    {
        return $this->belongsTo(ExpedienteAlumno::class, 'expediente_id');
    }

    public function alumno()
    {
        return $this->belongsTo(Familiar::class, 'alumno_id');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }
}
