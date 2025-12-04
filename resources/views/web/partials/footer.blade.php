<footer class="site-footer border-top mt-5">
  <style>
    .site-footer{color:#0f1724; background: linear-gradient(180deg, #f7fcff 0%, #f3fbff 100%);}
    .site-footer.border-top{border-top:1px solid rgba(11,99,214,0.06)}
    .site-footer .footer-brand{font-weight:800;color:#06283d;font-size:1.25rem;text-decoration:none}
    .site-footer .footer-note{color:#475569;font-size:0.95rem}
    .site-footer .footer-links a{color:#0f1724;text-decoration:none}
    .site-footer .footer-links a:hover{color:#0b63d6}
    .site-footer .social-link{display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:8px;background:#f4fbff;color:#0b63d6;border:1px solid rgba(11,99,214,0.06)}
    .site-footer .social-link:hover{transform:translateY(-2px)}
    .site-footer .newsletter-input{border-radius:8px 0 0 8px;border:1px solid rgba(11,99,214,0.08);padding:.5rem .75rem;flex:1;min-width:0}
    .site-footer .newsletter-input:focus{box-shadow:0 6px 18px rgba(63,195,255,0.08);border-color:rgba(63,195,255,0.22)}
    .site-footer .newsletter-btn{border-radius:0 8px 8px 0;background:linear-gradient(90deg,#9fe9ff,#3fc3ff);border:0;color:#04223a;padding:.45rem .9rem;display:inline-flex;align-items:center;gap:.5rem;font-weight:700;box-shadow:0 8px 20px rgba(63,195,255,0.08);transition:transform .12s ease,box-shadow .12s ease}
    .site-footer .newsletter-btn i{font-size:1rem}
    .site-footer .newsletter-btn:hover{transform:translateY(-2px);box-shadow:0 12px 28px rgba(63,195,255,0.12)}
    .site-footer .newsletter-btn:active{transform:translateY(0)}
    .site-footer .small-muted{color:#6b7280}
    @media (max-width:767px){.site-footer .social-col{margin-top:12px}}
    /* Newsletter responsive: ensure input is visible and button doesn't overlap */
    .site-footer .newsletter-row{display:flex;gap:.5rem;align-items:center}
    .site-footer .newsletter-row .newsletter-input{flex:1;min-width:0}
    .site-footer .newsletter-row .newsletter-btn{white-space:nowrap}
    @media (max-width:576px){
      .site-footer .newsletter-row{flex-direction:column;align-items:stretch}
      .site-footer .newsletter-row .newsletter-input{border-radius:8px 8px 0 0}
      .site-footer .newsletter-row .newsletter-btn{border-radius:0 0 8px 8px;margin-top:8px;width:100%}
    }
  </style>

  <div class="container py-5">
    <div class="row gy-4">
      <div class="col-md-4">
        <a href="{{ route('web.index') }}" class="footer-brand d-inline-block mb-2">
          <img src="{{ asset('images/Logo.png') }}" alt="StartPlace" style="height:40px; vertical-align:middle;"> <span class="ms-2">StartPlace</span>
        </a>
        <p class="footer-note">StartPlace conecta compradores con vendedores locales. Ofrecemos envío seguro, atención rápida y opciones de pago confiables.</p>
        <p class="small-muted small mt-2">© {{ date('Y') }} {{ config('app.name', 'StartPlace') }}. Todos los derechos reservados.</p>
      </div>

      <div class="col-md-2 footer-links">
        <h6 class="mb-3">Enlaces</h6>
        <ul class="list-unstyled">
          <li><a href="{{ route('web.index') }}">Inicio</a></li>
          <li><a href="{{ route('web.tienda') }}">Tienda</a></li>
          <li><a href="{{ route('web.equipo') }}">Acerca</a></li>
          <li><a href="{{ route('web.contactanos') }}">Contáctanos</a></li>
        </ul>
      </div>

      <div class="col-md-3">
        <h6 class="mb-3">Contáctanos</h6>
        <p class="small mb-1"><i class="bi bi-geo-alt-fill me-2 text-muted"></i>Colombia — Envío nacional</p>
        <p class="small mb-1"><i class="bi bi-telephone-fill me-2 text-muted"></i>+57 322 295 1853</p>
        <p class="small"><i class="bi bi-envelope-fill me-2 text-muted"></i>{{ config('mail.from.address', 'startplace.com@gmail.com') }}</p>

        <div class="d-flex align-items-center mt-3 social-col">
          <a href="#" class="social-link me-2"><i class="bi bi-facebook" aria-hidden="true"></i></a>
          <a href="#" class="social-link me-2"><i class="bi bi-instagram" aria-hidden="true"></i></a>
          <a href="#" class="social-link"><i class="bi bi-twitter" aria-hidden="true"></i></a>
        </div>
      </div>

      <div class="col-md-3">
        <h6 class="mb-3">Boletín</h6>
        <form action="{{ route('contacto.enviar') }}" method="POST">
          @csrf
          <div class="newsletter-row">
            <input type="email" name="email" class="form-control newsletter-input" placeholder="Tu correo" required aria-label="Correo para suscripción">
            <button class="btn newsletter-btn" type="submit" aria-label="Suscribirse al boletín"><i class="bi bi-envelope-check-fill" aria-hidden="true"></i><span class="d-none d-sm-inline"> Suscribirme</span></button>
          </div>
        </form>
        <p class="small-muted small mt-2">Recibe ofertas, novedades y promociones increibles de StartPlace.<br>Puedes darte de baja cuando quieras.</p>
      </div>
    </div>
  </div>
</footer>
