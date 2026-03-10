<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class DocenteDocumento extends Model
{
    use HasFactory;

    protected $fillable = [
        'docente_id',
        'tipo_documento', // Ej: Certificado Buena Conducta
        'ruta_archivo',
        'fecha_vencimiento',
        'estado'
    ];

    /**
     * Asegura que el campo fecha_vencimiento sea tratado como tipo Carbon/Date
     */
    protected $casts = [
        'fecha_vencimiento' => 'date'
    ];

    /**
     * Atributo computado: Determina si el doc. ya venció el día de hoy
     */
    public function getEstaVencidoAttribute()
    {
        if (!$this->fecha_vencimiento) {
            return false;
        }
        return Carbon::now()->startOfDay()->greaterThan($this->fecha_vencimiento);
    }
}
