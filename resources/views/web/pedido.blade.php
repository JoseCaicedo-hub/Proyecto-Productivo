@extends('web.app')

@section('contenido')
<!-- Carrito Section -->
<section class="carrito-section">
    <div class="carrito-header">
        <img src="{{ asset('images/Logo.png') }}" alt="StartPlace">
        <h1>Tu Carrito de Compras</h1>
    </div>

    <div class="container px-4 px-lg-5">
        <!-- Alertas -->
        @if (session('mensaje') || session('error') || $errors->any())
        <div class="alerts-container">
            @if (session('mensaje'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('mensaje') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>
        @endif

        <div class="cart-container">
            <!-- Items del Carrito -->
            <div class="cart-items-card">
                @forelse($carrito as $id => $item)
                    @if($loop->first)
                    <div class="cart-items-header">
                        <div class="row">
                            <div><strong>Producto</strong></div>
                            <div style="text-align: center;"><strong>Precio</strong></div>
                            <div style="text-align: center;"><strong>Cantidad</strong></div>
                            <div style="text-align: right;"><strong>Acción</strong></div>
                        </div>
                    </div>
                    <div class="cart-items-body">
                    @endif

                    <!-- Cart Item -->
                    <div class="cart-item" data-item-id="{{ $id }}" data-subtotal="{{ $item['precio'] * $item['cantidad'] }}" data-category="{{ $categoriasPorItem[$id] ?? '' }}">
                        <div class="product-info">
                            <label class="item-select" title="Seleccionar para compra">
                                <input type="checkbox" class="cart-select" value="{{ $id }}" checked>
                                <span class="item-select-dot"></span>
                            </label>
                            <div class="product-image">
                                <img src="{{ asset('uploads/productos/' . $item['imagen']) }}" alt="{{ $item['nombre'] }}">
                            </div>
                            <div class="product-details">
                                <h6>{{ $item['nombre'] }}</h6>
                                <small>{{ $item['codigo'] }}</small>
                                @if(!empty($item['talla']))
                                    <span class="talla-badge">Talla: {{ $item['talla'] }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="price-cell">
                            <span class="fw-bold">@formatCOP($item['precio'])</span>
                        </div>

                        <div class="quantity-control">
                            <a href="{{ route('carrito.restar', ['producto_id' => $id]) }}" class="btn btn-sm js-cart-qty">−</a>
                            <input type="text" value="{{ $item['cantidad'] }}" readonly>
                            <a href="{{ route('carrito.sumar', ['producto_id' => $id]) }}" class="btn btn-sm js-cart-qty">+</a>
                        </div>

                        <div class="subtotal-cell">
                            <a href="{{ route('carrito.eliminar', $id) }}" class="btn-delete js-cart-remove" title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </a>
                        </div>
                    </div>

                    @if($loop->last)
                    </div>
                    @endif

                @empty
                    <div class="empty-cart">
                        <i class="bi bi-bag"></i>
                        <p class="mb-4">Tu carrito está vacío</p>
                        <a href="{{ route('web.tienda') }}" class="btn btn-primary">
                            Explorar artículos
                        </a>
                    </div>
                @endforelse

            </div>

            <!-- Resumen del Pedido -->
            @if(count($carrito) > 0)
            <div class="order-summary">
                <div class="summary-header">
                    <i class="bi bi-receipt me-2"></i>Resumen del Pedido
                </div>
                <div class="summary-body">
                    @php
                        $subtotal = 0;
                        foreach ($carrito as $item) {
                            $subtotal += $item['precio'] * $item['cantidad'];
                        }
                        $envio = 0; // Puedes calcular esto dinámicamente
                        $total = $subtotal + $envio;
                    @endphp

                    <div class="summary-row subtotal-row">
                        <label>Subtotal</label>
                        <span id="subtotalAmount">@formatCOP($subtotal)</span>
                    </div>

                    <div class="summary-row">
                        <label>Envío</label>
                        <span id="shippingAmount">@formatCOP($envio)</span>
                    </div>

                    <div class="summary-row" style="padding: 16px 0; border-bottom: none;">
                        <label style="font-size: 1.1rem;">Total</label>
                        <span class="total" id="orderTotal">@formatCOP($total)</span>
                    </div>

                    <button type="button" id="openCheckoutBtn" class="btn btn-checkout">
                        <i class="bi bi-credit-card me-2"></i>Realizar pedido
                    </button>

                    <button type="button" class="btn btn-majorista" data-bs-toggle="modal" data-bs-target="#solicitarMayoristaModal" style="width: 100%; margin-top: 10px; background-color: #ffc107; color: #000; border: none;">
                        <i class="bi bi-shop me-2"></i>¿Quieres comprar al por mayor?
                    </button>

                    <a href="/" class="btn btn-continue">
                        <i class="bi bi-arrow-left me-2"></i>Continuar comprando
                    </a>
                </div>
            </div>
            @else
            <div class="order-summary order-summary-empty">
                <div class="summary-header">
                    <i class="bi bi-shield-check me-2"></i>Resumen
                </div>
                <div class="summary-body">
                    <div class="summary-row subtotal-row">
                        <label>Costo total</label>
                        <span class="total" id="orderTotalEmpty">$0.00</span>
                    </div>

                    <button type="button" class="btn btn-checkout btn-security-info" data-bs-toggle="modal" data-bs-target="#securityPaymentsModal">
                        Privacidad y seguridad en los pagos
                    </button>

                    <div class="payment-methods mt-3">
                        <div class="payment-methods-title">Métodos de pago</div>
                        <div class="payment-methods-list">
                            <span class="payment-pill">Visa</span>
                            <span class="payment-pill">Mastercard</span>
                            <span class="payment-pill">PSE</span>
                            <span class="payment-pill">Nequi</span>
                            <span class="payment-pill">Daviplata</span>
                        </div>
                    </div>

                    <div class="secure-note mt-3">
                        <i class="bi bi-lock-fill me-1"></i>
                        Tus datos están protegidos.
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</section>

<div id="cartLoadingOverlay" class="cart-loading-overlay d-none" aria-hidden="true">
    <div class="cart-loading-box">
        <div class="spinner-border text-light" role="status" aria-label="Cargando"></div>
        <p id="cartLoadingText" class="mb-0">Eliminando producto...</p>
    </div>
</div>

@if(isset($articulosSimilares) && $articulosSimilares->isNotEmpty())
<section class="similar-section pb-5">
    <div class="container px-4 px-lg-5">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0">Artículos similares</h4>
        </div>

        <div class="row gx-4 gy-4 row-cols-1 row-cols-sm-2 row-cols-lg-4">
            @foreach($articulosSimilares as $producto)
                <div class="col similar-col" data-related-category="{{ \Illuminate\Support\Str::lower(trim((string)($producto->categoria ?? ''))) }}">
                    <div class="card similar-card h-100 border-0 shadow-sm">
                        <a href="{{ route('web.show', $producto->id) }}" class="similar-image-wrap">
                            <img src="{{ asset('uploads/productos/' . $producto->imagen) }}" class="card-img-top" alt="{{ $producto->nombre }}">
                        </a>
                        <div class="card-body text-start">
                            <p class="small text-muted mb-1">{{ $producto->categoria ?? 'Sin categoría' }}</p>
                            <h6 class="mb-2">{{ $producto->nombre }}</h6>
                            <p class="fw-semibold mb-0">@formatCOP($producto->precio)</p>
                        </div>
                        <div class="card-footer bg-transparent border-0 pt-0 pb-3">
                            <a href="{{ route('web.show', $producto->id) }}" class="btn btn-view-article w-100">Ver artículo</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('web.tienda') }}" class="btn btn-continue btn-similar-more">Ver más</a>
        </div>
    </div>
</section>
@endif

<!-- Modal de Envío y Pago -->
<div class="modal fade" id="shippingModal" tabindex="-1" aria-labelledby="shippingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form id="checkoutForm" action="{{ route('pedido.realizar') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="shippingModalLabel">
                        <i class="bi bi-box2-heart me-2"></i>Datos de envío y pago
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="selectedItemsContainer"></div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Dirección de envío</h6>
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-primary"
                            id="btnUseProfileData"
                            data-pais="{{ old('pais', optional(auth()->user())->pais ?? optional(auth()->user())->ciudad ?? '') }}"
                            data-departamento="{{ old('departamento', optional(auth()->user())->departamento ?? '') }}"
                            data-ciudad="{{ old('ciudad', optional(auth()->user())->municipio ?? '') }}"
                            data-direccion="{{ old('direccion', optional(auth()->user())->direccion ?? '') }}"
                            data-telefono="{{ old('telefono', optional(auth()->user())->telefono ?? '') }}"
                        >
                            Usar datos de mi perfil
                        </button>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="pais" class="form-label">País <span class="text-danger">*</span></label>
                            <select name="pais" id="pais" class="form-select @error('pais') is-invalid @enderror" required>
                                <option value="">Selecciona un país</option>
                                @php
                                    $paises = ['Colombia','Argentina','Brasil','Chile','Ecuador','Perú','Venezuela','México','Costa Rica','Panamá','Uruguay','Paraguay','Bolivia','Guatemala','Honduras'];
                                    $paisSeleccionado = old('pais', optional(auth()->user())->pais ?? optional(auth()->user())->ciudad ?? '');
                                @endphp
                                @foreach($paises as $paisOption)
                                    <option value="{{ $paisOption }}" {{ $paisSeleccionado === $paisOption ? 'selected' : '' }}>{{ $paisOption }}</option>
                                @endforeach
                            </select>
                            @error('pais')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                            <small id="paisFeedback" class="text-danger d-none"></small>
                            <small id="paisStatus" class="d-block mt-1 text-muted">Inválido</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="departamento" class="form-label">Departamento / Estado <span class="text-danger">*</span></label>
                            <select name="departamento" id="departamento" class="form-select @error('departamento') is-invalid @enderror" required>
                                <option value="">Selecciona un departamento/estado</option>
                            </select>
                            @error('departamento')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="ciudad" class="form-label">Municipio / Ciudad <span class="text-danger">*</span></label>
                            <select name="ciudad" id="ciudad" class="form-select @error('ciudad') is-invalid @enderror" required>
                                <option value="">Selecciona un municipio/ciudad</option>
                            </select>
                            @error('ciudad')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                            <small id="ciudadFeedback" class="text-danger d-none"></small>
                            <small id="ciudadStatus" class="d-block mt-1 text-muted">Inválido</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="telefono" class="form-label">Teléfono <span class="text-danger">*</span></label>
                            <input type="text" name="telefono" id="telefono" class="form-control @error('telefono') is-invalid @enderror" value="{{ old('telefono', optional(auth()->user())->telefono ?? '') }}" inputmode="numeric" pattern="[0-9]{10,15}" minlength="10" maxlength="15" title="Ingresa solo números (10 a 15 dígitos)" required>
                            @error('telefono')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                            <small id="telefonoFeedback" class="text-danger d-none"></small>
                            <small id="telefonoStatus" class="d-block mt-1 text-muted">Inválido</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="direccion" class="form-label">Dirección completa <span class="text-danger">*</span></label>
                        <textarea name="direccion" id="direccion" class="form-control @error('direccion') is-invalid @enderror" rows="3" required>{{ old('direccion', optional(auth()->user())->direccion ?? '') }}</textarea>
                        @error('direccion')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tipo_documento" class="form-label">Tipo de documento <span class="text-danger">*</span></label>
                            <select name="tipo_documento" id="tipo_documento" class="form-select @error('tipo_documento') is-invalid @enderror" required>
                                <option value="">Seleccione...</option>
                                <option value="CC" {{ old('tipo_documento') == 'CC' ? 'selected' : '' }}>Cédula de ciudadanía (CC)</option>
                                <option value="NIT" {{ old('tipo_documento') == 'NIT' ? 'selected' : '' }}>NIT</option>
                                <option value="CE" {{ old('tipo_documento') == 'CE' ? 'selected' : '' }}>Cédula de extranjería (CE)</option>
                                <option value="PAS" {{ old('tipo_documento') == 'PAS' ? 'selected' : '' }}>Pasaporte</option>
                            </select>
                            @error('tipo_documento')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="numero_documento" class="form-label">Número de documento <span class="text-danger">*</span></label>
                            <input type="text" name="numero_documento" id="numero_documento" class="form-control @error('numero_documento') is-invalid @enderror" value="{{ old('numero_documento') }}" inputmode="numeric" pattern="[0-9]{7,10}" minlength="7" maxlength="10" title="Ingresa solo números (7 a 10 dígitos)" required>
                            @error('numero_documento')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                            <small id="cedulaFeedback" class="text-danger d-none"></small>
                            <small id="cedulaStatus" class="d-block mt-1 text-muted">Inválido</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="metodo_pago" class="form-label">Método de pago <span class="text-danger">*</span></label>
                            <select name="metodo_pago" id="metodo_pago" class="form-select @error('metodo_pago') is-invalid @enderror" required>
                                <option value="">Seleccione...</option>
                                <option value="efectivo" {{ old('metodo_pago') == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                                <option value="tarjeta" {{ old('metodo_pago') == 'tarjeta' ? 'selected' : '' }}>Tarjeta débito/crédito</option>
                                <option value="transferencia" {{ old('metodo_pago') == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                            </select>
                            @error('metodo_pago')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3"></div>
                    </div>

                    <div id="tarjetaFields" class="border rounded p-3 mb-3 d-none">
                        <h6 class="mb-3">Datos de tarjeta</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="card_number" class="form-label">Número de tarjeta</label>
                                <input type="text" name="card_number" id="card_number" class="form-control @error('card_number') is-invalid @enderror" value="{{ old('card_number') }}" inputmode="numeric" minlength="13" maxlength="19" autocomplete="off">
                                @error('card_number')<small class="text-danger">{{ $message }}</small>@enderror
                                <small id="cardNumberFeedback" class="text-danger d-none"></small>
                                <small id="cardNumberStatus" class="d-block mt-1 text-muted">Inválido</small>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="card_expiry" class="form-label">Vencimiento (MM/AA)</label>
                                <input type="text" name="card_expiry" id="card_expiry" class="form-control @error('card_expiry') is-invalid @enderror" value="{{ old('card_expiry') }}" maxlength="5" placeholder="MM/AA" autocomplete="off">
                                @error('card_expiry')<small class="text-danger">{{ $message }}</small>@enderror
                                <small id="cardExpiryFeedback" class="text-danger d-none"></small>
                                <small id="cardExpiryStatus" class="d-block mt-1 text-muted">Inválido</small>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="card_cvv" class="form-label">CVV</label>
                                <input type="text" name="card_cvv" id="card_cvv" class="form-control @error('card_cvv') is-invalid @enderror" value="{{ old('card_cvv') }}" inputmode="numeric" minlength="3" maxlength="4" autocomplete="off">
                                @error('card_cvv')<small class="text-danger">{{ $message }}</small>@enderror
                                <small id="cardCvvFeedback" class="text-danger d-none"></small>
                                <small id="cardCvvStatus" class="d-block mt-1 text-muted">Inválido</small>
                            </div>
                        </div>
                    </div>

                    <div id="transferenciaFields" class="border rounded p-3 mb-3 d-none">
                        <h6 class="mb-3">Datos de transferencia</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="payment_provider" class="form-label">Entidad</label>
                                <select name="payment_provider" id="payment_provider" class="form-select @error('payment_provider') is-invalid @enderror">
                                    <option value="">Seleccione entidad...</option>
                                    <option value="nequi" {{ old('payment_provider') == 'nequi' ? 'selected' : '' }}>Nequi</option>
                                    <option value="bancolombia" {{ old('payment_provider') == 'bancolombia' ? 'selected' : '' }}>Bancolombia</option>
                                    <option value="daviplata" {{ old('payment_provider') == 'daviplata' ? 'selected' : '' }}>Daviplata</option>
                                    <option value="otro" {{ old('payment_provider') == 'otro' ? 'selected' : '' }}>Otro</option>
                                </select>
                                @error('payment_provider')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="account_number" class="form-label">Número de cuenta / billetera</label>
                                <input type="text" name="account_number" id="account_number" class="form-control @error('account_number') is-invalid @enderror" value="{{ old('account_number') }}" inputmode="numeric" maxlength="20" autocomplete="off">
                                @error('account_number')<small class="text-danger">{{ $message }}</small>@enderror
                                <small id="accountNumberFeedback" class="text-danger d-none"></small>
                                <small id="accountNumberStatus" class="d-block mt-1 text-muted">Inválido</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="referencia" class="form-label">Referencia / Instrucciones (Casa, Apto, Residencia)</label>
                        <input type="text" name="referencia" id="referencia" class="form-control" value="{{ old('referencia') }}">
                    </div>

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" value="1" id="guardar_direccion_perfil" name="guardar_direccion_perfil" {{ old('guardar_direccion_perfil') ? 'checked' : '' }}>
                        <label class="form-check-label" for="guardar_direccion_perfil">
                            Guardar esta dirección en mi perfil
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" id="confirmCheckoutBtn" class="btn btn-primary" disabled>
                        <i class="bi bi-lock-fill me-2"></i>Confirmar y pagar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Privacidad y Seguridad en Pagos -->
<div class="modal fade" id="securityPaymentsModal" tabindex="-1" aria-labelledby="securityPaymentsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="securityPaymentsModalLabel">
                    <i class="bi bi-shield-lock-fill me-2"></i>Privacidad y seguridad en los pagos
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">En StartPlace protegemos tu información financiera y personal durante todo el proceso de compra.</p>

                <ul class="list-group list-group-flush">
                    <li class="list-group-item px-0">
                        <i class="bi bi-lock-fill text-success me-2"></i>
                        Conexión cifrada (HTTPS) para proteger tus datos.
                    </li>
                    <li class="list-group-item px-0">
                        <i class="bi bi-credit-card-2-front-fill text-primary me-2"></i>
                        Procesamiento seguro con métodos de pago confiables.
                    </li>
                    <li class="list-group-item px-0">
                        <i class="bi bi-eye-slash-fill text-secondary me-2"></i>
                        No compartimos información sensible con terceros no autorizados.
                    </li>
                    <li class="list-group-item px-0">
                        <i class="bi bi-person-check-fill text-info me-2"></i>
                        Verificación básica para prevenir actividades sospechosas.
                    </li>
                </ul>

                <div class="mt-3 small text-muted">
                    Métodos disponibles: Visa, Mastercard, PSE, Nequi y Daviplata.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Entendido</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="noSelectionModal" tabindex="-1" aria-labelledby="noSelectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="noSelectionModalLabel">
                    <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>Advertencia
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                Por favor selecciona al menos un producto para realizar la compra.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Entendido</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Solicitar Compra al Por Mayor -->
<div class="modal fade" id="solicitarMayoristaModal" tabindex="-1" aria-labelledby="solicitarMayoristaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="solicitarMayoristaLabel">
                    <i class="bi bi-shop me-2"></i>Solicitar Compra al Por Mayor
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            @if(!auth()->check())
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Para solicitar una compra al por mayor, debes iniciar sesión primero.
                </div>
                <a href="{{ route('login') }}" class="btn btn-primary w-100">Iniciar sesión</a>
            </div>
            @else
            <form action="{{ route('mayorista.solicitud.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="empresa_id" class="form-label">Empresa *</label>
                            <select name="empresa_id" id="empresa_id" class="form-select" required>
                                <option value="">-- Selecciona una empresa --</option>
                                @php
                                    $empresas = \App\Models\Empresa::where('estado', 'activo')->get();
                                @endphp
                                @foreach($empresas as $empresa)
                                    <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nombre_cliente" class="form-label">Tu Nombre *</label>
                            <input type="text" name="nombre_cliente" id="nombre_cliente" class="form-control" value="{{ auth()->user()->name }}" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email_cliente" class="form-label">Tu Email *</label>
                            <input type="email" name="email_cliente" id="email_cliente" class="form-control" value="{{ auth()->user()->email }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="telefono_cliente" class="form-label">Tu Teléfono *</label>
                            <input type="tel" name="telefono_cliente" id="telefono_cliente" class="form-control" value="{{ auth()->user()->telefono }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción de tu Solicitud *</label>
                        <textarea name="descripcion" id="descripcion" class="form-control" rows="4" placeholder="Cuéntanos qué productos deseas y en qué cantidad..." required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="documento" class="form-label">Documento (Opcional)</label>
                        <input type="file" name="documento" id="documento" class="form-control" accept=".pdf,.doc,.docx">
                        <small class="text-muted">PDF, DOC o DOCX (máx. 5MB)</small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning text-dark fw-bold">
                        <i class="bi bi-send me-2"></i>Enviar Solicitud
                    </button>
                </div>
            </form>
            @endif
        </div>
    </div>
</div>

@if(count($carrito) > 0)
@push('scripts')
<script>
    (function () {
        const selects = Array.from(document.querySelectorAll('.cart-select'));
        const subtotalEl = document.getElementById('subtotalAmount');
        const totalEl = document.getElementById('orderTotal');
        const selectedItemsContainer = document.getElementById('selectedItemsContainer');
        const openCheckoutBtn = document.getElementById('openCheckoutBtn');
        const shippingModalEl = document.getElementById('shippingModal');
        const noSelectionModalEl = document.getElementById('noSelectionModal');
        const checkoutForm = document.getElementById('checkoutForm');
        const similarSection = document.querySelector('.similar-section');
        const similarCols = Array.from(document.querySelectorAll('.similar-col'));
        const cartLoadingOverlay = document.getElementById('cartLoadingOverlay');
        const cartLoadingText = document.getElementById('cartLoadingText');
        const removeLinks = Array.from(document.querySelectorAll('.js-cart-remove'));
        const clearLink = document.querySelector('.js-cart-clear');
        const quantityLinks = Array.from(document.querySelectorAll('.js-cart-qty'));

        if (!selects.length || !subtotalEl || !totalEl || !selectedItemsContainer || !checkoutForm) {
            return;
        }

        const shippingModalInstance = (shippingModalEl && window.bootstrap && window.bootstrap.Modal)
            ? new window.bootstrap.Modal(shippingModalEl)
            : null;

        const noSelectionModalInstance = (noSelectionModalEl && window.bootstrap && window.bootstrap.Modal)
            ? new window.bootstrap.Modal(noSelectionModalEl)
            : null;

        const storageKey = 'cart_selection_state_{{ \App\Http\Controllers\CarritoController::getCartKey() }}';

        function loadSelectionState() {
            try {
                const raw = localStorage.getItem(storageKey);
                if (!raw) {
                    return;
                }
                const saved = JSON.parse(raw);
                if (!saved || typeof saved !== 'object') {
                    return;
                }

                selects.forEach(input => {
                    if (Object.prototype.hasOwnProperty.call(saved, input.value)) {
                        input.checked = !!saved[input.value];
                    }
                });
            } catch (error) {
            }
        }

        function persistSelectionState() {
            try {
                const state = {};
                selects.forEach(input => {
                    state[input.value] = input.checked;
                });
                localStorage.setItem(storageKey, JSON.stringify(state));
            } catch (error) {
            }
        }

        function formatMoney(value) {
            return '$' + Number(value).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            });
        }

        function getSelectedRows() {
            return selects
                .filter(input => input.checked)
                .map(input => input.closest('.cart-item'))
                .filter(Boolean);
        }

        function refreshSummary() {
            const selectedRows = getSelectedRows();
            const subtotal = selectedRows.reduce((acc, row) => {
                const value = parseFloat(row.dataset.subtotal || '0');
                return acc + (isNaN(value) ? 0 : value);
            }, 0);

            subtotalEl.textContent = formatMoney(subtotal);
            totalEl.textContent = formatMoney(subtotal);
        }

        function getSelectedSubtotal() {
            return getSelectedRows().reduce((acc, row) => {
                const value = parseFloat(row.dataset.subtotal || '0');
                return acc + (isNaN(value) ? 0 : value);
            }, 0);
        }

        function hasValidSelection() {
            return getSelectedRows().length > 0 && getSelectedSubtotal() > 0;
        }

        function syncSelectedToForm() {
            selectedItemsContainer.innerHTML = '';
            getSelectedRows().forEach(row => {
                const itemId = row.dataset.itemId;
                if (!itemId) {
                    return;
                }
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'selected_items[]';
                hidden.value = itemId;
                selectedItemsContainer.appendChild(hidden);
            });
        }

        function refreshSimilarBySelectedCategories() {
            if (!similarSection || !similarCols.length) {
                return;
            }

            const selectedCategories = new Set(
                getSelectedRows()
                    .map(row => (row.dataset.category || '').trim().toLowerCase())
                    .filter(Boolean)
            );

            let visibleCount = 0;
            similarCols.forEach(col => {
                const category = (col.dataset.relatedCategory || '').trim().toLowerCase();
                const visible = selectedCategories.size > 0 && selectedCategories.has(category);
                col.style.display = visible ? '' : 'none';
                if (visible) {
                    visibleCount += 1;
                }
            });

            similarSection.style.display = visibleCount > 0 ? '' : 'none';
        }

        function shouldHandleNavigationClick(event) {
            return event.button === 0 && !event.metaKey && !event.ctrlKey && !event.shiftKey && !event.altKey;
        }

        function showCartLoading(message) {
            if (!cartLoadingOverlay) {
                return;
            }
            if (cartLoadingText && message) {
                cartLoadingText.textContent = message;
            }
            cartLoadingOverlay.classList.remove('d-none');
        }

        function hideCartLoading() {
            if (!cartLoadingOverlay) {
                return;
            }
            cartLoadingOverlay.classList.add('d-none');
        }

        function showNoSelectionWarning() {
            if (!noSelectionModalInstance) {
                return;
            }
            noSelectionModalInstance.show();
        }

        function hideNoSelectionWarning() {
            if (!noSelectionModalInstance) {
                return;
            }
            noSelectionModalInstance.hide();
        }

        selects.forEach(input => {
            input.addEventListener('change', function () {
                persistSelectionState();
                refreshSummary();
                syncSelectedToForm();
                refreshSimilarBySelectedCategories();
                if (getSelectedRows().length > 0) {
                    hideNoSelectionWarning();
                }
            });
        });

        if (openCheckoutBtn) {
            openCheckoutBtn.addEventListener('click', function (event) {
                if (!hasValidSelection()) {
                    event.preventDefault();
                    event.stopPropagation();
                    showNoSelectionWarning();
                    return;
                }
                hideNoSelectionWarning();
                syncSelectedToForm();
                if (shippingModalInstance) {
                    shippingModalInstance.show();
                }
            });
        }

        const numeroDocumentoInput = document.getElementById('numero_documento');
        const tipoDocumentoInput = document.getElementById('tipo_documento');
        const metodoPagoInput = document.getElementById('metodo_pago');
        const telefonoInput = document.getElementById('telefono');
        const paisInput = document.getElementById('pais');
        const departamentoInput = document.getElementById('departamento');
        const ciudadInput = document.getElementById('ciudad');
        const direccionInput = document.getElementById('direccion');
        const btnUseProfileData = document.getElementById('btnUseProfileData');
        const confirmCheckoutBtn = document.getElementById('confirmCheckoutBtn');
        const paisFeedback = document.getElementById('paisFeedback');
        const ciudadFeedback = document.getElementById('ciudadFeedback');
        const telefonoFeedback = document.getElementById('telefonoFeedback');
        const cedulaFeedback = document.getElementById('cedulaFeedback');
        const paisStatus = document.getElementById('paisStatus');
        const ciudadStatus = document.getElementById('ciudadStatus');
        const telefonoStatus = document.getElementById('telefonoStatus');
        const cedulaStatus = document.getElementById('cedulaStatus');
        const tarjetaFields = document.getElementById('tarjetaFields');
        const transferenciaFields = document.getElementById('transferenciaFields');
        const cardNumberInput = document.getElementById('card_number');
        const cardExpiryInput = document.getElementById('card_expiry');
        const cardCvvInput = document.getElementById('card_cvv');
        const paymentProviderInput = document.getElementById('payment_provider');
        const accountNumberInput = document.getElementById('account_number');
        const cardNumberFeedback = document.getElementById('cardNumberFeedback');
        const cardExpiryFeedback = document.getElementById('cardExpiryFeedback');
        const cardCvvFeedback = document.getElementById('cardCvvFeedback');
        const accountNumberFeedback = document.getElementById('accountNumberFeedback');
        const cardNumberStatus = document.getElementById('cardNumberStatus');
        const cardExpiryStatus = document.getElementById('cardExpiryStatus');
        const cardCvvStatus = document.getElementById('cardCvvStatus');
        const accountNumberStatus = document.getElementById('accountNumberStatus');
        const lettersRegex = /^[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s\.-]+$/;
        const initialLocation = {
            pais: @json(old('pais', optional(auth()->user())->pais ?? optional(auth()->user())->ciudad ?? '')),
            departamento: @json(old('departamento', optional(auth()->user())->departamento ?? '')),
            ciudad: @json(old('ciudad', optional(auth()->user())->municipio ?? '')),
        };

        const locationData = {
            'Colombia': {
                'Antioquia': ['Medellín', 'Bello', 'Envigado', 'Itagüí', 'Rionegro'],
                'Cundinamarca': ['Bogotá', 'Soacha', 'Zipaquirá', 'Chía', 'Facatativá'],
                'Valle del Cauca': ['Cali', 'Palmira', 'Buenaventura', 'Tuluá', 'Cartago'],
                'Atlántico': ['Barranquilla', 'Soledad', 'Malambo', 'Puerto Colombia'],
                'Santander': ['Bucaramanga', 'Floridablanca', 'Girón', 'Piedecuesta'],
            },
            'México': {
                'Ciudad de México': ['Ciudad de México'],
                'Jalisco': ['Guadalajara', 'Zapopan', 'Tlaquepaque'],
                'Nuevo León': ['Monterrey', 'San Nicolás', 'Guadalupe'],
            },
            'Argentina': {
                'Buenos Aires': ['Buenos Aires', 'La Plata', 'Mar del Plata'],
                'Córdoba': ['Córdoba', 'Villa Carlos Paz'],
                'Santa Fe': ['Rosario', 'Santa Fe'],
            },
            'Brasil': {
                'São Paulo': ['São Paulo', 'Campinas'],
                'Rio de Janeiro': ['Rio de Janeiro', 'Niterói'],
                'Minas Gerais': ['Belo Horizonte', 'Uberlândia'],
            },
            'Chile': {
                'Región Metropolitana': ['Santiago', 'Puente Alto'],
                'Valparaíso': ['Valparaíso', 'Viña del Mar'],
            },
            'Ecuador': {
                'Pichincha': ['Quito'],
                'Guayas': ['Guayaquil'],
                'Azuay': ['Cuenca'],
            },
            'Perú': {
                'Lima': ['Lima'],
                'Arequipa': ['Arequipa'],
                'La Libertad': ['Trujillo'],
            },
            'Venezuela': {
                'Distrito Capital': ['Caracas'],
                'Zulia': ['Maracaibo'],
                'Carabobo': ['Valencia'],
            },
            'Costa Rica': {
                'San José': ['San José'],
                'Alajuela': ['Alajuela'],
                'Heredia': ['Heredia'],
            },
            'Panamá': {
                'Panamá': ['Ciudad de Panamá', 'San Miguelito'],
                'Chiriquí': ['David'],
            },
            'Uruguay': {
                'Montevideo': ['Montevideo'],
                'Salto': ['Salto'],
            },
            'Paraguay': {
                'Asunción': ['Asunción'],
                'Alto Paraná': ['Ciudad del Este'],
            },
            'Bolivia': {
                'La Paz': ['La Paz', 'El Alto'],
                'Santa Cruz': ['Santa Cruz de la Sierra'],
            },
            'Guatemala': {
                'Guatemala': ['Ciudad de Guatemala'],
                'Quetzaltenango': ['Quetzaltenango'],
            },
            'Honduras': {
                'Francisco Morazán': ['Tegucigalpa'],
                'Cortés': ['San Pedro Sula'],
            },
        };
        const colombiaDataUrl = "{{ asset('data/colombia.min.json') }}";
        const phoneRulesByCountry = {
            colombia: { exact: 10 },
        };
        const accountRulesByProvider = {
            nequi: { exact: 10 },
            daviplata: { exact: 10 },
            bancolombia: { min: 10, max: 16 },
            otro: { min: 6, max: 20 },
        };

        function allowOnlyDigits(input) {
            if (!input) {
                return;
            }
            input.addEventListener('input', function () {
                this.value = this.value.replace(/\D+/g, '');
            });
        }

        function formatExpiryInput(input) {
            if (!input) {
                return;
            }
            input.addEventListener('input', function () {
                let value = this.value.replace(/[^0-9]/g, '').slice(0, 4);
                if (value.length >= 3) {
                    value = value.slice(0, 2) + '/' + value.slice(2);
                }
                this.value = value;
            });
        }

        function setSelectOptions(select, values, placeholder, selectedValue = '') {
            if (!select) {
                return;
            }

            select.innerHTML = '';
            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.textContent = placeholder;
            select.appendChild(defaultOption);

            values.forEach(value => {
                const option = document.createElement('option');
                option.value = value;
                option.textContent = value;
                if (selectedValue && selectedValue === value) {
                    option.selected = true;
                }
                select.appendChild(option);
            });
        }

        function populateDepartamentos(selectedDepartamento = '') {
            const pais = (paisInput?.value || '').trim();
            const departamentos = Object.keys(locationData[pais] || {});
            setSelectOptions(departamentoInput, departamentos, 'Selecciona un departamento/estado', selectedDepartamento);
        }

        function populateCiudades(selectedCiudad = '') {
            const pais = (paisInput?.value || '').trim();
            const departamento = (departamentoInput?.value || '').trim();
            const ciudades = (locationData[pais] && locationData[pais][departamento]) ? locationData[pais][departamento] : [];
            setSelectOptions(ciudadInput, ciudades, 'Selecciona un municipio/ciudad', selectedCiudad);
        }

        async function loadColombiaData() {
            try {
                const response = await fetch(colombiaDataUrl, { cache: 'no-store' });
                if (!response.ok) {
                    return;
                }

                const data = await response.json();
                if (!Array.isArray(data) || !data.length) {
                    return;
                }

                const colombiaMap = {};
                data.forEach(item => {
                    const departamento = item?.departamento;
                    const ciudades = Array.isArray(item?.ciudades) ? item.ciudades : [];
                    if (departamento) {
                        colombiaMap[departamento] = ciudades;
                    }
                });

                if (Object.keys(colombiaMap).length > 0) {
                    locationData['Colombia'] = colombiaMap;

                    if ((paisInput?.value || '').trim() === 'Colombia') {
                        const selectedDepartamento = (departamentoInput?.value || '').trim();
                        const selectedCiudad = (ciudadInput?.value || '').trim();
                        populateDepartamentos(selectedDepartamento);
                        populateCiudades(selectedCiudad);
                    }
                }
            } catch (error) {
            }
        }

        function getPhoneRuleByCountry(countryValue) {
            const key = (countryValue || '').trim().toLowerCase();
            return phoneRulesByCountry[key] || { min: 10, max: 15 };
        }

        function getAccountRuleByProvider(provider) {
            const key = (provider || '').trim().toLowerCase();
            return accountRulesByProvider[key] || { min: 6, max: 20 };
        }

        function updatePaymentFieldsVisibility() {
            const method = (metodoPagoInput?.value || '').trim();
            if (tarjetaFields) tarjetaFields.classList.toggle('d-none', method !== 'tarjeta');
            if (transferenciaFields) transferenciaFields.classList.toggle('d-none', method !== 'transferencia');
        }

        function updateDocumentoInputMode() {
            const tipo = (tipoDocumentoInput?.value || '').trim();
            if (!numeroDocumentoInput) return;

            if (tipo === 'PAS') {
                numeroDocumentoInput.removeAttribute('inputmode');
                numeroDocumentoInput.setAttribute('maxlength', '10');
                numeroDocumentoInput.setAttribute('minlength', '7');
                numeroDocumentoInput.setAttribute('pattern', '[A-Za-z0-9]{7,10}');
                numeroDocumentoInput.setAttribute('title', 'El pasaporte debe ser alfanumérico (7 a 10 caracteres).');
            } else {
                numeroDocumentoInput.setAttribute('inputmode', 'numeric');
                numeroDocumentoInput.setAttribute('maxlength', '10');
                numeroDocumentoInput.setAttribute('minlength', '7');
                numeroDocumentoInput.setAttribute('pattern', '[0-9]{7,10}');
                numeroDocumentoInput.setAttribute('title', 'Ingresa solo números (7 a 10 dígitos).');
            }
        }

        function setFieldState(input, feedbackEl, statusEl, valid, message) {
            if (!input) {
                return;
            }

            input.classList.remove('is-valid', 'is-invalid');
            input.classList.add(valid ? 'is-valid' : 'is-invalid');

            if (feedbackEl) {
                if (valid) {
                    feedbackEl.classList.add('d-none');
                    feedbackEl.textContent = '';
                } else {
                    feedbackEl.classList.remove('d-none');
                    feedbackEl.textContent = message;
                }
            }

            if (statusEl) {
                statusEl.textContent = valid ? 'Válido' : 'Inválido';
                statusEl.className = valid ? 'd-block mt-1 text-success' : 'd-block mt-1 text-danger';
            }
        }

        function validatePais() {
            const value = (paisInput?.value || '').trim();
            const valid = value.length > 0;
            setFieldState(paisInput, paisFeedback, paisStatus, valid, 'El país solo puede contener letras.');
            return valid;
        }

        function validateCiudad() {
            const value = (ciudadInput?.value || '').trim();
            const valid = value.length > 0;
            setFieldState(ciudadInput, ciudadFeedback, ciudadStatus, valid, 'La ciudad solo puede contener letras.');
            return valid;
        }

        function validateDocumento() {
            const value = (numeroDocumentoInput?.value || '').trim();
            const tipo = (tipoDocumentoInput?.value || '').trim();
            const valid = tipo === 'PAS'
                ? /^[A-Za-z0-9]{7,10}$/.test(value)
                : /^\d{7,10}$/.test(value);
            const msg = tipo === 'PAS'
                ? 'El pasaporte debe ser alfanumérico y tener entre 7 y 10 caracteres.'
                : 'La cédula debe tener entre 7 y 10 dígitos.';
            setFieldState(numeroDocumentoInput, cedulaFeedback, cedulaStatus, valid, msg);
            return valid;
        }

        function validateTelefono() {
            const value = (telefonoInput?.value || '').trim();
            const country = (paisInput?.value || '').trim();
            const rule = getPhoneRuleByCountry(country);
            let valid = false;
            let message = 'El teléfono debe contener solo números.';

            if (!/^\d+$/.test(value)) {
                valid = false;
                message = 'El teléfono debe contener solo números.';
            } else if (typeof rule.exact === 'number') {
                valid = value.length === rule.exact;
                message = `El teléfono para ${country || 'este país'} debe tener ${rule.exact} dígitos.`;
            } else {
                const min = rule.min ?? 10;
                const max = rule.max ?? 15;
                valid = value.length >= min && value.length <= max;
                message = `El teléfono debe tener entre ${min} y ${max} dígitos.`;
            }

            setFieldState(telefonoInput, telefonoFeedback, telefonoStatus, valid, message);
            return valid;
        }

        function validateTarjeta() {
            const method = (metodoPagoInput?.value || '').trim();
            if (method !== 'tarjeta') {
                setFieldState(cardNumberInput, cardNumberFeedback, cardNumberStatus, true, '');
                setFieldState(cardExpiryInput, cardExpiryFeedback, cardExpiryStatus, true, '');
                setFieldState(cardCvvInput, cardCvvFeedback, cardCvvStatus, true, '');
                return true;
            }

            const cardNumber = (cardNumberInput?.value || '').trim();
            const cardExpiry = (cardExpiryInput?.value || '').trim();
            const cardCvv = (cardCvvInput?.value || '').trim();

            const validNumber = /^\d{13,19}$/.test(cardNumber);
            const validExpiry = /^(0[1-9]|1[0-2])\/\d{2}$/.test(cardExpiry);
            const validCvv = /^\d{3,4}$/.test(cardCvv);

            setFieldState(cardNumberInput, cardNumberFeedback, cardNumberStatus, validNumber, 'El número de tarjeta debe tener entre 13 y 19 dígitos.');
            setFieldState(cardExpiryInput, cardExpiryFeedback, cardExpiryStatus, validExpiry, 'La fecha debe tener formato MM/AA.');
            setFieldState(cardCvvInput, cardCvvFeedback, cardCvvStatus, validCvv, 'El CVV debe tener 3 o 4 dígitos.');

            return validNumber && validExpiry && validCvv;
        }

        function validateTransferencia() {
            const method = (metodoPagoInput?.value || '').trim();
            if (method !== 'transferencia') {
                setFieldState(accountNumberInput, accountNumberFeedback, accountNumberStatus, true, '');
                return true;
            }

            const provider = (paymentProviderInput?.value || '').trim();
            const account = (accountNumberInput?.value || '').trim();
            const rule = getAccountRuleByProvider(provider);

            if (!provider) {
                setFieldState(accountNumberInput, accountNumberFeedback, accountNumberStatus, false, 'Selecciona una entidad para transferencia.');
                return false;
            }

            let valid = false;
            let msg = 'Número inválido.';

            if (!/^\d+$/.test(account)) {
                valid = false;
                msg = 'El número de cuenta debe contener solo números.';
            } else if (typeof rule.exact === 'number') {
                valid = account.length === rule.exact;
                msg = `El número para ${provider} debe tener ${rule.exact} dígitos.`;
            } else {
                valid = account.length >= (rule.min ?? 6) && account.length <= (rule.max ?? 20);
                msg = `El número de cuenta debe tener entre ${rule.min ?? 6} y ${rule.max ?? 20} dígitos.`;
            }

            setFieldState(accountNumberInput, accountNumberFeedback, accountNumberStatus, valid, msg);
            return valid;
        }

        function updateCheckoutButtonState() {
            if (!confirmCheckoutBtn) {
                return;
            }

            const validDireccion = (direccionInput?.value || '').trim().length > 0;
            const validDepartamento = (departamentoInput?.value || '').trim().length > 0;
            const allValid = validatePais() && validateCiudad() && validateDocumento() && validateTelefono() && validateTarjeta() && validateTransferencia() && validDireccion && validDepartamento;
            confirmCheckoutBtn.disabled = !allValid;
        }

        allowOnlyDigits(telefonoInput);
        allowOnlyDigits(cardNumberInput);
        allowOnlyDigits(cardCvvInput);
        allowOnlyDigits(accountNumberInput);
        formatExpiryInput(cardExpiryInput);

        if (paisInput) {
            paisInput.addEventListener('change', function () {
                populateDepartamentos('');
                populateCiudades('');
                updateCheckoutButtonState();
            });
        }

        if (departamentoInput) {
            departamentoInput.addEventListener('change', function () {
                populateCiudades('');
                updateCheckoutButtonState();
            });
        }

        if (numeroDocumentoInput) {
            numeroDocumentoInput.addEventListener('input', function () {
                const tipo = (tipoDocumentoInput?.value || '').trim();
                if (tipo === 'PAS') {
                    this.value = this.value.replace(/[^A-Za-z0-9]/g, '').toUpperCase();
                } else {
                    this.value = this.value.replace(/\D+/g, '');
                }
            });
        }

        [paisInput, ciudadInput, numeroDocumentoInput, tipoDocumentoInput, telefonoInput, direccionInput, departamentoInput, metodoPagoInput, cardNumberInput, cardExpiryInput, cardCvvInput, paymentProviderInput, accountNumberInput].forEach(function (input) {
            if (!input) {
                return;
            }
            input.addEventListener('input', updateCheckoutButtonState);
            input.addEventListener('change', updateCheckoutButtonState);
            input.addEventListener('blur', updateCheckoutButtonState);
        });

        function isDigitsWithLength(value, min, max) {
            return /^\d+$/.test(value) && value.length >= min && value.length <= max;
        }

        checkoutForm.addEventListener('submit', function (event) {
            if (!hasValidSelection()) {
                event.preventDefault();
                hideCartLoading();
                showNoSelectionWarning();
                return;
            }

            const numeroDocumento = (numeroDocumentoInput?.value || '').trim();
            const telefono = (telefonoInput?.value || '').trim();
            const pais = (paisInput?.value || '').trim();
            const departamento = (departamentoInput?.value || '').trim();
            const ciudad = (ciudadInput?.value || '').trim();
            const direccion = (direccionInput?.value || '').trim();

            if (!pais || !departamento || !ciudad || !direccion) {
                event.preventDefault();
                hideCartLoading();
                alert('Debes completar país, departamento/estado, ciudad y dirección completa.');
                return;
            }

            const tipoDocumento = (tipoDocumentoInput?.value || '').trim();
            const validDocumento = tipoDocumento === 'PAS' ? /^[A-Za-z0-9]{7,10}$/.test(numeroDocumento) : isDigitsWithLength(numeroDocumento, 7, 10);

            if (!validDocumento) {
                event.preventDefault();
                hideCartLoading();
                alert(tipoDocumento === 'PAS'
                    ? 'El pasaporte debe ser alfanumérico y tener entre 7 y 10 caracteres.'
                    : 'La cédula debe tener entre 7 y 10 dígitos numéricos.');
                return;
            }

            const phoneRule = getPhoneRuleByCountry(pais);
            const validTelefono = typeof phoneRule.exact === 'number'
                ? isDigitsWithLength(telefono, phoneRule.exact, phoneRule.exact)
                : isDigitsWithLength(telefono, phoneRule.min ?? 10, phoneRule.max ?? 15);

            if (!validTelefono) {
                event.preventDefault();
                hideCartLoading();
                if (typeof phoneRule.exact === 'number') {
                    alert(`El teléfono para ${pais} debe tener exactamente ${phoneRule.exact} dígitos.`);
                } else {
                    alert(`El teléfono debe tener entre ${phoneRule.min ?? 10} y ${phoneRule.max ?? 15} dígitos numéricos.`);
                }
                return;
            }

            if (!validateTarjeta() || !validateTransferencia()) {
                event.preventDefault();
                hideCartLoading();
                alert('Revisa los datos del método de pago seleccionado.');
                return;
            }

            hideNoSelectionWarning();
            syncSelectedToForm();
            showCartLoading('Procesando pago...');
        });

        if (btnUseProfileData) {
            btnUseProfileData.addEventListener('click', function () {
                if (paisInput) paisInput.value = this.dataset.pais || '';
                populateDepartamentos(this.dataset.departamento || '');
                populateCiudades(this.dataset.ciudad || '');
                if (direccionInput) direccionInput.value = this.dataset.direccion || '';
                if (telefonoInput) telefonoInput.value = (this.dataset.telefono || '').replace(/\D+/g, '');
                updateCheckoutButtonState();
            });
        }

        if (metodoPagoInput) {
            metodoPagoInput.addEventListener('change', function () {
                updatePaymentFieldsVisibility();
                updateCheckoutButtonState();
            });
        }

        if (tipoDocumentoInput) {
            tipoDocumentoInput.addEventListener('change', function () {
                updateDocumentoInputMode();
                updateCheckoutButtonState();
            });
        }

        updatePaymentFieldsVisibility();
        updateDocumentoInputMode();
        if (paisInput) {
            paisInput.value = initialLocation.pais || paisInput.value;
        }
        populateDepartamentos(initialLocation.departamento || '');
        populateCiudades(initialLocation.ciudad || '');
        loadColombiaData().then(() => {
            updateCheckoutButtonState();
        });
        updateCheckoutButtonState();

        quantityLinks.forEach(link => {
            link.addEventListener('click', function (event) {
                if (!shouldHandleNavigationClick(event)) {
                    return;
                }
                if (this.href && (this.href.indexOf('/carrito/sumar') !== -1 || this.href.indexOf('/carrito/restar') !== -1)) {
                    return;
                }
                showCartLoading('Actualizando carrito...');
            });
        });

        removeLinks.forEach(link => {
            link.addEventListener('click', function (event) {
                if (!shouldHandleNavigationClick(event)) {
                    return;
                }
                showCartLoading('Eliminando producto...');
            });
        });

        if (clearLink) {
            clearLink.addEventListener('click', function (event) {
                if (!shouldHandleNavigationClick(event)) {
                    return;
                }
                showCartLoading('Vaciando carrito...');
            });
        }

        loadSelectionState();
        persistSelectionState();
        refreshSummary();
        syncSelectedToForm();
        refreshSimilarBySelectedCategories();
    })();
</script>
@endpush
@endif
@endsection