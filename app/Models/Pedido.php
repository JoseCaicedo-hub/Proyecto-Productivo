<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $fillable = [
        'user_id', 'nombre_cliente', 'total', 'estado',
        'direccion', 'pais', 'departamento', 'ciudad',
        'tipo_documento', 'numero_documento', 'metodo_pago',
        'payment_provider', 'account_number', 'card_last4', 'card_cvv_hash',
        'telefono', 'referencia'
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
