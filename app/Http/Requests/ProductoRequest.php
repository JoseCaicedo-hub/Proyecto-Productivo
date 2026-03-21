<?php

namespace App\Http\Requests;

use App\Models\Producto;
use App\Helpers\PriceHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductoRequest extends FormRequest
{
    /**
     * Prepare the data for validation by cleaning price input
     */
    protected function prepareForValidation(): void
    {
        // Limpiar precio: convertir formatos como "1.000" o "1,000" a "1000"
        if ($this->has('precio')) {
            $cleanedPrice = PriceHelper::cleanPrice($this->input('precio'));
            if ($cleanedPrice !== null) {
                $this->merge([
                    'precio' => $cleanedPrice,
                ]);
            }
        }
    }

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
        $id = $this->route('producto');
        $ownerId = auth()->id();
        $isAdmin = auth()->check() && auth()->user()->hasRole('admin');

        if ($id && auth()->check() && auth()->user()->hasRole('admin')) {
            $producto = Producto::find($id);
            if ($producto && $producto->user_id) {
                $ownerId = $producto->user_id;
            }
        }

        $empresaRules = [
            'nullable',
            'integer',
            Rule::exists('empresas', 'id')->where(function ($query) use ($ownerId) {
                $query->where('estado', 'activo')
                      ->where('user_id', $ownerId);
            }),
        ];

        if ($isAdmin) {
            $empresaRules[0] = 'required';
        }

        $rules = [
            'codigo' => ['required', 'string', 'max:16', 'unique:productos,codigo,' . $id],
            'nombre' => ['required', 'string', 'max:100'],
            'empresa_id' => $empresaRules,
            'categoria' => ['nullable', 'string', 'max:100', Rule::exists('categories', 'name')],
            'precio' => ['required', 'integer', 'min:1'],
            'cantidad_almacen' => ['required', 'integer', 'min:0'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'imagen' => [$method === 'POST' ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
        return $rules;
    }
    public function messages(): array
    {
        return [
            'codigo.required' => 'El código del producto es obligatorio.',
            'codigo.unique' => 'Este código ya está registrado en otro producto.',
            'codigo.max' => 'El código no puede tener más de 50 caracteres.',

            'nombre.required' => 'El nombre del producto es obligatorio.',
            'nombre.max' => 'El nombre no puede tener más de 255 caracteres.',

            'empresa_id.required' => 'Debes seleccionar una empresa para el producto.',
            'empresa_id.exists' => 'La empresa seleccionada no está aprobada o no te pertenece.',

            'precio.required' => 'El precio del producto es obligatorio.',
            'precio.integer' => 'El precio debe ser un número entero en COP (sin decimales).',
            'precio.min' => 'El precio debe ser mayor a 0.',

            'cantidad_almacen.required' => 'La cantidad en almacén es obligatoria.',
            'cantidad_almacen.integer' => 'La cantidad en almacén debe ser un número entero.',
            'cantidad_almacen.min' => 'La cantidad en almacén no puede ser negativa.',

            'descripcion.max' => 'La descripción no puede tener más de 1000 caracteres.',

            'categoria.max' => 'La categoría no puede tener más de 100 caracteres.',
            'categoria.exists' => 'La categoría seleccionada no es válida.',

            'imagen.required' => 'La imagen del producto es obligatoria.',
            'imagen.image' => 'El archivo debe ser una imagen.',
            'imagen.mimes' => 'La imagen debe ser de tipo JPG o PNG.',
            'imagen.max' => 'La imagen no debe pesar más de 2MB.',
        ];
    }
}
