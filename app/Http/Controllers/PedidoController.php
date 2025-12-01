<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\PedidoDetalle;
use App\Models\Producto;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Carrito;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class PedidoController extends Controller
{
    public function index(Request $request){
        $texto = $request->input('texto');
        $query = Pedido::with('user', 'detalles.producto')->orderBy('id', 'desc');

        // Permisos
        if (auth()->user()->can('pedido-list')) {
            // Puede ver todos los pedidos
        } elseif (auth()->user()->can('pedido-view')) {
            // Solo puede ver sus propios pedidos
            $query->where('user_id', auth()->id());
        } else {
            abort(403, 'No tienes permisos para ver pedidos.');
        }

        // Búsqueda por nombre del usuario
        if (!empty($texto)) {
            $query->whereHas('user', function ($q) use ($texto) {
                $q->where('name', 'like', "%{$texto}%");
            });
        }
        $registros = $query->paginate(10);
        return view('pedido.index', compact('registros', 'texto'));
    }

    /**
     * Listado de pedidos del usuario autenticado (sin depender de permisos)
     */
    public function misPedidos(Request $request)
    {
        $texto = $request->input('texto');
        $query = Pedido::with('user', 'detalles.producto')
            ->where('user_id', auth()->id())
            ->orderBy('id', 'desc');

        // Búsqueda por nombre (aunque normalmente no es necesaria para el propio usuario)
        if (!empty($texto)) {
            $query->whereHas('user', function ($q) use ($texto) {
                $q->where('name', 'like', "%{$texto}%");
            });
        }

        $registros = $query->paginate(10);
        return view('pedido.index', compact('registros', 'texto'));
    }

    public function realizar(Request $request){
        $carrito = \App\Http\Controllers\CarritoController::getCartStatic();

        if (empty($carrito)) {
            return redirect()->back()->with('mensaje', 'El carrito está vacío.');
        }
        // Validar datos de envío y pago
        $validated = $request->validate([
            'direccion' => 'required|string|max:1000',
            'tipo_documento' => 'required|string|max:30',
            'numero_documento' => 'required|string|max:50',
            'metodo_pago' => 'required|string|max:50',
            'telefono' => 'nullable|string|max:30',
            'referencia' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            // 1. Calcular el total
            $total = 0;
            foreach ($carrito as $item) {
                $total += $item['precio'] * $item['cantidad'];
            }
            // 2. Crear el pedido incluyendo datos de envío/pago
            $pedidoData = [
                'user_id' => auth()->id(),
                'total' => $total,
                'estado' => 'pendiente',
                'direccion' => $validated['direccion'] ?? null,
                'tipo_documento' => $validated['tipo_documento'] ?? null,
                'numero_documento' => $validated['numero_documento'] ?? null,
                'metodo_pago' => $validated['metodo_pago'] ?? null,
                'telefono' => $validated['telefono'] ?? null,
                'referencia' => $validated['referencia'] ?? null,
            ];

            $pedido = Pedido::create($pedidoData);
            // 3. Crear los detalles del pedido
            foreach ($carrito as $productoId => $item) {
                // Manejar claves compuestas con talla: "id[:talla]"
                $talla = null;
                $pid = $productoId;
                if (strpos((string)$productoId, ':') !== false) {
                    [$pid, $talla] = explode(':', $productoId, 2);
                }

                // Validar que el producto exista
                $producto = Producto::find($pid);
                if (!$producto) {
                    throw new \Exception("Producto con id {$pid} no encontrado en el sistema.");
                }

                PedidoDetalle::create([
                    'pedido_id' => $pedido->id,
                    'producto_id' => $pid,
                    'cantidad' => $item['cantidad'],
                    'precio' => $item['precio'],
                    'talla' => $item['talla'] ?? $talla,
                    'entregado' => false,
                ]);
            }
            // 4. Vaciar el carrito de la sesión
            // Limpiar carrito en sesión
            session()->forget(\App\Http\Controllers\CarritoController::getCartKey());
            // Si el usuario está autenticado, eliminar también los items del carrito guardados en BD
            if (auth()->check()) {
                Carrito::where('user_id', auth()->id())->delete();
            }
            DB::commit();
            return redirect()->route('carrito.mostrar')->with('mensaje', 'Pedido realizado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            // Loggear el error completo para depuración
            Log::error('Error al realizar pedido: ' . $e->getMessage(), ['exception' => $e]);
            // Devolver mensaje más detallado en entorno de desarrollo
            $msg = env('APP_DEBUG') ? $e->getMessage() : 'Hubo un error al procesar el pedido.';
            return redirect()->back()->with('error', $msg);
        }
    }

    public function cambiarEstado(Request $request, $id){
        $pedido = Pedido::findOrFail($id);
        $estadoNuevo = $request->input('estado');

        // Validar que el estado nuevo sea uno permitido
        $estadosPermitidos = ['enviado', 'anulado', 'cancelado'];

        if (!in_array($estadoNuevo, $estadosPermitidos)) {
            abort(403, 'Estado no válido');
        }

        // Verificar permisos según el estado
        if (in_array($estadoNuevo, ['enviado', 'anulado'])) {
            if (!auth()->user()->can('pedido-anulate')) {
                abort(403, 'No tiene permiso para cambiar a "enviado" o "anulado"');
            }
        }

        if ($estadoNuevo === 'cancelado') {
            if (!auth()->user()->can('pedido-cancel')) {
                abort(403, 'No tiene permiso para cancelar pedidos');
            }
        }

        // Cambiar el estado
        $pedido->estado = $estadoNuevo;
        $pedido->save();

        return redirect()->back()->with('mensaje', 'El estado del pedido fue actualizado a "' . ucfirst($estadoNuevo) . '"');
    }

    /**
     * Permite al usuario cancelar su propio pedido (pasa a estado 'cancelado').
     */
    public function cancelar(Request $request, $id)
    {
        $pedido = Pedido::findOrFail($id);

        // Verificar que el pedido pertenece al usuario autenticado
        if ($pedido->user_id !== auth()->id()) {
            abort(403, 'No puedes cancelar este pedido.');
        }

        // Solo permitir cancelar pedidos pendientes
        if ($pedido->estado !== 'pendiente') {
            return redirect()->back()->with('error', 'Solo se pueden cancelar pedidos en estado pendiente.');
        }

        $pedido->estado = 'cancelado';
        $pedido->save();

        // Marcar los detalles del pedido como cancelados para que no aparezcan en Almacén
        $pedido->detalles()->update([
            'envio_estado' => 'cancelado',
            'entregado' => false,
        ]);

        return redirect()->back()->with('mensaje', 'Pedido cancelado correctamente.');
    }

    /**
     * Eliminar un pedido (solo si está en estado 'cancelado' o por admins con permiso).
     */
    public function destroy(Request $request, $id)
    {
        $pedido = Pedido::findOrFail($id);

        // Permitir borrarlo si el usuario es propietario o tiene permiso administrativo
        $isOwner = auth()->check() && $pedido->user_id === auth()->id();
        $isAdmin = auth()->check() && auth()->user()->can('pedido-anulate');

        if (! $isOwner && ! $isAdmin) {
            abort(403, 'No tienes permiso para eliminar este pedido.');
        }

        // Solo permitir borrado si el pedido ya está cancelado, a menos que sea admin
        if ($pedido->estado !== 'cancelado' && ! $isAdmin) {
            return redirect()->back()->with('error', 'Solo se pueden eliminar pedidos que estén cancelados.');
        }

        // Borrar detalles y luego el pedido
        $pedido->detalles()->delete();
        $pedido->delete();

        return redirect()->back()->with('mensaje', 'Pedido eliminado correctamente.');
    }

    /**
     * Eliminar permanentemente vía POST (alternativa cuando hay problemas con method spoofing)
     */
    public function destroyPermanent(Request $request, $id)
    {
        return $this->destroy($request, $id);
    }

    /**
     * El comprador marca un detalle como recibido (recibido por el cliente)
     */
    public function recibirDetalle(Request $request, $detalleId)
    {
        $detalle = PedidoDetalle::with('pedido','producto')->findOrFail($detalleId);

        // Asegurar que el que marca es el comprador del pedido
        if (!auth()->check() || $detalle->pedido->user_id !== auth()->id()) {
            abort(403, 'No autorizado');
        }

        // Solo se puede marcar recibido si el estado de envío es 'enviado'
        if ($detalle->envio_estado !== 'enviado') {
            return redirect()->back()->with('error', 'No se puede marcar como recibido hasta que el vendedor lo marque como enviado.');
        }

        $detalle->envio_estado = 'entregado';
        $detalle->entregado = true;
        $detalle->fecha_recibido = now();
        $detalle->entregado_por = auth()->id();
        $detalle->save();

        // Si todos los detalles del pedido están entregados, actualizar el estado del pedido
        $pedido = $detalle->pedido;
        if ($pedido) {
            $faltantes = $pedido->detalles()->where(function($q){
                $q->where('envio_estado', '!=', 'entregado')->orWhereNull('envio_estado');
            })->count();

            if ($faltantes === 0) {
                $pedido->estado = 'entregado';
                $pedido->save();
            }
        }

        return redirect()->back()->with('mensaje', 'Has marcado el artículo como recibido.');
    }

    /**
     * Generar y descargar PDF (estilo factura) del pedido.
     */
    public function downloadPdf(Request $request, $id)
    {
        $pedido = Pedido::with('user', 'detalles.producto')->findOrFail($id);

        // Autorizar: propietario o permisos administrativos
        if (! (auth()->check() && (auth()->id() === $pedido->user_id || auth()->user()->can('pedido-list')))) {
            abort(403, 'No autorizado para descargar este pedido');
        }

        // Verificar que la clase de PDF esté disponible
        if (!class_exists(Pdf::class)) {
            return redirect()->back()->with('error', 'La librería PDF no está instalada. Ejecute: composer require barryvdh/laravel-dompdf');
        }

        $data = ['pedido' => $pedido];

        $pdf = Pdf::loadView('pedido.pdf', $data)->setPaper('a4', 'portrait');

        $filename = 'pedido_' . $pedido->id . '.pdf';
        return $pdf->download($filename);
    }
}