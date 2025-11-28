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
        <h2 class="text-center mb-5 fw-bold cssunique">Colección destacada</h2>

        @php
            $items = \App\Http\Controllers\HeaderController::topProductos(4);
            if (!$items || $items->isEmpty()) {
                $items = isset($productos) ? $productos->take(4) : collect();
            }
        @endphp

        <div class="row gx-4 gx-lg-5 row-cols-1 row-cols-md-2 row-cols-xl-4 justify-content-center cssunique">
            @foreach($items as $producto)
            <div class="col mb-5 cssunique">
                <div class="card h-100 border-0 shadow-sm cssunique">
                    <!-- Imagen del producto -->
                        <div class="image-wrapper cssunique">
                            <a href="{{ route('web.show', $producto->id) }}">
                                <img class="card-img-top cssunique"
                                     src="{{ asset('uploads/productos/' . $producto->imagen) }}"
                                     alt="{{ $producto->nombre }}">
                            </a>
                        </div>

                        <!-- Detalles del producto (alineados a la izquierda) -->
                        <div class="card-body text-start p-4 cssunique">
                            <p class="text-muted mb-1 small">{{ $producto->categoria ?? 'Sin categoría' }}</p>
                            <h5 class="fw-bold mb-2 cssunique">{{ $producto->nombre }}</h5>
                            <p class="text-primary fw-semibold fs-5 cssunique">
                                $ {{ number_format($producto->precio, 2) }}
                            </p>
                        </div>

                        <!-- Botón de añadir al carrito (centrado) -->
                        <div class="card-footer bg-transparent border-0 text-center pb-4 cssunique">
                            <form action="{{ route('carrito.agregar') }}" method="POST" class="d-inline-block">
                                @csrf
                                <input type="hidden" name="producto_id" value="{{ $producto->id }}">
                                <input type="hidden" name="cantidad" value="1">
                                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">Añadir al carrito</button>
                            </form>
                        </div>
                </div>
            </div>
            @endforeach
        </div>
        </div>

        <!-- Botón ver más centrado -->
        <div class="text-center mt-4">
            <a href="{{ route('web.index') }}" class="btn btn-dark px-4 py-2">Ver más</a>
        </div>

        <!-- Colecciones destacadas -->
        <div class="container px-4 px-lg-5 mt-5 category-collection">
            <h3 class="text-center mb-4 fw-bold">Categorias destacadas</h3>

            <style>
                /* Estilos de las tarjetas de colección - puedes ajustar imágenes en public/images/ */
                .category-collection .cat-card { height:200px; border-radius:8px; background-size:cover; background-position:center; background-repeat:no-repeat; display:flex; align-items:flex-end; color:#fff; position:relative; overflow:hidden; }
                .category-collection .cat-card .cat-label { background:rgba(0,0,0,0.45); width:100%; padding:12px; box-sizing:border-box; }
                .category-collection .cat-actions { text-align:center; margin-top:10px; }
                .btn-cat { background:#7ed6ff; color:#063244; border-radius:0; padding:8px 16px; display:inline-block; text-decoration:none; font-weight:600; }
                /* Reemplaza las rutas por tus imágenes en public/images/ (nombre en minúsculas y sin tildes) */
                .cat-card.hogar { background-image: url('/images/hogar.jpg'); }
                .cat-card.accesorios { background-image: url('/images/accesorios.png'); }
                .cat-card.electronica { background-image: url('/images/electronica.jpg'); }
                .cat-card.ropa { background-image: url('/images/ropa.jpg'); }
                @media (max-width:576px){ .category-collection .cat-card { height:160px; } }
            </style>

            <div class="row gx-4 gx-lg-5 row-cols-1 row-cols-md-2 row-cols-xl-4 justify-content-center">
                <div class="col mb-4">
                    <a href="{{ route('web.index', ['category' => 'Hogar']) }}" class="text-decoration-none">
                        <div class="cat-card hogar shadow-sm">
                            <div class="cat-label"><h5 class="mb-0">Hogar</h5></div>
                        </div>
                    </a>
                    <div class="cat-actions">
                        <a href="{{ route('web.index', ['category' => 'Hogar']) }}" class="btn-cat" aria-label="Ver más Hogar">Ver más</a>
                    </div>
                </div>

                <div class="col mb-4">
                    <a href="{{ route('web.index', ['category' => 'Accesorios']) }}" class="text-decoration-none">
                        <div class="cat-card accesorios shadow-sm">
                            <div class="cat-label"><h5 class="mb-0">Accesorios</h5></div>
                        </div>
                    </a>
                    <div class="cat-actions">
                        <a href="{{ route('web.index', ['category' => 'Accesorios']) }}" class="btn-cat" aria-label="Ver más Accesorios">Ver más</a>
                    </div>
                </div>

                <div class="col mb-4">
                    <a href="{{ route('web.index', ['category' => 'Electrónica']) }}" class="text-decoration-none">
                        <div class="cat-card electronica shadow-sm">
                            <div class="cat-label"><h5 class="mb-0">Electrónica</h5></div>
                        </div>
                    </a>
                    <div class="cat-actions">
                        <a href="{{ route('web.index', ['category' => 'Electrónica']) }}" class="btn-cat" aria-label="Ver más Electrónica">Ver más</a>
                    </div>
                </div>

                <div class="col mb-4">
                    <a href="{{ route('web.index', ['category' => 'Ropa']) }}" class="text-decoration-none">
                        <div class="cat-card ropa shadow-sm">
                            <div class="cat-label"><h5 class="mb-0">Ropa</h5></div>
                        </div>
                    </a>
                    <div class="cat-actions">
                        <a href="{{ route('web.index', ['category' => 'Ropa']) }}" class="btn-cat" aria-label="Ver más Ropa">Ver más</a>
                    </div>
                </div>
            </div>
        </div>

        

        <!-- Caja translúcida: ¿Por qué Nosotros? (dentro de la misma sección) -->
        <div class="container px-4 px-lg-5 mt-4">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="why-box mx-auto">
                        <div class="p-4 p-md-5 text-start text-md-center">
                            <h3 class="fw-bold mb-3">¿Por qué Nosotros?</h3>
                            <p class="mb-3 text-muted">En StarPlace nos esforzamos por ofrecer productos de alta calidad, atención al cliente personalizada y envíos rápidos. Trabajamos con marcas confiables y garantizamos soporte postventa para que compres con tranquilidad.</p>
                            <div class="d-flex justify-content-start justify-content-md-center">
                                <a href="{{ route('web.index') }}" class="btn btn-black" aria-label="Ver más sobre StarPlace">VER MAS</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection