<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Http\Requests\UserRequest;

class PerfilController extends Controller
{
    public function edit(){
        $registro=Auth::user();
        return view('autenticacion.perfil', compact('registro'));
    }
    public function update(UserRequest $request){
        $registro=Auth::user();
        $registro->name = $request->name;
        $registro->email = $request->email;
        // nuevos campos de contacto
        $registro->telefono = $request->telefono;
        $registro->ciudad = $request->ciudad;
        $registro->municipio = $request->municipio;
        if ($request->filled('password')) {
            $registro->password = Hash::make($request->password);
        }
        $registro->save();

        return redirect()->route('perfil.edit')->with('mensaje', 'Datos actualizados correctamente.');
    }

    /**
     * Upload avatar image for the authenticated user.
     */
    public function uploadAvatar(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $file = $request->file('avatar');
        $path = $file->store('avatars', 'public'); // stored in storage/app/public/avatars

        // Build public url (eg /storage/avatars/xxx.jpg)
        $url = Storage::url($path);

        $user->avatar = $url;
        $user->save();

        return back()->with('mensaje', 'Avatar actualizado correctamente.');
    }
}
