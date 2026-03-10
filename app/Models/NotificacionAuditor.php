<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NotificacionAuditor extends Model
{
    use HasFactory;

    protected $table = 'notificaciones_auditor';

    protected $fillable = [
        'empresa_id',
        'titulo',
        'mensaje',
        'tipo',
        'leida'
    ];
}
