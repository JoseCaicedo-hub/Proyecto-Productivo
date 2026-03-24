<!doctype html>
<html lang="es">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>StartPlace.com</title>
    <!--begin::Primary Meta Tags-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="title" content="Sistema | ArtCode.com" />
    <meta name="author" content="ArtCode" />
    <meta
      name="description"
      content="Sistema."
    />
    <meta
      name="keywords"
      content="Sistema, ArtCode"
    />
    <!--end::Primary Meta Tags-->
    <!--begin::Fonts-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
      integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q="
      crossorigin="anonymous"
    />
    <!--end::Fonts-->
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/styles/overlayscrollbars.min.css"
      integrity="sha256-tZHrRjVqNSRyWg2wbppGnT833E/Ys0DHWGwT04GiqQg="
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin(OverlayScrollbars)-->
    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
      integrity="sha256-9kPW/n5nn53j4WMRYAxe9c1rCY96Oogo/MKSVdKzPmI="
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin(Bootstrap Icons)-->
    <!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="{{asset('css/adminlte.css')}}" />
    <!--end::Required Plugin(AdminLTE)-->
    <!--begin::Custom Themes-->
    <link rel="stylesheet" href="{{asset('css/custom-themes.css')}}" />
    <!--end::Custom Themes-->
    <script>
      (function () {
        try {
          var savedTheme = localStorage.getItem('theme');
          if (savedTheme === 'dark') {
            document.documentElement.classList.add('dark-mode');
          }
        } catch (e) {}
      })();
    </script>
    <style>
      html.dark-mode body,
      html.dark-mode .app-wrapper,
      html.dark-mode .app-main,
      html.dark-mode .app-content {
        background-color: #0b1220 !important;
        color: #e2e8f0;
      }
      html.dark-mode .app-header,
      html.dark-mode .app-content-header,
      html.dark-mode .card,
      html.dark-mode .dropdown-menu,
      html.dark-mode .app-footer,
      html.dark-mode .app-sidebar,
      html.dark-mode .sidebar-brand,
      html.dark-mode .sidebar-wrapper,
      html.dark-mode .nav-treeview,
      html.dark-mode .bg-white {
        background-color: #111827 !important;
        color: #e2e8f0 !important;
        border-color: rgba(148,163,184,.2) !important;
      }
      html.dark-mode .nav-link,
      html.dark-mode .breadcrumb-item,
      html.dark-mode .text-muted,
      html.dark-mode .small {
        color: #cbd5e1 !important;
      }
      html.dark-mode .breadcrumb-item.active,
      html.dark-mode .breadcrumb-item + .breadcrumb-item::before {
        color: #94a3b8 !important;
      }
      html.dark-mode .card-header,
      html.dark-mode .card-body,
      html.dark-mode .card-footer {
        background-color: #111827 !important;
        color: #e2e8f0 !important;
      }
      html.dark-mode .dropdown-menu {
        --bs-dropdown-bg: #0f172a;
        --bs-dropdown-color: #e2e8f0;
        --bs-dropdown-link-color: #e2e8f0;
        --bs-dropdown-link-hover-color: #7dd3fc;
        --bs-dropdown-link-hover-bg: rgba(125,211,252,0.08);
        border-color: rgba(148,163,184,.24) !important;
      }
      html.dark-mode .plantilla-user-header,
      html.dark-mode .plantilla-user-actions {
        background: #0f172a !important;
        color: #e2e8f0 !important;
        border-color: rgba(148,163,184,.2) !important;
      }
      html.dark-mode .plantilla-user-name { color: #f8fafc !important; }
      html.dark-mode .plantilla-user-email { color: #94a3b8 !important; }
      html.dark-mode .plantilla-avatar-placeholder {
        background: linear-gradient(180deg, #111827, #0b1220) !important;
        color: #7dd3fc !important;
        box-shadow: 0 8px 22px rgba(2, 6, 23, 0.45) !important;
      }
      html.dark-mode .plantilla-btn.btn-outline-primary {
        border-color: rgba(125,211,252,.45) !important;
        color: #7dd3fc !important;
        background: rgba(125,211,252,.05) !important;
      }
      html.dark-mode .plantilla-btn.btn-outline-secondary,
      html.dark-mode .plantilla-btn.btn-light {
        border-color: rgba(148,163,184,.35) !important;
        color: #cbd5e1 !important;
        background: #111827 !important;
      }
      html.dark-mode .nav-link:hover,
      html.dark-mode a:hover { color: #7dd3fc !important; }
      html.dark-mode .custom-sidebar {
        background: #0f172a !important;
        border-right-color: rgba(148,163,184,.18) !important;
      }
      html.dark-mode .custom-sidebar .sidebar-brand {
        background: #111827 !important;
        border-bottom-color: rgba(148,163,184,.18) !important;
      }
      html.dark-mode .custom-sidebar .brand-text,
      html.dark-mode .custom-sidebar .nav-link,
      html.dark-mode .custom-sidebar .nav-link p,
      html.dark-mode .custom-sidebar .nav-arrow {
        color: #cbd5e1 !important;
      }
      html.dark-mode .custom-sidebar .nav-link .nav-icon {
        color: #60a5fa !important;
      }
      html.dark-mode .custom-sidebar .nav-link:hover {
        background: rgba(96,165,250,.12) !important;
        color: #f8fafc !important;
      }
      html.dark-mode .custom-sidebar .nav-link.active,
      html.dark-mode .custom-sidebar .nav-link.active:hover {
        background: rgba(59,130,246,.24) !important;
        color: #f8fafc !important;
        font-weight: 600;
      }
      html.dark-mode .custom-sidebar .nav-treeview {
        background-color: #0b1220 !important;
      }
      html.dark-mode .custom-sidebar .nav-treeview .nav-link.active {
        background: rgba(14,165,233,.20) !important;
      }
      html.dark-mode .alert-success {
        background-color: rgba(16, 185, 129, 0.12) !important;
        border-color: rgba(16, 185, 129, 0.35) !important;
        color: #a7f3d0 !important;
      }
      html.dark-mode .form-control,
      html.dark-mode .form-select,
      html.dark-mode textarea,
      html.dark-mode input {
        background-color: #0b1220 !important;
        color: #e2e8f0 !important;
        border-color: rgba(148,163,184,.28) !important;
      }
      html.dark-mode .table,
      html.dark-mode .table td,
      html.dark-mode .table th {
        color: #e2e8f0 !important;
        border-color: rgba(148,163,184,.24) !important;
      }
      html.dark-mode .table {
        --bs-table-bg: #0f172a;
        --bs-table-color: #e2e8f0;
        --bs-table-border-color: rgba(148,163,184,.24);
        --bs-table-striped-bg: #111827;
        --bs-table-striped-color: #e2e8f0;
        --bs-table-hover-bg: #1e293b;
        --bs-table-hover-color: #f8fafc;
      }
      html.dark-mode .table thead th { background-color: #111827 !important; }
      html.dark-mode .table tbody td { background-color: #0f172a !important; }
      html.dark-mode .modal-content,
      html.dark-mode .modal-header,
      html.dark-mode .modal-footer {
        background-color: #0f172a !important;
        color: #e2e8f0 !important;
        border-color: rgba(148,163,184,.24) !important;
      }
      html.dark-mode .btn-close {
        filter: invert(1) grayscale(100%) brightness(180%);
      }
    </style>
    @stack('estilos')
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      @include('plantilla.header')
      <!--end::Header-->
      <!--begin::Sidebar-->
      @include('plantilla.menu')
      <!--end::Sidebar-->
      <!--begin::App Main-->
      <main class="app-main">
        <!--begin::App Content Header-->
        <div class="app-content-header bg-white border-bottom">
          <!--begin::Container-->
          <div class="container-fluid py-3">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                @php
                  $roleLabel = 'User';
                  if (auth()->check()) {
                    if (auth()->user()->hasAnyRole(['admin', 'administrator', 'Admin', 'Administrador'])) {
                      $roleLabel = 'Admin';
                    } elseif (auth()->user()->hasRole('vendedor')) {
                      $roleLabel = 'Vendedor';
                    } elseif (auth()->user()->hasRole('cliente')) {
                      $roleLabel = 'Cliente';
                    } else {
                      $roleLabel = 'Usuario';
                    }
                  }
                @endphp
                <h1 class="h4 mb-0">@yield('titulo', $roleLabel)</h1>
                <div class="small text-muted mt-1">@yield('subtitulo', '')</div>
              </div>
              <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                  <li class="breadcrumb-item"><a href="{{ route('web.index') }}">Inicio</a></li>
                  <li class="breadcrumb-item active" aria-current="page">@yield('titulo', 'Panel')</li>
                </ol>
              </nav>
            </div>
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content Header-->

        <!--begin::App Content-->
        <div class="app-content">
          <div class="container-fluid py-4">
            <div class="row">
              <div class="col-12">
                <div class="card shadow-sm">
                  <div class="card-body">
                    @yield('contenido')
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!--end::App Content-->
      </main>
      <!--end::App Main-->
      <!--begin::Footer-->
      <footer class="app-footer">
        <!--begin::To the end-->
        <div class="float-end d-none d-sm-inline"></div>
        <!--end::To the end-->
        <!--begin::Copyright-->
        <strong>
          Copyright &copy; 2025&nbsp;
          <a href="#" class="text-decoration-none">StartPlace</a>.
        </strong>
        All rights reserved.
        <!--end::Copyright-->
      </footer>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <script
      src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/browser/overlayscrollbars.browser.es6.min.js"
      integrity="sha256-dghWARbRe2eLlIJ56wNB+b760ywulqK3DzZYEpsg2fQ="
      crossorigin="anonymous"
    ></script>
    <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script
      src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
      integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
      crossorigin="anonymous"
    ></script>
    <!--end::Required Plugin(popperjs for Bootstrap 5)--><!--begin::Required Plugin(Bootstrap 5)-->
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
      integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
      crossorigin="anonymous"
    ></script>
    <!--end::Required Plugin(Bootstrap 5)--><!--begin::Required Plugin(AdminLTE)-->
    <script src="{{asset('js/adminlte.js')}}"></script>
    <!--end::Required Plugin(AdminLTE)--><!--begin::OverlayScrollbars Configure-->
    <script>
      const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
      const Default = {
        scrollbarTheme: 'os-theme-light',
        scrollbarAutoHide: 'leave',
        scrollbarClickScroll: true,
      };
      document.addEventListener('DOMContentLoaded', function () {
        const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
        if (sidebarWrapper && typeof OverlayScrollbarsGlobal?.OverlayScrollbars !== 'undefined') {
          OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
            scrollbars: {
              theme: Default.scrollbarTheme,
              autoHide: Default.scrollbarAutoHide,
              clickScroll: Default.scrollbarClickScroll,
            },
          });
        }
      });
    </script>
    <!--end::OverlayScrollbars Configure-->
    <script>
      (function () {
        var root = document.documentElement;
        var button = document.getElementById('panelThemeToggleBtn');
        if (!button) return;

        var moon = button.querySelector('.panel-theme-icon-moon');
        var sun = button.querySelector('.panel-theme-icon-sun');

        function renderIcons() {
          var isDark = root.classList.contains('dark-mode');
          if (moon && sun) {
            moon.classList.toggle('d-none', isDark);
            sun.classList.toggle('d-none', !isDark);
          }
        }

        function setTheme(theme) {
          var isDark = theme === 'dark';
          root.classList.toggle('dark-mode', isDark);
          try { localStorage.setItem('theme', theme); } catch (e) {}
          renderIcons();
        }

        renderIcons();

        button.addEventListener('click', function () {
          var nextTheme = root.classList.contains('dark-mode') ? 'light' : 'dark';
          setTheme(nextTheme);
        });
      })();
    </script>
    <!--end::Script-->
    @stack('scripts')
  </body>
  <!--end::Body-->
</html>
