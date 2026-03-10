<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Formacion extends Model {
    protected $table = 'formaciones'; // Importante por el nombre en la migración
    protected $fillable = ['empresa_id', 'nombre'];
}
