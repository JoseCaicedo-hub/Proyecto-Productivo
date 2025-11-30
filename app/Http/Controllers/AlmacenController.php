<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PedidoDetalle;
use App\Models\Producto;

class AlmacenController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Lista los detalles de pedidos que este almacen debe entregar (productos publicados por el admin)
    public function index()
    {
        $this->authorize('producto-list');

        $userId = auth()->id();

        // Mostrar detalles que aún no fueron entregados ni cancelados
        $detalles = PedidoDetalle::with(['pedido.user', 'producto'])
            ->where(function($q){
                $q->whereNull('envio_estado')
                  ->orWhereIn('envio_estado', ['pendiente','enviado']);
            })
            ->whereHas('producto', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return view('almacen.index', compact('detalles'));
    }

    // Marcar un detalle como entregado
    public function entregar(Request $request, $detalleId)
    {
        // Acción del admin: marcar el detalle como 'enviado'
        $this->authorize('producto-list');

        $detalle = PedidoDetalle::with('producto','pedido')->findOrFail($detalleId);

        // verificar que el producto pertenezca al usuario
        if (!$detalle->producto || $detalle->producto->user_id !== auth()->id()) {
            abort(403);
        }

        $detalle->envio_estado = 'enviado';
        $detalle->entregado = false;
        $detalle->fecha_envio = now();
        $detalle->save();

        // Si el pedido está en 'pendiente', marcarlo como 'enviado' para reflejar movimiento
        if ($detalle->pedido && $detalle->pedido->estado === 'pendiente') {
            $detalle->pedido->estado = 'enviado';
            $detalle->pedido->save();
        }

        return redirect()->back()->with('mensaje', 'Detalle marcado como enviado.');
    }

    /**
     * Debug: listar los detalles recientes con el product->user_id para diagnóstico
     */
    public function debug()
    {
        $this->authorize('producto-list');

        $userId = auth()->id();

        $detalles = PedidoDetalle::with(['pedido.user', 'producto'])
            ->latest()
            ->take(200)
            ->get();

        return view('almacen.debug', compact('detalles','userId'));
    }

    /**
     * Mostrar pedidos (detalles) que ya fueron entregados para el admin
     */
    public function entregados()
    {
        $this->authorize('producto-list');

        $userId = auth()->id();

        $detalles = PedidoDetalle::with(['pedido.user', 'producto', 'entregadoPor'])
            ->where('envio_estado', 'entregado')
            ->whereHas('producto', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->orderBy('fecha_recibido', 'desc')
            ->get();

        return view('almacen.entregados', compact('detalles','userId'));
    }
}
