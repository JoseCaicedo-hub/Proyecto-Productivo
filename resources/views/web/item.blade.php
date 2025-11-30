@extends('web.app')

@section('contenido')
<!-- Detalle del producto -->
<form action="{{ route('carrito.agregar') }}" method="POST" class="d-flex justify-content-center">
    @csrf
    <section class="py-5" >
        <style>
            /* Estilos específicos para la tarjeta de detalle de producto */
            .product-card-detail { border-radius:14px; box-shadow: 0 18px 40px rgba(2,6,23,0.06); border:1px solid rgba(2,6,23,0.04); background: linear-gradient(180deg,#ffffff 0%, #f6fbff 100%); }
            .product-card-detail .img-fluid { border-radius:12px; }
            .product-card-detail .display-5 { color: #0b5ed7; }
            .product-card-detail .fs-3 { color: #0ca678; }
            .product-card-detail .btn-primary.btn-lg { padding: 10px 20px; border-radius:8px; box-shadow: 0 6px 18px rgba(13,110,253,0.12); }
            .product-card-detail .qty-box { background: #f0f9ff; }
        </style>
        <div class="container px-4 px-lg-5 my-5">
            <div class="row gx-5 align-items-center shadow-lg p-5 rounded bg-white product-card-detail" style="max-width: 1000px; margin: auto;">
                
                <!-- Imagen del producto -->
                <div class="col-md-6 text-center">
                    <img 
                        class="img-fluid rounded-4 shadow-sm border border-light" 
                        src="{{ asset('uploads/productos/' . $producto->imagen) }}" 
                        alt="{{ $producto->nombre }}"
                        style="max-height: 650px; width: 100%; object-fit: cover;"
                    />
                </div>

                <!-- Información del producto -->
                <div class="col-md-6 mt-4 mt-md-0">
                    <div class="small text-muted mb-2">
                        <i class="bi bi-upc-scan"></i> Código: <strong>{{ $producto->codigo }}</strong>
                    </div>

                    <h1 class="display-5 fw-bold text-primary mb-3">{{ $producto->nombre }}</h1>

                    <div class="fs-3 fw-semibold mb-4 text-success">
                        <i class="bi bi-currency-dollar"></i>{{ number_format($producto->precio, 2) }}
                    </div>

                    @if(($producto->cantidad_almacen ?? 0) > 0)
                        <div class="small mb-4">
                            <i class="bi bi-box-seam"></i> 
                            <strong>Cantidad disponible:</strong> 
                            <span class="text-dark">{{ $producto->cantidad_almacen }}</span>
                        </div>
                    @else
                        <div class="small mb-4 text-danger">
                            <i class="bi bi-x-circle"></i> 
                            <strong>Producto agotado</strong>
                        </div>
                    @endif

                    <p class="lead text-secondary mb-4">{{ $producto->descripcion }}</p>

                    @if (session('mensaje'))
                        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                            <i class="bi bi-check-circle-fill"></i> {{ session('mensaje') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                        </div>
                    @endif

                    <hr class="my-4">

                    <div class="mb-4">
                        <label class="fw-semibold mb-3 d-block">Cantidad:</label>
                        <input type="hidden" name="producto_id" value="{{ $producto->id }}">

                        <style>
                            /* Tamaños reducidos para mejor ajuste en la tarjeta */
                            .qty-control { display:flex; align-items:center; gap:8px; }
                            .qty-btn { width:40px; height:40px; display:flex; align-items:center; justify-content:center; font-size:1.05rem; padding:0; }
                            .qty-box { width:90px; height:40px; display:flex; align-items:center; justify-content:center; border-radius:8px; background:#f8fbff; padding:4px; }
                            .qty-input { border:0; background:transparent; text-align:center; font-size:1.05rem; font-weight:700; width:60px; }
                            .qty-input:focus { outline: none; box-shadow:none; }
                            .qty-pulse { animation: qtyPulse .32s ease; }
                            @keyframes qtyPulse { 0% { transform: scale(1); } 30% { transform: scale(1.04); } 100% { transform: scale(1); } }
                        </style>

                        @php
                            // Límite máximo por pedido: 100 o lo que haya en almacen (si existe)
                            $stock = $producto->cantidad_almacen ?? 0;
                            $maxOrder = $stock > 0 ? min($stock, 100) : 100;
                        @endphp

                        <div class="qty-control">
                            <button type="button" class="btn btn-outline-primary qty-btn" id="qtyDecrease" aria-label="Disminuir cantidad">
                                <i class="bi bi-dash-lg"></i>
                            </button>

                            <div class="qty-box">
                                <input type="number"
                                       id="selectedQuantity"
                                       name="cantidad"
                                       class="qty-input"
                                       min="1"
                                       max="{{ $maxOrder }}"
                                       value="1">
                            </div>

                            <button type="button" class="btn btn-outline-primary qty-btn" id="qtyIncrease" aria-label="Aumentar cantidad">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                        </div>

                        <small class="form-text text-muted mt-2">Pedido máximo por orden: <strong>{{ $maxOrder }}</strong></small>
                    </div>

                    {{-- Selector de talla para productos en la categoría Ropa --}}
                    @if(isset($producto->categoria) && strtolower(trim($producto->categoria)) === 'ropa')
                        <div class="mb-4">
                            <label class="fw-semibold mb-2 d-block">Talla:</label>
                            <select name="talla" id="selectedSize" class="form-select" style="max-width:200px;">
                                <option value="">-- Elige una talla --</option>
                                <option value="XS">XS</option>
                                <option value="S">S</option>
                                <option value="M">M</option>
                                <option value="L">L</option>
                                <option value="XL">XL</option>
                                <option value="XXL">XXL</option>
                            </select>
                            <small class="form-text text-muted mt-2">Selecciona la talla antes de añadir al carrito.</small>
                        </div>
                    @endif

                    <div class="d-flex align-items-center flex-wrap gap-3">
                        <button class="btn btn-primary btn-lg" type="submit" id="addToCartBtn">
                            <i class="bi bi-cart-plus-fill me-1"></i>
                            Agregar al carrito
                        </button>

                        <a class="btn btn-outline-secondary" href="javascript:history.back()">
                            <i class="bi bi-arrow-left-circle me-1"></i> Regresar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</form>

{{-- Productos relacionados (misma categoría) --}}
@php
    $related = [];
    if(!empty($producto->categoria)){
        $related = \Illuminate\Support\Facades\DB::table('productos')
            ->where('categoria', $producto->categoria)
            ->where('id', '<>', $producto->id)
            ->inRandomOrder()
            ->limit(8)
            ->get();
    }
@endphp

@if(!empty($related) && count($related))
    <style>
        /* Resaltar tarjetas relacionadas al pasar el cursor */
        .producto-card { transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease; border-radius:12px; overflow:hidden; }
        .producto-card:hover { transform: translateY(-6px) scale(1.01); box-shadow: 0 18px 40px rgba(2,6,23,0.12); border:1px solid rgba(11,94,215,0.12); }
        .producto-img { transition: transform .28s ease; }
        .producto-card:hover .producto-img { transform: scale(1.06); }

        /* Botón celeste coherente con la tienda (más grande y rectangular) */
        .btn-celeste { background: #0b5ed7; color: #fff; border: none; padding: .48rem .9rem; border-radius: 8px; font-weight:600; display:inline-block; min-width:110px; }
        .btn-celeste:hover, .btn-celeste:focus { background: #0a58ca; color: #fff; transform: translateY(-1px); }

        /* Ajustes móviles */
        @media (max-width: 576px) {
            .producto-card { margin-bottom: .75rem; }
            .producto-img { height: 160px !important; }
        }
    </style>

    <section class="py-5">
        <div class="container px-4 px-lg-5">
            <h3 class="mb-4 fw-bold">Productos relacionados</h3>
            <div class="row gx-4 gx-lg-5 row-cols-1 row-cols-md-2 row-cols-xl-4">
                @foreach($related as $p)
                    <div class="col mb-4">
                        <div class="card h-100 border-0 shadow-sm producto-card">
                            <a href="{{ url('/producto/'.$p->id) }}" class="text-decoration-none">
                                <img class="card-img-top producto-img" src="{{ asset('uploads/productos/' . ($p->imagen ?: 'placeholder.png')) }}" alt="{{ $p->nombre }}" style="height:220px; object-fit:cover; width:100%">
                            </a>
                            <div class="card-body p-3 text-start">
                                <h6 class="fw-bold mb-1">{{ Str::limit($p->nombre, 60) }}</h6>
                                <p class="text-primary fw-semibold mb-0">$ {{ number_format($p->precio,2) }}</p>
                            </div>
                            <div class="card-footer bg-transparent border-0 text-center pb-3">
                                <a href="{{ url('/producto/'.$p->id) }}" class="btn btn-celeste">Ver producto</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('web.tienda') }}" class="btn btn-primary btn-lg">Ver más</a>
            </div>
        </div>
    </section>
@endif

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const decreaseBtn = document.getElementById('qtyDecrease');
        const increaseBtn = document.getElementById('qtyIncrease');
        const quantityInput = document.getElementById('selectedQuantity');
        const addToCartBtn = document.getElementById('addToCartBtn');
        // maxAvailable ahora es el máximo por pedido (min(stock,100)) y se respeta también el stock
        const maxAvailable = {{ $maxOrder }};

        // Normaliza y retorna número seguro
        const normalize = (val) => {
            let n = parseInt(val) || 1;
            if (n < 1) n = 1;
            if (maxAvailable > 0 && n > maxAvailable) n = maxAvailable;
            return n;
        };

        const pulseInput = () => {
            quantityInput.classList.add('qty-pulse');
            setTimeout(() => quantityInput.classList.remove('qty-pulse'), 360);
        };

        const updateButtonsState = () => {
            const val = normalize(quantityInput.value);
            if (val <= 1) {
                decreaseBtn.setAttribute('disabled', 'disabled');
            } else {
                decreaseBtn.removeAttribute('disabled');
            }
            if (maxAvailable > 0 && val >= maxAvailable) {
                increaseBtn.setAttribute('disabled', 'disabled');
            } else {
                increaseBtn.removeAttribute('disabled');
            }
        };

        // Inicializar estado
        quantityInput.value = normalize(quantityInput.value);
        updateButtonsState();

        decreaseBtn.addEventListener('click', () => {
            quantityInput.value = normalize(parseInt(quantityInput.value) - 1);
            pulseInput();
            updateButtonsState();
        });

        increaseBtn.addEventListener('click', () => {
            quantityInput.value = normalize(parseInt(quantityInput.value) + 1);
            pulseInput();
            updateButtonsState();
        });

        quantityInput.addEventListener('input', () => {
            quantityInput.value = normalize(quantityInput.value);
            pulseInput();
            updateButtonsState();
        });

        // Validar stock y talla (si aplica) antes de enviar
        addToCartBtn.addEventListener('click', (e) => {
            const quantity = normalize(quantityInput.value);

            if (maxAvailable > 0 && quantity > maxAvailable) {
                e.preventDefault();
                alert('La cantidad seleccionada excede el stock/limite permitido (' + maxAvailable + ' unidades)');
                return false;
            }

            if (maxAvailable <= 0) {
                e.preventDefault();
                alert('Este producto no tiene stock disponible');
                return false;
            }

            // Validar talla si el producto es Ropa
            @if(isset($producto->categoria) && strtolower(trim($producto->categoria)) === 'ropa')
                const sizeSelect = document.getElementById('selectedSize');
                if(!sizeSelect || !sizeSelect.value) {
                    e.preventDefault();
                    alert('Por favor selecciona la talla antes de agregar al carrito.');
                    sizeSelect && sizeSelect.focus();
                    return false;
                }
            @endif
        });
    });
</script>

@endsection
