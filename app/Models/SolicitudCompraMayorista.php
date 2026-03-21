<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudCompraMayorista extends Model
{
    protected $table = 'solicitud_compra_mayorista';

    protected $fillable = [
        'user_id',
        'empresa_id',
        'nombre_cliente',
        'email_cliente',
        'telefono_cliente',
        'descripcion',
        'documento',
        'estado',
        'visto_en',
        'respondido_en',
    ];

    protected $casts = [
        'visto_en' => 'datetime',
        'respondido_en' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }
}
