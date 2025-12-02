<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'user_id', 'producto_id', 'rating', 'comment'
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function producto()
    {
        return $this->belongsTo(\App\Models\Producto::class);
    }
}
