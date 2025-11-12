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

                    @if ($producto->stock > 0)
                        <div class="small mb-4">
                            <i class="bi bi-box-seam"></i> 
                            <strong>Stock disponible:</strong> 
                            <span class="text-dark">{{ $producto->stock }}</span>
                        </div>
                    @else
                        <div class="small mb-4 text-danger">
                            <i class="bi bi-x-circle"></i> 
                            <strong>Producto agotado</strong><br>
                            @if ($producto->fecha_reposicion)
                                <span class="text-secondary">
                                    Disponible nuevamente el 
                                    <strong>{{ \Carbon\Carbon::parse($producto->fecha_reposicion)->format('d/m/Y') }}</strong>
                                </span>
                            @else
                                <span class="text-secondary">Fecha de reposición próximamente</span>
                            @endif
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

                    <div class="d-flex align-items-center">
                        <input type="hidden" name="producto_id" value="{{ $producto->id }}">  

                        <label for="inputQuantity" class="me-2 fw-semibold">Cantidad:</label>
                        <input 
                            class="form-control text-center me-3" 
                            id="inputQuantity" 
                            type="number" 
                            name="cantidad" 
                            min="1" 
                            value="1"
                            style="width: 60px; border-radius: 6px;"
                        />

                        <button class="btn btn-sm btn-primary me-2" type="submit">
                            <i class="bi bi-cart-plus-fill me-1"></i>
                            Agregar
                        </button>

                        <a class="btn btn-sm btn-outline-secondary" href="javascript:history.back()">
                            <i class="bi bi-arrow-left-circle me-1"></i> Regresar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</form>

<!-- JS opcional para mejorar input de cantidad -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const qtyInput = document.getElementById('inputQuantity');
        qtyInput.addEventListener('input', () => {
            if (qtyInput.value < 1) qtyInput.value = 1;
        });
    });
</script>
@endsection
