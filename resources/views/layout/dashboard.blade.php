<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>@yield('title', 'Dashboard') | AdminLTE</title>

    <!--begin::Accessibility Meta Tags-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <meta name="color-scheme" content="light dark" />
    <meta name="theme-color" content="#007bff" media="(prefers-color-scheme: light)" />
    <meta name="theme-color" content="#1a1a1a" media="(prefers-color-scheme: dark)" />
    <!--end::Accessibility Meta Tags-->

    <!--begin::Primary Meta Tags-->
    <meta name="title" content="AdminLTE v4 | Dashboard" />
    <meta name="author" content="ColorlibHQ" />
    <meta
      name="description"
      content="AdminLTE is a Free Bootstrap 5 Admin Dashboard, 30 example pages using Vanilla JS. Fully accessible with WCAG 2.1 AA compliance."
    />
    <meta
      name="keywords"
      content="bootstrap 5, bootstrap, bootstrap 5 admin dashboard, bootstrap 5 dashboard, bootstrap 5 charts, bootstrap 5 calendar, bootstrap 5 datepicker, bootstrap 5 tables, bootstrap 5 datatable, vanilla js datatable, colorlibhq, colorlibhq dashboard, colorlibhq admin dashboard, accessible admin panel, WCAG compliant"
    />
    <!--end::Primary Meta Tags-->

    <!--begin::Accessibility Features-->
    <!-- Skip links will be dynamically added by accessibility.js -->
    <meta name="supported-color-schemes" content="light dark" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="preload" href="{{ asset('dist/css/adminlte.css') }}" as="style" />
    <!--end::Accessibility Features-->

    <!--begin::Fonts-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
      integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q="
      crossorigin="anonymous"
      media="print"
      onload="this.media = 'all'"
    />
    <!--end::Fonts-->

    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin(OverlayScrollbars)-->

    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin(Bootstrap Icons)-->

    <!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.css') }}" />
    <!--end::Required Plugin(AdminLTE)-->

    @stack('styles')
    <style>
      .catalog-html-content img {
        max-width: 100%;
        height: auto;
      }
      /* Store dropdown: logo stacked above faded cover — explicit stacking contexts */
      .navbar-store-dropdown-hero {
        position: relative;
        isolation: isolate;
        overflow: hidden;
      }
      .navbar-store-dropdown-hero-cover {
        display: block;
        width: 100%;
        max-height: 120px;
        min-height: 72px;
        object-fit: cover;
        /* Keep image stacking normal; softness comes from overlays */
        opacity: 0.55;
      }
      .navbar-store-dropdown-hero-cover-dim {
        position: absolute;
        inset: 0;
        z-index: 1;
        pointer-events: none;
        background: linear-gradient(to bottom, rgba(255, 255, 255, 0.18), rgba(15, 23, 42, 0.35));
      }
      .navbar-store-dropdown-hero-logo {
        position: absolute;
        left: 50%;
        top: 50%;
        z-index: 2;
        width: 72px;
        height: 72px;
        transform: translate(-50%, -50%);
        object-fit: cover;
        margin: 0;
        box-shadow: 0 0.25rem 0.85rem rgba(0, 0, 0, 0.45);
      }
    </style>
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      <nav class="app-header navbar navbar-expand bg-body">
        <!--begin::Container-->
        <div class="container-fluid">
          <!--begin::Start Navbar Links-->
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                <i class="bi bi-list"></i>
              </a>
            </li>
            @auth
              @php
                $dashStoreModel = auth()->user()?->store;
              @endphp
              @if ($dashStoreModel)
                <li class="nav-item d-none d-sm-flex align-items-center ps-2 pe-2 ms-1 border-start">
                  @if ($dashStoreModel->logo)
                    <a
                      href="{{ route('dashboard') }}"
                      class="d-flex align-items-center gap-2 text-decoration-none navbar-store-brand-link"
                      title="{{ $dashStoreModel->name }}"
                    >
                      <img
                        src="{{ asset('storage/'.$dashStoreModel->logo) }}"
                        alt=""
                        class="rounded border bg-body-secondary shadow-sm"
                        style="height: 34px; width: 34px; object-fit: cover"
                      />
                      <span class="fw-semibold text-body small text-truncate" style="max-width: 10rem">{{ $dashStoreModel->name }}</span>
                    </a>
                  @else
                    <span class="small fw-semibold text-body-secondary text-truncate" style="max-width: 12rem">{{ $dashStoreModel->name }}</span>
                  @endif
                </li>
              @endif
            @endauth
            <li class="nav-item d-none d-md-block">
              <a href="#" class="nav-link">Home</a>
            </li>
            <li class="nav-item d-none d-md-block">
              <a href="#" class="nav-link">Contact</a>
            </li>
          </ul>
          <!--end::Start Navbar Links-->

          <!--begin::End Navbar Links-->
          <ul class="navbar-nav ms-auto">
            <!--begin::Navbar Search-->
            <li class="nav-item">
              <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                <i class="bi bi-search"></i>
              </a>
            </li>
            <!--end::Navbar Search-->

            <!--begin::Messages Dropdown Menu-->
            <li class="nav-item dropdown">
              <a class="nav-link" data-bs-toggle="dropdown" href="#">
                <i class="bi bi-chat-text"></i>
                <span class="navbar-badge badge text-bg-danger">3</span>
              </a>
              <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                <a href="#" class="dropdown-item">
                  <!--begin::Message-->
                  <div class="d-flex">
                    <div class="flex-shrink-0">
                      <img
                        src="{{ asset('dist/assets/img/user1-128x128.jpg') }}"
                        alt="User Avatar"
                        class="img-size-50 rounded-circle me-3"
                      />
                    </div>
                    <div class="flex-grow-1">
                      <h3 class="dropdown-item-title">
                        Brad Diesel
                        <span class="float-end fs-7 text-danger"
                          ><i class="bi bi-star-fill"></i
                        ></span>
                      </h3>
                      <p class="fs-7">Call me whenever you can...</p>
                      <p class="fs-7 text-secondary">
                        <i class="bi bi-clock-fill me-1"></i> 4 Hours Ago
                      </p>
                    </div>
                  </div>
                  <!--end::Message-->
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                  <!--begin::Message-->
                  <div class="d-flex">
                    <div class="flex-shrink-0">
                      <img
                        src="{{ asset('dist/assets/img/user8-128x128.jpg') }}"
                        alt="User Avatar"
                        class="img-size-50 rounded-circle me-3"
                      />
                    </div>
                    <div class="flex-grow-1">
                      <h3 class="dropdown-item-title">
                        John Pierce
                        <span class="float-end fs-7 text-secondary">
                          <i class="bi bi-star-fill"></i>
                        </span>
                      </h3>
                      <p class="fs-7">I got your message bro</p>
                      <p class="fs-7 text-secondary">
                        <i class="bi bi-clock-fill me-1"></i> 4 Hours Ago
                      </p>
                    </div>
                  </div>
                  <!--end::Message-->
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                  <!--begin::Message-->
                  <div class="d-flex">
                    <div class="flex-shrink-0">
                      <img
                        src="{{ asset('dist/assets/img/user3-128x128.jpg') }}"
                        alt="User Avatar"
                        class="img-size-50 rounded-circle me-3"
                      />
                    </div>
                    <div class="flex-grow-1">
                      <h3 class="dropdown-item-title">
                        Nora Silvester
                        <span class="float-end fs-7 text-warning">
                          <i class="bi bi-star-fill"></i>
                        </span>
                      </h3>
                      <p class="fs-7">The subject goes here</p>
                      <p class="fs-7 text-secondary">
                        <i class="bi bi-clock-fill me-1"></i> 4 Hours Ago
                      </p>
                    </div>
                  </div>
                  <!--end::Message-->
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
              </div>
            </li>
            <!--end::Messages Dropdown Menu-->

            <!--begin::Notifications Dropdown Menu-->
            <li class="nav-item dropdown">
              <a class="nav-link" data-bs-toggle="dropdown" href="#">
                <i class="bi bi-bell-fill"></i>
                <span class="navbar-badge badge text-bg-warning">15</span>
              </a>
              <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                <span class="dropdown-item dropdown-header">15 Notifications</span>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                  <i class="bi bi-envelope me-2"></i> 4 new messages
                  <span class="float-end text-secondary fs-7">3 mins</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                  <i class="bi bi-people-fill me-2"></i> 8 friend requests
                  <span class="float-end text-secondary fs-7">12 hours</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                  <i class="bi bi-file-earmark-fill me-2"></i> 3 new reports
                  <span class="float-end text-secondary fs-7">2 days</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item dropdown-footer"> See All Notifications </a>
              </div>
            </li>
            <!--end::Notifications Dropdown Menu-->

            <!--begin::Fullscreen Toggle-->
            <li class="nav-item">
              <a class="nav-link" href="#" data-lte-toggle="fullscreen">
                <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
                <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none"></i>
              </a>
            </li>
            <!--end::Fullscreen Toggle-->

            <!--begin::User Menu Dropdown-->
            <li class="nav-item dropdown user-menu">
              @php
                $currentUser = auth()->user();
                /** @var \App\Models\User|null $currentUser */
                $dashStoreDropdown = $currentUser?->store;
                $firstName = $currentUser ? ($currentUser->first_name ?: $currentUser->name) : '';
                $fullName = $currentUser
                    ? (trim(($currentUser->first_name ?? '').' '.($currentUser->last_name ?? '')))
                    : '';
                $fullName = $fullName !== '' ? $fullName : ($currentUser?->name ?? '');
                $navAvatar =
                    $dashStoreDropdown && $dashStoreDropdown->logo
                        ? asset('storage/'.$dashStoreDropdown->logo)
                        : asset('dist/assets/img/user2-160x160.jpg');
              @endphp
              <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                <img src="{{ $navAvatar }}" class="user-image rounded-circle shadow border" alt="" />
                <span class="d-none d-md-inline">{{ $firstName }}</span>
              </a>
              <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0 overflow-hidden">
                <!--begin::Store + user branding-->
                @if ($dashStoreDropdown && ($dashStoreDropdown->cover_image || $dashStoreDropdown->logo))
                  @if ($dashStoreDropdown->cover_image)
                    <li class="p-0 list-unstyled">
                      <div class="navbar-store-dropdown-hero rounded-top">
                        {{-- Bottom: cover — opacity + overlays sit inside same isolated context --}}
                        <img
                          src="{{ asset('storage/'.$dashStoreDropdown->cover_image) }}"
                          alt=""
                          aria-hidden="true"
                          class="navbar-store-dropdown-hero-cover rounded-top"
                        />
                        <div class="navbar-store-dropdown-hero-cover-dim"></div>
                        @if ($dashStoreDropdown->logo)
                          {{-- Top: logo above cover + dim (higher paint order, same stacking context via z-index) --}}
                          <img
                            src="{{ asset('storage/'.$dashStoreDropdown->logo) }}"
                            alt=""
                            width="72"
                            height="72"
                            class="navbar-store-dropdown-hero-logo rounded-circle border border-2 border-white bg-body-secondary"
                          />
                        @endif
                      </div>
                    </li>
                  @endif
                  <li
                    class="px-3 py-3 text-center bg-body-secondary border-top border-secondary-subtle list-unstyled text-body @if ($dashStoreDropdown->cover_image) pt-3 @elseif ($dashStoreDropdown->logo && ! $dashStoreDropdown->cover_image) pt-2 @endif"
                  >
                    @if (! $dashStoreDropdown->cover_image && $dashStoreDropdown->logo)
                      <img
                        src="{{ asset('storage/'.$dashStoreDropdown->logo) }}"
                        alt=""
                        width="72"
                        height="72"
                        class="rounded-circle shadow-sm border mb-2 mx-auto bg-body-secondary d-block"
                        style="object-fit: cover"
                      />
                    @endif
                    <p class="mb-1 fw-semibold">{{ $dashStoreDropdown->name }}</p>
                    <small class="d-block text-body-secondary">{{ $fullName }}</small>
                    <small class="text-muted">{{ $currentUser?->email }}</small>
                  </li>
                @else
                  <li class="user-header bg-body-secondary text-body px-4 py-3 rounded-0 mb-0 border-bottom border-secondary-subtle text-center">
                    <img src="{{ $navAvatar }}" class="rounded-circle shadow-sm border mb-2" alt="" width="80" height="80" />
                    <p class="mb-1 fw-semibold">{{ $fullName }}</p>
                    <small class="d-block text-muted">{{ $currentUser?->email }}</small>
                  </li>
                @endif
                <!--end::Store + user branding-->
                <!--begin::Menu Footer-->
                <li class="user-footer">
                  <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary">الملف الشخصي</a>
                  <form method="post" action="{{ route('logout') }}" class="d-inline float-end">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">تسجيل الخروج</button>
                  </form>
                </li>
                <!--end::Menu Footer-->
              </ul>
            </li>
            <!--end::User Menu Dropdown-->
          </ul>
          <!--end::End Navbar Links-->
        </div>
        <!--end::Container-->
      </nav>
      <!--end::Header-->
      <!--begin::Sidebar-->
      <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
        <!--begin::Sidebar Brand-->
        <div class="sidebar-brand">
          <!--begin::Brand Link-->
          <a href="{{ url('/') }}" class="brand-link">
            <!--begin::Brand Image-->
            <img
              src="{{ asset('dist/assets/img/AdminLTELogo.png') }}"
              alt="AdminLTE Logo"
              class="brand-image opacity-75 shadow"
            />
            <!--end::Brand Image-->
            <!--begin::Brand Text-->
            <span class="brand-text fw-light">AdminLTE 4</span>
            <!--end::Brand Text-->
          </a>
          <!--end::Brand Link-->
        </div>
        <!--end::Sidebar Brand-->
        <!--begin::Sidebar Wrapper-->
        <div class="sidebar-wrapper">
          <nav class="mt-2">
            <!--begin::Sidebar Menu-->
            @include('layout.partials.dashboard-sidebar')
            <!--end::Sidebar Menu-->
          </nav>
        </div>
        <!--end::Sidebar Wrapper-->
      </aside>
      <!--end::Sidebar-->
      <!--begin::App Main-->
      <main class="app-main">
        <!--begin::App Content Header-->
        <div class="app-content-header">
          <!--begin::Container-->
          <div class="container-fluid">
            <!--begin::Row-->
            @hasSection('content_header')
              <div class="row">
                <div class="col-12">
                  @yield('content_header')
                </div>
              </div>
            @else
              <div class="row">
                <div class="col-sm-6">
                  <h3 class="mb-0">@yield('page_title', 'Dashboard')</h3>
                </div>
                <div class="col-sm-6">
                  @hasSection('breadcrumb')
                    <div class="float-sm-end">
                      @yield('breadcrumb')
                    </div>
                  @else
                    @php
                      $breadcrumbCurrent = trim($__env->yieldContent('page_title')) ?: 'Dashboard';
                    @endphp
                    <div class="float-sm-end">
                      <x-breadcrumb
                        :items="[
                            ['label' => 'Home', 'url' => route('dashboard')],
                            ['label' => $breadcrumbCurrent],
                        ]"
                      />
                    </div>
                  @endif
                </div>
              </div>
            @endif
            <!--end::Row-->
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content Header-->
        <!--begin::App Content-->
        <div class="app-content">
          <div class="container-fluid">
            @yield('content')
          </div>
        </div>
        <!--end::App Content-->
      </main>
      <!--end::App Main-->
      <!--begin::Footer-->
      <footer class="app-footer">
        <!--begin::To the end-->
        <div class="float-end d-none d-sm-inline">Anything you want</div>
        <!--end::To the end-->
        <!--begin::Copyright-->
        <strong>
          Copyright &copy; 2014-2026&nbsp;
          <a href="https://adminlte.io" class="text-decoration-none">AdminLTE.io</a>.
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
      src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"
      crossorigin="anonymous"
    ></script>
    <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script
      src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
      crossorigin="anonymous"
    ></script>
    <!--end::Required Plugin(popperjs for Bootstrap 5)--><!--begin::Required Plugin(Bootstrap 5)-->
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js"
      crossorigin="anonymous"
    ></script>
    <!--end::Required Plugin(Bootstrap 5)--><!--begin::Required Plugin(AdminLTE)-->
    <script src="{{ asset('dist/js/adminlte.js') }}"></script>
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

        // Disable OverlayScrollbars on mobile devices to prevent touch interference
        const isMobile = window.innerWidth <= 992;

        if (
          sidebarWrapper &&
          OverlayScrollbarsGlobal?.OverlayScrollbars !== undefined &&
          !isMobile
        ) {
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

    @stack('scripts')
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
