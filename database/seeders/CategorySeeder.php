<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Electrónica', 'description' => null],
            ['name' => 'Moda', 'description' => null],
            ['name' => 'Hogar y Muebles', 'description' => null],
            ['name' => 'Belleza y Cuidado Personal', 'description' => null],
            ['name' => 'Supermercado', 'description' => null],
            ['name' => 'Deportes y Ocio', 'description' => null],
            ['name' => 'Juguetes y Niños', 'description' => null],
            ['name' => 'Automotriz', 'description' => null],
            ['name' => 'Mascotas', 'description' => null],
            ['name' => 'Libros y Educación', 'description' => null],
            ['name' => 'Herramientas y Construcción', 'description' => null],
            ['name' => 'Oficina y Papelería', 'description' => null],
            ['name' => 'Tecnología Gaming', 'description' => null],
            ['name' => 'Artesanías y Emprendedores', 'description' => null],
            ['name' => 'Segunda Mano', 'description' => null],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                ['description' => $category['description']]
            );
        }
    }
}
