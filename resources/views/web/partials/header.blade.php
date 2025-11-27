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
                            <i class="bi miclase"></i> Ver producto
                        </a>
                    </div>
                </div>
            </div>
            @endforeach

            <!-- Slide promocional: tarjeta que invita a iniciar venta -->
            <div class="carousel-item miclase">
                <div class="container px-4 px-lg-5 d-flex flex-column flex-md-row align-items-center justify-content-center text-center text-md-start miclase">

                    <!-- Imagen ilustrativa (placeholder) -->
                    <div class="col-md-6 mb-4 mb-md-0 d-flex justify-content-center miclase me-md-5">
                        <div class="d-flex align-items-center justify-content-center bg-white rounded shadow-lg miclase" style="width:100%; max-width:520px; height:350px;">
                            <div class="text-center">
                                <i class="bi bi-shop-window" style="font-size:72px; color:#0d6efd;"></i>
                                <p class="mt-3 text-muted">Empieza a vender hoy</p>
                            </div>
                        </div>
                    </div>

                    <!-- Información promocional -->
                    <div class="col-md-6 text-dark miclase d-flex flex-column align-items-center align-items-md-start text-center text-md-start">
                        <h2 class="fw-bold mb-3 miclase">¿Tienes ideas para tu negocio?</h2>
                        <p class="text-muted mb-4 miclase">¿Qué esperas para empezar a vender con nosotros? Crea tu tienda y llega a nuevos clientes rápidamente.</p>
                        <div class="d-flex justify-content-center w-100">
                          <a href="{{ route('registro') }}" class="btn btn-primary rounded-pill px-4 py-2 miclase mx-auto">
                              <i class="bi miclase"></i> INICIAR
                          </a>
                        </div>
                    </div>
                </div>
            </div>

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
