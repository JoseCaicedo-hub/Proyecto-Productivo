<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = [
        'codigo',
        'nombre',
        'user_id',
        'categoria',
        'precio',
        'cantidad_almacen',
        'descripcion',
        'imagen',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function reviews()
    {
        return $this->hasMany(\App\Models\Review::class, 'producto_id');
    }

    public function imagenes()
    {
        return $this->hasMany(\App\Models\ProductoImagen::class, 'producto_id')->orderBy('orden');
    }
}
