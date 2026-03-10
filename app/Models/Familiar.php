<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Familiar extends Model
{
    use HasFactory;

    protected $table = 'familiares';

    protected $fillable = [
        'empresa_id',
        'titular_id',
        'nombre',
        'dni',
        'parentesco',
        'diagnostico_id',
        'escuela_id',
        'tiene_patologia'
    ];

    /**
     * Número de Afiliado / Obra Social generado dinámicamente.
     * Ejemplo: 1 (Prefijo) + 12345678 (DNI Titular) + 01 (Hijo N°1)
     */
    public function getNumeroAfiliadoAttribute()
    {
        if (!$this->titular) {
            return 'SIN-TITULAR';
        }

        // Buscamos secuencialmente el nº de este hijo
        $hijos = self::where('titular_id', $this->titular_id)
                     ->orderBy('created_at', 'asc')
                     ->get();

        $posicion = 1;
        foreach ($hijos as $index => $hijo) {
            if ($hijo->id == $this->id) {
                $posicion = $index + 1;
                break;
            }
        }

        $posFormateada = str_pad($posicion, 2, '0', STR_PAD_LEFT);
        return $this->titular->dni . "/" . $posFormateada;
    }

    /**
     * Relación: Un familiar pertenece a un titular
     */
    public function titular()
    {
        return $this->belongsTo(Titular::class);
    }

    public function diagnostico()
    {
        return $this->belongsTo(Diagnostico::class, 'diagnostico_id');
    }

    public function escuela()
    {
        return $this->belongsTo(Escuela::class, 'escuela_id');
    }
}
