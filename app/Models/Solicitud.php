<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    // Nombre de la tabla en plural en español (evita pluralización irregular)
    protected $table = 'solicitudes';
    protected $fillable = ['user_id','nombre','email','telefono','titulo','idea','detalle','estado','admin_id','respuesta'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
