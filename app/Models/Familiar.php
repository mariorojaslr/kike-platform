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
        'diagnostico_detalle',
        'escuela_id',
        'docente_id',
        'resolucion',
        'tiene_patologia',
        'n_afiliado',
        'grado_division',
        'turno',
        'horario',
        'foto_perfil'
    ];

    /**
     * Número de Afiliado / Obra Social (Prioriza el N° oficial de APOS cargado, o lo calcula con extensión).
     * Ejemplo APOS: 1 (Prefijo) + DNI Titular + 01/02/03 (Extensión Hijo)
     */
    public function getNumeroAfiliadoAttribute()
    {
        if (!empty($this->n_afiliado)) {
            return $this->n_afiliado;
        }

        if (!$this->titular) {
            return 'SIN-TITULAR';
        }

        // Buscamos secuencialmente el nº de este hijo
        $hijos = self::where('titular_id', $this->titular_id)
                     ->orderBy('id', 'asc')
                     ->get();

        $posicion = 1;
        foreach ($hijos as $index => $hijo) {
            if ($hijo->id == $this->id) {
                $posicion = $index + 1;
                break;
            }
        }

        $posFormateada = str_pad($posicion, 2, '0', STR_PAD_LEFT);
        return '1' . $this->titular->dni . $posFormateada;
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

    public function diagnosticoRel()
    {
        return $this->belongsTo(Diagnostico::class, 'diagnostico_id');
    }

    public function escuela()
    {
        return $this->belongsTo(Escuela::class, 'escuela_id');
    }

    public function docente()
    {
        return $this->belongsTo(Docente::class, 'docente_id');
    }

    public function getNombreAttribute($value)
    {
        return \App\Support\SpanishTextCleaner::clean($value, true);
    }

    public function getDiagnosticoDetalleAttribute($value)
    {
        return \App\Support\SpanishTextCleaner::clean($value, false);
    }

    public function getGradoDivisionAttribute($value)
    {
        return \App\Support\SpanishTextCleaner::clean($value, false);
    }

    public function getTurnoAttribute($value)
    {
        return \App\Support\SpanishTextCleaner::clean($value, false);
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
