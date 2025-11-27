<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\PedidoDetalle;
use Illuminate\Support\Facades\DB;

class HeaderController
{
    /**
     * Devuelve los productos más pedidos.
     *
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public static function topProductos(int $limit = 5)
    {
        // Obtener los IDs de producto más pedidos según la suma de cantidades
        $topIds = PedidoDetalle::select('producto_id', DB::raw('SUM(cantidad) as total'))
            ->groupBy('producto_id')
            ->orderByDesc('total')
            ->limit($limit)
            ->pluck('producto_id')
            ->toArray();

        if (empty($topIds)) {
            return collect();
        }

        // Recuperar los productos en el mismo orden
        $productos = Producto::whereIn('id', $topIds)->get()->keyBy('id');

        $ordered = collect($topIds)->map(function ($id) use ($productos) {
            return $productos->get($id);
        })->filter();

        return $ordered;
    }

    /**
     * Devuelve las categorías más pedidas (limit por defecto 4).
     * Cada item tendrá: categoria, total (cantidad pedida) y sample_product (Producto|null)
     *
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public static function topCategorias(int $limit = 4)
    {
        $rows = DB::table('pedido_detalles')
            ->join('productos', 'pedido_detalles.producto_id', '=', 'productos.id')
            ->select('productos.categoria', DB::raw('SUM(pedido_detalles.cantidad) as total'))
            ->whereNotNull('productos.categoria')
            ->groupBy('productos.categoria')
            ->orderByDesc('total')
            ->limit($limit)
            ->get();

        $result = collect();
        foreach ($rows as $row) {
            $sample = Producto::where('categoria', $row->categoria)->latest()->first();
            $result->push((object)[
                'categoria' => $row->categoria,
                'total' => $row->total,
                'sample_product' => $sample,
            ]);
        }

        return $result;
    }
}
