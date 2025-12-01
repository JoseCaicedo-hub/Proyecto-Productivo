<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Http\Requests\ProductoRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str;


class ProductoController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('producto-list'); 
        $texto=$request->input('texto');
        $query = Producto::query();

        // Si el usuario autenticado es vendedor, mostrar sólo sus productos
        if (auth()->check() && auth()->user()->hasRole('vendedor')) {
            $query->where('user_id', auth()->id());
        }

        $registros = $query->where(function($q) use ($texto) {
                        $q->where('nombre', 'like', "%{$texto}%")
                          ->orWhere('codigo', 'like', "%{$texto}%");
                    })
                    ->orderBy('id', 'desc')
                    ->paginate(10);
        return view('producto.index', compact('registros','texto'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('producto-create'); 
        $categorias = ['Electrónica','Ropa','Hogar','Accesorios','Alimentos','Otros'];
        return view('producto.action', compact('categorias'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductoRequest $request)
    {
        $this->authorize('producto-create'); 
        $registro = new Producto();
        // Asociar el producto al usuario que lo publica (si hay sesión)
        if (auth()->check()) {
            $registro->user_id = auth()->id();
        }
        $registro->codigo=$request->input('codigo');
        $registro->nombre=$request->input('nombre');
        $registro->categoria=$request->input('categoria');
        $registro->precio=$request->input('precio');
        $registro->cantidad_almacen=$request->input('cantidad_almacen');
        $registro->descripcion=$request->input('descripcion');
        $sufijo=strtolower(Str::random(2));
        $image = $request->file('imagen');
        if (!is_null($image)){            
            $nombreImagen=$sufijo.'-'.$image->getClientOriginalName();
            $image->move('uploads/productos', $nombreImagen);
            $registro->imagen = $nombreImagen;
        }

        $registro->save();
        return redirect()->route('productos.index')->with('mensaje', 'Registro '.$registro->nombre. '  agregado correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $this->authorize('producto-edit'); 
        $registro=Producto::findOrFail($id);
        // Si el usuario es vendedor, sólo puede editar sus propios productos
        if (auth()->check() && auth()->user()->hasRole('vendedor')) {
            if ($registro->user_id !== auth()->id()) {
                abort(403, 'No autorizado a editar este producto');
            }
        }
        $categorias = ['Electrónica','Ropa','Hogar','Accesorios','Alimentos','Otros'];
        return view('producto.action', compact('registro','categorias'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductoRequest $request, $id)
    {
        $this->authorize('producto-edit'); 
        $registro=Producto::findOrFail($id);
        // Si el usuario es vendedor, sólo puede actualizar sus propios productos
        if (auth()->check() && auth()->user()->hasRole('vendedor')) {
            if ($registro->user_id !== auth()->id()) {
                abort(403, 'No autorizado a actualizar este producto');
            }
        }
        $registro->codigo=$request->input('codigo');
        $registro->nombre=$request->input('nombre');
        $registro->categoria=$request->input('categoria');
        $registro->precio=$request->input('precio');
        $registro->cantidad_almacen=$request->input('cantidad_almacen');
        $registro->descripcion=$request->input('descripcion');
        $sufijo=strtolower(Str::random(2));
        $image = $request->file('imagen');
        if (!is_null($image)){            
            $nombreImagen=$sufijo.'-'.$image->getClientOriginalName();
            $image->move('uploads/productos', $nombreImagen);
            $old_image = 'uploads/productos/'.$registro->imagen;
            if (file_exists($old_image)) {
                @unlink($old_image);
            }
            $registro->imagen = $nombreImagen;
        }

        $registro->save();

        return redirect()->route('productos.index')->with('mensaje', 'Registro '.$registro->nombre. '  actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->authorize('producto-delete');
        $registro=Producto::findOrFail($id);
        // Si el usuario es vendedor, sólo puede eliminar sus propios productos
        if (auth()->check() && auth()->user()->hasRole('vendedor')) {
            if ($registro->user_id !== auth()->id()) {
                abort(403, 'No autorizado a eliminar este producto');
            }
        }
        $old_image = 'uploads/productos/'.$registro->imagen;
        if (file_exists($old_image)) {
            @unlink($old_image);
        }
        $registro->delete();
        return redirect()->route('productos.index')->with('mensaje', $registro->nombre. ' eliminado correctamente.');
    }
}
