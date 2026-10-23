<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diagnostico extends Model
{
    protected $table = 'diagnosticos';
    protected $fillable = ['empresa_id', 'codigo', 'nombre'];

    public function getNombreAttribute($value)
    {
        return \App\Support\SpanishTextCleaner::clean($value, false);
    }
}
