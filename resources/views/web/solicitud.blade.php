@extends('web.app')

@section('contenido')
<section class="py-5">
  <div class="container px-4 px-lg-6">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="card p-4">
          <h2 class="mb-3">Enviar solicitud de emprendimiento</h2>
          <p class="small text-muted">Cuéntanos tu idea y tus datos. Nuestro equipo la revisará y te contactaremos.</p>

          @if(session('mensaje'))
            <div class="alert alert-success">{{ session('mensaje') }}</div>
          @endif
          @if ($errors->any())
            <div class="alert alert-danger">
              <ul class="mb-0">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form action="{{ route('solicitudes.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
              <label for="nombre" class="form-label">Nombre completo</label>
              <input name="nombre" id="nombre" class="form-control" value="{{ auth()->user()->name ?? old('nombre') }}" required>
            </div>

            <div class="mb-3">
              <label for="email" class="form-label">Correo electrónico</label>
              <input type="email" name="email" id="email" class="form-control" value="{{ auth()->user()->email ?? old('email') }}" required>
            </div>

            <div class="mb-3">
              <label for="telefono" class="form-label">Teléfono (opcional)</label>
              <input name="telefono" id="telefono" class="form-control" value="{{ old('telefono') }}">
            </div>

            <div class="mb-3">
              <label for="titulo" class="form-label">Título / Nombre del proyecto (opcional)</label>
              <input name="titulo" id="titulo" class="form-control" value="{{ old('titulo') }}">
            </div>

            <div class="mb-3">
              <label for="idea" class="form-label">Describe tu idea (qué vendes, público, diferencial)</label>
              <textarea name="idea" id="idea" rows="6" class="form-control" required>{{ old('idea') }}</textarea>
            </div>

            <div class="mb-3">
              <label for="detalle" class="form-label">Detalles adicionales (opcional)</label>
              <textarea name="detalle" id="detalle" rows="3" class="form-control">{{ old('detalle') }}</textarea>
            </div>

            <div class="mb-3">
              <label for="producto_img" class="form-label">Imagen representativa del producto (obligatorio)</label>
              <input type="file" name="producto_img" id="producto_img" accept="image/*" class="form-control @error('producto_img') is-invalid @enderror" required>
              <small class="form-text text-muted">JPG, PNG. Máx. recomendado 2MB.</small>
              @error('producto_img')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
              <div class="mt-2">
                <img id="previewImage" src="#" alt="Vista previa" style="max-width:220px; max-height:160px; display:none; object-fit:cover; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.08);">
              </div>
            </div>

            <div class="mb-3">
              <label for="carta" class="form-label">Carta de intención / por qué iniciar con nosotros (PDF o DOC) <span class="text-muted small">(obligatorio)</span></label>
              <input type="file" name="carta" id="carta" accept=".pdf,.doc,.docx" class="form-control @error('carta') is-invalid @enderror" required>
              @error('carta')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
              <small id="cartaName" class="form-text text-muted">No se ha seleccionado ningún archivo.</small>
            </div>

            <div class="d-flex gap-2">
              <button class="btn btn-primary">Enviar solicitud</button>
              <a class="btn btn-outline-secondary" href="{{ route('web.equipo') }}">Volver</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@section('scripts')
<script>
  (function(){
    // Preview de imagen seleccionada
    var inputImg = document.getElementById('producto_img');
    var preview = document.getElementById('previewImage');
    if(inputImg){
      inputImg.addEventListener('change', function(e){
        var file = e.target.files && e.target.files[0];
        if(file && file.type.startsWith('image/')){
          var reader = new FileReader();
          reader.onload = function(ev){
            preview.src = ev.target.result;
            preview.style.display = 'block';
          };
          reader.readAsDataURL(file);
        } else {
          preview.style.display = 'none';
          preview.src = '#';
        }
      });
    }

    // Mostrar nombre del archivo de la carta
    var cartaInput = document.getElementById('carta');
    var cartaName = document.getElementById('cartaName');
    if(cartaInput && cartaName){
      cartaInput.addEventListener('change', function(e){
        var file = e.target.files && e.target.files[0];
        cartaName.textContent = file ? (file.name + ' (' + Math.round(file.size/1024) + ' KB)') : 'No se ha seleccionado ningún archivo.';
      });
    }
  })();
</script>
@endsection
