@extends('web.app')

@section('contenido')
<!-- Detalle del producto -->
<form action="{{ route('carrito.agregar') }}" method="POST" class="d-flex justify-content-center">
    @csrf
    <section class="py-5" >
        <div class="container px-4 px-lg-5 my-5">
            <div class="row gx-5 align-items-center shadow-lg p-5 rounded bg-white" style="max-width: 1000px; margin: auto;">
                
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
                        <input type="hidden" name="cantidad" id="selectedQuantity" value="1">
                        
                        <!-- Selector de cantidad tipo bloque -->
                        <div class="d-flex flex-wrap gap-2" id="quantitySelector">
                            @php
                                $maxAvailable = $producto->cantidad_almacen ?? 0;
                                $maxButtons = $maxAvailable > 0 ? min($maxAvailable, 10) : 10;
                            @endphp
                            
                            @for($i = 1; $i <= $maxButtons; $i++)
                                <button 
                                    type="button" 
                                    class="btn quantity-btn {{ $i === 1 ? 'btn-primary' : 'btn-outline-primary' }}"
                                    data-quantity="{{ $i }}"
                                    style="min-width: 50px;"
                                >
                                    {{ $i }}
                                </button>
                            @endfor
                            
                            @if($maxAvailable > 10)
                                <button 
                                    type="button" 
                                    class="btn btn-outline-secondary quantity-btn-custom"
                                    data-bs-toggle="modal"
                                    data-bs-target="#customQuantityModal"
                                >
                                    +10
                                </button>
                            @endif
                        </div>
                    </div>

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

<!-- Modal para cantidad personalizada -->
@if(($producto->cantidad_almacen ?? 0) > 10)
<div class="modal fade" id="customQuantityModal" tabindex="-1" aria-labelledby="customQuantityModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customQuantityModalLabel">Cantidad personalizada</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label for="customQuantityInput" class="form-label">Ingrese la cantidad (máximo: {{ $producto->cantidad_almacen }})</label>
                <input 
                    type="number" 
                    class="form-control" 
                    id="customQuantityInput" 
                    min="1" 
                    max="{{ $producto->cantidad_almacen }}" 
                    value="1"
                >
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmCustomQuantity">Confirmar</button>
            </div>
        </div>
    </div>
</div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const quantityButtons = document.querySelectorAll('.quantity-btn');
        const selectedQuantityInput = document.getElementById('selectedQuantity');
        const addToCartBtn = document.getElementById('addToCartBtn');
        const maxAvailable = {{ $producto->cantidad_almacen ?? 0 }};
        
        // Manejar clic en botones de cantidad
        quantityButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                const quantity = parseInt(btn.getAttribute('data-quantity'));
                
                // Remover clase activa de todos los botones
                quantityButtons.forEach(b => {
                    b.classList.remove('btn-primary');
                    b.classList.add('btn-outline-primary');
                });
                
                // Agregar clase activa al botón seleccionado
                btn.classList.remove('btn-outline-primary');
                btn.classList.add('btn-primary');
                
                // Actualizar el input hidden
                selectedQuantityInput.value = quantity;
            });
        });
        
        // Manejar cantidad personalizada
        @if(($producto->cantidad_almacen ?? 0) > 10)
        const customQuantityInput = document.getElementById('customQuantityInput');
        const confirmCustomBtn = document.getElementById('confirmCustomQuantity');
        const customQuantityModal = new bootstrap.Modal(document.getElementById('customQuantityModal'));
        
        confirmCustomBtn.addEventListener('click', () => {
            let customQty = parseInt(customQuantityInput.value) || 1;
            
            if (customQty < 1) customQty = 1;
            if (customQty > maxAvailable) customQty = maxAvailable;
            
            selectedQuantityInput.value = customQty;
            
            // Remover selección de botones
            quantityButtons.forEach(b => {
                b.classList.remove('btn-primary');
                b.classList.add('btn-outline-primary');
            });
            
            customQuantityModal.hide();
        });
        @endif
        
        // Validar stock antes de enviar
        addToCartBtn.addEventListener('click', (e) => {
            const quantity = parseInt(selectedQuantityInput.value) || 1;
            
            if (maxAvailable > 0 && quantity > maxAvailable) {
                e.preventDefault();
                alert('La cantidad seleccionada excede el stock disponible (' + maxAvailable + ' unidades)');
                return false;
            }
            
            if (maxAvailable <= 0) {
                e.preventDefault();
                alert('Este producto no tiene stock disponible');
                return false;
            }
        });
    });
</script>

@endsection
