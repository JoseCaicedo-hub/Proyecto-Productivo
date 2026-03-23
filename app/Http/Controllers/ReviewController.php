<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Producto;

class ReviewController extends Controller
{
    public function store(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        if (!auth()->check()) {
            return redirect()->route('web.show', $producto->id)->with('mensaje', 'Debes iniciar sesión para dejar una reseña.');
        }

        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'foto_producto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto_producto')) {
            $fotoPath = $request->file('foto_producto')->store('reviews', 'public');
        }

        $review = Review::create([
            'user_id' => auth()->id(),
            'producto_id' => $producto->id,
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
            'foto_producto' => $fotoPath,
        ]);

        return redirect()->route('web.show', $producto->id)->with('mensaje', 'Gracias por tu reseña.');
    }
}
