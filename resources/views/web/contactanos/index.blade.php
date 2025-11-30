@extends('web.app')

@section('titulo', 'Contáctanos - StartPlace')

@section('contenido')
<section class="py-5 bg-white">
  <div class="container">
    <div class="row">
      <div class="col-12 mb-4">
        <h1 class="h3">Contacto — StartPlace</h1>
        <p class="text-muted">¿Necesitas soporte con un pedido, contactar a un vendedor o pedir una cotización? Usa este formulario y te pondremos en contacto con la persona indicada.</p>
      </div>

      <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
          <div class="card-body">
            <form action="{{ route('contactanos.send') }}" method="POST" enctype="multipart/form-data">
              @csrf

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="tipo" class="form-label">Tipo de consulta <span class="text-danger">*</span></label>
                  <select id="tipo" name="tipo" class="form-select @error('tipo') is-invalid @enderror" required>
                    <option value="">Selecciona...</option>
                    <option value="soporte" {{ old('tipo')=='soporte' ? 'selected' : '' }}>Soporte / Reclamo</option>
                    <option value="venta" {{ old('tipo')=='venta' ? 'selected' : '' }}>Consulta de venta</option>
                    <option value="cotizacion" {{ old('tipo')=='cotizacion' ? 'selected' : '' }}>Solicitud de cotización</option>
                    <option value="otro" {{ old('tipo')=='otro' ? 'selected' : '' }}>Otro</option>
                  </select>
                  @error('tipo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6 mb-3">
                  <label for="vendedor" class="form-label">Vendedor (opcional)</label>
                  <select id="vendedor" name="vendedor" class="form-select @error('vendedor') is-invalid @enderror">
                    <option value="">— Todos los vendedores —</option>
                    <option value="vendedor-1" {{ old('vendedor')=='vendedor-1' ? 'selected' : '' }}>Tienda A</option>
                    <option value="vendedor-2" {{ old('vendedor')=='vendedor-2' ? 'selected' : '' }}>Tienda B</option>
                    <option value="vendedor-3" {{ old('vendedor')=='vendedor-3' ? 'selected' : '' }}>Tienda C</option>
                  </select>
                  @error('vendedor') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                  <input id="nombre" name="nombre" type="text" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required>
                  @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6 mb-3">
                  <label for="email" class="form-label">Correo <span class="text-danger">*</span></label>
                  <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                  @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="telefono" class="form-label">Teléfono</label>
                  <input id="telefono" name="telefono" type="text" class="form-control @error('telefono') is-invalid @enderror" value="{{ old('telefono') }}">
                  @error('telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6 mb-3">
                  <label for="pedido_id" class="form-label">ID de pedido (si aplica)</label>
                  <input id="pedido_id" name="pedido_id" type="text" class="form-control @error('pedido_id') is-invalid @enderror" value="{{ old('pedido_id') }}">
                  @error('pedido_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
              </div>

              <div class="mb-3">
                <label for="producto" class="form-label">Producto / Detalle</label>
                <input id="producto" name="producto" type="text" class="form-control @error('producto') is-invalid @enderror" value="{{ old('producto') }}">
                @error('producto') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="mb-3">
                <label for="mensaje" class="form-label">Mensaje <span class="text-danger">*</span></label>
                <textarea id="mensaje" name="mensaje" rows="5" class="form-control @error('mensaje') is-invalid @enderror" required>{{ old('mensaje') }}</textarea>
                @error('mensaje') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="mb-3">
                <label for="adjunto" class="form-label">Adjuntar archivo (foto factura / captura) — opcional</label>
                <input id="adjunto" name="adjunto" type="file" class="form-control @error('adjunto') is-invalid @enderror" accept="image/*,application/pdf">
                @error('adjunto') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="d-flex justify-content-between align-items-center mt-4">
                <div>
                  <input type="checkbox" id="newsletter" name="newsletter" class="form-check-input" {{ old('newsletter') ? 'checked' : '' }}>
                  <label for="newsletter" class="form-check-label ms-2">Deseo recibir novedades</label>
                </div>

                <button type="submit" class="btn btn-success">Enviar consulta</button>
              </div>
            </form>
          </div>
        </div>

        <div class="text-muted small">Los campos con <span class="text-danger">*</span> son obligatorios.</div>
      </div>

      <aside class="col-lg-4">
        <div class="card mb-3 shadow-sm">
          <div class="card-body">
            <h5 class="mb-2">Soporte rápido</h5>
            <p class="mb-1"><strong>Teléfono:</strong> <a href="tel:+571234567890">(+57) 1 234 5678</a></p>
            <p class="mb-1"><strong>Correo:</strong> <a href="mailto:contacto@startplace.com">contacto@startplace.com</a></p>
            <hr>
            <h6 class="mb-2">Estado de pedidos</h6>
            <p class="mb-0">Para consultas de seguimiento, incluye tu ID de pedido para acelerar la respuesta.</p>
          </div>
        </div>

        <div class="card shadow-sm">
          <div class="card-body">
            <h6 class="mb-2">Preguntas frecuentes</h6>
            <ul class="list-unstyled small mb-0">
              <li><strong>¿Cuánto tarda la respuesta?</strong> Normalmente 24-48 horas hábiles.</li>
              <li><strong>¿Puedo devolver un producto?</strong> Consulta la política de devoluciones del vendedor.</li>
            </ul>
          </div>
        </div>
      </aside>
    </div>
  </div>
</section>
@endsection
