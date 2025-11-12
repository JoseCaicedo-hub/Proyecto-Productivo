<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;

class DashboardController extends Controller
{
    public function dashboard()
{
    $productos = Producto::paginate(10); // 👈 en lugar de all()
    return view('dashboard', compact('productos'));
}
}
