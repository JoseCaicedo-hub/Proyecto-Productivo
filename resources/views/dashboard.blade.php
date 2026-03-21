@extends('plantilla.app')
@section('contenido')
@push('estilos')
<style>
    /* Tarjetas de producto uniformes */
    .product-card .image-wrapper { height: 220px; overflow: hidden; display: flex; align-items: center; justify-content: center; }
    .product-card .image-wrapper img { width: 100%; height: 100%; object-fit: cover; }
    .product-card .card-body { display: flex; flex-direction: column; }
    .product-card .card-footer { margin-top: auto; }
    
    /* Tableros de comprador */
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        padding: 24px;
        text-align: center;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
    }
    .stat-card.compras {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        box-shadow: 0 4px 15px rgba(245, 87, 108, 0.3);
    }
    .stat-card.compras:hover {
        box-shadow: 0 8px 25px rgba(245, 87, 108, 0.5);
    }
    .stat-card.comentarios {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        box-shadow: 0 4px 15px rgba(79, 172, 254, 0.3);
    }
    .stat-card.comentarios:hover {
        box-shadow: 0 8px 25px rgba(79, 172, 254, 0.5);
    }
    .stat-card.gastado {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        box-shadow: 0 4px 15px rgba(67, 233, 123, 0.3);
    }
    .stat-card.gastado:hover {
        box-shadow: 0 8px 25px rgba(67, 233, 123, 0.5);
    }
    .stat-number {
        font-size: 48px;
        font-weight: bold;
        margin: 12px 0;
    }
    .stat-label {
        font-size: 14px;
        opacity: 0.95;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .pedidos-recientes {
        margin-top: 32px;
    }
    .pedido-item {
        padding: 16px;
        border-left: 4px solid #667eea;
        background: #f8f9ff;
        border-radius: 6px;
        margin-bottom: 12px;
    }
    .pedido-item:hover {
        background: #f0f2ff;
        border-left-color: #f5576c;
    }
    .pedido-fecha {
        font-size: 12px;
        color: #999;
    }
    .pedido-monto {
        font-weight: bold;
        color: #667eea;
        font-size: 16px;
    }
</style>
@endpush
<div class="app-content">
    <div class="container-fluid">
        <!-- Dashboard para Vendedores -->
        @if($user && $user->hasRole('vendedor') && isset($productos))
            <div class="row">
                <div class="col-md-12 mb-4">
                    <h2 class="mb-4">Panel de Vendedor - Bienvenido, {{ $user->name }}! 👋</h2>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="bi bi-boxes"></i> Mis Productos</h3>
                            <div class="card-tools">
                                <a href="{{ route('productos.create') }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-plus-circle"></i> Nuevo Producto
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row row-cols-1 row-cols-md-3 g-4">
                                @foreach($productos as $producto)
                                    <div class="col">
                                        <div class="card h-100 border-0 shadow-sm product-card">
                                            <div class="image-wrapper">
                                                <img src="{{ asset('uploads/productos/' . $producto->imagen) }}" alt="{{ $producto->nombre }}">
                                            </div>
                                            <div class="card-body">
                                                <p class="text-muted small">{{ $producto->categoria }}</p>
                                                <h5 class="card-title">{{ $producto->nombre }}</h5>
                                                <p class="text-primary fw-bold">${{ number_format($producto->precio, 2) }}</p>
                                                <p class="text-muted small">Stock: {{ $producto->cantidad_almacen }}</p>
                                            </div>
                                            <div class="card-footer bg-transparent">
                                                <a href="{{ route('productos.edit', $producto->id) }}" class="btn btn-sm btn-info">
                                                    <i class="bi bi-pencil"></i> Editar
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="d-flex justify-content-center">{{ $productos->links() }}</div>
                        </div>
                    </div>
                </div>
            </div>

        <!-- Dashboard para Clientes/Compradores -->
        @elseif($user && $user->hasRole('cliente') && isset($estadisticas))
            <div class="row">
                <div class="col-md-12 mb-4">
                    <h2 class="mb-4">Bienvenido, {{ $user->name }}! 👋</h2>
                </div>
            </div>
            
            <!-- Tableros de Estadísticas -->
            <div class="row mb-5">
                <div class="col-md-4 mb-3">
                    <div class="stat-card compras">
                        <i class="bi bi-bag-check" style="font-size: 32px;"></i>
                        <div class="stat-number">{{ $estadisticas['compras_realizadas'] }}</div>
                        <div class="stat-label">Compras Realizadas</div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="stat-card comentarios">
                        <i class="bi bi-chat-left-text" style="font-size: 32px;"></i>
                        <div class="stat-number">{{ $estadisticas['comentarios_dejados'] }}</div>
                        <div class="stat-label">Comentarios Dejados</div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="stat-card gastado">
                        <i class="bi bi-currency-dollar" style="font-size: 32px;"></i>
                        <div class="stat-number">${{ number_format($estadisticas['total_gastado'], 0) }}</div>
                        <div class="stat-label">Total Gastado</div>
                    </div>
                </div>
            </div>
            
            <!-- Pedidos Recientes -->
            @if($estadisticas['pedidos_recientes']->count() > 0)
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Mis Últimas Compras</h3>
                            </div>
                            <div class="card-body pedidos-recientes">
                                @foreach($estadisticas['pedidos_recientes'] as $pedido)
                                    <div class="pedido-item">
                                        <div class="row align-items-center">
                                            <div class="col-md-8">
                                                <div>
                                                    <strong>Pedido #{{ $pedido->id }}</strong>
                                                    <div class="pedido-fecha">{{ $pedido->created_at->format('d M Y - H:i') }}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 text-end">
                                                <div class="pedido-monto">${{ number_format($pedido->total, 2) }}</div>
                                                <span class="badge bg-{{ $pedido->estado == 'completado' ? 'success' : ($pedido->estado == 'pendiente' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($pedido->estado) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>Aún no has realizado compras. ¡Explora nuestros productos!
                        </div>
                    </div>
                </div>
            @endif
        
        @elseif($user && $user->hasRole('admin'))
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Panel de Administrador</h3>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-success mb-0">Bienvenido, {{ $user->name }}. Has iniciado sesión como administrador.</div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif(!$user)
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Dashboard</h3>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">Por favor inicia sesión para ver tu dashboard.</div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Dashboard</h3>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning">No tienes acceso a este dashboard. Por favor contacta al administrador.</div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('mnuDashboard').classList.add('active');
</script>
@endpush
