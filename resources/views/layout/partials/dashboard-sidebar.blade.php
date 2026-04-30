@php
  $u = auth()->user();
  $catalogOpen = $u && $u->is_admin && request()->routeIs(['categories.*', 'sub_categories.*', 'sub_sub_categories.*']);
  $storeMgmtOpen = $u && $u->is_admin && request()->routeIs(['admin.stores.*', 'admin.products.*']);
  $productMgmtOpen = $u && $u->store_id && request()->routeIs(['products.*', 'inventory.*', 'stock.*', 'coupons.*']);
@endphp

<ul
  class="nav sidebar-menu flex-column"
  data-lte-toggle="treeview"
  role="navigation"
  aria-label="Main navigation"
  data-accordion="true"
  id="navigation"
>
  <li class="nav-item">
    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
      <i class="nav-icon bi bi-speedometer2"></i>
      <p>Dashboard</p>
    </a>
  </li>

  @if ($u?->is_admin)
    <li class="nav-header text-uppercase opacity-75 small">Catalog</li>

    <li class="nav-item {{ $catalogOpen ? 'menu-open' : '' }}">
      <a href="#" class="nav-link {{ $catalogOpen ? 'active' : '' }}">
        <i class="nav-icon bi bi-folder2-open"></i>
        <p>
          Catalog tree
          <i class="nav-arrow bi bi-chevron-right"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item">
          <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
            <i class="nav-icon bi bi-folder"></i>
            <p>Categories</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('sub_categories.index') }}" class="nav-link {{ request()->routeIs('sub_categories.*') ? 'active' : '' }}">
            <i class="nav-icon bi bi-folder-symlink"></i>
            <p>Sub-categories</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('sub_sub_categories.index') }}" class="nav-link {{ request()->routeIs('sub_sub_categories.*') ? 'active' : '' }}">
            <i class="nav-icon bi bi-folder-plus"></i>
            <p>Sub-sub-categories</p>
          </a>
        </li>
      </ul>
    </li>
  @endif

  @if ($u?->store_id)
    <li class="nav-header text-uppercase opacity-75 small">Product Management</li>

    <li class="nav-item {{ $productMgmtOpen ? 'menu-open' : '' }}">
      <a href="#" class="nav-link {{ $productMgmtOpen ? 'active' : '' }}">
        <i class="nav-icon bi bi-box-seam"></i>
        <p>
          Products
          <i class="nav-arrow bi bi-chevron-right"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item">
          <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.index') ? 'active' : '' }}">
            <i class="nav-icon bi bi-grid-3x2-gap"></i>
            <p>All Products</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('inventory.index') }}" class="nav-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
            <i class="nav-icon bi bi-clipboard-data"></i>
            <p>Inventory</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link {{ request()->routeIs('stock.*') ? 'active' : '' }}">
            <i class="nav-icon bi bi-boxes"></i>
            <p>Stock Transactions</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link {{ request()->routeIs('coupons.*') ? 'active' : '' }}">
            <i class="nav-icon bi bi-ticket-perforated"></i>
            <p>Coupons</p>
          </a>
        </li>
      </ul>
    </li>
  @endif

  @if ($u?->is_admin || $u?->store_id)
    <li class="nav-item">
      <a href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
        <i class="nav-icon bi bi-person-circle"></i>
        <p>Profile</p>
      </a>
    </li>
  @endif

  @if ($u?->is_admin)
    <li class="nav-header text-uppercase opacity-75 small">Admin</li>

    <li class="nav-item {{ $storeMgmtOpen ? 'menu-open' : '' }}">
      <a href="#" class="nav-link {{ $storeMgmtOpen ? 'active' : '' }}">
        <i class="nav-icon bi bi-shop-window"></i>
        <p>
          Store management
          <i class="nav-arrow bi bi-chevron-right"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item">
          <a href="{{ route('admin.stores.index') }}" class="nav-link {{ request()->routeIs('admin.stores.*') ? 'active' : '' }}">
            <i class="nav-icon bi bi-shop"></i>
            <p>Stores</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
            <i class="nav-icon bi bi-grid-3x2-gap"></i>
            <p>All products</p>
          </a>
        </li>
      </ul>
    </li>
  @endif
</ul>
