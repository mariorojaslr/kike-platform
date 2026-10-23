<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Formacion extends Model
{
    protected $table = 'formaciones';
    protected $fillable = ['empresa_id', 'nombre'];

    public function getNombreAttribute($value)
    {
        return \App\Support\SpanishTextCleaner::clean($value, true);
    }
}
