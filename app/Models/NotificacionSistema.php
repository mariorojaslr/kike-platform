<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificacionSistema extends Model
{
    use HasFactory;

    protected $table = 'notificaciones_sistema';

    protected $fillable = [
        'user_id',
        'empresa_id',
        'titulo',
        'mensaje',
        'link',
        'leido',
    ];

    protected $casts = [
        'leido' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }
}
