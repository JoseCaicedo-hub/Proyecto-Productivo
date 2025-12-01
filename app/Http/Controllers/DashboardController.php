<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;

class DashboardController extends Controller
{
    public function dashboard()
{
    // Mostrar solo los productos publicados por el usuario autenticado
    if (auth()->check()) {
        $productos = Producto::where('user_id', auth()->id())->orderBy('created_at', 'desc')->paginate(12);
    } else {
        $productos = Producto::whereNull('id')->paginate(12); // colección vacía
    }
    return view('dashboard', compact('productos'));
}
}
