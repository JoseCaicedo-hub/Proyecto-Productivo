@extends('web.app')

@section('contenido')
<section class="py-5">
    <div class="container px-4 px-lg-5">
        <div class="text-center mb-4">
            <h2 class="fw-bold">Preguntas Frecuentes</h2>
            <p class="text-muted">Aquí respondemos las dudas más comunes para ayudarte mientras navegas por nuestra tienda.</p>
        </div>

        <div class="row gx-4 gx-lg-5">
            <div class="col-md-6 mb-4">
                <h5 class="mb-3">Categoría 1</h5>
                <div class="accordion" id="faqCat1">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="q1">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseQ1" aria-expanded="false" aria-controls="collapseQ1">
                                ¿Cómo realizo un pedido?
                            </button>
                        </h2>
                        <div id="collapseQ1" class="accordion-collapse collapse" aria-labelledby="q1" data-bs-parent="#faqCat1">
                            <div class="accordion-body text-muted">Para realizar un pedido, busca el producto que deseas, abre su ficha y presiona "Agregar al carrito". Luego ve al carrito y procede a finalizar la compra con tu dirección y método de pago.</div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="q2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseQ2" aria-expanded="false" aria-controls="collapseQ2">
                                ¿Puedo modificar mi pedido después de hacerlo?
                            </button>
                        </h2>
                        <div id="collapseQ2" class="accordion-collapse collapse" aria-labelledby="q2" data-bs-parent="#faqCat1">
                            <div class="accordion-body text-muted">Si el pedido no ha sido procesado aún, puedes contactarnos vía soporte y solicitar la modificación. Si ya fue procesado, no será posible modificarlo.</div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="q3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseQ3" aria-expanded="false" aria-controls="collapseQ3">
                                ¿Cuáles son los tiempos de entrega?
                            </button>
                        </h2>
                        <div id="collapseQ3" class="accordion-collapse collapse" aria-labelledby="q3" data-bs-parent="#faqCat1">
                            <div class="accordion-body text-muted">Los tiempos varían según la ubicación y el método de envío; normalmente entre 2 y 7 días hábiles. Durante promociones pueden variar.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <h5 class="mb-3">Categoría 2</h5>
                <div class="accordion" id="faqCat2">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="q4">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseQ4" aria-expanded="false" aria-controls="collapseQ4">
                                ¿Qué métodos de pago aceptan?
                            </button>
                        </h2>
                        <div id="collapseQ4" class="accordion-collapse collapse" aria-labelledby="q4" data-bs-parent="#faqCat2">
                            <div class="accordion-body text-muted">Aceptamos tarjetas de crédito y débito, transferencias y pagos por plataformas locales según el país. Los métodos disponibles se muestran al finalizar la compra.</div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="q5">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseQ5" aria-expanded="false" aria-controls="collapseQ5">
                                ¿Cómo solicito una devolución?
                            </button>
                        </h2>
                        <div id="collapseQ5" class="accordion-collapse collapse" aria-labelledby="q5" data-bs-parent="#faqCat2">
                            <div class="accordion-body text-muted">Para solicitar una devolución debes ingresar a tu perfil > pedidos, seleccionar el pedido correspondiente y seguir las instrucciones. También puedes escribir a soporte si requieres ayuda.</div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="q6">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseQ6" aria-expanded="false" aria-controls="collapseQ6">
                                ¿Tienen garantía en los productos?
                            </button>
                        </h2>
                        <div id="collapseQ6" class="accordion-collapse collapse" aria-labelledby="q6" data-bs-parent="#faqCat2">
                            <div class="accordion-body text-muted">Sí, muchos de nuestros productos incluyen garantía. La duración y condiciones dependen del proveedor; revisa la ficha del producto o contáctanos para más detalles.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <h5 class="mb-3">Categoría 3</h5>
                <div class="accordion" id="faqCat3">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="q7">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseQ7" aria-expanded="false" aria-controls="collapseQ7">
                                ¿Cómo contacto al soporte?
                            </button>
                        </h2>
                        <div id="collapseQ7" class="accordion-collapse collapse" aria-labelledby="q7" data-bs-parent="#faqCat3">
                            <div class="accordion-body text-muted">Puedes contactarnos mediante el formulario de contacto, el chat en el sitio o por nuestras redes sociales. Responderemos lo antes posible.</div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="q8">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseQ8" aria-expanded="false" aria-controls="collapseQ8">
                                ¿Puedo cambiar mi dirección de envío?
                            </button>
                        </h2>
                        <div id="collapseQ8" class="accordion-collapse collapse" aria-labelledby="q8" data-bs-parent="#faqCat3">
                            <div class="accordion-body text-muted">Si el pedido aún no fue procesado, puedes pedir cambiar la dirección contactando soporte con tus datos y número de pedido.</div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="q9">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseQ9" aria-expanded="false" aria-controls="collapseQ9">
                                ¿Cómo sé si un producto tiene stock?
                            </button>
                        </h2>
                        <div id="collapseQ9" class="accordion-collapse collapse" aria-labelledby="q9" data-bs-parent="#faqCat3">
                            <div class="accordion-body text-muted">En la ficha del producto mostramos la cantidad disponible si el proveedor la ha registrado. Si no aparece, puedes contactarnos para confirmar disponibilidad.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <h5 class="mb-3">Categoría 4</h5>
                <div class="accordion" id="faqCat4">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="q10">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseQ10" aria-expanded="false" aria-controls="collapseQ10">
                                ¿Puedo retirar en tienda?
                            </button>
                        </h2>
                        <div id="collapseQ10" class="accordion-collapse collapse" aria-labelledby="q10" data-bs-parent="#faqCat4">
                            <div class="accordion-body text-muted">Depende del producto y del vendedor. Si está disponible la opción de retiro, aparecerá en las opciones de envío durante el checkout.</div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="q11">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseQ11" aria-expanded="false" aria-controls="collapseQ11">
                                ¿Ofrecen facturación?
                            </button>
                        </h2>
                        <div id="collapseQ11" class="accordion-collapse collapse" aria-labelledby="q11" data-bs-parent="#faqCat4">
                            <div class="accordion-body text-muted">Sí, podemos emitir factura electrónica cuando sea necesario. Indícanos los datos fiscales al momento de la compra o contacta soporte.</div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="q12">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseQ12" aria-expanded="false" aria-controls="collapseQ12">
                                ¿Cómo aplico un cupón de descuento?
                            </button>
                        </h2>
                        <div id="collapseQ12" class="accordion-collapse collapse" aria-labelledby="q12" data-bs-parent="#faqCat4">
                            <div class="accordion-body text-muted">En la página del carrito o durante el checkout verás un campo para ingresar el código del cupón. Ingresa el código y aplica para ver el descuento reflejado.</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</section>
@endsection
