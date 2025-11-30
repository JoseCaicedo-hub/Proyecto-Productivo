<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\PerfilController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\WebController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\DashboardController;

Route::get('/', [WebController::class, 'index'])->name('web.index');
Route::get('/producto/{id}', [WebController::class, 'show'])->name('web.show');

// Página del equipo (acerca)
Route::view('/equipo', 'web.equipo.index')->name('web.equipo');

// Página tienda
Route::view('/tienda', 'web.tienda.index')->name('web.tienda');

// Preguntas frecuentes
Route::view('/preguntas', 'web.preguntas.index')->name('web.preguntas');

// Contacto
Route::view('/contacto', 'web.contacto.index')->name('web.contacto');
Route::post('/contacto/enviar', function(\Illuminate\Http\Request $request){
    $data = $request->validate([
        'nombre' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'telefono' => 'nullable|string|max:50',
        'mensaje' => 'required|string|max:2000',
    ]);

    // Aquí puedes enviar un email, guardar en BD o crear un ticket.
    // Por ahora solo guardamos un mensaje de sesión para confirmar al usuario.

    $request->session()->flash('mensaje', 'Gracias por contactarnos. Te responderemos pronto.');
    return redirect()->route('web.contacto');
})->name('contacto.enviar');

// Contactanos (vista marketplace dentro de web/contacto/contactanos)
Route::get('/contactanos', function () {
    return view('web.contacto.contactanos.index');
})->name('web.contactanos');

Route::post('/contactanos', function(\Illuminate\Http\Request $request){
    $data = $request->validate([
        'tipo' => 'required|string',
        'vendedor' => 'nullable|string',
        'nombre' => 'required|string|max:120',
        'email' => 'required|email|max:150',
        'telefono' => 'nullable|string|max:30',
        'pedido_id' => 'nullable|string|max:80',
        'producto' => 'nullable|string|max:200',
        'mensaje' => 'required|string|max:2000',
        'adjunto' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        'newsletter' => 'nullable|boolean',
    ]);

    if ($request->hasFile('adjunto')) {
        $path = $request->file('adjunto')->store('contactanos/adjuntos', 'public');
        $data['adjunto_path'] = $path;
    }

    \Log::info('Contacto contactanos recibido', $data);

    $request->session()->flash('mensaje', 'Tu consulta ha sido enviada. Te contactaremos pronto.');
    return redirect()->route('web.contactanos');
})->name('contactanos.send');

Route::get('/carrito', [CarritoController::class, 'mostrar'])->name('carrito.mostrar');
Route::post('/carrito/agregar', [CarritoController::class, 'agregar'])->name('carrito.agregar');
Route::get('/carrito/sumar', [CarritoController::class, 'sumar'])->name('carrito.sumar');
Route::get('/carrito/restar', [CarritoController::class, 'restar'])->name('carrito.restar');
Route::get('/carrito/eliminar/{id}', [CarritoController::class, 'eliminar'])->name('carrito.eliminar');
Route::get('/carrito/vaciar', [CarritoController::class, 'vaciar'])->name('carrito.vaciar');

Route::middleware(['auth'])->group(function(){
    Route::resource('usuarios', UserController::class);
    Route::patch('usuarios/{usuario}/toggle', [UserController::class, 'toggleStatus'])->name('usuarios.toggle');
    Route::resource('roles', RoleController::class);
    Route::resource('productos', ProductoController::class);

    Route::post('/pedido/realizar', [PedidoController::class, 'realizar'])->name('pedido.realizar');
    Route::get('/perfil/pedidos', [PedidoController::class, 'index'])->name('perfil.pedidos');
    Route::patch('/pedidos/{id}/estado', [PedidoController::class, 'cambiarEstado'])->name('pedidos.cambiar.estado');    

    Route::get('dashboard', function(){
        return view('dashboard');
    })->name('dashboard');

    Route::post('logout', function(){
        // Limpiar solo el carrito de sesión (el carrito en BD se mantiene)
        $cartKey = \App\Http\Controllers\CarritoController::getCartKey();
        session()->forget($cartKey);
        
        // Limpiar cualquier otro carrito de sesión que pueda quedar
        foreach (session()->all() as $key => $value) {
            if (strpos($key, 'carrito_') === 0) {
                session()->forget($key);
            }
        }
        
        // NO eliminar el carrito de la BD, se mantiene para cuando el usuario vuelva a iniciar sesión
        Auth::logout();
        return redirect('/login');
    })->name('logout');

    Route::get('/perfil', [PerfilController::class, 'edit'])->name('perfil.edit');
    Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');

    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
});

Route::middleware('guest')->group(function(){
    Route::get('login', function(){
        return view('autenticacion.login');
    })->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.post');

    Route::get('/registro', [RegisterController::class, 'showRegistroForm'])->name('registro');
    Route::post('/registro', [RegisterController::class, 'registrar'])->name('registro.store');

    Route::get('password/reset', [ResetPasswordController::class, 'showRequestForm'])->name('password.request');
    Route::post('password/email', [ResetPasswordController::class, 'sendResetLinkEmail'])->name('password.send-link');
    Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/reset', [ResetPasswordController::class, 'resetPassword'])->name('password.update');

});


