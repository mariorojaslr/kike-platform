<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Escuela extends Model
{
    use HasFactory;

    protected $guarded = ['id']; // Protección de asignación masiva

    /**
     * Relación: Una escuela pertenece a una única Empresa (Tenant)
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    /**
     * Relación: Una escuela puede tener muchos afiliados (Alumnos/Pacientes)
     */
    public function afiliados()
    {
        return $this->hasMany(Afiliado::class, 'escuela_id');
    }

}
