<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="title" content="Shop | ArtCode.com" />
        <meta name="author" content="ArtCode" />
        <meta name="description" content="Shop | ArtCode.com"/>
        <meta name="keywords" content="Shop, ArtCode"
        />
        <title>@yield('titulo', 'StartPlace.com')</title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <!-- Bootstrap icons-->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="{{asset('css/styles.css')}}" rel="stylesheet" />
        <!-- Custom Themes -->
        <link rel="stylesheet" href="{{asset('css/custom-themes.css')}}" />
        <script>
            (function () {
                try {
                    var savedTheme = localStorage.getItem('theme');
                    var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                    if ((savedTheme && savedTheme === 'dark') || (!savedTheme && prefersDark)) {
                        document.documentElement.classList.add('dark-mode');
                    }
                } catch (e) {}
            })();
        </script>
        <style>
            html, body { transition: background-color .18s ease, color .18s ease; }
            html.dark-mode body { background-color: #0f172a; color: #e2e8f0; }
            html.dark-mode .bg-white,
            html.dark-mode .bg-light,
            html.dark-mode .card,
            html.dark-mode .dropdown-menu,
            html.dark-mode .modal-content,
            html.dark-mode .site-footer,
            html.dark-mode .navbar {
                background: #111827 !important;
                color: #e2e8f0 !important;
                border-color: rgba(148, 163, 184, 0.2) !important;
            }
            html.dark-mode .text-muted,
            html.dark-mode .small-muted { color: #94a3b8 !important; }
            html.dark-mode .nav-link,
            html.dark-mode a { color: #cbd5e1; }
            html.dark-mode .nav-link:hover,
            html.dark-mode a:hover { color: #7dd3fc; }
            html.dark-mode .form-control,
            html.dark-mode .form-select,
            html.dark-mode input,
            html.dark-mode textarea {
                background-color: #0b1220 !important;
                color: #e2e8f0 !important;
                border-color: rgba(148, 163, 184, 0.25) !important;
            }
            html.dark-mode .form-control::placeholder,
            html.dark-mode input::placeholder,
            html.dark-mode textarea::placeholder { color: #94a3b8 !important; }
            html.dark-mode .btn-outline-dark {
                color: #e2e8f0;
                border-color: rgba(148, 163, 184, 0.35);
            }
            html.dark-mode .btn-dark,
            html.dark-mode .btn-black {
                background: #334155 !important;
                border-color: #475569 !important;
                color: #e2e8f0 !important;
            }
            html.dark-mode .btn-dark:hover,
            html.dark-mode .btn-black:hover {
                background: #475569 !important;
                border-color: #64748b !important;
                color: #f8fafc !important;
            }
            html.dark-mode h1,
            html.dark-mode h2,
            html.dark-mode h3,
            html.dark-mode h4,
            html.dark-mode h5,
            html.dark-mode h6,
            html.dark-mode .text-dark,
            html.dark-mode .fw-bold,
            html.dark-mode .fw-semibold {
                color: #e2e8f0 !important;
            }
            html.dark-mode .hero-section,
            html.dark-mode #heroCarousel,
            html.dark-mode #heroCarousel .carousel-inner,
            html.dark-mode #heroCarousel .carousel-item {
                background: linear-gradient(180deg, #0b1220 0%, #111827 100%) !important;
            }
            html.dark-mode #heroCarousel .text-muted,
            html.dark-mode .hero-section .text-muted {
                color: #93c5fd !important;
            }
            html.dark-mode #heroCarousel .bg-white {
                background: #0f172a !important;
                border: 1px solid rgba(148, 163, 184, 0.2) !important;
            }
            html.dark-mode .category-collection .cat-card .cat-label {
                background: rgba(2, 6, 23, 0.62) !important;
            }
            html.dark-mode .why-box,
            html.dark-mode .review-card {
                background: #111827 !important;
                border: 1px solid rgba(148, 163, 184, 0.2) !important;
            }

            .add-cart-loading-overlay {
                position: fixed;
                inset: 0;
                z-index: 5000;
                background: rgba(2, 6, 23, 0.42);
                display: flex;
                align-items: center;
                justify-content: center;
                backdrop-filter: blur(1.5px);
            }

            .add-cart-loading-box {
                min-width: 230px;
                border-radius: 12px;
                padding: 16px 18px;
                background: rgba(15, 23, 42, 0.94);
                color: #f8fafc;
                display: flex;
                align-items: center;
                gap: 12px;
                box-shadow: 0 16px 38px rgba(2, 6, 23, 0.35);
            }

            .add-cart-loading-box .spinner-border {
                width: 1.15rem;
                height: 1.15rem;
                border-width: .15em;
            }

            .add-cart-loading-box p {
                margin: 0;
                font-weight: 600;
            }

            html.dark-mode .add-cart-loading-overlay {
                background: rgba(2, 6, 23, 0.62);
            }

            html.dark-mode .add-cart-loading-box {
                background: rgba(11, 18, 32, 0.96);
                border: 1px solid rgba(148, 163, 184, 0.25);
            }

            .chatbot-fab {
                position: fixed;
                right: 20px;
                bottom: 20px;
                z-index: 1050;
                width: 56px;
                height: 56px;
                border-radius: 999px;
                border: 0;
                background: #0d6efd;
                color: #fff;
                box-shadow: 0 10px 24px rgba(13, 110, 253, .35);
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .chatbot-panel {
                position: fixed;
                right: 20px;
                bottom: 88px;
                width: 340px;
                max-width: calc(100vw - 24px);
                height: 460px;
                z-index: 1050;
                border-radius: 14px;
                border: 1px solid rgba(15, 23, 42, .08);
                background: #fff;
                box-shadow: 0 14px 38px rgba(2, 6, 23, .2);
                overflow: hidden;
                transform: translateY(12px) scale(.98);
                opacity: 0;
                pointer-events: none;
                transition: .18s ease;
            }

            .chatbot-panel.open {
                transform: translateY(0) scale(1);
                opacity: 1;
                pointer-events: auto;
            }

            .chatbot-header {
                background: #0d6efd;
                color: #fff;
                padding: 10px 12px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                font-weight: 600;
            }

            .chatbot-messages {
                height: 330px;
                overflow-y: auto;
                padding: 12px;
                background: #f8fafc;
            }

            .chat-msg {
                margin-bottom: 10px;
                display: flex;
            }

            .chat-msg.user { justify-content: flex-end; }
            .chat-msg.bot { justify-content: flex-start; }

            .chat-bubble {
                max-width: 82%;
                padding: 8px 10px;
                border-radius: 10px;
                font-size: .93rem;
                white-space: pre-line;
            }

            .chat-msg.user .chat-bubble {
                background: #0d6efd;
                color: #fff;
            }

            .chat-msg.bot .chat-bubble {
                background: #fff;
                color: #0f172a;
                border: 1px solid rgba(15, 23, 42, .08);
            }

            .chatbot-input {
                border-top: 1px solid rgba(15, 23, 42, .08);
                background: #fff;
                padding: 10px;
                display: flex;
                gap: 8px;
            }

            .chatbot-input input {
                flex: 1;
            }

            html.dark-mode .chatbot-panel { background: #111827; border-color: rgba(148,163,184,.25); }
            html.dark-mode .chatbot-messages { background: #0b1220; }
            html.dark-mode .chat-msg.bot .chat-bubble { background: #111827; color: #e2e8f0; border-color: rgba(148,163,184,.25); }
            html.dark-mode .chatbot-input { background: #111827; border-top-color: rgba(148,163,184,.25); }
        </style>
        @stack('estilos')
    </head>
    <body>
        <!-- Navigation-->
        @include('web.partials.nav')
        <!-- Header-->
        @if(View::hasSection('header'))
            @include('web.partials.header')
        @endif
        <!-- Search and Filter Section -->
        @yield('contenido')

        <div id="addCartLoadingOverlay" class="add-cart-loading-overlay d-none" aria-hidden="true">
            <div class="add-cart-loading-box">
                <div class="spinner-border text-light" role="status" aria-label="Cargando"></div>
                <p>Agregando producto...</p>
            </div>
        </div>

        <button type="button" id="chatbotFab" class="chatbot-fab" aria-label="Abrir chat de ayuda" title="Chat de ayuda">
            <i class="bi bi-chat-dots-fill"></i>
        </button>

        <div id="chatbotPanel" class="chatbot-panel" aria-hidden="true">
            <div class="chatbot-header">
                <span><i class="bi bi-robot me-1"></i> Asistente StartPlace</span>
                <button type="button" id="chatbotClose" class="btn btn-sm btn-light">Cerrar</button>
            </div>
            <div id="chatbotMessages" class="chatbot-messages"></div>
            <div class="chatbot-input">
                <input type="text" id="chatbotInput" class="form-control" placeholder="Escribe tu pregunta...">
                <button type="button" id="chatbotSend" class="btn btn-primary">Enviar</button>
            </div>
        </div>

        <!-- Footer-->
        @include('web.partials.footer')
        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="{{asset('js/scripts.js')}}"></script>
        <script>
            (function () {
                var overlay = document.getElementById('addCartLoadingOverlay');
                var scrollStateKey = 'add_cart_scroll_restore';

                function showOverlay() {
                    if (!overlay) return;
                    overlay.classList.remove('d-none');
                }

                function hideOverlay() {
                    if (!overlay) return;
                    overlay.classList.add('d-none');
                }

                function isAddCartForm(form) {
                    if (!form) return false;
                    var action = (form.getAttribute('action') || '').toLowerCase();
                    return action.indexOf('/carrito/agregar') !== -1;
                }

                function saveScrollState() {
                    try {
                        var state = {
                            path: window.location.pathname + window.location.search,
                            y: window.scrollY || window.pageYOffset || 0,
                        };
                        sessionStorage.setItem(scrollStateKey, JSON.stringify(state));
                    } catch (e) {}
                }

                function restoreScrollState() {
                    try {
                        var raw = sessionStorage.getItem(scrollStateKey);
                        if (!raw) return;

                        var state = JSON.parse(raw);
                        if (!state || typeof state !== 'object') {
                            sessionStorage.removeItem(scrollStateKey);
                            return;
                        }

                        var currentPath = window.location.pathname + window.location.search;
                        if (state.path === currentPath) {
                            requestAnimationFrame(function () {
                                window.scrollTo(0, Number(state.y || 0));
                            });
                        }

                        sessionStorage.removeItem(scrollStateKey);
                    } catch (e) {}
                }

                document.addEventListener('submit', function (event) {
                    var form = event.target;
                    if (!isAddCartForm(form)) return;

                    if (typeof form.checkValidity === 'function' && !form.checkValidity()) {
                        return;
                    }

                    setTimeout(function () {
                        if (!event.defaultPrevented) {
                            saveScrollState();
                            showOverlay();
                        }
                    }, 0);
                });

                window.addEventListener('pageshow', function () {
                    hideOverlay();
                    restoreScrollState();
                });

                document.addEventListener('DOMContentLoaded', restoreScrollState);
            })();
        </script>
        <script>
            (function () {
                var root = document.documentElement;
                var button = document.getElementById('themeToggleBtn');

                function updateButton(theme) {
                    if (!button) return;
                    var moon = button.querySelector('.theme-icon-moon');
                    var sun = button.querySelector('.theme-icon-sun');
                    if (theme === 'dark') {
                        moon && moon.classList.add('d-none');
                        sun && sun.classList.remove('d-none');
                        button.setAttribute('title', 'Cambiar a modo claro');
                        button.setAttribute('aria-label', 'Cambiar a modo claro');
                    } else {
                        sun && sun.classList.add('d-none');
                        moon && moon.classList.remove('d-none');
                        button.setAttribute('title', 'Cambiar a modo oscuro');
                        button.setAttribute('aria-label', 'Cambiar a modo oscuro');
                    }
                }

                function applyTheme(theme, persist) {
                    var isDark = theme === 'dark';
                    root.classList.toggle('dark-mode', isDark);
                    if (persist !== false) {
                        try { localStorage.setItem('theme', theme); } catch (e) {}
                    }
                    updateButton(theme);
                }

                var savedTheme = null;
                try { savedTheme = localStorage.getItem('theme'); } catch (e) {}
                var initialTheme = savedTheme || ((window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) ? 'dark' : 'light');
                applyTheme(initialTheme, false);

                if (button) {
                    button.addEventListener('click', function () {
                        var nextTheme = root.classList.contains('dark-mode') ? 'light' : 'dark';
                        applyTheme(nextTheme, true);
                    });
                }
            })();
        </script>
        <script>
            (function () {
                var fab = document.getElementById('chatbotFab');
                var panel = document.getElementById('chatbotPanel');
                var closeBtn = document.getElementById('chatbotClose');
                var messages = document.getElementById('chatbotMessages');
                var input = document.getElementById('chatbotInput');
                var sendBtn = document.getElementById('chatbotSend');
                var endpoint = "{{ route('chatbot.message') }}";
                var csrf = "{{ csrf_token() }}";
                var welcomed = false;

                if (!fab || !panel || !messages || !input || !sendBtn) {
                    return;
                }

                function addMessage(text, role) {
                    var wrap = document.createElement('div');
                    wrap.className = 'chat-msg ' + role;
                    var bubble = document.createElement('div');
                    bubble.className = 'chat-bubble';
                    bubble.textContent = text;
                    wrap.appendChild(bubble);
                    messages.appendChild(wrap);
                    messages.scrollTop = messages.scrollHeight;
                }

                function openPanel() {
                    panel.classList.add('open');
                    panel.setAttribute('aria-hidden', 'false');
                    if (!welcomed) {
                        addMessage('Hola 👋 ¿En qué puedo ayudarte hoy?', 'bot');
                        welcomed = true;
                    }
                    input.focus();
                }

                function closePanel() {
                    panel.classList.remove('open');
                    panel.setAttribute('aria-hidden', 'true');
                }

                async function sendMessage() {
                    var text = (input.value || '').trim();
                    if (!text) {
                        return;
                    }

                    addMessage(text, 'user');
                    input.value = '';
                    sendBtn.disabled = true;

                    try {
                        var response = await fetch(endpoint, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ message: text })
                        });

                        if (!response.ok) {
                            addMessage('No pude procesar tu mensaje en este momento. Intenta nuevamente.', 'bot');
                        } else {
                            var payload = await response.json();
                            addMessage((payload && payload.reply) ? payload.reply : 'No tengo respuesta para eso todavía.', 'bot');
                        }
                    } catch (error) {
                        addMessage('Ocurrió un error de conexión con el asistente.', 'bot');
                    } finally {
                        sendBtn.disabled = false;
                        input.focus();
                    }
                }

                fab.addEventListener('click', function () {
                    if (panel.classList.contains('open')) {
                        closePanel();
                    } else {
                        openPanel();
                    }
                });

                closeBtn.addEventListener('click', closePanel);
                sendBtn.addEventListener('click', sendMessage);
                input.addEventListener('keydown', function (event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        sendMessage();
                    }
                });
            })();
        </script>
        @stack('scripts')
    </body>
</html>
