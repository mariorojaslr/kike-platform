<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Afiliado extends Model
{
    use HasFactory;

    protected $guarded = ['id']; // Protección de asignación masiva

    /**
     * Relación: Un afiliado pertenece a una Empresa o Clínica.
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    /**
     * Relación: Un afiliado puede estar asignado a una Escuela.
     */
    public function escuela()
    {
        return $this->belongsTo(Escuela::class, 'escuela_id');
    }
}
