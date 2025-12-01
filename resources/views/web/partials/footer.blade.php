<footer class="bg-dark text-light pt-4 mt-5">
  <div class="container">
    <div class="row">
      <!-- Columna 1 -->
      <div class="col-md-4 mb-3 d-flex align-items-center">
        <div>
          <a href="/">
            StartPlace
          </a>
          <p class="small mt-2">
            Tu tienda en línea de confianza. Ofrecemos productos de calidad y un servicio rápido y seguro.
          </p>
        </div>
      </div>

      <!-- Columna 2 -->
      <div class="col-md-4 mb-3">
        <h5>Enlaces útiles</h5>
        <ul class="list-unstyled">
          <li><a href="/" class="text-light text-decoration-none">Inicio</a></li>
          <li><a href="{{ route('web.equipo') }}" class="text-light text-decoration-none">Acerca de</a></li>
          <li><a href="#" class="text-light text-decoration-none">Tienda</a></li>
          <li><a href="#" class="text-light text-decoration-none">Contacto</a></li>
        </ul>
      </div>

      <!-- Columna 3 -->
      <div class="col-md-4 mb-3">
        <h5>Contáctanos</h5>
        <ul class="list-unstyled small">
          <li><i class="bi bi-geo-alt-fill"></i> Colombia, Envio Nacional</li>
          <li><i class="bi bi-telephone-fill"></i> +57 322 295 1853</li>
          <li><i class="bi bi-envelope-fill"></i> {{ config('mail.from.address', 'soporte@empresa.com') }}</li>
        </ul>
        <div class="mt-3">
          <a href="#" class="text-light me-3"><i class="bi bi-facebook"></i></a>
          <a href="#" class="text-light me-3"><i class="bi bi-instagram"></i></a>
          <a href="#" class="text-light"><i class="bi bi-twitter-x"></i></a>
        </div>
      </div>
    </div>

    <hr class="border-light">

    <div class="text-center pb-3 small">
      © {{ config('app.name', 'Nuestra Empresa') }} — Todos los derechos reservados.
    </div>
  </div>
</footer>
