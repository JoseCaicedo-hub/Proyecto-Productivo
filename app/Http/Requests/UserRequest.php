<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $method = $this->method();
        $id = $this->route('usuario') ?? Auth::id(); 
        
        $rules= [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($id), // 👈 Correcto, todo en array
            ],
            'telefono' => 'nullable|regex:/^[0-9]+$/|digits_between:7,15',
            'pais' => 'nullable|string|max:100',
            'departamento' => 'nullable|string|max:120',
            'direccion' => 'nullable|string|max:1000',
            'ciudad' => 'nullable|string|max:100',
            'municipio' => 'nullable|string|max:100',
        ];

        if ($method === 'POST') {
            $rules['password'] = 'required|min:8|confirmed'; // Requerido solo en POST (crear)
        } else if (in_array($method, ['PUT', 'PATCH'])) {
            $rules['telefono'] = 'required|regex:/^[0-9]+$/|digits_between:7,15';
            $rules['pais'] = 'nullable|string|max:100';
            $rules['departamento'] = 'required|string|max:120';
            $rules['direccion'] = 'required|string|max:1000';
            $rules['ciudad'] = 'required|string|max:100';
            $rules['municipio'] = 'required|string|max:100';
            $rules['password'] = 'nullable|min:8|confirmed'; // No obligatorio en PUT (editar)
        }

        return $rules;
    }
    public function messages()
    {
        return [
            'name.required' => 'El campo nombre es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',

            'email.required' => 'El campo correo electrónico es obligatorio.',
            'email.email' => 'Debe ingresar un correo electrónico válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',

            'telefono.regex' => 'El teléfono solo debe contener números.',
            'telefono.digits_between' => 'El teléfono debe tener entre 7 y 15 dígitos.',
            'telefono.required' => 'El campo teléfono es obligatorio.',

            'ciudad.required' => 'El campo país es obligatorio.',
            'municipio.required' => 'El campo ciudad/municipio es obligatorio.',
            'departamento.required' => 'El campo departamento/estado es obligatorio.',
            'direccion.required' => 'El campo dirección es obligatorio.',

            'password.required' => 'El campo contraseña es obligatorio.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.'
        ];
    }

}
