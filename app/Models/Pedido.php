<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $fillable = [
        'user_id', 'total', 'estado',
        'direccion', 'tipo_documento', 'numero_documento', 'metodo_pago', 'telefono', 'referencia'
    ];
    
    public function detalles()
    {
        return $this->hasMany(PedidoDetalle::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
