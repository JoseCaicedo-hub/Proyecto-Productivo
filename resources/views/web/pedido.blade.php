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
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
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
                            <span class="fw-bold">${{ number_format($item['precio'], 2) }}</span>
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
                        <span id="subtotalAmount">${{ number_format($subtotal, 2) }}</span>
                    </div>

                    <div class="summary-row">
                        <label>Envío</label>
                        <span id="shippingAmount">${{ number_format($envio, 2) }}</span>
                    </div>

                    <div class="summary-row" style="padding: 16px 0; border-bottom: none;">
                        <label style="font-size: 1.1rem;">Total</label>
                        <span class="total" id="orderTotal">${{ number_format($total, 2) }}</span>
                    </div>

                    <button type="button" id="openCheckoutBtn" class="btn btn-checkout">
                        <i class="bi bi-credit-card me-2"></i>Realizar pedido
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
                            <p class="fw-semibold mb-0">$ {{ number_format($producto->precio, 2) }}</p>
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
                    <div class="mb-3">
                        <label for="direccion" class="form-label">Dirección exacta <span class="text-danger">*</span></label>
                        <textarea name="direccion" id="direccion" class="form-control" rows="3" required>{{ old('direccion') }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tipo_documento" class="form-label">Tipo de documento <span class="text-danger">*</span></label>
                            <select name="tipo_documento" id="tipo_documento" class="form-select" required>
                                <option value="">Seleccione...</option>
                                <option value="CC" {{ old('tipo_documento') == 'CC' ? 'selected' : '' }}>Cédula de ciudadanía (CC)</option>
                                <option value="NIT" {{ old('tipo_documento') == 'NIT' ? 'selected' : '' }}>NIT</option>
                                <option value="CE" {{ old('tipo_documento') == 'CE' ? 'selected' : '' }}>Cédula de extranjería (CE)</option>
                                <option value="PAS" {{ old('tipo_documento') == 'PAS' ? 'selected' : '' }}>Pasaporte</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="numero_documento" class="form-label">Número de documento <span class="text-danger">*</span></label>
                            <input type="text" name="numero_documento" id="numero_documento" class="form-control" value="{{ old('numero_documento') }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="metodo_pago" class="form-label">Método de pago <span class="text-danger">*</span></label>
                            <select name="metodo_pago" id="metodo_pago" class="form-select" required>
                                <option value="">Seleccione...</option>
                                <option value="efectivo" {{ old('metodo_pago') == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                                <option value="tarjeta" {{ old('metodo_pago') == 'tarjeta' ? 'selected' : '' }}>Tarjeta débito/crédito</option>
                                <option value="transferencia" {{ old('metodo_pago') == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" name="telefono" id="telefono" class="form-control" value="{{ old('telefono') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="referencia" class="form-label">Referencia / Instrucciones (Casa, Apto, Residencia)</label>
                        <input type="text" name="referencia" id="referencia" class="form-control" value="{{ old('referencia') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
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

        checkoutForm.addEventListener('submit', function (event) {
            if (!hasValidSelection()) {
                event.preventDefault();
                hideCartLoading();
                showNoSelectionWarning();
                return;
            }
            hideNoSelectionWarning();
            syncSelectedToForm();
            showCartLoading('Procesando pago...');
        });

        quantityLinks.forEach(link => {
            link.addEventListener('click', function (event) {
                if (!shouldHandleNavigationClick(event)) {
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