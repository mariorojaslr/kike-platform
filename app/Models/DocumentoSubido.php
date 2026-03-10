<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoSubido extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'empresa_id',
        'tipo_documento_id',
        'entidad_tipo',
        'entidad_id',
        'ruta_archivo',
        'estado',
        'comentarios_auditor',
        'fecha_vencimiento'
    ];

    public function tipoDocumento()
    {
        return $this->belongsTo(TipoDocumento::class, 'tipo_documento_id');
    }

    public function getEntidadNombreAttribute()
    {
        if ($this->entidad_tipo === 'docente') {
            $docente = Docente::find($this->entidad_id);
            return $docente ? $docente->nombre_completo : 'Desconocido';
        } elseif ($this->entidad_tipo === 'alumno') {
            $alumno = Familiar::find($this->entidad_id);
            return $alumno ? $alumno->nombre : 'Desconocido';
        }
        return 'N/A';
    }
}
