<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{
    protected $fillable = [
        'empresa_id',
        'entidad_tipo',
        'nombre',
        'descripcion',
        'es_obligatorio',
        'vencimiento_dias'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }
    
    public function documentosSubidos()
    {
        return $this->hasMany(DocumentoSubido::class, 'tipo_documento_id');
    }
}
