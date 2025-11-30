<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Carrito;

class CarritoController extends Controller
{
    /**
     * Obtiene la clave única de sesión para el carrito del usuario
     */
    public static function getCartKey()
    {
        if (auth()->check()) {
            // Si el usuario está autenticado, usar su ID
            return 'carrito_user_' . auth()->id();
        } else {
            // Si no está autenticado, usar la sesión actual
            return 'carrito_guest_' . session()->getId();
        }
    }
    
    /**
     * Obtiene el carrito del usuario actual (método estático para usar en vistas)
     */
    public static function getCartStatic()
    {
        if (auth()->check()) {
            // Si está autenticado, cargar desde BD y sincronizar con sesión
            return self::loadCartFromDatabase();
        } else {
            // Si no está autenticado, usar sesión
            return session()->get(self::getCartKey(), []);
        }
    }

    /**
     * Carga el carrito desde la base de datos y lo sincroniza con la sesión
     */
    public static function loadCartFromDatabase()
    {
        if (!auth()->check()) {
            return [];
        }

        $userId = auth()->id();
        $carritoItems = Carrito::where('user_id', $userId)
            ->with('producto')
            ->get();

        $carrito = [];
        foreach ($carritoItems as $item) {
            $producto = $item->producto;
            if ($producto) {
                // Key compuesto: productoId[:talla]
                $key = $producto->id . ($item->talla ? ':' . $item->talla : '');
                $carrito[$key] = [
                    'codigo' => $producto->codigo,
                    'nombre' => $producto->nombre,
                    'precio' => $producto->precio,
                    'imagen' => $producto->imagen,
                    'cantidad' => $item->cantidad,
                    'talla' => $item->talla,
                ];
            }
        }

        // Sincronizar con sesión
        session()->put(self::getCartKey(), $carrito);
        return $carrito;
    }

    /**
     * Guarda el carrito en la base de datos (solo para usuarios autenticados)
     */
    private function saveCartToDatabase($carrito)
    {
        if (!auth()->check()) {
            return;
        }

        $userId = auth()->id();

        // Eliminar todos los items del carrito del usuario
        Carrito::where('user_id', $userId)->delete();

        // Guardar los nuevos items
        foreach ($carrito as $productoId => $item) {
            // Si la clave contiene talla (formato "id:talla"), separarla
            $talla = null;
            $pid = $productoId;
            if (strpos($productoId, ':') !== false) {
                [$pid, $talla] = explode(':', $productoId, 2);
            }
            Carrito::create([
                'user_id' => $userId,
                'producto_id' => $pid,
                'cantidad' => $item['cantidad'],
                'talla' => $item['talla'] ?? $talla,
            ]);
        }
    }

    /**
     * Obtiene el carrito del usuario actual
     */
    private function getCart()
    {
        if (auth()->check()) {
            // Si está autenticado, cargar desde BD
            return self::loadCartFromDatabase();
        } else {
            // Si no está autenticado, usar sesión
            return session()->get(self::getCartKey(), []);
        }
    }

    /**
     * Guarda el carrito del usuario actual
     */
    private function saveCart($carrito)
    {
        $this->saveCartPublic($carrito);
    }

    /**
     * Guarda el carrito (método público para usar desde otros controladores)
     */
    public function saveCartPublic($carrito)
    {
        // Guardar en sesión
        session()->put(self::getCartKey(), $carrito);
        
        // Si está autenticado, también guardar en BD
        if (auth()->check()) {
            $this->saveCartToDatabase($carrito);
        }
    }

    /**
     * Limpia el carrito del usuario actual
     */
    private function clearCart()
    {
        // Limpiar sesión
        session()->forget(self::getCartKey());
        
        // Si está autenticado, también limpiar BD
        if (auth()->check()) {
            Carrito::where('user_id', auth()->id())->delete();
        }
    }

    public function agregar(Request $request){
        $producto = Producto::findOrFail($request->producto_id);
        $cantidad = $request->cantidad ?? 1;
        $talla = $request->talla ?? null;

        // Si el producto es ropa, forzar selección de talla
        if (is_string($producto->categoria) && strtolower(trim($producto->categoria)) === 'ropa') {
            if (!$talla || trim($talla) === '') {
                return redirect()->back()->with('error', 'Por favor selecciona una talla para este producto.');
            }
        }

        $carrito = $this->getCart();
        // Key compuesto si hay talla
        $key = $producto->id . ($talla ? ':' . $talla : '');

        if (isset($carrito[$key])) {
            // Ya existe en el carrito, solo aumenta la cantidad
            $carrito[$key]['cantidad'] += $cantidad;
        } else {
            // No existe, lo agregamos
            $carrito[$key] = [
                'codigo' => $producto->codigo,
                'nombre' => $producto->nombre,
                'precio' => $producto->precio,
                'imagen' => $producto->imagen,
                'cantidad' => $cantidad,
                'talla' => $talla,
            ];
        }
        
        $this->saveCart($carrito);
        return redirect()->back()->with('mensaje', 'Producto agregado al carrito');
    }

    public function mostrar(){
        $carrito = $this->getCart();
        return view('web.pedido', compact('carrito'));
    }

    public function sumar(Request $request){
        $productoId = $request->producto_id;
        $carrito = $this->getCart();

        if (isset($carrito[$productoId])) {
            $carrito[$productoId]['cantidad'] += 1;
            $this->saveCart($carrito);
        }

        return redirect()->back()->with('mensaje', 'Cantidad actualizada en el carrito');
    }

    public function restar(Request $request){
        $productoId = $request->producto_id;
        $carrito = $this->getCart();

        if (isset($carrito[$productoId])) {
            if ($carrito[$productoId]['cantidad'] > 1) {
                // Resta 1 si la cantidad es mayor a 1
                $carrito[$productoId]['cantidad'] -= 1;
            } else {
                // Si es 1, lo quitamos del carrito
                unset($carrito[$productoId]);
            }
            $this->saveCart($carrito);
        }

        return redirect()->back()->with('mensaje', 'Cantidad actualizada en el carrito');
    }
    
    public function eliminar($id){
        $carrito = $this->getCart();
        if (isset($carrito[$id])) {
            unset($carrito[$id]);
            $this->saveCart($carrito);
        }
        return redirect()->back()->with('success', 'Producto eliminado');
    }
    
    public function vaciar(){
        $this->clearCart();
        return redirect()->back()->with('success', 'Carrito vaciado');
    }
}
