<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;


class RegisterController extends Controller
{
    public function showRegistroForm(){
        return view('autenticacion.registro');
    }

    public function registrar(Request $request){
        // Validar datos básicos
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:8',
            'telefono' => 'required|string',
            'pais' => 'required|string',
            'ciudad' => 'required|string',
        ]);

        // Crear usuario como cliente
        $usuario = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'telefono' => $request->input('telefono'),
            'pais' => $request->input('pais'),
            'ciudad' => $request->input('ciudad'),
            'municipio' => $request->input('ciudad'),
            'activo' => 1,
        ]);

        // Asignar rol cliente
        $clienteRol = Role::where('name', 'cliente')->first();
        if ($clienteRol) {
            $usuario->assignRole($clienteRol);
        }

        Auth::login($usuario);
        return redirect()->route('dashboard')->with('mensaje', 'Registro exitoso. ¡Bienvenido!');
    }
}

