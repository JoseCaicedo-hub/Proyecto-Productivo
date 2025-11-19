@extends('web.app')
@section('header')
@endsection
@section('contenido')
<form method="GET" action="{{route('web.index')}}">
    <div class="container px-4 px-lg-5 mt-4">
        <div class="row">
            <div class="col-md-8 mb-3">
                <div class="input-group">
                    <input type="text" class="form-control" id="searchInput" placeholder="Buscar productos..."
                        aria-label="Buscar productos" name="search" value="{{request('search')}}">
                    <button class="btn btn-outline-dark" type="submit" id="searchButton">
                        <i class="bi bi-search"></i> Buscar
                    </button>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="input-group">
                    <label class="input-group-text" for="sortSelect">Ordenar por:</label>
                    <select class="form-select" id="sortSelect" name="sort">
                        <option value="priceAsc" {{ request('sort') == 'priceAsc' ? 'selected' : '' }}>Precio: menor a
                            mayor</option>
                        <option value="priceDesc" {{ request('sort') == 'priceDesc' ? 'selected' : '' }}>Precio: mayor a
                            menor</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</form>
<!-- Section-->
<section class="py-5 bg-light cssunique">
    <div class="container px-4 px-lg-5 mt-1 cssunique">
        <h2 class="text-center mb-5 fw-bold cssunique">Nuestros Productos</h2>
        <div class="row gx-4 gx-lg-5 row-cols-1 row-cols-md-3 row-cols-xl-4 justify-content-center cssunique">
            @foreach($productos as $producto)
            <div class="col mb-5 cssunique">
                <div class="card h-100 border-0 shadow-sm cssunique">
                    <!-- Imagen del producto -->
                    <div class="image-wrapper cssunique">
                        <img class="card-img-top cssunique"
                             src="{{ asset('uploads/productos/' . $producto->imagen) }}"
                             alt="{{ $producto->nombre }}">
                    </div>

                    <!-- Detalles del producto -->
                    <div class="card-body text-center p-4 cssunique">
                        <h5 class="fw-bold mb-2 cssunique">{{ $producto->nombre }}</h5>
                        <p class="text-primary fw-semibold fs-5 cssunique">
                            $ {{ number_format($producto->precio, 2) }}
                        </p>
                        
                        <!-- Cantidad disponible -->
                        <div class="mb-2 cssunique">
                            @if(($producto->cantidad_almacen ?? 0) > 0)
                                <span class="badge bg-success cssunique">
                                    <i class="bi bi-box-seam"></i> Disponible: {{ $producto->cantidad_almacen }}
                                </span>
                            @else
                                <span class="badge bg-danger cssunique">
                                    <i class="bi bi-x-circle"></i> Agotado
                                </span>
                            @endif
                        </div>

                        <!-- Estrellas -->
                        <div class="stars d-flex justify-content-center mb-3 cssunique">
                            <i class="bi bi-star-fill text-warning cssunique"></i>
                            <i class="bi bi-star-fill text-warning cssunique"></i>
                            <i class="bi bi-star-fill text-warning cssunique"></i>
                            <i class="bi bi-star-half text-warning cssunique"></i>
                            <i class="bi bi-star text-warning cssunique"></i>
                        </div>
                    </div>

                    <!-- Botón de acción -->
                    <div class="card-footer bg-transparent border-0 text-center pb-4 cssunique">
                        <a href="{{ route('web.show', $producto->id) }}"
                           class="btn btn-primary rounded-pill px-4 fw-semibold cssunique">
                            Ver producto
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Paginación -->
        <div class="d-flex justify-content-center mt-4 cssunique">
            {{ $productos->appends(['search' => request('search'), 'sort' => request('sort')])->links() }}
        </div>
    </div>
</section>

@endsection