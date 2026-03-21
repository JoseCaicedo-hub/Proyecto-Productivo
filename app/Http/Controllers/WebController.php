<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;

class WebController extends Controller
{
    public function index(Request $request){
        // Obtener los productos más vendidos para el carrusel
        $topProducts = \App\Http\Controllers\HeaderController::topProductos(5);
        
        $query = Producto::with('empresa')
            ->whereNotNull('empresa_id')
            ->whereHas('empresa', function ($q) {
                $q->where('estado', 'activo');
            });
        // Búsqueda por nombre
        if ($request->has('search') && $request->search) {
            $query->where('nombre', 'like', '%' . $request->search . '%');
        }

        // Filtrar por categoria si se pasa en la query string
        if ($request->has('category') && $request->category) {
            $query->where('categoria', $request->category);
        }

        if ($request->filled('empresa')) {
            $query->where('empresa_id', $request->empresa);
        }

        // Filtro de orden (Ordenar por precio)
        if ($request->has('sort') && $request->sort) {
            switch ($request->sort) {
                case 'priceAsc':
                    $query->orderBy('precio', 'asc');
                    break;
                case 'priceDesc':
                    $query->orderBy('precio', 'desc');
                    break;
                default:
                    $query->orderBy('nombre', 'asc');
                    break;
            }
        }
        // Obtener productos filtrados
        $productos = $query->paginate(10);    
        return view('web.index', compact('productos', 'topProducts'));

    }

    public function show($id){
        // Obtener el producto por ID
        $producto = Producto::with('empresa')->findOrFail($id);        
        // Pasar el producto a la vista
        return view('web.item', compact('producto'));
    }
}
