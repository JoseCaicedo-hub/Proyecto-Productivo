<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Empresa;
use App\Models\Category;
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
        $query = Producto::with('empresa');

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
        $categorias = Category::orderBy('name')->pluck('name')->toArray();
        $user = auth()->user();
        
        // Verificar que el vendedor tenga una empresa asignada (NO aplica para admins)
        if (auth()->check() && !$user->hasRole('admin') && !$this->resolveEmpresaIdForUser($user)) {
            return redirect()->route('dashboard')->with('error', 'Debes completar tu perfil como vendedor y tener una empresa asignada para crear productos.');
        }

        // Para admins, pasar lista de todas las empresas
        $empresas = $user->hasRole('admin')
            ? Empresa::whereIn('estado', ['activo', 'aprobada'])->orderBy('nombre')->get()
            : null;

        return view('producto.action', compact('categorias', 'empresas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductoRequest $request)
    {
        $this->authorize('producto-create'); 
        $user = auth()->user();
        $empresaIdVendedor = $this->resolveEmpresaIdForUser($user);
        
        // Verificar que el usuario (no admin) tenga empresa asignada
        if (!$user->hasRole('admin') && !$empresaIdVendedor) {
            return redirect()->route('dashboard')->with('error', 'No tienes una empresa asignada. Por favor completa tu perfil.');
        }

        $registro = new Producto();
        // Asociar el producto al usuario que lo publica
        $registro->user_id = auth()->id();
        // Automáticamente asignar la empresa del vendedor
        if (!$user->hasRole('admin')) {
            $registro->empresa_id = $empresaIdVendedor;
        } elseif ($request->filled('empresa_id')) {
            // Admins pueden asignar empresa manualmente
            $registro->empresa_id = $request->input('empresa_id');
        }
        
        $registro->codigo = $request->input('codigo');
        $registro->nombre = $request->input('nombre');
        $registro->categoria = $request->input('categoria');
        $registro->precio = $request->input('precio');
        $registro->cantidad_almacen = $request->input('cantidad_almacen');
        $registro->descripcion = $request->input('descripcion');
        $sufijo = strtolower(Str::random(2));
        $image = $request->file('imagen');
        if (!is_null($image)){            
            $nombreImagen = $sufijo.'-'.$image->getClientOriginalName();
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
        $user = auth()->user();
        if (!$user->can('producto-edit') && !$user->hasRole('vendedor')) {
            abort(403, 'No autorizado para editar productos.');
        }

        $registro=Producto::findOrFail($id);
        // Si el usuario es vendedor, sólo puede editar sus propios productos
        if (auth()->check() && auth()->user()->hasRole('vendedor')) {
            $empresaId = $this->resolveEmpresaIdForUser(auth()->user());
            $esPropioPorUsuario = ((int) $registro->user_id === (int) auth()->id());
            $esPropioPorEmpresa = ($empresaId && (int) $registro->empresa_id === (int) $empresaId);

            if (!$esPropioPorUsuario && !$esPropioPorEmpresa) {
                abort(403, 'No autorizado a editar este producto');
            }
        }
        $categorias = Category::orderBy('name')->pluck('name')->toArray();
        $ownerId = auth()->user()->hasRole('admin') ? ($registro->user_id ?? auth()->id()) : auth()->id();

        $empresas = Empresa::where('user_id', $ownerId)
            ->whereIn('estado', ['activo', 'aprobada'])
            ->orderBy('nombre')
            ->get();

        if ($registro->empresa_id && !$empresas->contains('id', $registro->empresa_id)) {
            $empresaActual = Empresa::find($registro->empresa_id);
            if ($empresaActual) {
                $empresas->prepend($empresaActual);
            }
        }

        return view('producto.action', compact('registro','categorias', 'empresas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductoRequest $request, $id)
    {
        $user = auth()->user();
        if (!$user->can('producto-edit') && !$user->hasRole('vendedor')) {
            abort(403, 'No autorizado para actualizar productos.');
        }

        $registro=Producto::findOrFail($id);
        // Si el usuario es vendedor, sólo puede actualizar sus propios productos
        if (auth()->check() && auth()->user()->hasRole('vendedor')) {
            $empresaId = $this->resolveEmpresaIdForUser(auth()->user());
            $esPropioPorUsuario = ((int) $registro->user_id === (int) auth()->id());
            $esPropioPorEmpresa = ($empresaId && (int) $registro->empresa_id === (int) $empresaId);

            if (!$esPropioPorUsuario && !$esPropioPorEmpresa) {
                abort(403, 'No autorizado a actualizar este producto');
            }
        }
        $registro->codigo=$request->input('codigo');
        $registro->nombre=$request->input('nombre');
        if ($user->hasRole('vendedor')) {
            $registro->empresa_id = $this->resolveEmpresaIdForUser($user) ?: $registro->empresa_id;
        } else {
            $registro->empresa_id = $request->input('empresa_id', $registro->empresa_id);
        }
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

    private function resolveEmpresaIdForUser($user): ?int
    {
        if (!$user || $user->hasRole('admin')) {
            return $user?->empresa_id ? (int) $user->empresa_id : null;
        }

        if ($user->empresa_id) {
            return (int) $user->empresa_id;
        }

        $empresa = Empresa::where('user_id', $user->id)
            ->whereIn('estado', ['activo', 'aprobada'])
            ->orderByDesc('id')
            ->first();

        if (!$empresa) {
            return null;
        }

        $user->empresa_id = $empresa->id;
        $user->save();

        return (int) $empresa->id;
    }
}
