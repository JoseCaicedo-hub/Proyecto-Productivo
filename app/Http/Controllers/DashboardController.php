<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Pedido;
use App\Models\Review;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        
        // Datos para vendedores
        if ($user && $user->hasRole('vendedor')) {
            $productos = Producto::where('user_id', $user->id)->orderBy('created_at', 'desc')->paginate(12);
            return view('dashboard', compact('productos', 'user'));
        }
        
        // Datos para compradores / clientes
        if ($user && $user->hasRole('cliente')) {
            $estadisticas = [
                'compras_realizadas' => Pedido::where('user_id', $user->id)->count(),
                'comentarios_dejados' => Review::where('user_id', $user->id)->count(),
                'total_gastado' => Pedido::where('user_id', $user->id)->sum('total'),
                'pedidos_recientes' => Pedido::where('user_id', $user->id)->orderBy('created_at', 'desc')->limit(5)->get(),
            ];
            return view('dashboard', compact('user', 'estadisticas'));
        }
        
        // Usuario no autenticado
        $productos = Producto::whereNull('id')->paginate(12);
        return view('dashboard', compact('productos', 'user'));
    }
}
