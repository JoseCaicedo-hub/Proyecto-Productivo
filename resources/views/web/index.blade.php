@extends('web.app')
@section('header')
@endsection
@section('contenido')
<form method="GET" action="{{route('web.index')}}">
    <style>
        .search-panel{border-radius:14px;background:linear-gradient(180deg,#ffffff,#fbfdff)}
        .search-input-wrap{position:relative}
        .search-icon{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#38bdf8;font-size:1rem}
        .search-input{padding-left:2.15rem;border:1px solid rgba(11,99,214,0.12);border-radius:10px;height:48px}
        .search-input:focus{border-color:rgba(56,189,248,.45);box-shadow:0 0 0 .2rem rgba(56,189,248,.15)}
        .btn-search{background:linear-gradient(90deg,#38bdf8,#7dd3fc);border:0;color:#083344;font-weight:700;border-radius:10px;width:48px;height:48px;padding:0;display:inline-flex;align-items:center;justify-content:center;transition:transform .14s ease, box-shadow .14s ease}
        .btn-search i{transition:transform .14s ease}
        .btn-search:hover{filter:brightness(.98);color:#05202b;transform:translateY(-1px);box-shadow:0 10px 20px rgba(56,189,248,.22)}
        .btn-search:hover i{transform:scale(1.12) rotate(-8deg)}
        .btn-search-clear{border:1px solid rgba(11,99,214,0.18);border-radius:10px;background:#fff;color:#0b63d6;width:48px;height:48px;padding:0;display:inline-flex;align-items:center;justify-content:center}
        .btn-search-clear:hover{background:#f0f9ff;color:#0a58c7}
        .search-hint{font-size:.9rem;color:#64748b}
        html.dark-mode .search-panel{background:#111827;border:1px solid rgba(148,163,184,.2)}
        html.dark-mode .search-input{background:#0b1220;color:#e2e8f0;border-color:rgba(148,163,184,.28)}
        html.dark-mode .search-hint{color:#94a3b8}
        html.dark-mode .btn-search-clear{background:#0f172a;color:#93c5fd;border-color:rgba(148,163,184,.3)}
    </style>
    <div class="container px-4 px-lg-5 mt-4">
        <div class="card border-0 shadow-sm search-panel mb-3">
            <div class="card-body p-3 p-md-4">
                <div class="row g-2 align-items-end">
                    <div class="col-12 col-lg-8">
                        <label for="searchInput" class="form-label fw-semibold mb-1">Buscar producto</label>
                        <div class="search-input-wrap">
                            <i class="bi bi-search search-icon" aria-hidden="true"></i>
                            <input type="text" class="form-control search-input" id="searchInput" placeholder="Ej: audífonos, camiseta, hogar..."
                                aria-label="Buscar productos" name="search" value="{{request('search')}}">
                        </div>
                    </div>
                    @php
                        $empresasFiltro = \App\Models\Empresa::query()
                            ->where('estado', 'aprobada')
                            ->whereHas('productos')
                            ->orderBy('nombre')
                            ->get(['id','nombre']);
                    @endphp
                    <div class="col-12 col-lg-2">
                        <label for="empresa" class="form-label fw-semibold mb-1">Empresa</label>
                        <select id="empresa" name="empresa" class="form-select">
                            <option value="">Todas</option>
                            @foreach($empresasFiltro as $empresaItem)
                                <option value="{{ $empresaItem->id }}" {{ (string)request('empresa') === (string)$empresaItem->id ? 'selected' : '' }}>{{ $empresaItem->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-lg-2 d-flex gap-2 justify-content-lg-start">
                        <button class="btn btn-search" type="submit" id="searchButton" aria-label="Buscar">
                            <i class="bi bi-search"></i>
                            <span class="visually-hidden">Buscar</span>
                        </button>
                        @if(request()->filled('search') || request()->filled('empresa'))
                            <a href="{{ route('web.index') }}" class="btn btn-search-clear" title="Limpiar búsqueda" aria-label="Limpiar búsqueda">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        @endif
                    </div>
                </div>
                <div class="search-hint mt-2">
                    <i class="bi bi-lightbulb me-1"></i> Tip: escribe nombre del producto o categoría para encontrar resultados más rápido.
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
            // Si no hay suficientes productos en los más vendidos, completar con productos recientes
            if (!$items) {
                $items = collect();
            }
            if ($items->count() < 4) {
                $existingIds = $items->pluck('id')->filter()->toArray();
                $needed = 4 - $items->count();
                $extras = \App\Models\Producto::with('empresa')
                    ->whereNotIn('id', $existingIds)
                    ->whereNotNull('empresa_id')
                    ->whereHas('empresa', function($q){
                        $q->where('estado', 'aprobada');
                    })
                    ->latest()
                    ->take($needed)
                    ->get();
                $items = $items->concat($extras);
            }
            // Si aún está vacío, intentar usar la variable $productos pasada desde el controlador
            if ($items->isEmpty() && isset($productos)) {
                $items = $productos->take(4);
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
                            <div class="small text-muted d-flex align-items-center gap-2">
                                @if(optional($producto->empresa)->logo)
                                    <img src="{{ asset($producto->empresa->logo) }}" alt="{{ $producto->empresa->nombre }}" style="width:22px;height:22px;border-radius:50%;object-fit:cover;">
                                @endif
                                <span>{{ optional($producto->empresa)->nombre ?? 'Sin empresa' }}</span>
                            </div>
                        </div>

                        <!-- Botón de añadir al carrito (centrado) -->
                        <div class="card-footer bg-transparent border-0 text-center pb-4 cssunique">
                            @php
                                $categoria = $producto->categoria ?? '';
                                $esRopa = is_string($categoria) && strtolower(trim($categoria)) === 'ropa';
                            @endphp

                            <div class="product-actions">
                                <form action="{{ route('carrito.agregar') }}" method="POST" class="{{ $esRopa ? 'add-with-size' : '' }}">
                                    @csrf
                                    <input type="hidden" name="producto_id" value="{{ $producto->id }}">
                                    <input type="hidden" name="cantidad" value="1">

                                    @if($esRopa)
                                        <div class="mb-2">
                                            <select name="talla" class="form-select form-select-sm talla-select" aria-label="Selecciona talla" required>
                                                <option value="">Selecciona talla</option>
                                                <option value="XS">XS</option>
                                                <option value="S">S</option>
                                                <option value="M">M</option>
                                                <option value="L">L</option>
                                                <option value="XL">XL</option>
                                            </select>
                                        </div>
                                    @endif

                                    <button type="submit" class="btn btn-primary btn-add-cart fw-semibold">Añadir al carrito</button>
                                </form>
                                <a href="{{ route('web.show', $producto->id) }}" class="btn btn-view-article fw-semibold">Ver artículo</a>
                            </div>
                        </div>
                </div>
            </div>
            @endforeach
        </div>
        </div>

        <!-- Botón ver más centrado -->
        <div class="text-center mt-4 mb-5">
            <a href="{{ route('web.tienda') }}" class="btn btn-dark px-4 py-2">Ver más</a>
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
                    <a href="{{ route('web.tienda', ['category' => 'Hogar']) }}" class="text-decoration-none">
                        <div class="cat-card hogar shadow-sm">
                            <div class="cat-label"><h5 class="mb-0">Hogar</h5></div>
                        </div>
                    </a>
                    <div class="cat-actions">
                        <a href="{{ route('web.tienda', ['category' => 'Hogar']) }}" class="btn-cat" aria-label="Ver más Hogar">Ver más</a>
                    </div>
                </div>

                <div class="col mb-4">
                    <a href="{{ route('web.tienda', ['category' => 'Accesorios']) }}" class="text-decoration-none">
                        <div class="cat-card accesorios shadow-sm">
                            <div class="cat-label"><h5 class="mb-0">Accesorios</h5></div>
                        </div>
                    </a>
                    <div class="cat-actions">
                        <a href="{{ route('web.tienda', ['category' => 'Accesorios']) }}" class="btn-cat" aria-label="Ver más Accesorios">Ver más</a>
                    </div>
                </div>

                <div class="col mb-4">
                    <a href="{{ route('web.tienda', ['category' => 'Electrónica']) }}" class="text-decoration-none">
                        <div class="cat-card electronica shadow-sm">
                            <div class="cat-label"><h5 class="mb-0">Electrónica</h5></div>
                        </div>
                    </a>
                    <div class="cat-actions">
                        <a href="{{ route('web.tienda', ['category' => 'Electrónica']) }}" class="btn-cat" aria-label="Ver más Electrónica">Ver más</a>
                    </div>
                </div>

                <div class="col mb-4">
                    <a href="{{ route('web.tienda', ['category' => 'Ropa']) }}" class="text-decoration-none">
                        <div class="cat-card ropa shadow-sm">
                            <div class="cat-label"><h5 class="mb-0">Ropa</h5></div>
                        </div>
                    </a>
                    <div class="cat-actions">
                        <a href="{{ route('web.tienda', ['category' => 'Ropa']) }}" class="btn-cat" aria-label="Ver más Ropa">Ver más</a>
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
                                <a href="{{ route('web.equipo') }}" class="btn btn-black" aria-label="Ver más sobre StarPlace">VER MAS</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</section>

    <!-- Reseñas -->
    <div class="container px-4 px-lg-5 mt-5">
        <h3 class="text-center mb-4 fw-bold">Reseñas</h3>
        <div class="row gx-4 gx-lg-5">
            <div class="col-md-4 mb-4">
                <div class="card review-card h-100 position-relative p-4">
                    <div class="review-social position-absolute top-0 end-0 m-2"><i class="bi bi-facebook" aria-hidden="true"></i></div>
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ asset('images/user.png') }}" alt="Avatar María Pérez" class="rounded-circle me-3" style="width:56px;height:56px;object-fit:cover;">
                        <div>
                            <strong>María Pérez</strong><br>
                            <small class="text-muted">Clienta</small>
                        </div>
                    </div>
                    <p class="mb-0 text-muted">StartPlace ha sido una de las mejores experiencias para vender. La plataforma es súper fácil de usar, puedo subir mis productos en segundos y llegar a más personas sin complicaciones. ¡Es genial lo rápido que se puede empezar a generar ventas!</p>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card review-card h-100 position-relative p-4">
                    <div class="review-social position-absolute top-0 end-0 m-2"><i class="bi bi-instagram" aria-hidden="true"></i></div>
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ asset('images/user.png') }}" alt="Avatar Juan López" class="rounded-circle me-3" style="width:56px;height:56px;object-fit:cover;">
                        <div>
                            <strong>Juan López</strong><br>
                            <small class="text-muted">Comprador</small>
                        </div>
                    </div>
                    <p class="mb-0 text-muted">Buena atención al cliente. Resolveron una duda rápidamente y el producto llegó en perfecto estado.</p>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card review-card h-100 position-relative p-4">
                    <div class="review-social position-absolute top-0 end-0 m-2"><i class="bi bi-facebook" aria-hidden="true"></i></div>
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ asset('images/user.png') }}" alt="Avatar Laura Gómez" class="rounded-circle me-3" style="width:56px;height:56px;object-fit:cover;">
                        <div>
                            <strong>Laura Gómez</strong><br>
                            <small class="text-muted">Clienta</small>
                        </div>
                    </div>
                    <p class="mb-0 text-muted">Gran variedad de productos. Volveré a comprar sin dudar. El seguimiento del pedido fue claro y puntual.</p>
                </div>
            </div>
        </div>
    </div>

    </div>
</section>

<!-- Sección: Logo + Llamado a la acción (debajo de Reseñas) -->
<section class="py-5">
    <div class="container px-4 px-lg-5">
        <div class="row align-items-center gx-4">
            <div class="col-md-4 text-center text-md-start mb-3 mb-md-0">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="img-fluid" style="max-width:600px;">
            </div>
            <div class="col-md-8 cta-ps" style="padding-left:18rem;">
                <h3 class="fw-bold">Empieza a vender con StartPlace</h3>
                <p class="text-muted">Por qué vender con nosotros:</p>
                <ul class="text-muted">
                    <li>Alcance a miles de clientes potenciales.</li>
                    <li>Comisiones competitivas y pagos rápidos.</li>
                    <li>Soporte dedicado y herramientas para gestionar tus ventas.</li>
                </ul>
            <a href="{{ route('web.solicitud') }}" class="btn btn-dark px-4 py-2">Me interesa</a>
            </div>
        </div>
    </div>
</section>

@endsection

<script>
    (function(){
        // Validación simple para formularios en tarjetas que requieren talla
        document.addEventListener('DOMContentLoaded', function(){
            document.querySelectorAll('form.add-with-size').forEach(function(form){
                form.addEventListener('submit', function(e){
                    var select = form.querySelector('.talla-select');
                    if(select && (!select.value || select.value.trim() === '')){
                        e.preventDefault();
                        alert('Por favor selecciona una talla antes de añadir al carrito.');
                    }
                });
            });
        });
    })();
</script>