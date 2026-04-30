@extends('layout.dashboard')

@section('title', 'All products')

@section('content_header')
  <x-page-heading
    title="All products (read-only)"
    :breadcrumb-items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Products'],
    ]"
  />
@endsection

@section('page_title', 'Products')
@section('content')
  <div class="card shadow-sm mb-3">
    <div class="card-body py-3">
      <form method="get" action="{{ route('admin.products.index') }}" class="row g-2 align-items-end">
        <div class="col-lg-3 col-md-6">
          <label class="form-label small mb-1" for="category_id">{{ __('Category') }}</label>
          <select id="category_id" name="category_id" class="form-select">
            @foreach ($categoryFilterOptions ?? [] as $opt)
              <option value="{{ $opt['value'] }}" @selected(old('category_id', (string) ($filters['category_id'] ?? '')) === (string) $opt['value'])>
                {{ $opt['label'] }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-lg-3 col-md-6">
          <label class="form-label small mb-1" for="product_status">{{ __('Status') }}</label>
          <select id="product_status" name="status" class="form-select">
            @foreach ($statusFilterOptions ?? [] as $opt)
              <option value="{{ $opt['value'] }}" @selected(old('status', (string) ($filters['status'] ?? '')) === (string) $opt['value'])>
                {{ $opt['label'] }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-lg col-md-6">
          <label class="form-label small mb-1" for="search">{{ __('Search (name / description)') }}</label>
          <input
            id="search"
            type="search"
            name="search"
            value="{{ old('search', $filters['search'] ?? '') }}"
            class="form-control"
            placeholder="{{ __('Type to search…') }}"
            autocomplete="off"
          />
        </div>
        <div class="col-lg-2 col-md-6">
          <label class="form-label small mb-1" for="tag">{{ __('Tag') }}</label>
          <input
            id="tag"
            type="text"
            name="tag"
            value="{{ old('tag', $filters['tag'] ?? '') }}"
            class="form-control"
            placeholder="{{ __('Optional') }}"
          />
        </div>
        <div class="col-auto d-flex gap-2 flex-shrink-0">
          <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
          <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">{{ __('Reset') }}</a>
        </div>
      </form>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-body p-0">
      <x-data-table
        :columns="[
            ['key' => 'id', 'label' => '#'],
            ['key' => 'image', 'label' => 'Image'],
            ['key' => 'store.name', 'label' => 'Store'],
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'category.name', 'label' => 'Category'],
            ['key' => 'tag_list', 'label' => 'Tags'],
            ['key' => 'price', 'label' => 'Price'],
            ['key' => 'status', 'label' => 'Status'],
            ['key' => 'created_at', 'label' => 'Created'],
        ]"
        :rows="$products"
        resource="admin.products"
        :manage-actions="false"
      />
    </div>
    <x-pagination-footer :paginator="$products" />
  </div>
@endsection
