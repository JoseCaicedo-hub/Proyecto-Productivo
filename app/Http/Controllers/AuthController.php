<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request){
        $request->validate([
            'email'=>'required|email',
            'password'=>'required'
        ]);

        $credenciales=$request->only('email', 'password');

        if(Auth::attempt($credenciales)){
            $user= Auth::user();

            if($user->activo){
                // Guardar el carrito de invitado (si existe) antes de limpiar
                $guestCartKey = 'carrito_guest_' . session()->getId();
                $guestCart = session()->get($guestCartKey, []);
                
                // Limpiar cualquier carrito de sesión anterior antes de iniciar sesión
                // Esto previene que se mezclen carritos entre usuarios
                $oldCartKeys = [];
                foreach (session()->all() as $key => $value) {
                    if (strpos($key, 'carrito_') === 0) {
                        $oldCartKeys[] = $key;
                    }
                }
                foreach ($oldCartKeys as $key) {
                    session()->forget($key);
                }
                
                // Cargar el carrito del usuario desde la base de datos
                $userCart = \App\Http\Controllers\CarritoController::loadCartFromDatabase();
                
                // Si había un carrito de invitado, fusionarlo con el carrito del usuario
                if (!empty($guestCart)) {
                    $mergedCart = $userCart;
                    foreach ($guestCart as $productoId => $item) {
                        if (isset($mergedCart[$productoId])) {
                            // Si el producto ya existe, sumar las cantidades
                            $mergedCart[$productoId]['cantidad'] += $item['cantidad'];
                        } else {
                            // Si no existe, agregarlo
                            $mergedCart[$productoId] = $item;
                        }
                    }
                    // Guardar el carrito fusionado
                    $controller = new \App\Http\Controllers\CarritoController();
                    $controller->saveCartPublic($mergedCart);
                }
                
                return redirect()->intended();
            }else{
                Auth::logout();
                return back()->with('error', 'Su cuenta esta inactiva. Contacte con el administrador');
            }
        }
        return back()->with('error', 'Las credenciales no son correctas')->withInput();
    }
}
