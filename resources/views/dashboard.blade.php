@extends('plantilla.app')
@section('contenido')
@push('estilos')
<style>
    /* Tarjetas de producto uniformes */
    .product-card .image-wrapper { height: 220px; overflow: hidden; display: flex; align-items: center; justify-content: center; }
    .product-card .image-wrapper img { width: 100%; height: 100%; object-fit: cover; }
    .product-card .card-body { display: flex; flex-direction: column; }
    .product-card .card-footer { margin-top: auto; }
</style>
@endpush
<div class="app-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="card-title">Dashboard</h3>
                    </div>
                    <div class="card-body">
                        @if(Session::has('mensaje'))
                            <div class="alert alert-info alert-dismissible fade show mt-2">
                                {{ Session::get('mensaje') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                            </div>
                        @endif

                        <!-- Mostrar solo tarjetas de los productos del usuario -->
                        <div class="row gx-4 gx-lg-5 row-cols-1 row-cols-md-3 row-cols-xl-3 justify-content-center">
                            @if($productos->count() === 0)
                                <div class="col-12">
                                    <div class="alert alert-info">No tienes productos publicados todavía.</div>
                                </div>
                            @else
                                @foreach($productos as $producto)
                                    <div class="col mb-5">
                                        <div class="card product-card h-100 border-0 shadow-sm">
                                            <div class="image-wrapper">
                                                <a href="{{ route('web.show', $producto->id) }}">
                                                    <img class="card-img-top" src="{{ asset('uploads/productos/' . $producto->imagen) }}" alt="{{ $producto->nombre }}">
                                                </a>
                                            </div>
                                            <div class="card-body text-start p-4">
                                                <p class="text-muted mb-1 small">{{ $producto->categoria ?? 'Sin categoría' }}</p>
                                                <h5 class="fw-bold mb-2">{{ $producto->nombre }}</h5>
                                                <p class="text-primary fw-semibold fs-5">$ {{ number_format($producto->precio, 2) }}</p>
                                            </div>
                                            <div class="card-footer bg-transparent border-0 text-center pb-4">
                                                <a href="{{ route('productos.edit', $producto->id) }}" class="btn btn-outline-secondary btn-lg">Editar</a>
                                                <a href="{{ route('web.show', $producto->id) }}" class="btn btn-primary btn-lg ms-2">Ver</a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <div class="d-flex justify-content-center">{{ $productos->links() }}</div>

                    </div>
                    <div class="card-footer clearfix">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('mnuDashboard').classList.add('active');
</script>
@endpush
