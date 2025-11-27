<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = [
        'codigo',
        'nombre',
        'categoria',
        'precio',
        'cantidad_almacen',
        'descripcion',
        'imagen',
    ];
}
