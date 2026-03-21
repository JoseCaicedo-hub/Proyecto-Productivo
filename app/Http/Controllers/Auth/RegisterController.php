<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Empresa;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UserRequest;


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
            'tipo_usuario' => 'required|in:cliente,vendedor',
            'empresa_nombre' => 'required_if:tipo_usuario,vendedor|string|max:255',
            'empresa_logo' => 'required_if:tipo_usuario,vendedor|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Crear usuario
        $usuario = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'telefono' => $request->input('telefono'),
            'ciudad' => $request->input('ciudad'),
            'municipio' => $request->input('ciudad'),
            'activo' => 1,
        ]);

        // Si es vendedor, crear empresa automáticamente
        if ($request->input('tipo_usuario') === 'vendedor') {
            $logoPath = $request->file('empresa_logo')->store('empresas', 'public');
            
            $empresa = Empresa::create([
                'user_id' => $usuario->id,
                'nombre' => $request->input('empresa_nombre'),
                'logo' => $logoPath,
                'descripcion' => '',
                'contacto' => $request->input('telefono'),
                'estado' => 'activo',
            ]);

            // Asociar empresa al usuario
            $usuario->update(['empresa_id' => $empresa->id]);

            // Asignar rol vendedor
            $vendedorRol = Role::where('name', 'vendedor')->first();
            if ($vendedorRol) {
                $usuario->assignRole($vendedorRol);
            }
        } else {
            // Asignar rol cliente
            $clienteRol = Role::where('name', 'cliente')->first();
            if ($clienteRol) {
                $usuario->assignRole($clienteRol);
            }
        }

        Auth::login($usuario);
        return redirect()->route('dashboard')->with('mensaje', 'Registro exitoso. ¡Bienvenido!');
    }
}

