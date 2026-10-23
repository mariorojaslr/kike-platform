<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Titular extends Model
{
    use HasFactory;

    protected $fillable = ['empresa_id', 'nombre', 'dni', 'cuil', 'n_afiliado', 'resolucion'];

    public function familiares()
    {
        return $this->hasMany(Familiar::class);
    }

    // Accesor para el código especial: 1 + DNI + Cantidad de Hijos
    public function getCodigoAfiliadoAttribute()
    {
        $cantidadHijos = $this->familiares()->where('parentesco', 'Hijo')->count();
        $hijosFormateado = str_pad($cantidadHijos, 2, '0', STR_PAD_LEFT);
        return "1" . $this->dni . $hijosFormateado;
    }

    public function getNombreAttribute($value)
    {
        return \App\Support\SpanishTextCleaner::clean($value, true);
    }

    public function getNombreCompletoAttribute()
    {
        $raw = trim(($this->attributes['apellido'] ?? '') . ' ' . ($this->attributes['nombre'] ?? ''));
        return \App\Support\SpanishTextCleaner::clean($raw, true);
    }

    public function getNroAfiliadoAttribute()
    {
        return $this->attributes['n_afiliado'] ?? ($this->attributes['nro_afiliado'] ?? $this->dni);
    }
}
