<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PagoEmpresa extends Model
{
    use HasFactory;

    protected $table = 'pago_empresas';

    protected $fillable = [
        'empresa_id',
        'user_id',
        'monto',
        'nro_comprobante',
        'banco_origen',
        'fecha_pago',
        'comprobante_url',
        'estado',
        'notas_owner',
        'datos_extraidos_ia',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_pago' => 'datetime',
        'datos_extraidos_ia' => 'array',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
