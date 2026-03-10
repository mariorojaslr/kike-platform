<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Localidad extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla en la base de datos.
     */
    protected $table = 'localidades';

    /**
     * Usamos $guarded vacío para permitir la asignación masiva de cualquier campo.
     * Esto evita errores de 'MassAssignmentException' si decides agregar más
     * datos geográficos en el futuro.
     */
    protected $guarded = [];

    /**
     * Relación: Una localidad pertenece a una Provincia.
     * Fundamental para que el combo dependiente sepa a qué región pertenece la ciudad.
     */
    public function provincia()
    {
        return $this->belongsTo(Provincia::class, 'provincia_id');
    }

    /**
     * Relación: Una localidad puede tener muchas Escuelas.
     * Permite hacer consultas como: Localidad::find(1)->escuelas;
     */
    public function escuelas()
    {
        return $this->hasMany(Escuela::class, 'localidad_id');
    }
}
