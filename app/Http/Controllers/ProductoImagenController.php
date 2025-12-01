<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductoImagen;

class ProductoImagenController extends Controller
{
    /** Subir imágenes adicionales para un producto (desde admin) */
    public function store(Request $request, $productoId)
    {
        $this->authorize('producto-edit');

        $request->validate([
            'imagenes' => ['required','array','min:1'],
            'imagenes.*' => ['image','mimes:jpg,jpeg,png','max:4096'],
        ]);

        $files = $request->file('imagenes');
        $sufijo = strtolower(\Illuminate\Support\Str::random(2));
        $orden = (int) (ProductoImagen::where('producto_id', $productoId)->max('orden') ?? 0);

        foreach ($files as $file) {
            if ($file && $file->isValid()) {
                $nombre = $sufijo.'-'.time().'-'.$orden.'-'.$file->getClientOriginalName();
                $file->move('uploads/productos', $nombre);
                ProductoImagen::create([
                    'producto_id' => $productoId,
                    'filename' => $nombre,
                    'orden' => $orden,
                ]);
                $orden++;
            }
        }

        return back()->with('mensaje','Imágenes agregadas correctamente');
    }

    public function destroy($id)
    {
        $imagen = ProductoImagen::findOrFail($id);
        $this->authorize('producto-edit');
        $path = 'uploads/productos/'.$imagen->filename;
        if (file_exists($path)) {@unlink($path);}        
        $imagen->delete();
        return back()->with('mensaje','Imagen eliminada');
    }
}
