<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Docente extends Model
{
    use HasFactory;

    protected $table = 'docentes';

    protected $fillable = [
        'empresa_id',
        'nombre',
        'dni',
        'email',
        'telefono',
        'direccion',
        'formacion_id', // Se cruza con formaciones globales (Psicopedagoga, Maestra integradora)
        'validado_auditoria',
        'activo'
    ];

    /**
     * Documentación vinculada a este docente.
     */
    public function documentos()
    {
        return $this->hasMany(DocenteDocumento::class, 'docente_id');
    }

    /**
     * Título/Formación del Terapeuta.
     */
    public function formacion()
    {
        return $this->belongsTo(Formacion::class, 'formacion_id');
    }
}
