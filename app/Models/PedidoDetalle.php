<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoDetalle extends Model
{
    protected $fillable = ['pedido_id', 'producto_id', 'cantidad', 'precio', 'talla', 'entregado', 'envio_estado', 'fecha_envio', 'fecha_recibido', 'entregado_por'];

    protected $casts = [
        'fecha_envio' => 'datetime',
        'fecha_recibido' => 'datetime',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }
    
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function entregadoPor()
    {
        return $this->belongsTo(\App\Models\User::class, 'entregado_por');
    }
}
