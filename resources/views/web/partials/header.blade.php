<header class="hero-section py-5 miclase" style="background: linear-gradient(180deg, #cbe7f5 0%, #e8f4fa 100%);">
    <div id="heroCarousel" class="carousel slide miclase" data-bs-ride="carousel" data-bs-interval="5000">
        <div class="carousel-inner miclase">

            @foreach($productos->take(3) as $index => $producto)
            <div class="carousel-item {{ $index == 0 ? 'active' : '' }} miclase">
                <div class="container px-4 px-lg-5 d-flex flex-column flex-md-row align-items-center justify-content-center text-center text-md-start miclase">
                    
                    <!-- Imagen del producto -->
                    <div class="col-md-6 mb-4 mb-md-0 d-flex justify-content-center miclase">
                        <img src="{{ asset('uploads/productos/' . $producto->imagen) }}"
                             alt="{{ $producto->nombre }}"
                             class="img-fluid rounded shadow-lg miclase"
                             style="max-height: 350px; object-fit: cover;">
                    </div>

                    <!-- Información -->
                    <div class="col-md-6 text-dark miclase">
                        <h2 class="fw-bold mb-3 miclase">{{ $producto->nombre }}</h2>
                        <p class="text-muted mb-4 miclase">{{ Str::limit($producto->descripcion, 120, '...') }}</p>
                        <p class="fs-4 fw-semibold text-primary mb-4 miclase">$ {{ number_format($producto->precio, 2) }}</p>
                        <a href="{{ route('web.show', $producto->id) }}" class="btn btn-primary rounded-pill px-4 py-2 miclase">
                            <i class="bi bi-eye miclase"></i> Ver producto
                        </a>
                    </div>
                </div>
            </div>
            @endforeach

        </div>

        <!-- Controles -->
        <button class="carousel-control-prev miclase" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon miclase" aria-hidden="true" style="filter: invert(1);"></span>
            <span class="visually-hidden miclase">Anterior</span>
        </button>
        <button class="carousel-control-next miclase" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon miclase" aria-hidden="true" style="filter: invert(1);"></span>
            <span class="visually-hidden miclase">Siguiente</span>
        </button>
    </div>
</header>
